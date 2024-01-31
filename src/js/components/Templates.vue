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
        let templates = []

        if (this.data === true) {
          templates = this.$root['templates']
        } else if (Array.isArray(this.data)) {
          templates = this.$root['templates'].filter(i => this.data.includes(i.key))
        }

        if (templates.length) {
          return templates
        }
      }
    }
  },
  methods: {
    open () {
      if (this.templates.length === 1) {
        this.select(this.templates[0].key)
      }
    },
    select (key) {
      this.$emit('select:template', key)
    }
  }
}
</script>

<template>
  <div class="mf3-templates" v-if="templates">
    <button type="button" class="mf3-templates__add" @mousedown="open"/>
    <div v-if="templates.length > 1" class="mf3-templates__list">
      <div v-for="i in templates" @mousedown="select(i.key)">
        {{ i.key || i.title }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.mf3-templates {
  @apply absolute left-0 right-0 bottom-1 flex items-center justify-center opacity-0 invisible
}
.mf3-templates__add {
  @apply absolute w-4 h-4 p-0 z-10 flex items-center justify-center -bottom-2 bg-green-500 border-none rounded-full cursor-pointer
}
.darkness .mf3-templates__add {
  @apply bg-green-500
}
.mf3-templates__add::before, .mf3-templates__add::after {
  @apply absolute w-2 h-0.5 bg-white content-[""]
}
.mf3-templates__add::after {
  @apply rotate-90
}
.mf3-templates__list {
  @apply absolute z-20 mt-3 w-52 max-h-52 overflow-auto py-1 bg-white text-gray-900 rounded shadow-lg invisible opacity-0
}
.mf3-templates__add:focus + .mf3-templates__list {
  @apply visible opacity-100
}
.darkness .mf3-templates__list {
  @apply bg-gray-600 text-gray-100
}
.mf3-templates__list > div {
  @apply px-2 py-1 text-xs select-none cursor-pointer hover:bg-blue-500 hover:text-white
}
</style>
