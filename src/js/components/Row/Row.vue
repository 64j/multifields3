<script>
export default {
  __isStatic: true,
  name: 'mf:row',
  props: ['type', 'name', 'title', 'value', 'items'],
}
</script>

<template>
  <div class="mf3-row mf3-group">
    <div v-if="value" class="mf3-row__value">
      <input type="text" :value="value">
    </div>
    <slot/>
  </div>
</template>

<style scoped>
.mf3-row__value {
  @apply px-1 pb-1
}
.mf3-row__value input {
  @apply w-full text-sm
}
</style>
