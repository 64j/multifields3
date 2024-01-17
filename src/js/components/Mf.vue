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
    //this.elements = this.dataEl.value && JSON.parse(this.dataEl.value) || []
    return {
      elements: this.dataEl.value && JSON.parse(this.dataEl.value) || [],
      templates: this.getTemplates(),
      settings: this.getSettings()
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
            list: elements,
            itemKey: a => a,
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
      let template = {}

      if (this.templates?.[element.key]) {
        template = { ...this.templates[element.key] }

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
      }

      const name = element.name ? 'mf:' + element.name : null

      if (!mf3Components[name]) {
        return
      }

      return h(
          mf3Components[name],
          {
            ...element,
            ...template,
            'onAction': (action, ...args) => this.action(action, elements, index, ...args),
            'onUpdate:value': (...args) => this.updateValue(element, elements, ...args),
            'onSelect:template': (...args) => this.selectTemplate(element, ...args)
          },
          element?.items && (() => this.getElements(element.items))
      )
    },
    action (action, data, index, values) {
      switch (action) {
        case 'del':
          data.splice(index, 1)
          break

        case 'add':
          data.splice(index + 1, 0, this.clearValue({ ...data[index] }))

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
    }
  }
}
</script>

<template>
  <div class="mf3 mf3-group">
    <templates :data="true" @select:template="selectTemplate"/>
    <component :is="() => getElements(elements)" :key="0"/>
  </div>
</template>

<style scoped>
@tailwind base;
</style>
