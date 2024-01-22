<script>
import Actions from '../Actions.vue'

export default {
  name: 'mf:thumb',
  __isStatic: true,
  components: { Actions },
  props: {
    key: String,
    type: {
      type: String,
      default: 'thumb'
    },
    name: String,
    title: String,
    value: String,
    actions: {
      default: ['add', 'move', 'del']
    },
    elements: [String, Array],
    multi: Boolean,
    input: String,
    items: Array
  },
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
    action (action, ...args) {
      this.$emit('action', action, ...args)
    },
    updateValue (event) {
      this.$emit('update:value', event.target.value, undefined, this.input)
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
  <div class="mf3-item mf3-thumb" :class="`mf3-thumb__` + this.type"
       :style="{ backgroundImage: value ? 'url(../' + value + ')' : null }">
    <actions @action="action" :actions="actions"/>
    <button type="button" @click="select">
      <i/>
    </button>
    <input v-model="model" :id="this.id" type="hidden" @change="updateValue">
    <slot/>
  </div>
</template>

<style scoped>
.mf3-item {
  @apply max-w-24 flex items-center justify-center bg-no-repeat;
  background-size: 0;
}
.mf3-item::before {
  @apply relative left-0 top-0 right-0 bottom-0 w-full bg-white bg-no-repeat bg-contain bg-center;
  background-image: inherit;
  padding-top: 100%;
}
.darkness .mf3-item::before {
  @apply bg-transparent
}
.mf3-thumb__thumb button {
  @apply absolute flex items-center justify-center w-8 h-8 p-0 bg-white/50 rounded-full border-none opacity-65 hover:opacity-85
}
.mf3-thumb__thumb button i {
  @apply flex relative justify-center w-4 h-4 bg-cyan-200  rounded-[2px] border border-solid border-black
}
.mf3-thumb__thumb button i::before {
  @apply content-[""] absolute left-0.5 top-1 w-3.5 h-0.5 outline-1 outline outline-black bg-black -rotate-45
}
.mf3-thumb__thumb button i::after {
  @apply content-[""] absolute top-2.5 -left-0.5 w-0 h-0 border-[3px] border-transparent border-solid border-t-black rotate-45
}
.mf3-thumb__image button {
  @apply absolute flex items-center justify-center w-8 h-8 p-0 bg-white/50 rounded-full border-none opacity-65 hover:opacity-85
}
.mf3-thumb__image button i {
  @apply flex relative justify-center w-4 h-4 overflow-hidden bg-cyan-200 rounded-[2px] border border-solid border-black
}
.mf3-thumb__image button i::before {
  @apply content-[""] absolute left-1 top-2 w-3 h-3 border border-solid border-black bg-red-500 rotate-45
}
.mf3-thumb__image button i::after {
  @apply content-[""] absolute left-0.5 top-0.5 w-1 h-1 border border-solid border-amber-800 bg-amber-400 rounded-full
}
</style>

<style>
.mf3-thumb > .mf3-items {
  @apply hidden
}
</style>
