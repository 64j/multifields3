<script>
import Actions from '../Actions.vue'
import MfText from '../Text/Text.vue'

export default {
  components: { Actions },
  __isStatic: true,
  name: 'mf:number',
  extends: MfText
}
</script>

<template>
  <div class="mf3-item">
    <actions @action="action"/>
    <input type="number" :value="value" @input="updateValue">
  </div>
</template>

<style scoped>
.mf3-item input {
  @apply w-full border
}
</style>
