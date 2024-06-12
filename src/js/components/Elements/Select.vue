<script>
import Actions from '../Actions.vue'
import Loader from '../Loader.vue'
import Element from '../Element.vue'

export default {
  name: 'mf:select',
  __isStatic: true,
  extends: Element,
  components: { Loader, Actions },
  props: {
    multiple: {
      type: Boolean,
      default: undefined
    },
    size: Number
  },
  data () {
    return {
      data: this.values && Object.values(this.values).length ? [this.values] : [],
      load: this.multiple ?? false,
      focus: false
    }
  },
  computed: {
    model: {
      set (value) {
        this.$emit('update:value', value, { ...this.data.filter(i => i.key === value)[0] || {} })
      },
      get () {
        return this.value || (this.multiple ? [] : null)
      }
    }
  },
  methods: {
    action (action) {
      this.$emit('action', action)
    },
    getOptions () {
      if (this?.elements?.[0] === '@' || this.url) {
        if (!this.focus && !this.load) {
          this.getData()
        }
      } else {
        this.data = this.parseData(this.elements)
      }
    }
  }
}
</script>

<template>
  <div class="mf3-item" :class="className" v-bind="attrs">
    <actions @action="action" :actions="actions"/>

    <div class="mf3-items" v-bind="itemsAttrs">
      <loader v-if="loading" class="mf3-loader"/>
      <select v-model="model"
              :size="multiple ? (size || 8) : (size || 1)"
              :multiple="multiple"
              v-bind="itemAttrs"
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
  @apply w-full min-h-7
}
.mf3-item select:not([size="1"]) {
  @apply h-auto
}
.mf3-item select option {
  @apply checked:bg-blue-600 checked:text-white
}
.mf3-items {
  @apply items-center
}
.mf3-loader {
  @apply absolute left-3.5 top-3.5
}
</style>
