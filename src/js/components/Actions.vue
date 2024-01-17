<script>
export default {
  name: 'Actions',
  props: {
    actions: {
      type: [null, Array, Boolean],
      default: ['add', 'move', 'del']
    }
  }
}
</script>

<template>
  <div v-if="actions?.length" class="mf3-actions">
    <div v-for="i in actions" :class="`mf3-actions__action mf3-actions__` + i" @click="$emit('action', i)">
      <i/>
      <i/>
    </div>
  </div>
</template>

<style scoped>
.mf3-actions {
  @apply absolute left-1 top-1 bottom-1 right-1 flex justify-center ring-blue-500 ring-2 ring-offset-0 opacity-0 invisible transition-all
}
.mf3-actions__action {
  @apply w-4 h-4 z-10 cursor-pointer
}
.mf3-actions__add {
  @apply absolute flex items-center justify-center -bottom-2 bg-blue-500 rounded-full
}
.mf3-actions__add::before, .mf3-actions__add::after {
  @apply absolute w-2 h-0.5 bg-white content-[""]
}
.mf3-actions__add::after {
  @apply rotate-90
}
.mf3-actions__del {
  @apply flex items-center justify-center -mt-4 bg-rose-500
}
.mf3-actions__del::before, .mf3-actions__del::after {
  @apply absolute w-2 h-0.5 bg-white content-[""] rotate-45
}
.mf3-actions__del::after {
  @apply -rotate-45
}
.mf3-actions__move {
  @apply flex items-center justify-center -mt-4 cursor-move bg-blue-500
}
.mf3-actions__move i {
  @apply content-[""] absolute w-2.5 h-0.5 bg-white
}
.mf3-actions__move i:last-of-type {
  @apply rotate-90
}
.mf3-actions__move i::before, .mf3-actions__move i::after {
  @apply content-[""] absolute h-0 w-0 border-[3px] border-solid border-transparent border-r-white
}
.mf3-actions__move i::before {
  @apply -left-1 -top-0.5
}
.mf3-actions__move i::after {
  @apply -right-1 -bottom-0.5 rotate-180
}
</style>

<style>
.mf3-group > .mf3-actions > .mf3-actions__add {
  @apply bg-green-500
}
.mf3-group > .mf3-actions {
  @apply ring-green-500
}
</style>
