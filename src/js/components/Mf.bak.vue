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
      elements: this.setElements()
    }
  },
  watch: {
    elements: {
      handler (elements) {
        this.dataEl.innerHTML = elements.length ? JSON.stringify(this.setData(elements)) : ''
      },
      deep: true
    }
  },
  methods: {
    setElements () {
      let value

      try {
        value = Object.values(JSON.parse(this.dataEl.value))
      } catch (errors) {
        value = []
      }

      return this.setElementFromTemplates(value, this.config.templates)
    },
    setElementFromTemplates (elements, templates) {
      elements.forEach(element => {
        let template = templates.find(i => i.key === element.key) ??
            this.config.templates.find(i => i.key === element.key) ?? null

        if (template) {
          template = { ...template }

          const items = template.items

          if (items) {
            element.items = this.setElementFromTemplates(element.items || [], items)
            delete template.items
          }

          if (template.value !== undefined) {
            if (template.value === false) {
              delete element.value
            } else {
              delete template.value
            }
          }

          Object.assign(element, template)
        }
      })

      return elements
    },
    getElements (elements, element) {
      return h(
          draggable,
          {
            tag: 'div',
            list: elements,
            itemKey: a => a,
            class: ['mf3-items', element?.['itemsClass']],
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
      const name = element.name ? 'mf:' + element.name : null

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
          if (data[index]['key'] && this.config.templates[data[index]['key']]) {
            element = { ...this.config.templates[data[index]['key']] }
            element.key = data[index]['key']
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
      //element.values = values

      if (keys !== undefined) {
        keys = typeof keys !== 'object' ? [keys] : keys

        elements.forEach(i => {
          if (keys.includes(i.key)) {
            i.value = value
          }
        })
      }
    },
    selectTemplate (element, template, key) {
      if (key !== undefined) {
        if (!element.items) {
          element.items = []
        }

        element.items.push(template)
      } else {
        element.key ??= template
        this.elements.push(element)
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
      elements = Object.assign([], elements)

      for (let j in elements) {
        const element = Object.assign({}, elements[j])

        for (const i in element) {
          if (![
            'id',
            'key',
            'type',
            'name',
            'value',
            'values',
            'default',
            'items'
          ].includes(i)) {
            delete element[i]
          }
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
