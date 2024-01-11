<script>
export default {
  __isStatic: true,
  name: 'mf:thumb:image',
  props: ['type', 'name', 'title', 'value', 'multi', 'thumb'],
  emits: ['update:value', 'delete'],
  methods: {
    remove () {
      this.$emit('delete')
    }
  }
}
</script>

<template>
  <div class="mf3-thumb__image mf3-item"
       :style="{ backgroundImage: value ? 'url(' + value + ')' : null }">
    <button @click="remove">Del</button>
  </div>
</template>

<style scoped>
.mf3-item {
  @apply w-24 h-24 max-w-24 bg-no-repeat bg-center bg-contain
}
</style>
