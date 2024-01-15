<script>
import Input from './Input.vue'

export default {
  name: 'mf:time',
  extends: Input
}
</script>

<template>
  <div class="mf3-item">
    <actions @action="action"/>
    <input v-model="model" type="time">
  </div>
</template>

<style scoped>
.mf3-item input {
  @apply w-full border
}
</style>
