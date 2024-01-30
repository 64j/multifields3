<script>
import { h } from 'vue'
import draggable from 'vuedraggable'
import Actions from './Actions.vue'
import Templates from './Templates.vue'

export default {
  name: 'mf',
  components: { Templates, Actions, draggable },
  props: ['dataEl', 'tvId', 'tvName'],
  data () {
    this.data = window['mf3Config'][this.tvName] ?? {}

    return {
      templates: this.setTemplates(),
      settings: this.setSettings(),
      elements: null,
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
  created () {
    this.elements = this.setElements()
  },
  methods: {
    setElements () {
      return this.setElementFromTemplates(
          this.dataEl.value && JSON.parse(this.dataEl.value) || [],
          this.data.templates
      )
    },
    setElementFromTemplates (elements, templates) {
      templates = typeof templates === 'object' ? (Array.isArray(templates)
          ? Object.fromEntries(templates.map(item => [item.key, item]))
          : templates) : {}

      elements.forEach(element => {
        let template = templates?.[element.key] ?? this.templates?.[element.key] ?? null

        if (template) {
          template = { ...template }

          const items = template.items// || element.items

          if (items) {
            element.items = this.setElementFromTemplates(element.items || [], items)
            delete template.items
          }

          /*if (template.items) {
            element.items = this.setElementFromTemplates(element.items || [], template.items)
            delete template.items
          } else if (element.items) {
            delete element.items
          }*/

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
    setSettings () {
      return this.data?.settings || {}
    },
    setTemplates () {
      if (this.data?.templates) {
        let templates

        if (Array.isArray(this.data.templates)) {
          templates = {}

          this.data.templates.forEach(i => {
            if (i.key) {
              templates[i.key] = i
            }
          })
        }

        return templates || this.data.templates
      }
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
          if (data[index]['key'] && this.templates[data[index]['key']]) {
            element = { ...this.templates[data[index]['key']] }
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
        element.key = template
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
    <templates :data="true" @select:template="selectTemplate"/>
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
