<script>
import Actions from '../Actions.vue'
import Templates from '../Templates.vue'

export default {
  components: { Templates, Actions },
  __isStatic: true,
  name: 'mf:row',
  props: ['type', 'name', 'title', 'value', 'items', 'templates'],
  mounted () {

  },
  methods: {
    action (action) {
      this.$emit('action', action)
    }
  }
}
</script>

<template>
  <div class="mf3-row mf3-group mf3-item">
    <actions @action="action"/>
    <templates :templates="templates"/>
    <div v-if="value !== undefined" class="mf3-row__value">
      <input type="text" :value="value" @input="$emit('update:value', $event.target.value)">
    </div>
    <slot/>
  </div>
</template>

<style scoped>
.mf3-row__value {
  @apply relative py-1 px-1.5
}
.mf3-row__value input {
  @apply w-full text-sm
}
</style>
