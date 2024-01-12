<script>
import Actions from '../Actions.vue'

export default {
  components: { Actions },
  __isStatic: true,
  name: 'mf:thumb:image',
  props: ['type', 'name', 'title', 'value', 'multi', 'thumb'],
  methods: {
    action (action) {
      this.$emit('action', action)
    }
  }
}
</script>

<template>
  <div class="mf3-item" :style="{ backgroundImage: value ? 'url(' + value + ')' : null }">
    <actions @action="action"/>
    <button>
      <i/>
    </button>
  </div>
</template>

<style scoped>
.mf3-item {
  @apply w-24 h-24 max-w-24 flex items-center justify-center bg-no-repeat bg-center bg-contain
}
.mf3-item button {
  @apply relative flex items-center justify-center w-8 h-8 bg-white/50 rounded-full border-none
}
.mf3-item button i {
  @apply flex relative justify-center w-4 h-4 overflow-hidden bg-cyan-200 rounded-[2px] border border-black opacity-65
}
.mf3-item button:hover i {
  @apply opacity-85
}
.mf3-item button i::before {
  @apply content-[""] absolute left-1 top-2 w-3 h-3 border border-black bg-red-500 rotate-45
}
.mf3-item button i::after {
  @apply content-[""] absolute left-0.5 top-0.5 w-1 h-1 border border-amber-800 bg-amber-400 rounded-full
}
</style>
