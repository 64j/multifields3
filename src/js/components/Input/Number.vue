<script>
import Input from './Input.vue'

export default {
  name: 'mf:number',
  extends: Input
}
</script>

<template>
  <div class="mf3-item">
    <actions @action="action"/>
    <input v-model="model" type="number">
  </div>
</template>

<style scoped>
.mf3-item input {
  @apply w-full border
}
</style>
