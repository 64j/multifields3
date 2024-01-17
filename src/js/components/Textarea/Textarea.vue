<script>
import Actions from '../Actions.vue'

export default {
  __isStatic: true,
  name: 'mf:textarea',
  components: { Actions },
  props: ['type', 'name', 'title', 'value', 'rows'],
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
    <div class="mf3-items">
      <textarea @input="updateValue" :rows="rows">{{ value }}</textarea>
    </div>
  </div>
</template>

<style scoped>
.mf3-item textarea {
  @apply w-full border block resize-y min-h-7
}
</style>
