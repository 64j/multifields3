<script>
import Input from './Input.vue'

export default {
  name: 'mf:input:text',
  extends: Input
}
</script>

<template>
  <div class="mf3-item">
    <actions @action="action"/>
    <input type="text" :value="value" @input="updateValue">
  </div>
</template>

<style scoped>
.mf3-item input {
  @apply w-full border
}
</style>
