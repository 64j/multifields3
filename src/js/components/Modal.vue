<script>
export default {
  name: 'Modal',
  props: {
    title: String,
    open: Boolean,
    opener: Object,
    component: Object
  },
  data () {
    return {
      style: {},
      position: {}
    }
  },
  updated () {
    this.style = {}
  },
  methods: {
    onMousedown (event) {
      this.position = this.$refs.modal.getBoundingClientRect()
      this.position['clientY'] = event.clientY
      this.position['clientX'] = event.clientX
      this.position['layerX'] = event.layerX
      this.position['layerY'] = event.layerY
      this.style = {
        transform: 'none',
        top: this.position['top'] + 'px',
        left: this.position['left'] + 'px',
        width: this.position['width'] + 'px'
      }
      this.$refs.modal.addEventListener('mousemove', this.onMousemove)
      this.$refs.modal.addEventListener('mouseup', this.onMouseup)
      this.$refs.modal.classList.add('mf3-modal__resizable')
      window.onselectstart = () => false
    },
    onMousemove (event) {
      let top = this.position['top'] + (event.clientY - this.position['clientY'])
      let left = this.position['left'] + (event.clientX - this.position['clientX'])

      if (top < 2) {
        top = 2
      } else if (top > window.innerHeight - this.position['height'] - 2) {
        top = window.innerHeight - this.position['height'] - 2
      }

      if (left < 2) {
        left = 2
      } else if (left > window.innerWidth - this.position['width'] - 2) {
        left = window.innerWidth - this.position['width'] - 2
      }

      this.style.top = top + 'px'
      this.style.left = left + 'px'
    },
    onMouseup () {
      this.$refs.modal.removeEventListener('mousemove', this.onMousemove)
      this.$refs.modal.removeEventListener('mouseup', this.onMouseup)
      this.$refs.modal.classList.remove('mf3-modal__resizable')
      window.onselectstart = null
    },
    onClose () {
      if (this.$root['modal']['open']) {
        this.$root['modal']['open'] = false
      }
    }
  }
}
</script>

<template>
  <Teleport to="body">
    <transition name="fade">
      <div v-if="open">
        <div class="mf3 mf3-modal" :style="style" ref="modal">
          <div class="mf3-modal__header" @mousedown="onMousedown">
            <div class="mf3-modal__title">{{ title ?? opener?.['@title'] }}</div>
            <button class="mf3-modal__close" @mousedown.stop="onClose">
              <i/>
              <i/>
            </button>
          </div>
          <div class="mf3-modal__content">
            <component :is="component || opener['$slots'].default"/>
          </div>
        </div>
        <div class="mf3-modal__mask" @click="onClose"/>
      </div>
    </transition>
  </Teleport>
</template>

<style scoped>
@tailwind base;
.mf3-modal {
  @apply flex flex-col fixed z-[1002] w-[95%] resize overflow-hidden lg:w-auto min-w-96 max-w-full min-h-8 max-h-full left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 scale-100 p-0 m-0 bg-white dark:bg-gray-650 rounded shadow-2xl;
  backface-visibility: hidden;
}
.mf3-modal.mf3-modal__resizable {
  @apply opacity-50
}
.mf3-modal .mf3-modal__header {
  @apply z-20 flex items-center w-full h-7 px-2 py-1 rounded-t bg-slate-50 cursor-all-scroll dark:bg-gray-600
}
.mf3-modal .mf3-modal__header::before {
  @apply content-[""] absolute left-0 top-0 right-0 bottom-0 ring-2 ring-blue-500/50 rounded opacity-0 invisible transition
}
.mf3-modal .mf3-modal__header:active::before {
  @apply opacity-100 visible
}
.mf3-modal .mf3-modal__header:active::after {
  @apply content-[""] fixed z-10 left-0 top-0 right-0 bottom-0
}
.mf3-modal .mf3-modal__title {
  @apply grow truncate
}
.mf3-modal .mf3-modal__close {
  @apply grow-0 flex items-center justify-center w-5 h-5 border-0 bg-transparent hover:opacity-80
}
.mf3-modal .mf3-modal__close i {
  @apply absolute w-3 h-0.5 bg-rose-500 rotate-45 last:-rotate-45
}
.mf3-modal .mf3-modal__content {
  @apply z-20 p-1.5 grow overflow-auto
}
.mf3-modal__mask {
  @apply fixed z-10 left-0 top-0 right-0 bottom-0 scale-[10]
}
</style>
