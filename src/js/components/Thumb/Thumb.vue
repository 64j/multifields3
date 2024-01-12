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
  <div class="mf3-item mf3-thumb" :style="{ backgroundImage: value ? 'url(' + value + ')' : null }">
    <actions @action="action"/>
    <slot/>
  </div>
</template>

<style scoped>
.mf3-item {
  @apply w-24 h-24 max-w-24 bg-no-repeat bg-center bg-contain
}
</style>

<style>
.mf3-thumb > .mf3-items {
  @apply hidden
}
</style>
