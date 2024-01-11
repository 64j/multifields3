<script>
export default {
  __isStatic: true,
  name: 'mf:thumb',
  props: ['type', 'name', 'title', 'value', 'multi', 'items']
}
</script>

<template>
  <div class="mf3-thumb mf3-item"
       :style="{ backgroundImage: value ? 'url(' + value + ')' : null }">
    <slot/>
  </div>
</template>

<style scoped>
.mf3-item {
  @apply w-24 h-24 max-w-24 bg-no-repeat bg-center bg-contain
}
</style>
