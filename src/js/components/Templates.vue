<script>
export default {
  name: 'Templates',
  props: ['data'],
  data () {
    return {
      templates: this.getTemplates(),
      isShow: false
    }
  },
  mounted () {
    document.addEventListener('click', () => this.isShow = false)
  },
  methods: {
    getTemplates () {
      if (this.data && this.$root['templates'] && Object.values(this.$root['templates']).length) {
        const templates = []

        if (this.data === true) {
          for (const i in this.$root['templates']) {
            templates.push({ key: i, title: this.$root['templates'][i]['title'] || i })
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
    },
    open () {
      this.isShow = !this.isShow
    },
    select (key) {
      this.isShow = false
      this.$emit('select:template', { ...this.$root['templates'][key] }, key )
    }
  }
}
</script>

<template>
  <div class="mf3-templates" v-if="templates">
    <transition>
      <div class="mf3-templates__list" v-show="isShow">
        <div v-for="i in templates" @click.stop="select(i['key'])">
          {{ i['title'] || i['key'] }}
        </div>
      </div>
    </transition>
    <div class="mf3-templates__add" @click.stop="open"/>
  </div>
</template>

<style scoped>
.mf3-templates {
  @apply absolute left-0 top-0 right-0 bottom-0 flex items-center justify-center opacity-0 invisible
}
.mf3-templates__add {
  @apply absolute w-4 h-4 z-10 flex items-center justify-center -bottom-2 bg-green-500 rounded-full cursor-pointer
}
.mf3-templates__add::before, .mf3-templates__add::after {
  @apply absolute w-2 h-0.5 bg-white content-[""]
}
.mf3-templates__add::after {
  @apply rotate-90
}
.mf3-templates__list {
  @apply absolute bottom-2 z-20 w-52 py-1 bg-white rounded shadow-lg
}
.mf3-templates__list > div {
  @apply px-2 py-1 text-sm select-none cursor-pointer hover:bg-blue-500 hover:text-white
}
</style>
