<script>
import Element from '../Element.vue'
import Actions from '../Actions.vue'
import Templates from '../Templates.vue'
import { console } from 'vuedraggable/src/util/console'

export default {
  name: 'mf:row',
  __isStatic: true,
  extends: Element,
  components: { Templates, Actions },
  props: {
    templates: {
      type: [Boolean, Object, Array],
      default: true
    },
    actions: {
      default: ['add', 'move'/*, 'resize'*/, 'del']
    }
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
  <div class="mf3-row mf3-group mf3-item" :class="`mf3-${element}`" v-bind="attr">
    <actions @action="action" :actions="actions"/>
    <templates :data="templates" @select:template="selectTemplate"/>
    <div v-if="value !== false" class="mf3-row__value">
      <input type="text" :value="value" :placeholder="placeholder" @input="$emit('update:value', $event.target.value)">
    </div>
    <div v-if="label" class="mf3-row__label" v-html="label"/>
    <slot/>
  </div>
</template>

<style scoped>
.mf3-row {
  @apply pb-2.5 min-h-[2.3rem]
}
.mf3-row__value {
  @apply relative m-1 mb-0 p-1.5 bg-slate-500/5 border-0 border-b border-solid border-slate-200
}
.darkness .mf3-row__value {
  @apply border-gray-600
}
.mf3-row__value input {
  @apply w-full text-xs px-1.5 py-1.5 h-7
}
.mf3-row__label {
  @apply px-3 pt-2.5
}
</style>

<style>
.mf3-row > .mf3-templates > .mf3-templates__add {
  @apply opacity-0 invisible
}
.mf3-row:hover > .mf3-templates > .mf3-templates__add {
  @apply opacity-100 visible
}
.mf3-row > .mf3-items {
  @apply px-1.5
}
.mf3-row > .mf3-items:empty {
  @apply p-0
}
.mf3-item.mf3-row > .mf3-items > div {
  @apply order-1
}
.mf3-row > .mf3-actions .mf3-actions__panel {
  @apply bg-green-500
}
.mf3-row > .mf3-actions .mf3-actions__resize {
  @apply absolute left-0 top-0 w-full h-full cursor-default
}
.mf3-row > .mf3-actions .mf3-actions__resize i {
  @apply absolute z-10 top-0 bottom-0 first:left-[-1px] last:right-[-1px] flex items-center w-[0.2rem] bg-green-500 leading-[0] text-white text-lg font-medium cursor-col-resize overflow-hidden
}
.mf3-row > .mf3-actions .mf3-actions__resize i::before {
  @apply content-["..."] block -ml-[1px] rotate-90
}
.mf3-row > .mf3-actions .mf3-actions__hide, .mf3-row > .mf3-actions .mf3-actions__expand {
  @apply left-0 hidden
}
</style>
