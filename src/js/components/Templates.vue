<script>
export default {
  name: 'Templates',
  props: ['templates'],
  data () {
    return {
      showList: false
    }
  },
  computed: {
    getTemplates () {
      if (this.templates) {
        if (this.templates === true) {
          return this.$root['templates']
        } else if (Array.isArray(this.templates)) {
          const templates = []
          for (const i in this.$root['templates']) {
            const key = this.$root['templates'].key || i
            if (this.templates.includes(key)) {
              templates.push({ key, ...this.$root['templates'][i] })
            }
          }

          if (templates.length) {
            return templates
          }
        }
      }
    }
  },
  methods: {
    openList () {
      this.showList = !this.showList
      console.log('openList')
    }
  }
}
</script>

<template>
  <div class="mf3-templates" v-if="getTemplates">
    <transition>
      <div class="mf3-templates__list" v-if="showList">
        <div v-for="(i, k) in getTemplates">
          {{ i['title'] || i['key'] || k }}
        </div>
      </div>
    </transition>
    <div class="mf3-templates__add" @click="openList"/>
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
  @apply px-2 py-1 text-sm hover:bg-blue-500 hover:text-white
}
</style>
