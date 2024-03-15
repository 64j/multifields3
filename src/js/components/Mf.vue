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
      templates: this.setTemplates(),
      elements: this.setElements(),
      modalOpen: false,
      modalTitle: null,
      modalComponent: null
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
    document.addEventListener('click', (event) => {
      if (this.modalOpen && !event.target.closest('.mf3-modal')) {
        this.modalOpen = false
      }
    })
  },
  methods: {
    setTemplates () {
      const templates = []

      const f = (items) => Object.entries(items).map(i => {
        if (i[1].items) {
          i[1].items = f(i[1].items)
        }

        return { name: i[1].name ?? i[0], ...i[1] }
      })

      for (const i in this.config.templates) {
        const template = this.config.templates[i]
        if (mf3Elements[`mf:${template.element}`] && !template.hidden) {
          templates.push({ name: i, title: template.title, icon: template.icon })
        }

        if (template.items) {
          template.items = f(template.items)
        }
      }

      return templates
    },
    setElements () {
      let value

      try {
        value = Object.values(JSON.parse(this.dataEl.value))
      } catch (errors) {
        value = []
      }

      return this.setElementFromTemplates(value, [])
    },
    setElementFromTemplates (elements, templates) {
      elements.forEach(element => {
        let template = templates.find(i => i.name === element.name) ?? this.config.templates[element.name] ?? null

        if (template) {
          template = { ...template }

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
      return h(
          draggable,
          {
            tag: 'div',
            list: elements,
            itemKey: a => a,
            class: ['mf3-items', element?.['items.class']],
            style: element?.['itemsStyle'],
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

      return mf3Elements[name] && h(
          mf3Elements[name],
          {
            ...element,
            'onAction': (action, ...args) => this.action(action, elements, index, ...args),
            'onUpdate:value': (...args) => this.updateValue(element, elements, ...args),
            'onSelect:template': (...args) => this.selectTemplate(element, ...args),
            'onClick': this.select
          },
          element?.items && (() => this.getElements(element.items, element))
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
            element = { ...this.config.templates[data[index]['name']] }
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
      const template = { ...this.config.templates[id || element] }

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
    select (event) {
      this.$el.querySelectorAll('.mf3-item.active').forEach(i => i.classList.remove('active'))
      event.currentTarget.classList.add('active')
      event.stopPropagation()
    }
  }
}
</script>

<template>
  <div class="mf3 mf3-group">
    <templates class="mf3-templates" :data="true" @select:template="selectTemplate"/>

    <component :is="() => getElements(elements)"/>

    <Teleport to="body">
      <transition name="fade">
        <div v-if="modalOpen" class="mf3 mf3-modal">
          <div class="mf3-modal__header">
            <div class="mf3-modal__title">{{ modalTitle }}</div>
            <button class="mf3-modal__close" @click="modalOpen = false">
              <i></i>
              <i></i>
            </button>
          </div>
          <div class="mf3-modal__content">
            <component :is="modalComponent"/>
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
  @apply flex flex-col fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 scale-100 z-10 bg-white dark:bg-gray-700 rounded shadow-lg resize;
  backface-visibility: hidden;
}
.mf3-modal .mf3-modal__header {
  @apply flex items-center h-7 px-2 py-1
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
</style>
