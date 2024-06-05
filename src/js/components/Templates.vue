<script>
export default {
  name: 'Templates',
  props: {
    data: {
      type: [Boolean, Object, Array]
    }
  },
  computed: {
    templates () {
      if (this.data) {
        let templates = { ...this.$root['config']['templates'] }

        if (this.data === true) {
          for (const i in templates) {
            if (!mf3Elements[`mf:${templates[i].element}`] || templates[i].hidden) {
              delete templates[i]
            }
          }
        } else if (Array.isArray(this.data)) {
          for (const i in templates) {
            if (!this.data.includes(i)) {
              delete templates[i]
            }
          }
        }

        if (Object.values(templates).length) {
          return this.normalizeItems(templates)
        }
      }
    }
  },
  methods: {
    open () {
      if (Object.values(this.templates).length === 1) {
        for (const i in this.templates) {
          this.select(i)
          break
        }
      } else {
        this.$el.classList.add('mf3-templates-opened')
      }
    },
    close () {
      this.$el.classList.remove('mf3-templates-opened')
    },
    select (key) {
      this.$emit('select:template', key)
    },
    normalizeItems (data) {
      for (const i in data) {
        if (typeof data[i].items === 'object' && !Array.isArray(data[i].items)) {
          const keys = Object.keys(data[i].items)
          data[i].items = Object.values(data[i].items).map((i, k) => {
            i.name = keys[k]

            if (i.items !== undefined) {
              i.items = this.normalizeItems(i.items)
            }

            return i
          })
        }
      }

      return data
    }
  }
}
</script>

<template>
  <div class="mf3-templates" v-if="templates">
    <button type="button" class="mf3-templates__add" @click="open" @blur="close"/>
    <div v-if="Object.values(templates).length > 1" class="mf3-templates__list">
      <div v-for="(i, k) in templates" @mousedown="select(k)">
        {{ i.name || i.title?.replace(/\{([\w.]*)}/g, '') || k }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.mf3-templates {
  @apply absolute left-0 right-0 bottom-0 flex items-center justify-center opacity-0 invisible
}
.mf3-templates__add {
  @apply absolute w-4 h-4 p-0 z-10 flex items-center justify-center -bottom-2 bg-green-500 dark:bg-green-500 border-none rounded-full cursor-pointer
}
.mf3-templates__add::before, .mf3-templates__add::after {
  @apply absolute w-2 h-0.5 bg-white content-[""]
}
.mf3-templates__add::after {
  @apply rotate-90
}
.mf3-templates__list {
  @apply absolute z-20 mt-3 w-52 max-h-52 overflow-auto py-1 bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 rounded shadow-lg invisible opacity-0
}
.mf3-templates-opened .mf3-templates__list {
  @apply visible opacity-100
}
.mf3-templates__list > div {
  @apply px-2 py-1 select-none cursor-pointer hover:bg-blue-500 hover:text-white
}
</style>
