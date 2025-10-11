<template>
  <div class="rich-text-editor">
    <div class="editor-toolbar">
      <el-button-group>
        <el-button @click="insertImage" :disabled="disabled">
          <el-icon><Picture /></el-icon>
          Image
        </el-button>
        <el-button @click="insertLink" :disabled="disabled">
          <el-icon><Link /></el-icon>
          Link
        </el-button>
        <el-button @click="insertTable" :disabled="disabled">
          <el-icon><Grid /></el-icon>
          Table
        </el-button>
        <el-button @click="clearContent" :disabled="disabled">
          <el-icon><Delete /></el-icon>
          Clear
        </el-button>
      </el-button-group>
      
      <div class="editor-actions">
        <el-button @click="previewContent" :disabled="disabled">
          <el-icon><View /></el-icon>
          Preview
        </el-button>
        <el-button type="primary" @click="saveContent" :disabled="disabled" :loading="saving">
          <el-icon><Check /></el-icon>
          Save
        </el-button>
      </div>
    </div>

    <div class="editor-container">
      <Toolbar
        :editor="editorRef"
        :defaultConfig="toolbarConfig"
        :mode="mode"
        class="editor-toolbar-component"
      />
      <Editor
        v-model="editorValue"
        :defaultConfig="editorConfig"
        :mode="mode"
        @onCreated="handleCreated"
        @onChange="handleChange"
        class="editor-content"
        :style="{ height: editorHeight + 'px' }"
      />
    </div>

    <!-- 图片上传对话框 -->
    <el-dialog
      v-model="imageDialogVisible"
      title="Insert Image"
      width="500px"
    >
      <el-form :model="imageForm" label-width="80px">
        <el-form-item label="Image URL">
          <el-input
            v-model="imageForm.url"
            placeholder="Enter image URL"
          />
        </el-form-item>
        <el-form-item label="Alt Text">
          <el-input
            v-model="imageForm.alt"
            placeholder="Enter alt text"
          />
        </el-form-item>
        <el-form-item label="Upload">
          <FileUpload
            ref="imageUploadRef"
            :business-type="'general'"
            :multiple="false"
            :limit="1"
            :max-size="5"
            :button-text="'Select Image'"
            @success="handleImageUploadSuccess"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="imageDialogVisible = false">Cancel</el-button>
        <el-button type="primary" @click="insertImageFromUrl">Insert</el-button>
      </template>
    </el-dialog>

    <!-- 链接插入对话框 -->
    <el-dialog
      v-model="linkDialogVisible"
      title="Insert Link"
      width="400px"
    >
      <el-form :model="linkForm" label-width="80px">
        <el-form-item label="URL">
          <el-input
            v-model="linkForm.url"
            placeholder="Enter URL"
          />
        </el-form-item>
        <el-form-item label="Text">
          <el-input
            v-model="linkForm.text"
            placeholder="Enter link text"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="linkDialogVisible = false">Cancel</el-button>
        <el-button type="primary" @click="insertLinkFromForm">Insert</el-button>
      </template>
    </el-dialog>

    <!-- 预览对话框 -->
    <el-dialog
      v-model="previewVisible"
      title="Content Preview"
      width="80%"
      center
    >
      <div class="preview-content" v-html="editorValue"></div>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, onBeforeUnmount, shallowRef, nextTick, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { Picture, Link, Grid, Delete, View, Check } from '@element-plus/icons-vue'
import { Editor, Toolbar } from '@wangeditor/editor-for-vue'
import '@wangeditor/editor/dist/css/style.css'
import FileUpload from './FileUpload.vue'

interface Props {
  modelValue?: string
  height?: number
  disabled?: boolean
  placeholder?: string
  businessType?: 'product' | 'article' | 'general'
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: '',
  height: 400,
  disabled: false,
  placeholder: 'Please enter content...',
  businessType: 'general'
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  'change': [value: string]
  'save': [value: string]
}>()

const editorRef = shallowRef()
const editorValue = ref(props.modelValue)
const saving = ref(false)
const imageDialogVisible = ref(false)
const linkDialogVisible = ref(false)
const previewVisible = ref(false)
const imageUploadRef = ref()

const imageForm = reactive({
  url: '',
  alt: ''
})

const linkForm = reactive({
  url: '',
  text: ''
})

// 编辑器配置
const editorConfig = reactive({
  placeholder: props.placeholder,
  readOnly: props.disabled,
  MENU_CONF: {
    // 配置上传图片
    uploadImage: {
      server: '/api/upload/image',
      fieldName: 'file',
      headers: {
        // 如果需要token，在这里配置
      },
      // 上传成功后的回调
      onSuccess: (file: any, res: any) => {
        console.log('Image upload success:', res)
      },
      // 上传失败后的回调
      onFailed: (file: any, res: any) => {
        console.log('Image upload failed:', res)
        ElMessage.error('Image upload failed')
      }
    },
    // 配置上传视频
    uploadVideo: {
      server: '/api/upload/video',
      fieldName: 'file',
      headers: {},
      onSuccess: (file: any, res: any) => {
        console.log('Video upload success:', res)
      },
      onFailed: (file: any, res: any) => {
        console.log('Video upload failed:', res)
        ElMessage.error('Video upload failed')
      }
    }
  }
})

// 工具栏配置
const toolbarConfig = reactive({
  toolbarKeys: [
    'headerSelect',
    'bold',
    'italic',
    'underline',
    'through',
    'code',
    'sup',
    'sub',
    'clearStyle',
    '|',
    'color',
    'bgColor',
    '|',
    'fontSize',
    'fontFamily',
    'lineHeight',
    '|',
    'bulletedList',
    'numberedList',
    'todo',
    '|',
    'emotion',
    'insertLink',
    'insertImage',
    'insertVideo',
    'insertTable',
    'codeBlock',
    'divider',
    '|',
    'undo',
    'redo',
    '|',
    'fullScreen'
  ]
})

const mode = ref('default')
const editorHeight = ref(props.height)

// 编辑器创建完成
const handleCreated = (editor: any) => {
  editorRef.value = editor
}

// 内容变化
const handleChange = (editor: any) => {
  const html = editor.getHtml()
  editorValue.value = html
  emit('update:modelValue', html)
  emit('change', html)
}

// 插入图片
const insertImage = () => {
  imageForm.url = ''
  imageForm.alt = ''
  imageDialogVisible.value = true
}

// 从URL插入图片
const insertImageFromUrl = () => {
  if (!imageForm.url) {
    ElMessage.warning('Please enter image URL')
    return
  }
  
  if (editorRef.value) {
    editorRef.value.dangerouslyInsertHtml(
      `<img src="${imageForm.url}" alt="${imageForm.alt || ''}" style="max-width: 100%;" />`
    )
  }
  
  imageDialogVisible.value = false
  ElMessage.success('Image inserted successfully')
}

// 图片上传成功
const handleImageUploadSuccess = (response: any) => {
  if (response && response.url) {
    imageForm.url = response.url
    ElMessage.success('Image uploaded successfully')
  }
}

// 插入链接
const insertLink = () => {
  linkForm.url = ''
  linkForm.text = ''
  linkDialogVisible.value = true
}

// 从表单插入链接
const insertLinkFromForm = () => {
  if (!linkForm.url) {
    ElMessage.warning('Please enter URL')
    return
  }
  
  if (editorRef.value) {
    const text = linkForm.text || linkForm.url
    editorRef.value.dangerouslyInsertHtml(
      `<a href="${linkForm.url}" target="_blank">${text}</a>`
    )
  }
  
  linkDialogVisible.value = false
  ElMessage.success('Link inserted successfully')
}

// 插入表格
const insertTable = () => {
  if (editorRef.value) {
    editorRef.value.dangerouslyInsertHtml(`
      <table border="1" style="border-collapse: collapse; width: 100%;">
        <thead>
          <tr>
            <th style="padding: 8px;">Header 1</th>
            <th style="padding: 8px;">Header 2</th>
            <th style="padding: 8px;">Header 3</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="padding: 8px;">Cell 1</td>
            <td style="padding: 8px;">Cell 2</td>
            <td style="padding: 8px;">Cell 3</td>
          </tr>
          <tr>
            <td style="padding: 8px;">Cell 4</td>
            <td style="padding: 8px;">Cell 5</td>
            <td style="padding: 8px;">Cell 6</td>
          </tr>
        </tbody>
      </table>
    `)
    ElMessage.success('Table inserted successfully')
  }
}

// 清空内容
const clearContent = () => {
  if (editorRef.value) {
    editorRef.value.clear()
    ElMessage.success('Content cleared')
  }
}

// 预览内容
const previewContent = () => {
  previewVisible.value = true
}

// 保存内容
const saveContent = async () => {
  saving.value = true
  try {
    // 模拟保存
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    emit('save', editorValue.value)
    ElMessage.success('Content saved successfully')
  } catch (error) {
    ElMessage.error('Failed to save content')
  } finally {
    saving.value = false
  }
}

// 监听props变化
watch(() => props.modelValue, (newValue) => {
  if (newValue !== editorValue.value) {
    editorValue.value = newValue
  }
})

watch(() => props.disabled, (newValue) => {
  if (editorRef.value) {
    editorRef.value.disable()
  }
})

watch(() => props.height, (newValue) => {
  editorHeight.value = newValue
})

onMounted(() => {
  nextTick(() => {
    // 编辑器初始化完成后的操作
  })
})

onBeforeUnmount(() => {
  if (editorRef.value) {
    editorRef.value.destroy()
  }
})
</script>

<style scoped>
.rich-text-editor {
  border: 1px solid #dcdfe6;
  border-radius: 4px;
  overflow: hidden;
}

.editor-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 15px;
  background-color: #f5f7fa;
  border-bottom: 1px solid #dcdfe6;
}

.editor-actions {
  display: flex;
  gap: 10px;
}

.editor-container {
  position: relative;
}

.editor-toolbar-component {
  border-bottom: 1px solid #dcdfe6;
}

.editor-content {
  overflow-y: auto;
}

.preview-content {
  max-height: 500px;
  overflow-y: auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 4px;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .editor-toolbar {
    flex-direction: column;
    gap: 10px;
  }
  
  .editor-actions {
    width: 100%;
    justify-content: center;
  }
}
</style>
