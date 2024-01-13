<script>
import { h } from 'vue'
import draggable from 'vuedraggable'
import Actions from './Actions.vue'
import Templates from './Templates.vue'

export default {
  name: 'mf',
  components: { Templates, Actions, draggable },
  props: ['el', 'templates', 'settings', 'tvName'],
  data () {
    return {
      elements: this.el.value && JSON.parse(this.el.value) || []
    }
  },
  watch: {
    elements: {
      handler (elements) {
        this.el.innerHTML = elements.length ? JSON.stringify(elements) : ''
      },
      deep: true
    }
  },
  methods: {
    getElements (elements) {
      return h(
          draggable,
          {
            tag: 'div',
            itemKey: '',
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
      const name = element['type'] ? 'mf:' + element['type'] : null

      if (!mf3Components[name]) {
        return
      }

      return h(
          mf3Components[name],
          {
            ...element,
            index,
            'onAction': (action) => this.action(action, elements, index),
            'onUpdate:value': (value) => element.value = value,
            'onSelect:template': (value) => this.selectTemplate(value)
          },
          element?.items ? () => this.getElements(element.items) : null
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
    selectTemplate (value) {
      console.log(value)
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
    <templates :templates="true" @select:template="selectTemplate"/>
    <component :is="() => getElements(elements)" class="grow"/>
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
