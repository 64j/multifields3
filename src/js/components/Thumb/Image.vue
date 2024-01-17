<script>
import Actions from '../Actions.vue'

export default {
  components: { Actions },
  __isStatic: true,
  name: 'mf:thumb:image',
  props: ['type', 'name', 'title', 'value', 'elements', 'multi', 'thumb'],
  data () {
    return {
      id: 'v-' + crypto.getRandomValues(new Uint32Array(1))[0].toString(36)
    }
  },
  computed: {
    model: {
      set (value) {
        this.$emit('update:value', value)
      },
      get () {
        return this.value || (!this.value && this.elements && [])
      }
    }
  },
  methods: {
    action (action, values) {
      this.$emit('action', action, values)
    },
    updateValue (event) {
      this.$emit('update:value', event.target.value)
    },
    select () {
      BrowseServer(this.id)
      if (this.multi) {
        this.MultiBrowseServer()
      }
    },
    MultiBrowseServer () {
      const self = this

      this.interval = setInterval(() => {
        if (window.KCFinder) {
          clearInterval(this.interval)
          window.KCFinder.callBackMultiple = (files) => {
            window.KCFinder = null
            window.SetUrl(files.shift())

            for (let i of files) {
              self.action('add', { value: i })
            }
          }
        }
      }, 100)
    }
  }
}
</script>

<template>
  <div class="mf3-item" :style="{ backgroundImage: model ? 'url(../' + model + ')' : null }">
    <actions @action="action"/>
    <button type="button" @click="select">
      <i/>
    </button>
    <input v-model="model" :id="this.id" type="hidden" @change="updateValue">
  </div>
</template>

<style scoped>
.mf3-item {
  @apply w-24 h-24 max-w-24 flex items-center justify-center bg-no-repeat;
  background-size: 0;
}
.mf3-item::before {
  @apply bg-white bg-no-repeat bg-contain bg-center;
  background-image: inherit;
}
.mf3-item button {
  @apply relative flex items-center justify-center w-8 h-8 p-0 bg-white/50 rounded-full border-none opacity-65 hover:opacity-85
}
.mf3-item button i {
  @apply flex relative justify-center w-4 h-4 overflow-hidden bg-cyan-200 rounded-[2px] border border-solid border-black
}
.mf3-item button i::before {
  @apply content-[""] absolute left-1 top-2 w-3 h-3 border border-solid border-black bg-red-500 rotate-45
}
.mf3-item button i::after {
  @apply content-[""] absolute left-0.5 top-0.5 w-1 h-1 border border-solid border-amber-800 bg-amber-400 rounded-full
}
</style>
