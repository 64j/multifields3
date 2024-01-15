<script>
import Actions from '../Actions.vue'
import Templates from '../Templates.vue'

export default {
  components: { Templates, Actions },
  __isStatic: true,
  name: 'mf:row',
  props: ['type', 'name', 'title', 'value', 'items', 'templates'],
  mounted () {

  },
  methods: {
    action (action) {
      this.$emit('action', action)
    },
    selectTemplate () {
      this.$emit('select:template', ...arguments)
    }
  }
}
</script>

<template>
  <div class="mf3-row mf3-group mf3-item">
    <actions @action="action"/>
    <templates :data="templates" @select:template="selectTemplate"/>
    <div v-if="value !== false" class="mf3-row__value">
      <input type="text" :value="value" @input="$emit('update:value', $event.target.value)">
    </div>
    <slot/>
  </div>
</template>

<style scoped>
.mf3-row__value {
  @apply relative py-1 px-1.5
}
.mf3-row__value input {
  @apply w-full text-sm
}
</style>

<style>
.mf3-row > .mf3-templates > .mf3-templates__add {
  @apply opacity-0 invisible
}
.mf3-row:hover > .mf3-templates > .mf3-templates__add {
  @apply opacity-100 visible
}
</style>
