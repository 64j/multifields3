<script>
export default {
  name: 'Templates',
  props: ['data'],
  computed: {
    templates () {
      if ((this.data || this.data === undefined) && this.$root['templates'] &&
          Object.values(this.$root['templates']).length) {
        const templates = []

        if (this.data === true || this.data === undefined) {
          for (const i in this.$root['templates']) {
            if (mf3Components[`mf:${this.$root['templates'][i]['name']}`]) {
              templates.push({ key: i, title: this.$root['templates'][i]['title'] || i })
            }
          }
        } else if (Array.isArray(this.data)) {
          for (const i in this.$root['templates']) {
            if (this.data.includes(i)) {
              templates.push({ key: i, title: this.$root['templates'][i]['title'] || i })
            }
          }
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
      this.$emit('select:template', { ...this.$root['templates'][key] }, key )
    }
  }
}
</script>

<template>
  <div class="mf3-templates" v-if="templates">
    <button type="button" class="mf3-templates__add" @click="open"/>
    <div class="mf3-templates__list">
      <div v-for="i in templates" @mousedown="select(i['key'])">
        {{ i['title'] || i['key'] }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.mf3-templates {
  @apply absolute left-1 top-1 right-1 bottom-1 flex items-center justify-center opacity-0 invisible
}
.mf3-templates__add {
  @apply absolute w-4 h-4 p-0 z-10 flex items-center justify-center -bottom-2 bg-green-500 border-none rounded-full cursor-pointer
}
.mf3-templates__add::before, .mf3-templates__add::after {
  @apply absolute w-2 h-0.5 bg-white content-[""]
}
.mf3-templates__add::after {
  @apply rotate-90
}
.mf3-templates__list {
  @apply absolute bottom-2 z-20 w-52 py-1 bg-white text-gray-900 rounded shadow-lg invisible opacity-0
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
