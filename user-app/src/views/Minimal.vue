<template>
  <div class="minimal-test">
    <h1>Hello Vue.js!</h1>
    <p>如果你能看到这个页面，说明Vue.js正常工作！</p>
    <p>当前时间: {{ currentTime }}</p>
    <button @click="updateTime">更新时间</button>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const currentTime = ref('')

const updateTime = () => {
  currentTime.value = new Date().toLocaleString()
}

onMounted(() => {
  updateTime()
  console.log('Minimal test component mounted')
})
</script>

<style scoped>
.minimal-test {
  padding: 50px;
  text-align: center;
  background: #fff;
  min-height: 100vh;
}

h1 {
  color: #409EFF;
  font-size: 48px;
  margin-bottom: 20px;
}

p {
  color: #666;
  font-size: 18px;
  margin-bottom: 20px;
}

button {
  background: #409EFF;
  color: #fff;
  border: none;
  padding: 15px 30px;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
}

button:hover {
  background: #337ecc;
}
</style>
