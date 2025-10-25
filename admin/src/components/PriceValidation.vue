<template>
  <div class="price-validation">
    <el-form-item label="成本价" prop="costPrice" :rules="costPriceRules">
      <el-input-number 
        v-model="formData.costPrice" 
        :min="0.01" 
        :precision="2"
        :step="0.01"
        placeholder="请输入成本价"
        @change="handleCostPriceChange"
        @blur="validatePrices"
      />
      <div v-if="validationResult.warnings.length > 0" class="validation-warnings">
        <el-alert
          v-for="warning in validationResult.warnings"
          :key="warning"
          :title="warning"
          type="warning"
          :closable="false"
          show-icon
        />
      </div>
    </el-form-item>
    
    <el-form-item label="建议价" prop="suggestPrice" :rules="suggestPriceRules">
      <el-input-number 
        v-model="formData.suggestPrice" 
        :min="0.01" 
        :precision="2"
        :step="0.01"
        placeholder="请输入建议价"
        @change="handleSuggestPriceChange"
        @blur="validatePrices"
      />
      <div v-if="priceSuggestions" class="price-suggestions">
        <el-tag size="small" type="info">
          建议范围: RM{{ priceSuggestions.minSuggest }} - RM{{ priceSuggestions.maxSuggest }}
        </el-tag>
        <el-tag size="small" type="success">
          推荐: RM{{ priceSuggestions.recommendedSuggest }}
        </el-tag>
      </div>
    </el-form-item>
    
    <el-form-item label="售价" prop="salePrice" :rules="salePriceRules">
      <el-input-number 
        v-model="formData.salePrice" 
        :min="0.01" 
        :precision="2"
        :step="0.01"
        placeholder="请输入售价"
        @change="validatePrices"
        @blur="validatePrices"
      />
    </el-form-item>
    
    <!-- 验证结果显示 -->
    <div v-if="validationResult.errors.length > 0" class="validation-errors">
      <el-alert
        v-for="error in validationResult.errors"
        :key="error"
        :title="error"
        type="error"
        :closable="false"
        show-icon
      />
    </div>
    
    <div v-if="validationResult.suggestions.length > 0" class="validation-suggestions">
      <el-alert
        v-for="suggestion in validationResult.suggestions"
        :key="suggestion"
        :title="suggestion"
        type="info"
        :closable="false"
        show-icon
      />
    </div>
    
    <!-- 利润率显示 -->
    <div v-if="validationResult.profitMargin" class="profit-margin">
      <el-tag :type="getProfitMarginType(validationResult.profitMargin)">
        利润率: {{ validationResult.profitMargin }}%
      </el-tag>
    </div>
  </div>
</template>

<script>
import { ref, reactive, watch, computed } from 'vue';
import { ElMessage } from 'element-plus';

export default {
  name: 'PriceValidation',
  props: {
    modelValue: {
      type: Object,
      default: () => ({
        costPrice: 0,
        suggestPrice: 0,
        salePrice: 0
      })
    }
  },
  emits: ['update:modelValue', 'validation-change'],
  setup(props, { emit }) {
    const formData = reactive({ ...props.modelValue });
    const validationResult = reactive({
      isValid: true,
      errors: [],
      warnings: [],
      suggestions: [],
      profitMargin: 0
    });
    const priceSuggestions = ref(null);
    
    // 监听表单数据变化
    watch(formData, (newVal) => {
      emit('update:modelValue', newVal);
    }, { deep: true });
    
    // 成本价验证规则
    const costPriceRules = [
      { required: true, message: '请输入成本价', trigger: 'blur' },
      { 
        validator: (rule, value, callback) => {
          if (value <= 0) {
            callback(new Error('成本价必须大于0'));
          } else if (formData.suggestPrice && value >= formData.suggestPrice) {
            callback(new Error('成本价必须低于建议价'));
          } else {
            callback();
          }
        }, 
        trigger: 'blur' 
      }
    ];
    
    // 建议价验证规则
    const suggestPriceRules = [
      { required: true, message: '请输入建议价', trigger: 'blur' },
      { 
        validator: (rule, value, callback) => {
          if (value <= 0) {
            callback(new Error('建议价必须大于0'));
          } else if (formData.costPrice && value <= formData.costPrice) {
            callback(new Error('建议价必须高于成本价'));
          } else {
            callback();
          }
        }, 
        trigger: 'blur' 
      }
    ];
    
    // 售价验证规则
    const salePriceRules = [
      { 
        validator: (rule, value, callback) => {
          if (value && value <= 0) {
            callback(new Error('售价必须大于0'));
          } else if (value && formData.costPrice && value < formData.costPrice) {
            callback(new Error('售价不能低于成本价'));
          } else {
            callback();
          }
        }, 
        trigger: 'blur' 
      }
    ];
    
    // 处理成本价变化
    const handleCostPriceChange = async (value) => {
      if (value > 0) {
        try {
          const response = await fetch('/api/products/price-suggestions', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ costPrice: value })
          });
          const data = await response.json();
          priceSuggestions.value = data;
        } catch (error) {
          console.error('获取价格建议失败:', error);
        }
      }
      validatePrices();
    };
    
    // 处理建议价变化
    const handleSuggestPriceChange = (value) => {
      validatePrices();
    };
    
    // 验证价格
    const validatePrices = async () => {
      if (!formData.costPrice || !formData.suggestPrice) {
        return;
      }
      
      try {
        const response = await fetch('/api/products/validate-prices', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            costPrice: formData.costPrice,
            suggestPrice: formData.suggestPrice,
            salePrice: formData.salePrice
          })
        });
        const result = await response.json();
        
        Object.assign(validationResult, result);
        emit('validation-change', result);
        
        if (!result.isValid) {
          ElMessage.error('价格设置有问题，请检查');
        }
      } catch (error) {
        console.error('价格验证失败:', error);
        ElMessage.error('价格验证失败');
      }
    };
    
    // 获取利润率标签类型
    const getProfitMarginType = (margin) => {
      if (margin < 10) return 'danger';
      if (margin < 20) return 'warning';
      if (margin < 50) return 'success';
      if (margin < 100) return 'primary';
      return 'info';
    };
    
    return {
      formData,
      validationResult,
      priceSuggestions,
      costPriceRules,
      suggestPriceRules,
      salePriceRules,
      handleCostPriceChange,
      handleSuggestPriceChange,
      validatePrices,
      getProfitMarginType
    };
  }
};
</script>

<style scoped>
.price-validation {
  .validation-warnings,
  .validation-errors,
  .validation-suggestions {
    margin-top: 8px;
  }
  
  .price-suggestions {
    margin-top: 8px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  
  .profit-margin {
    margin-top: 12px;
    text-align: center;
  }
  
  .el-alert {
    margin-bottom: 8px;
  }
}
</style>
