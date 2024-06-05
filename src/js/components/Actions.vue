<script>
export default {
  name: 'Actions',
  props: {
    actions: {
      type: [Array, Boolean],
      default: ['add', 'move', 'del']
    }
  },
  data () {
    return {
      data: []
    }
  },
  created () {
    if (Array.isArray(this.actions)) {
      this.data = this.actions
    } else if (this.actions) {
      this.data = this.$parent.$options.props?.actions?.default ?? this.$options.props.actions.default
    }
  }
}
</script>

<template>
  <div v-if="data.length" class="mf3-actions">
    <div v-if="data.filter(i => i !== 'add').length" class="mf3-actions__panel" :data-title="$parent['@title']"/>
    <div v-for="i in data" :class="`mf3-actions__action mf3-actions__` + i" @click.stop="$emit('action', i)">
      <i/>
      <i/>
    </div>
  </div>
</template>

<style scoped>
.mf3-actions {
  @apply absolute left-0 top-0 bottom-0 right-0 flex justify-end ring-blue-500 ring-1 ring-offset-0 opacity-0 invisible
}
.mf3-actions__panel {
  @apply absolute z-10 -left-[1px] bottom-full -right-[1px] -mt-0 px-5 h-5 flex items-center rounded-t bg-blue-500 text-white
}
.mf3-actions__panel[data-title=""] {
  @apply pointer-events-none
}
.mf3-actions__panel::before {
  @apply content-[attr(data-title)] block truncate
}
.mf3-actions__action {
  @apply w-4 h-4 cursor-pointer
}
.mf3-actions__add {
  @apply absolute z-10 flex items-center justify-center -bottom-2 left-1/2 -translate-x-1/2 bg-blue-500 rounded-full
}
.mf3-actions__add i {
  @apply absolute w-2 h-0.5 bg-white last:rotate-90 translate-x-0
}
.mf3-actions__del {
  @apply relative z-10 flex items-center justify-center -mt-[1.125rem]
}
.mf3-actions__del i {
  @apply absolute w-3 h-0.5 bg-white rotate-45 last:-rotate-45
}
.mf3-actions__move {
  @apply absolute left-0 z-10 flex items-center justify-center -mt-[1.125rem] cursor-move
}
.mf3-actions__move i {
  @apply absolute w-3 h-0.5 bg-white last:rotate-90
}
.mf3-actions__move i::before, .mf3-actions__move i::after {
  @apply content-[""] absolute h-0 w-0 border-[0.1875rem] border-solid border-transparent border-r-white
}
.mf3-actions__move i::before {
  @apply -left-1 -top-0.5
}
.mf3-actions__move i::after {
  @apply -right-1 -bottom-0.5 rotate-180
}
.mf3-actions__hide {
  @apply relative z-10 -mt-[1.125rem] rounded-full bg-white
}
.mf3-actions__expand {
  @apply relative z-10 -mt-[1.125rem] rounded-full bg-white
}
</style>

<style>
.mf3-group > .mf3-actions > .mf3-actions__add, .darkness .mf3-group > .mf3-actions > .mf3-actions__add {
  @apply bg-green-500
}
.mf3-group > .mf3-actions {
  @apply ring-green-500
}
</style>
