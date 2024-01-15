<script>
import Actions from '../Actions.vue'

export default {
  components: { Actions },
  __isStatic: true,
  name: 'mf:file',
  props: ['type', 'name', 'title', 'value', 'items'],
  data () {
    this.id = 'v-' + crypto.getRandomValues(new Uint32Array(1))[0].toString(36)

    return {}
  },
  methods: {
    action (action) {
      this.$emit('action', action)
    },
    updateValue (event) {
      this.$emit('update:value', event.target.value)
    },
    select () {
      return BrowseFileServer(this.id)
    }
  }
}
</script>

<template>
  <div class="mf3-item">
    <actions @action="action"/>
    <input :id="this.id" type="text" :value="value" @change="updateValue">
    <button type="button" @click="select">
      <i/>
    </button>
  </div>
</template>

<style scoped>
.mf3-item input {
  @apply w-full border
}
.mf3-item button {
  @apply absolute z-10 right-1 top-1 bottom-1 p-0 flex items-center justify-center
}
.mf3-item button::before {
  @apply content-[""] absolute left-0 top-1 bottom-1 border-none border-l opacity-50
}
.mf3-item button i {
  @apply flex relative justify-center mx-2.5 w-3 h-4 overflow-hidden bg-blue-300 rounded-[2px] rounded-tr-[5px] border border-black opacity-65
}
.mf3-item button:hover i {
  @apply opacity-85
}
.mf3-item button i::before {
  @apply content-[""] absolute left-0.5 top-1.5 w-1.5 h-1 border-t border-b border-black
}
.mf3-item button i::after {
  @apply content-[""] absolute -right-0.5 -top-0.5 w-1.5 h-1.5 bg-white border border-black
}
</style>
