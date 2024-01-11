<script>
import Actions from '../Actions.vue'

export default {
  components: { Actions },
  __isStatic: true,
  name: 'mf:file',
  props: ['type', 'name', 'title', 'value', 'items'],
  methods: {
    action (action) {
      this.$emit('action', action)
    }
  }
}
</script>

<template>
  <div class="mf3-file mf3-item">
    <actions @action="action"/>
    File
  </div>
</template>

<style scoped>

</style>
