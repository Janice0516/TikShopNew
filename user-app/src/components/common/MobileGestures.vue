<template>
  <div 
    ref="gestureContainer"
    class="gesture-container"
    @touchstart="handleTouchStart"
    @touchmove="handleTouchMove"
    @touchend="handleTouchEnd"
  >
    <slot />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

interface TouchPoint {
  x: number
  y: number
  timestamp: number
}

interface SwipeDirection {
  direction: 'left' | 'right' | 'up' | 'down'
  distance: number
  velocity: number
}

const props = defineProps<{
  onSwipeLeft?: () => void
  onSwipeRight?: () => void
  onSwipeUp?: () => void
  onSwipeDown?: () => void
  onPinch?: (scale: number) => void
  onTap?: () => void
  onLongPress?: () => void
  swipeThreshold?: number
  swipeVelocityThreshold?: number
  longPressDelay?: number
}>()

const emit = defineEmits<{
  swipe: [direction: SwipeDirection]
  tap: []
  longPress: []
  pinch: [scale: number]
}>()

const gestureContainer = ref<HTMLElement | null>(null)
const touchStart = ref<TouchPoint | null>(null)
const touchMove = ref<TouchPoint | null>(null)
const longPressTimer = ref<number | null>(null)
const isLongPress = ref(false)

const swipeThreshold = props.swipeThreshold || 50
const swipeVelocityThreshold = props.swipeVelocityThreshold || 0.3
const longPressDelay = props.longPressDelay || 500

const handleTouchStart = (event: TouchEvent) => {
  if (event.touches.length === 1) {
    const touch = event.touches[0]
    touchStart.value = {
      x: touch.clientX,
      y: touch.clientY,
      timestamp: Date.now()
    }
    
    // 开始长按计时
    longPressTimer.value = window.setTimeout(() => {
      isLongPress.value = true
      props.onLongPress?.()
      emit('longPress')
    }, longPressDelay)
  }
}

const handleTouchMove = (event: TouchEvent) => {
  if (event.touches.length === 1 && touchStart.value) {
    const touch = event.touches[0]
    touchMove.value = {
      x: touch.clientX,
      y: touch.clientY,
      timestamp: Date.now()
    }
    
    // 如果移动距离超过阈值，取消长按
    const distance = Math.sqrt(
      Math.pow(touch.clientX - touchStart.value.x, 2) + 
      Math.pow(touch.clientY - touchStart.value.y, 2)
    )
    
    if (distance > 10 && longPressTimer.value) {
      clearTimeout(longPressTimer.value)
      longPressTimer.value = null
    }
  }
}

const handleTouchEnd = (event: TouchEvent) => {
  // 清除长按计时器
  if (longPressTimer.value) {
    clearTimeout(longPressTimer.value)
    longPressTimer.value = null
  }
  
  if (touchStart.value && touchMove.value && !isLongPress.value) {
    const deltaX = touchMove.value.x - touchStart.value.x
    const deltaY = touchMove.value.y - touchStart.value.y
    const deltaTime = touchMove.value.timestamp - touchStart.value.timestamp
    const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY)
    const velocity = distance / deltaTime
    
    // 判断是否为滑动
    if (distance > swipeThreshold && velocity > swipeVelocityThreshold) {
      const swipeDirection: SwipeDirection = {
        direction: Math.abs(deltaX) > Math.abs(deltaY) 
          ? (deltaX > 0 ? 'right' : 'left')
          : (deltaY > 0 ? 'down' : 'up'),
        distance,
        velocity
      }
      
      emit('swipe', swipeDirection)
      
      // 触发对应的回调
      switch (swipeDirection.direction) {
        case 'left':
          props.onSwipeLeft?.()
          break
        case 'right':
          props.onSwipeRight?.()
          break
        case 'up':
          props.onSwipeUp?.()
          break
        case 'down':
          props.onSwipeDown?.()
          break
      }
    } else if (distance < 10) {
      // 点击
      props.onTap?.()
      emit('tap')
    }
  }
  
  // 重置状态
  touchStart.value = null
  touchMove.value = null
  isLongPress.value = false
}

// 处理多点触控（缩放手势）
let initialDistance = 0
let initialScale = 1

const handleMultiTouch = (event: TouchEvent) => {
  if (event.touches.length === 2) {
    const touch1 = event.touches[0]
    const touch2 = event.touches[1]
    
    const distance = Math.sqrt(
      Math.pow(touch2.clientX - touch1.clientX, 2) + 
      Math.pow(touch2.clientY - touch1.clientY, 2)
    )
    
    if (initialDistance === 0) {
      initialDistance = distance
    } else {
      const scale = distance / initialDistance
      props.onPinch?.(scale)
      emit('pinch', scale)
    }
  } else {
    initialDistance = 0
  }
}

onMounted(() => {
  if (gestureContainer.value) {
    gestureContainer.value.addEventListener('touchmove', handleMultiTouch, { passive: true })
  }
})

onUnmounted(() => {
  if (gestureContainer.value) {
    gestureContainer.value.removeEventListener('touchmove', handleMultiTouch)
  }
  if (longPressTimer.value) {
    clearTimeout(longPressTimer.value)
  }
})
</script>

<style scoped lang="scss">
.gesture-container {
  width: 100%;
  height: 100%;
  touch-action: manipulation;
  
  /* 防止默认的触摸行为 */
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
</style>
