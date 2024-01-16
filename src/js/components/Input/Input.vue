<script>
import axios from 'axios'
import Loader from '../Loader.vue'
import Actions from '../Actions.vue'

export default {
  __isStatic: true,
  components: { Loader, Actions },
  name: 'mf:input',
  props: [
    'type',
    'name',
    'title',
    'label',
    'value',
    'elements',
    'default',
    'list',
    'min',
    'max',
    'minlength',
    'maxlength',
    'size',
    'step',
    'placeholder',
    'pattern',
    'required',
    'readonly',
    'disabled',
    'trueValue',
    'falseValue'
  ],
  data () {
    this.id = 'v-' + crypto.getRandomValues(new Uint32Array(1))[0].toString(36)
    return {
      data: null,
      loading: false
    }
  },
  computed: {
    model: {
      set (value) {
        let values

        if (this.data) {
          if (Array.isArray(value)) {
            values = [...this.data.filter(i => value.includes(i.key))]
          } else {
            values = { ...this.data.filter(i => i.key === value)[0] || {} }
          }
        }

        this.$emit('update:value', value, values)
      },
      get () {
        switch (this.type) {
          case 'checkbox':
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
            break
        }

        return this.value
      }
    },
    inputType () {
      return ['file', 'image'].includes(this.type) ? 'text' : this.type
    },
    bindAttributes () {
      const attrs = {}

      if (this.trueValue !== undefined) {
        attrs['true-value'] = this.trueValue
      }

      if (this.falseValue !== undefined) {
        attrs['false-value'] = this.falseValue
      }

      return attrs
    }
  },
  created () {
    if (this.elements) {
      this.data = []

      if (typeof this.elements === 'object') {
        if (Array.isArray(this.elements)) {
          return this.elements
        }

        for (const i in this.elements) {
          this.data.push({ key: this.elements[i].key || i, ...this.elements[i] })
        }

        return data
      }

      if (this.elements[0] === '@') {
        this.loading = true
        axios.post('?a=mf3&action=elements', { elements: this.elements }).then(({ data }) => {
          this.data = data
        }).finally(() => this.loading = false)

        return
      }

      for (const i of this.elements.split('||')) {
        const key = i.split('==')[0] ?? null
        const value = i.split('==')[1] ?? null

        this.data.push({
          key: key ?? value,
          value: value ?? key,
          selected: (this.value ?? this.default) === (key ?? value)
        })
      }

      return this.data
    }
  },
  methods: {
    action (action) {
      this.$emit('action', action)
    },
    updateValue (event) {
      if (['file', 'image'].includes(this.type)) {
        this.$emit('update:value', event.target.value)
      }
    },
    select (event) {
      if (this.type === 'image') {
        return BrowseServer(event.target.previousElementSibling.id)
      }

      return BrowseFileServer(event.target.previousElementSibling.id)
    }
  }
}
</script>

<template>
  <div class="mf3-item" :class="`mf3-input__` + type">
    <actions @action="action"/>

    <div v-if="data" class="mf3-items">
      <loader v-if="loading" class="mf3-loader"/>
      <div v-for="(i, k) in data">
          <input v-model="model"
                 :id="id + '-' + k"
                 :type="inputType"
                 :value="i.key"
                 :list="i.list"
                 :min="i.min"
                 :max="i.max"
                 :minlength="i.minlength"
                 :maxlength="i.maxlength"
                 :step="i.step"
                 :size="i.size"
                 :placeholder="i.placeholder"
                 :pattern="i.pattern"
                 :required="i.required"
                 :readonly="i.readonly"
                 :disabled="i.disabled"
                 @change="updateValue">

        <label v-if="i.value" :for="id + '-' + k">
          {{ i.value }}
        </label>
      </div>
    </div>
    <div v-else class="mf3-items">
        <input v-model="model"
               :id="id"
               :type="inputType"
               :list="list"
               :min="min"
               :max="max"
               :minlength="minlength"
               :maxlength="maxlength"
               :step="step"
               :size="size"
               :placeholder="placeholder"
               :pattern="pattern"
               :required="required"
               :readonly="readonly"
               :disabled="disabled"
               v-bind="bindAttributes"
               @change="updateValue">

      <button v-if="['file', 'image'].includes(type)" type="button" @click="select">
        <i/>
      </button>

      <label v-if="label" :for="id">
        {{ label }}
      </label>
    </div>
  </div>
</template>

<style scoped>
.mf3-item .mf3-items {
  @apply relative p-0 flex-row items-center
}
.mf3-item .mf3-items > div {
  @apply flex items-center w-full
}
.mf3-item label {
  @apply m-0 inline-flex items-center
}
.mf3-input__checkbox label, .mf3-input__radio label {
  @apply w-auto
}
.mf3-item input {
  @apply !w-full border
}
.mf3-item input[type="color"] {
  @apply !w-7 p-0
}
.mf3-item input[type="color"] + label {
  @apply ml-2
}
.mf3-item input[type="range"] {
  @apply px-0 py-1 appearance-auto outline-none
}
.mf3-item input[type="checkbox"], .mf3-item input[type="radio"] {
  @apply inline-block mr-2 !w-3.5 !h-3.5
}
.mf3-input__file button, .mf3-input__image button {
  @apply absolute z-10 right-0.5 top-0.5 bottom-0.5 p-0 flex items-center justify-center border-none
}
.mf3-input__file button::before {
  @apply content-[""] absolute left-0 top-1 bottom-1 border-none border-l opacity-50
}
.mf3-input__file button i {
  @apply flex relative justify-center mx-2.5 w-3 h-4 overflow-hidden bg-blue-300 rounded-[2px] rounded-tr-[5px] border border-black opacity-65 pointer-events-none
}
.mf3-input__file button:hover i {
  @apply opacity-85
}
.mf3-input__file button i::before {
  @apply content-[""] absolute left-0.5 top-1.5 w-1.5 h-1 border-t border-b border-black
}
.mf3-input__file button i::after {
  @apply content-[""] absolute -right-0.5 -top-0.5 w-1.5 h-1.5 bg-white border border-black
}
.mf3-input__image button::before {
  @apply content-[""] absolute left-0 top-1 bottom-1 border-none border-l opacity-50
}
.mf3-input__image button i {
  @apply flex relative justify-center mx-2 w-4 h-4 overflow-hidden bg-cyan-200 rounded-[2px] border border-black opacity-65 pointer-events-none
}
.mf3-input__image button:hover i {
  @apply opacity-85
}
.mf3-input__image button i::before {
  @apply content-[""] absolute left-1 top-2 w-3 h-3 border border-black bg-red-500 rotate-45
}
.mf3-input__image button i::after {
  @apply content-[""] absolute left-0.5 top-0.5 w-1 h-1 border border-amber-800 bg-amber-400 rounded-full
}
</style>
