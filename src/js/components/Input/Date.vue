<script>
import Input from './Input.vue'

export default {
  name: 'mf:date',
  extends: Input
}
</script>

<template>
  <div class="mf3-item">
    <actions @action="action"/>
    <input v-model="model" type="date">
  </div>
</template>

<style scoped>
.mf3-item input {
  @apply w-full border
}
</style>
