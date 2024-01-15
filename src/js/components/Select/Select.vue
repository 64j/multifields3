<script>
import axios from 'axios'
import Actions from '../Actions.vue'
import Loader from '../Loader.vue'

export default {
  components: { Loader, Actions },
  __isStatic: true,
  name: 'mf:select',
  props: ['type', 'name', 'title', 'value', 'values', 'elements', 'default', 'multiple', 'size', 'load'],
  data () {
    return {
      options: this.values && Object.values(this.values).length ? [this.values] : [],
      loading: false,
      focus: false
    }
  },
  computed: {
    model: {
      set (value) {
        this.$emit('update:value', value, { ...this.options.filter(i => i.key === value)[0] || {} })
      },
      get () {
        return this.value || (!this.value && this.multiple && [])
      }
    }
  },
  mounted () {
    if (this.multiple || this.load) {
      this.getOptions()
    }
  },
  methods: {
    action (action) {
      this.$emit('action', action)
    },
    getOptions () {
      if (this.elements[0] === '@') {
        if ((!this.focus && !this.load) || (this.load && !this.options.length)) {
          this.loading = true
          this.options = []
          axios.post('?a=mf3&action=elements', { elements: this.elements }).then(({ data }) => {
            this.options = this.parseOptions(data)
          }).finally(() => this.loading = false)
        }
      } else {
        this.options = this.parseOptions(this.elements)
      }
    },
    parseOptions (data) {
      if (Array.isArray(data)) {
        return data
      }

      const options = []

      for (const i of data.split('||')) {
        const key = i.split('==')[0] ?? null
        const value = i.split('==')[1] ?? null

        options.push({
          key: key ?? value,
          value: value ?? key,
          selected: (this.value ?? this.default) === (key ?? value)
        })
      }

      return options
    }
  }
}
</script>

<template>
  <div class="mf3-item">
    <actions @action="action"/>
    <select v-model="model"
            :size="multiple ? (size || 8) : 1"
            :multiple="multiple"
            @mousedown="getOptions"
            @focus="() => focus = true"
            @blur="() => focus = false">
      <option v-if="!multiple"/>
      <option v-for="i in options" :value="i.key">{{ i.value }}</option>
    </select>
    <loader v-if="loading" class="absolute left-2 top-2.5"/>
  </div>
</template>

<style scoped>
.mf3-item select {
  @apply w-full
}
.mf3-item select:not([size="1"]) {
  @apply h-auto
}
.mf3-item select option {
  @apply checked:bg-blue-600 checked:text-white
}
</style>
