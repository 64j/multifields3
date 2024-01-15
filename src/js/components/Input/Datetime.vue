<script>
import Input from './Input.vue'

export default {
  name: 'mf:input:datetime',
  extends: Input
}
</script>

<template>
  <div class="mf3-item">
    <actions @action="action"/>
    <input type="datetime-local" :value="value" @input="updateValue">
  </div>
</template>

<style scoped>
.mf3-item input {
  @apply w-full border
}
</style>
