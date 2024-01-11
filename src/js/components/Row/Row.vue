<script>
import Actions from '../Actions.vue'
import Sortable from 'sortablejs'

export default {
  components: { Actions },
  __isStatic: true,
  name: 'mf:row',
  props: ['type', 'name', 'title', 'value', 'items'],
  mounted () {
    Sortable.create(this.$refs.items, {
      draggable: '.mf3-item',
      handle: '.mf3-actions__move',
      ghostClass: 'mf3-draggable__active',
      chosenClass: 'mf3-draggable__chosen'
    })
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
    <div v-if="value !== undefined" class="mf3-row__value">
      <input type="text" :value="value" @input="$emit('update:value', $event.target.value)">
    </div>
    <div class="mf3-items" ref="items">
      <slot/>
    </div>
  </div>
</template>

<style scoped>
.mf3-row__value {
  @apply relative px-1 pb-1
}
.mf3-row__value input {
  @apply w-full text-sm
}
</style>
