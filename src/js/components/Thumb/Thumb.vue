<script>
import Actions from '../Actions.vue'

export default {
  components: { Actions },
  __isStatic: true,
  name: 'mf:thumb',
  props: ['type', 'name', 'title', 'value', 'multi', 'items'],
  methods: {
    action (action) {
      this.$emit('action', action)
    }
  }
}
</script>

<template>
  <div class="mf3-item mf3-thumb" :style="{ backgroundImage: value ? 'url(' + value + ')' : null }">
    <actions @action="action"/>
    <button>
      <i/>
    </button>
    <slot/>
  </div>
</template>

<style scoped>
.mf3-item {
  @apply w-24 h-24 max-w-24 flex items-center justify-center bg-no-repeat bg-center bg-contain
}
.mf3-item button {
  @apply relative flex items-center justify-center w-8 h-8 bg-white/50 rounded-full border-none opacity-65 hover:opacity-85
}
.mf3-item button i {
  @apply flex relative justify-center w-4 h-4 bg-cyan-200  rounded-[2px] border border-black
}
.mf3-item button i::before {
  @apply content-[""] absolute left-0.5 top-1 w-3.5 h-0.5 outline-1 outline outline-black bg-black -rotate-45
}
.mf3-item button i::after {
  @apply content-[""] absolute top-2.5 -left-0.5 w-0 h-0 border-[3px] border-transparent border-t-black rotate-45
}
</style>

<style>
.mf3-thumb > .mf3-items {
  @apply hidden
}
</style>
