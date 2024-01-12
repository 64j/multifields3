<script>
import { h } from 'vue'
import draggable from 'vuedraggable'

export default {
  name: 'Mf',
  components: { draggable },
  props: ['el', 'config', 'tvName'],
  data () {
    return {
      value: this.el.value && JSON.parse(this.el.value) || []
    }
  },
  watch: {
    value (value) {
      this.el.innerHTML = value.length ? JSON.stringify(value) : ''
    }
  },
  methods: {
    getComponent (item, key) {
      const name = item['type'] ? 'mf:' + item['type'] : null
      item.index = key

      return h(
          mf3Components[name],
          {
            ...item,
            'onUpdate:value': (value) => item.value = value,
            'onAction': (action) => this.action(action, this.value, key)
          },
          item?.items ? () => this.build(item.items) : null
      )
    },
    build (data) {
      const slots = []

      if (!Array.isArray(data)) {
        return slots
      }

      data.forEach((item, key) => {
        const name = item['type'] ? 'mf:' + item['type'] : null

        if (!name || !mf3Components[name]) {
          delete data[key]
          return
        }

        item.index = key

        slots.push(
            h(
                mf3Components[name],
                {
                  ...item,
                  'onUpdate:value': (value) => item.value = value,
                  'onAction': (action) => this.action(action, data, key)
                },
                item?.items ? () => this.build(item.items) : null
            )
        )
      })

      return slots
    },
    action (action, data, key) {
      switch (action) {
        case 'del':
          data.splice(key, 1)
          break

        case 'add':
          data.splice(key + 1, 0, this.clearValue({ ...data[key] }))
          break
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
  <draggable
      v-model="this.value"
      class="mf3"
      item-key=""
      handle=".mf3-actions__move"
      ghostClass="mf3-draggable__active"
      chosenClass="mf3-draggable__chosen">
    <template #item="{ element, index }">
      <component :is="getComponent(element, index)"/>
    </template>
  </draggable>
</template>

<style scoped>
.mf3 {
  @apply flex flex-wrap mb-3
}
</style>
