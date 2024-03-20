<script>
import { h } from 'vue'
import draggable from 'vuedraggable'
import Actions from './Actions.vue'
import Templates from './Templates.vue'

export default {
  name: 'mf',
  components: { Templates, Actions, draggable },
  props: ['dataEl', 'config'],
  data () {
    return {
      elements: [],
      modal: {}
    }
  },
  watch: {
    elements: {
      handler (elements) {
        this.dataEl.value = elements.length ? JSON.stringify(this.setData(elements)) : ''
      },
      deep: true
    }
  },
  created () {
    try {
      this.elements = Object.values(JSON.parse(this.dataEl.value))
    } catch (errors) {
      this.elements = []
    }

    this.elements = this.setElementFromTemplates(this.elements, [])

    document.addEventListener('click', (event) => {
      if (this.modal?.open && !event.target.closest('.mf3-modal')) {
        this.modal.open = false
      }
    })
  },
  methods: {
    setElementFromTemplates (elements, templates) {
      elements.forEach(element => {
        let template = structuredClone(
            templates.find(i => i.name === element.name) ?? this.config.templates[element.name] ?? null
        )

        if (template) {
          const items = template.items

          if (items || template.templates || mf3Elements[`mf:${template.element}`]?.props?.templates?.default) {
            element.items = this.setElementFromTemplates(element.items || [], items || [])
            delete template.items
          } else {
            if (element.items) {
              delete element.items
            }
          }

          if (template.value !== undefined) {
            if (template.value === false) {
              delete element.value
            } else {
              delete template.value
            }
          }

          Object.assign(element, template)
        } else {
          element.name = null
        }
      })

      return elements.filter(i => i.name)
    },
    getElements (elements, element) {
      elements ??= this.elements

      return h(
          draggable,
          {
            tag: 'div',
            list: elements,
            itemKey: a => a,
            class: ['mf3-items', element?.['items.class']],
            style: element?.['items.style'],
            handle: '.mf3-actions__move',
            ghostClass: 'mf3-draggable__active',
            chosenClass: 'mf3-draggable__chosen'
          },
          {
            item: ({ element, index }) => this.getElement(element, index, elements)
          }
      )
    },
    getElement (element, index, elements) {
      const name = element.element ? 'mf:' + element.element : null
      let slots

      if (element?.items) {
        slots = () => this.getElements(element.items, element)
      }

      const renderElement = {}
      const props = Object.keys(mf3Elements[name]?.['props'] || {}).
          concat(Object.keys(mf3Elements[name]?.extends?.['props'] || {})).
          concat(['class', 'style'])

      for (const i in element) {
        if (props.includes(i)) {
          renderElement[i] = element[i]
        }
      }

      return mf3Elements[name] && h(
          mf3Elements[name],
          {
            ...renderElement,
            'onAction': (action, ...args) => this.action(action, elements, index, ...args),
            'onUpdate:value': (...args) => this.updateValue(element, elements, ...args),
            'onSelect:template': (...args) => this.selectTemplate(element, ...args)
          },
          slots
      )
    },
    action (action, data, index, values) {
      index ??= data.length

      switch (action) {
        case 'del':
          data.splice(index, 1)
          break

        case 'add':
          let element = {}
          if (data[index]['name'] && this.config.templates[data[index]['name']]) {
            element = structuredClone(this.config.templates[data[index]['name']])
            element.name = data[index]['name']
          } else {
            element = this.clearValue({ ...data[index] })
          }

          data.splice(index + 1, 0, element)

          if (values) {
            Object.assign(data[index + 1], values)
          }
          break
      }
    },
    updateValue (element, elements, value, values, keys) {
      element.value = value
      element.values = values

      if (keys !== undefined) {
        keys = typeof keys !== 'object' ? [keys] : keys

        elements.forEach(i => {
          if (keys.includes(i.name)) {
            i.value = value
          }
        })
      }
    },
    selectTemplate (element, id) {
      const template = structuredClone(this.config.templates[id || element])

      if (id !== undefined) {
        if (!element.items) {
          element.items = []
        }

        template.name ??= id
        element.items = element.items.concat([template])
      } else {
        template.name ??= element
        this.elements.push(template)
      }
    },
    clearValue (data) {
      if (data.value !== undefined) {
        data.value = data.value === false ? data.value : ''
      }

      if (data.items) {
        data.items = data.items.map(i => this.clearValue({ ...i }))
      }

      return data
    },
    setData (elements) {
      elements = [...elements]

      for (let j in elements) {
        const element = { ...elements[j] }
        const append = element.append || []

        for (const i in element) {
          if (![
            'name',
            'value',
            'items'
          ].concat(append).includes(i)) {
            delete element[i]
          }
        }

        if (element.value === '') {
          delete element.value
        }

        if (element?.items) {
          element.items = this.setData(element.items)
        }

        elements[j] = element
      }

      return elements
    },
    modalOnMousedown (event) {
      this.modal.position = this.$refs.modal.getBoundingClientRect()
      this.modal.clientY = event.clientY
      this.modal.clientX = event.clientX
      this.modal.layerX = event.layerX
      this.modal.layerY = event.layerY
      this.$refs.modal.addEventListener('mousemove', this.modalOnMousemove)
      this.$refs.modal.addEventListener('mouseup', this.modalOnMouseup)
      window.onselectstart = () => false
    },
    modalOnMousemove (event) {
      let top = this.modal.position.top + (event.clientY - this.modal.clientY)
      let left = this.modal.position.left + (event.clientX - this.modal.clientX)

      if (top < 2) {
        top = 2
      } else if (top > window.innerHeight - this.modal.position.height - 2) {
        top = window.innerHeight - this.modal.position.height - 2
      }

      if (left < 2) {
        left = 2
      } else if (left > window.innerWidth - this.modal.position.width - 2) {
        left = window.innerWidth - this.modal.position.width - 2
      }

      this.$refs.modal.style.transform = 'none'
      this.$refs.modal.style.width = this.modal.position.width + 'px'
      this.$refs.modal.style.height = this.modal.position.height + 'px'
      this.$refs.modal.style.top = top + 'px'
      this.$refs.modal.style.left = left + 'px'
    },
    modalOnMouseup () {
      this.$refs.modal.removeEventListener('mousemove', this.modalOnMousemove)
      this.$refs.modal.removeEventListener('mouseup', this.modalOnMouseup)
      window.onselectstart = null
    }
  }
}
</script>

<template>
  <div class="mf3 mf3-group">
    <templates class="mf3-templates" :data="true" @select:template="selectTemplate"/>

    <component :is="() => getElements()"/>

    <Teleport to="body">
      <transition name="fade">
        <div v-if="modal?.open" class="mf3 mf3-modal" ref="modal">
          <div class="mf3-modal__header" @mousedown="modalOnMousedown">
            <div class="mf3-modal__title">{{ modal?.title ?? modal.opener?.['@title'] }}</div>
            <button class="mf3-modal__close" @mousedown.stop="modal.open = false">
              <i></i>
              <i></i>
            </button>
          </div>
          <div class="mf3-modal__content">
            <component :is="modal.component || modal.opener.$slots.default"/>
          </div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<style scoped>
@tailwind base;
.mf3 {
  @apply relative flex flex-wrap mb-6 p-0 m-0 border-none
}
.mf3::before {
  @apply hidden
}
.mf3 > .mf3-templates {
  @apply -bottom-3
}
.mf3-modal {
  @apply flex flex-col fixed w-[95%] lg:w-auto min-w-96 max-w-full max-h-full left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 scale-100 z-[1002] p-0 m-0 bg-white dark:bg-gray-700 rounded shadow-2xl;
  backface-visibility: hidden;
}
.mf3-modal .mf3-modal__header {
  @apply flex items-center w-full h-7 px-2 py-1 bg-slate-50 cursor-all-scroll
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
  @apply grow-0 flex items-center justify-center w-5 h-5 border-0
}
.mf3-modal .mf3-modal__close i {
  @apply absolute w-3 h-0.5 bg-rose-500 rotate-45 last:-rotate-45
}
.mf3-modal .mf3-modal__content {
  @apply p-1.5
}
.darkness .mf3-modal {
  @apply bg-gray-700 border
}
.darkness .mf3-modal .mf3-modal__header {
  @apply bg-gray-600
}
</style>
