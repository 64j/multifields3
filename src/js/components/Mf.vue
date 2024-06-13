<script>
import { h } from 'vue'
import draggable from 'vuedraggable'
import Actions from './Actions.vue'
import Templates from './Templates.vue'
import Modal from './Modal.vue'

export default {
  name: 'mf',
  components: { Templates, Actions, draggable, Modal },
  props: {
    dataEl: Object,
    config: Object
  },
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
    },
    'modal.open' (value) {
      if (value) {
        document.documentElement.style.overflow = 'hidden'
      } else {
        document.documentElement.style.overflow = null
      }
    }
  },
  created () {
    if (Array.isArray(this.config.templates)) {
      this.config.templates = Object.fromEntries(this.config.templates.map(i => [i.key || i.name, i]))
    }

    try {
      this.elements = Object.values(JSON.parse(this.dataEl.value))
    } catch (errors) {
      this.elements = []
    }

    this.elements = this.setElementFromTemplates(this.elements, [])
  },
  methods: {
    setElementFromTemplates (elements, templates) {
      elements.forEach(element => {
        let template = structuredClone(
            templates.find(i => i.name === element.name) ?? this.config.templates[element.name] ?? null
        )

        if (template) {
          let items = template.items

          if (items || template.templates || mf3Elements[`mf:${template.element}`]?.props?.templates?.default) {
            if (!Array.isArray(items)) {
              const els = []

              for (const i in items) {
                els.push({ ...items[i], name: i })
              }

              items = els
            }

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
            class: ['mf3-items', element?.itemsAttrs?.class],
            handle: '.mf3-actions__move',
            ghostClass: 'mf3-draggable__active',
            chosenClass: 'mf3-draggable__chosen',
            ...(element?.itemsAttrs ?? {})
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
      const props = Object.assign({}, mf3Elements[name]?.extends?.['props'] || {}, mf3Elements[name]?.['props'] || {})
      const propsKeys = Object.keys(props).concat(['class', 'style'])

      for (const i in element) {
        if (propsKeys.includes(i)) {
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
      if (!Array.isArray(elements)) {
        const els = []

        for (const i in elements) {
          els.push({ ...elements[i], name: i })
        }

        elements = els
      }

      elements = [...elements]

      for (let j in elements) {
        const element = { ...elements[j] }
        const append = element.append || []

        for (const i in element) {
          if (![
            'name',
            'value',
            'values',
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
    }
  }
}
</script>

<template>
  <div class="mf3 mf3-group">
    <templates class="mf3-templates" :data="true" @select:template="selectTemplate"/>

    <component :is="getElements()"/>

    <Modal v-bind="modal"/>
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
</style>
