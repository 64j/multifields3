<script>
import Actions from '../Actions.vue'

export default {
  components: { Actions },
  __isStatic: true,
  name: 'mf:text',
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
  <div class="mf3-text mf3-item">
    <actions @action="action"/>
    <input type="text" :value="value" class="w-full border" @input="updateValue">
  </div>
</template>

<style scoped>

</style>
