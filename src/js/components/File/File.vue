<script>
export default {
  __isStatic: true,
  name: 'mf:file',
  props: ['type', 'name', 'title', 'value', 'items'],
  emits: ['update:value']
}
</script>

<template>
  <div class="mf3-file mf3-item">File</div>
</template>

<style scoped>

</style>
