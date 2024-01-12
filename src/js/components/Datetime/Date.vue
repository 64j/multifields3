<script>
import Datetime from './Datetime.vue'

export default {
  name: 'mf:date',
  extends: Datetime
}
</script>

<template>
  <div class="mf3-text mf3-item">
    <actions @action="action"/>
    <input type="date" :value="value" class="w-full border" @input="updateValue">
  </div>
</template>

<style scoped>
.mf3-item {
  @apply w-full
}
</style>
