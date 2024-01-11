<script>
export default {
  __isStatic: true,
  name: 'mf:text',
  props: ['type', 'name', 'title', 'value'],
  emits: ['update:value', 'delete'],
  methods: {
    updateValue (event) {
      this.$emit('update:value', event.target.value)
    },
    remove () {
      this.$emit('delete')
    }
  }
}
</script>

<template>
  <div class="mf3-text mf3-item">
    <input type="text" :value="value" class="w-full border" @input="updateValue">
    <button @click="remove">Del</button>
  </div>
</template>

<style scoped>

</style>
