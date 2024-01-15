<script>
import Actions from '../Actions.vue'

export default {
  __isStatic: true,
  components: { Actions },
  name: 'mf:input',
  props: ['type', 'name', 'title', 'value', 'list', 'min', 'max', 'step', 'pattern', 'required', 'readonly'],
  computed: {
    model: {
      set (value) {
        this.$emit('update:value', value)
      },
      get () {
        return this.value
      }
    }
  },
  methods: {
    action (action) {
      this.$emit('action', action)
    }
  }
}
</script>

<template>
  <div class="mf3-item">
    <actions @action="action"/>
    <input v-model="model"
           :type="type"
           :list="list"
           :min="min"
           :max="max"
           :step="step"
           :pattern="pattern"
           :required="required"
           :readonly="readonly">
  </div>
</template>

<style scoped>
.mf3-item input {
  @apply !w-full border
}
.mf3-item input[type="color"] {
  @apply !w-7 p-0
}
.mf3-item input[type="range"] {
  @apply p-0 appearance-auto outline-none
}
</style>
