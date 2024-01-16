<script>
import Actions from '../Actions.vue'

export default {
  __isStatic: true,
  components: { Actions },
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
    return {
      id: 'v-' + crypto.getRandomValues(new Uint32Array(1))[0].toString(36),
      __type: ['file', 'image'].includes(this.type) ? 'text' : this.type
    }
  },
  computed: {
    model: {
      set (value) {
        this.$emit('update:value', value)
      },
      get () {
        if (this.elements) {
          if (this.value !== undefined) {
            if (!Array.isArray(this.value)) {
              return [this.value]
            }

            return this.value
          } else {
            return []
          }
        }

        return this.value
      }
    },
    data () {
      if (this.elements) {
        const data = []

        if (typeof this.elements === 'object') {
          if (Array.isArray(this.elements)) {
            return this.elements
          }

          for (const i in this.elements) {
            data.push({ key: this.elements[i].key || i, ...this.elements[i] })
          }

          return data
        }

        if (this.elements[0] === '@') {

          return data
        }

        for (const i of this.elements.split('||')) {
          const key = i.split('==')[0] ?? null
          const value = i.split('==')[1] ?? null

          data.push({
            key: key ?? value,
            value: value ?? key,
            selected: (this.value ?? this.default) === (key ?? value)
          })
        }

        return data
      }
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
      <div v-for="(i, k) in data">
          <input v-model="model"
                 :id="id + '-' + k"
                 :type="__type"
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
                 :disabled="i.disabled">

        <label v-if="i.value" :for="id + '-' + k">
          {{ i.value }}
        </label>

      </div>
    </div>
    <div v-else class="mf3-items">
        <input v-model="model"
               :id="id"
               :type="__type"
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
               :true-value="trueValue"
               :false-value="falseValue"
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
.mf3-item input[type="range"] {
  @apply p-0 appearance-auto outline-none
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
