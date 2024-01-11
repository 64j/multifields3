<script>
import Actions from '../Actions.vue'

export default {
  components: { Actions },
  __isStatic: true,
  name: 'mf:thumb',
  props: ['type', 'name', 'title', 'value', 'multi', 'items'],
  methods: {
    action (action) {
      this.$emit('action', action)
    }
  }
}
</script>

<template>
  <div class="mf3-thumb mf3-item" :style="{ backgroundImage: value ? 'url(' + value + ')' : null }">
    <actions @action="action"/>
    <div class="mf3-items">
      <slot/>
    </div>
  </div>
</template>

<style scoped>
.mf3-item {
  @apply w-24 h-24 max-w-24 bg-no-repeat bg-center bg-contain
}
</style>
