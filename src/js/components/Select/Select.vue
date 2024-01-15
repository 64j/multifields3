<script>
import Actions from '../Actions.vue'
import axios from 'axios'

export default {
  components: { Actions },
  __isStatic: true,
  name: 'mf:select',
  props: ['type', 'name', 'title', 'value', 'elements', 'default'],
  data () {
    return {
      options: [],
      focus: false
    }
  },
  methods: {
    action (action) {
      this.$emit('action', action)
    },
    updateValue (event) {
      this.$emit('update:value', event.target.value)
    },
    getOptions () {
      if (this.elements[0] === '@') {
        if (!this.focus) {
          axios.post('?a=mf3&action=elements', { elements: this.elements }).then(({ data }) => {
            this.options = this.parseOptions(data)
          })
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
    <select @input="updateValue" @mousedown="getOptions" @focus="() => focus = true" @blur="() => focus = false">
      <option/>
      <option v-for="i in options" :value="i.key" :selected="i.selected">{{ i.value }}</option>
    </select>
  </div>
</template>

<style scoped>
.mf3-item select {
  @apply w-full
}
</style>
