<script>
import { h } from 'vue'

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
  methods: {
    build (data) {
      const slots = []

      if (!Array.isArray(data)) {
        return slots
      }

      data.forEach((item, key) => {
        const name = item['type'] ? 'mf:' + item['type'] : null

        if (!name || !mfComponents[name]) {
          return
        }

        slots.push(
            h(
                mfComponents[name],
                {
                  ...item,
                  'onUpdate:value': (value) => item.value = value,
                  'onDelete': () => data.splice(key, 1)
                },
                item?.items ? () => this.build(item.items) : null
            )
        )
      })

      return slots
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
