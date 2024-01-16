<script>
import { h } from 'vue'
import draggable from 'vuedraggable'
import Actions from './Actions.vue'
import Templates from './Templates.vue'

export default {
  name: 'mf',
  components: { Templates, Actions, draggable },
  props: ['dataEl', 'data', 'tvName'],
  data () {
    return {
      elements: this.dataEl.value && JSON.parse(this.dataEl.value) || [],
      templates: this.getTemplates(),
      settings: this.getSettings()
    }
  },
  watch: {
    elements: {
      handler (elements) {
        this.dataEl.innerHTML = elements.length ? JSON.stringify(elements) : ''
      },
      deep: true
    }
  },
  methods: {
    getTemplates () {
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
    getSettings () {
      return this.data?.settings || {}
    },
    getElements (elements) {
      return h(
          draggable,
          {
            tag: 'div',
            itemKey: (a) => a,
            list: elements,
            class: 'mf3-items',
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
      if (this.templates?.[element.key]) {
        const template = { ...this.templates[element.key] }

        if (template.items) {
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

      const name = element.name ? 'mf:' + element.name : null

      if (!mf3Components[name]) {
        return
      }

      return h(
          mf3Components[name],
          Object.assign(element, {
            'onAction': (action) => this.action(action, elements, index),
            'onUpdate:value': (...args) => this.updateValue(element, ...args),
            'onSelect:template': (...args) => this.selectTemplate(element, ...args)
          }),
          element?.items ? this.getElements(element.items) : null
      )
    },
    action (action, data, key) {
      switch (action) {
        case 'del':
          data.splice(key, 1)
          break

        case 'add':
          data.splice(key + 1, 0, this.clearValue({ ...data[key] }))
          break

        case 'tpl':
          console.log('tpl')
          break
      }
    },
    updateValue (element, value, values) {
      element.value = value
      element.values = values
    },
    selectTemplate (element, template, key) {
      if (key !== undefined) {
        if (!element.items) {
          element.items = []
        }

        element.items.push(template)
      } else {
        this.elements.push(element)
      }
    },
    clearValue (data) {
      if (data.value !== undefined) {
        data.value = ''
      }

      if (data.items) {
        data.items = data.items.map(i => this.clearValue({ ...i }))
      }

      return data
    }
  }
}
</script>

<template>
  <div class="mf3 mf3-group">
    <templates :data="true" @select:template="selectTemplate"/>
    <component :is="getElements(elements)" :key="0"/>
  </div>
</template>

<style scoped>
.mf3 {
  @apply relative flex flex-wrap mb-6 p-0 m-0 border-none
}
.mf3 > .mf3-templates {
  @apply -bottom-3
}
</style>
