<script>
import Element from '../Element.vue'
import Actions from '../Actions.vue'

export default {
  name: 'mf:thumb',
  __isStatic: true,
  extends: Element,
  components: { Actions },
  props: {
    type: {
      type: String,
      default: 'thumb'
    },
    multi: [Boolean, String],
    input: String
  },
  computed: {
    model: {
      set (value) {
        this.updateValue(value)
      },
      get () {
        return this.value || (!this.value && this.elements && [])
      }
    }
  },
  methods: {
    action (action, ...args) {
      if (action === 'del' && this.name === this.$root['modal']?.opener?.name) {
        this.$root['modal'].open = false
      }

      this.$emit('action', action, ...args)
    },
    updateValue (event) {
      const value = event?.target?.value ?? event

      this.$emit('update:value', value, undefined, this.input)

      if (this.input && this.input === this.$root['modal']?.opener?.name) {
        this.$root['modal'].opener.$emit('update:value', value)
      }
    },
    select () {
      if (this.type === 'image') {
        BrowseServer(this.id)
        if (this.multi) {
          this.MultiBrowseServer()
        }
      } else if (this.items) {
        this.$root['modal'] = {
          opener: this,
          open: true
        }
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
  <div class="mf3-item mf3-thumb" :class="`mf3-${element}__${type}`"
       :style="{ backgroundImage: value ? 'url(../' + value + ')' : null }"
       v-bind="attr">
    <actions @action="action" :actions="actions"/>
    <button type="button" @click.stop="select">
      <i/>
    </button>
    <input v-model="model" :id="id" type="hidden" @change="updateValue">
    <slot/>
  </div>
</template>

<style scoped>
.mf3-item {
  @apply w-24 min-w-24 flex grow-0 basis-auto items-center justify-center bg-no-repeat;
  background-size: 0;
}
.mf3-item::before {
  @apply content-[""] relative left-0 top-0 right-0 bottom-0 w-full bg-white bg-no-repeat bg-contain bg-center;
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
  @apply !hidden
}
</style>
