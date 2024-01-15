<script>
import Actions from '../Actions.vue'

export default {
  __isStatic: true,
  components: { Actions },
  name: 'mf:input',
  props: ['type', 'name', 'title', 'value'],
  methods: {
    action (action) {
      this.$emit('action', action)
    },
    updateValue (event) {
      this.$emit('update:value', event.target.value)
    }
  }
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
