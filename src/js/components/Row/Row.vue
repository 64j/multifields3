<script>
import Actions from '../Actions.vue'

export default {
  components: { Actions },
  __isStatic: true,
  name: 'mf:row',
  props: ['type', 'name', 'title', 'value', 'items'],
  data () {
    return {
      propItems: this.items
    }
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
    <slot/>
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
