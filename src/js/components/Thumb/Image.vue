<script>
import Actions from '../Actions.vue'

export default {
  components: { Actions },
  __isStatic: true,
  name: 'mf:thumb:image',
  props: ['type', 'name', 'title', 'value', 'multi', 'thumb'],
  methods: {
    action (action) {
      this.$emit('action', action)
    }
  }
}
</script>

<template>
  <div class="mf3-thumb__image mf3-item" :style="{ backgroundImage: value ? 'url(' + value + ')' : null }">
    <actions @action="action"/>
  </div>
</template>

<style scoped>
.mf3-item {
  @apply w-24 h-24 max-w-24 bg-no-repeat bg-center bg-contain
}
</style>
