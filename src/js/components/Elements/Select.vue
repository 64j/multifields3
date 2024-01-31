<script>
import axios from 'axios'
import Actions from '../Actions.vue'
import Loader from '../Loader.vue'

export default {
  name: 'mf:select',
  __isStatic: true,
  components: { Loader, Actions },
  props: [
    'name',
    'type',
    'title',
    'value',
    'values',
    'elements',
    'default',
    'multiple',
    'size',
    'actions'
  ],
  data () {
    return {
      data: this.values && Object.values(this.values).length ? [this.values] : [],
      loading: false,
      load: this.multiple || this.size > 1,
      focus: false
    }
  },
  computed: {
    model: {
      set (value) {
        this.$emit('update:value', value, { ...this.data.filter(i => i.key === value)[0] || {} })
      },
      get () {
        if (this.multiple) {
          if (this.elements) {
            if (this.value === undefined) {
              return []
            }

            return Array.isArray(this.value) ? this.value : [this.value]
          } else {
            if (this.value !== undefined) {
              return Array.isArray(this.value) ? this.value.toString() : this.value
            }
          }
        }

        return this.value
      }
    }
  },
  created () {
    if (this.load) {
      this.getData()
    }
  },
  methods: {
    action (action) {
      this.$emit('action', action)
    },
    getOptions () {
      if (this.elements[0] === '@') {
        if (!this.focus && !this.load) {
          this.getData()
        }
      } else {
        this.data = this.parseData(this.elements)
      }
    },
    getData () {
      this.loading = true
      this.data = []
      axios.post('?a=mf3&action=elements', { elements: this.elements }).then(({ data }) => {
        this.data = this.parseData(data)
      }).finally(() => this.loading = false)
    },
    parseData (data) {
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
    <actions @action="action" :actions="actions"/>

    <div class="mf3-items">
      <loader v-if="loading" class="mf3-loader"/>
      <select v-model="model"
              :size="multiple ? (size || 8) : (size || 1)"
              :multiple="multiple"
              @mousedown="getOptions"
              @focus="() => focus = true"
              @blur="() => focus = false">
        <option v-if="!multiple"/>
        <option v-for="i in data" :value="i.key">{{ i.value }}</option>
      </select>
    </div>
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
.mf3-loader {
  @apply absolute left-2 top-2.5
}
</style>
