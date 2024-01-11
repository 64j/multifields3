<script>
import { h } from 'vue'
import Sortable from 'sortablejs'

export default {
  name: 'Mf',
  props: ['el', 'config', 'tvName'],
  data () {
    return {
      value: this.el.value && JSON.parse(this.el.value) || []
    }
  },
  watch: {
    value: {
      handler (value) {
        this.el.innerHTML = value.length ? JSON.stringify(value) : ''
      },
      deep: true
    }
  },
  mounted () {
    Sortable.create(this.$el, {
      draggable: '.mf3-item',
      handle: '.mf3-actions__move',
      ghostClass: 'mf3-draggable__active',
      chosenClass: 'mf3-draggable__chosen',
      onEnd: (event) => {
        const item = { ...this.value[event.oldIndex] }
        this.value.splice(event.oldIndex, 1)
        this.value.splice(event.newIndex + 1, 0, item)
      }
    })
  },
  methods: {
    build (data) {
      const slots = []

      if (!Array.isArray(data)) {
        return slots
      }

      data.forEach((item, key) => {
        const name = item['type'] ? 'mf:' + item['type'] : null

        if (!name || !mf3Components[name]) {
          return
        }

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
  <div class="mf3">
    <component :is="() => this.build(this.value)"/>
  </div>
</template>

<style scoped>
.mf3 {
  @apply flex flex-wrap mb-3
}
</style>
