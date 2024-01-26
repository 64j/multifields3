<script>
import axios from 'axios'
import Loader from '../Loader.vue'
import Actions from '../Actions.vue'

export default {
  name: 'mf:input',
  __isStatic: true,
  components: { Loader, Actions },
  props: [
    'key',
    'type',
    'name',
    'title',
    'label',
    'value',
    'values',
    'elements',
    'default',
    'list',
    'min',
    'max',
    'minlength',
    'maxlength',
    'size',
    'step',
    'multi',
    'thumb',
    'placeholder',
    'pattern',
    'required',
    'readonly',
    'disabled',
    'trueValue',
    'falseValue',
    'actions',
    'modelValue'
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

        if (this.type === 'checkbox') {
          if (this.data) {
            if (Array.isArray(value)) {
              values = [...this.data.filter(i => value.includes(i.key))]
            } else {
              values = { ...this.data.filter(i => i.key === value)[0] || {} }
            }
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
      switch (this.type) {
        case 'file':
        case 'image':
        case 'datepicker':
          return 'text'

        case 'datetime':
          return 'datetime-local'

        default:
          return this.type
      }
    },
    inputClass () {
      switch (this.type) {
        case 'datepicker':
          return 'DatePicker'
      }
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
    },
    showValue () {
      switch (this.type) {
        case 'color':
          return true

        case 'range':
          return !!this.elements
      }
    }
  },
  watch: {
    data: {
      handler (data) {
        if (!['radio', 'checkbox'].includes(this.type)) {
          this.model = [...data.map(i => i.key)]
        }
      },
      deep: true
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

      this.elements.split('||').forEach((i, k) => {
        const key = i.split('==')[0] ?? null
        const value = i.split('==')[1] ?? null
        let keyData = key ?? value

        if (!['radio', 'checkbox'].includes(this.type) && this.value?.[k] !== undefined) {
          keyData = this.value[k]
        }

        this.data.push({
          key: keyData,
          value: value ?? key
        })
      })

      return this.data
    }
  },
  mounted () {
    if (this.type === 'datepicker') {
      if (window['DatePicker']) {
        this.$el.querySelectorAll('input.DatePicker').forEach(i => {
          let format = i.getAttribute('data-format')
          const datepicker = new DatePicker(i, {
            yearOffset: dpOffset,
            format: format !== null ? format : dpformat,
            dayNames: dpdayNames,
            monthNames: dpmonthNames,
            startDay: dpstartDay
          })

          const updateValue = datepicker.constructor.updateValue

          datepicker.constructor.updateValue = (input) => {
            updateValue.call(datepicker.constructor, input)
            input.dispatchEvent(new Event('change'))
          }
        })
      }
    }
  },
  methods: {
    action (action, values) {
      this.$emit('action', action, values)
    },
    onChange (event, i, k) {
      if (this.elements) {
        if (['file', 'image', 'datepicker'].includes(this.type)) {
          i.key = event.target.value
        }
      }
    },
    select (event) {
      if (this.type === 'image') {
        BrowseServer(event.target.previousElementSibling.id)
        if (this.multi || this.elements) {
          this.MultiBrowseServer()
        }
      } else if (this.type === 'file') {
        BrowseFileServer(event.target.previousElementSibling.id)
      }
    },
    MultiBrowseServer () {
      const self = this

      this.interval = setInterval(() => {
        if (window.KCFinder) {
          clearInterval(this.interval)
          window.KCFinder.callBackMultiple = (files) => {
            window.KCFinder = null
            if (self.elements) {
              for (let i in files) {
                self.data[i].key = files[i]
              }
            } else {
              window.SetUrl(files.shift())

              for (let i of files) {
                self.action('add', { value: i })
              }
            }
          }
        }
      }, 100)
    }
  }
}
</script>

<template>
  <div class="mf3-item" :class="`mf3-input__` + type">
    <actions @action="action" :actions="actions"/>

    <div class="mf3-items">
      <template v-if="data">
        <loader v-if="loading" class="mf3-loader"/>
        <div v-for="(i, k) in data">
          <template v-if="['radio', 'checkbox'].includes(type)">
            <input :id="id + '-' + k"
                   :type="type"
                   :value="i.key"
                   :required="i.required"
                   v-model="model">
          </template>
          <template v-else>
            <input :id="id + '-' + k"
                   :type="inputType"
                   :value="i.key"
                   :min="min"
                   :max="max"
                   :step="step"
                   :size="size"
                   :minlength="minlength"
                   :maxlength="maxlength"
                   :required="required"
                   :readonly="readonly"
                   :pattern="pattern"
                   :placeholder="placeholder"
                   :class="inputClass"
                   v-model="i.key"
                   @change="onChange($event, i, k)">

            <button v-if="['file', 'image'].includes(type)" type="button" @click="select">
              <i/>
            </button>
          </template>

          <label v-if="i.value" :for="id + '-' + k">
            <span>{{ i.value }}</span>
            <span v-if="showValue && !(i.key === undefined || i.key === '')">{{ i.key }}</span>
          </label>
        </div>
      </template>
      <template v-else>
        <input :id="id"
               :type="inputType"
               :min="min"
               :max="max"
               :step="step"
               :size="size"
               :minlength="minlength"
               :maxlength="maxlength"
               :required="required"
               :readonly="readonly"
               :disabled="disabled"
               :pattern="pattern"
               :placeholder="placeholder"
               :list="list"
               :class="inputClass"
               v-bind="bindAttributes"
               v-model="model"
               @change="onChange">

        <button v-if="['file', 'image'].includes(type)" type="button" @click="select">
          <i/>
        </button>

        <label v-if="label" :for="id">
          <span>{{ label }}</span>
          <span v-if="showValue && !(value === undefined || value === '')">{{ value }}</span>
        </label>
      </template>
    </div>
  </div>
</template>

<style scoped>
.mf3-item {
  @apply flex-wrap
}
.mf3-items > div {
  @apply relative flex items-center w-full
}
.mf3-item > .mf3-items > div + div {
  @apply mt-1
}
.mf3-loader {
  @apply relative
}
.mf3-item label {
  @apply order-3 m-0 shrink-0 inline-flex items-center
}
.mf3-input__checkbox label, .mf3-input__radio label {
  @apply w-auto
}
.mf3-item input {
  @apply order-2 w-full h-7 border
}
.mf3-item input[type="date"] {
  @apply !w-full
}
.mf3-item > .mf3-items > input ~ label {
  @apply order-1
}
.mf3-item > .mf3-items > div > input ~ label {
  @apply order-1 mr-2 min-w-20
}
.mf3-item > .mf3-items > div > input ~ label span, .mf3-item > .mf3-items input ~ label span {
  @apply first:mr-2 first:opacity-100 opacity-75
}
.mf3-item.mf3-input__checkbox > .mf3-items input ~ label, .mf3-item.mf3-input__radio > .mf3-items input ~ label, .mf3-item.mf3-input__color > .mf3-items > div > input ~ label {
  @apply order-3 mr-0 min-w-fit
}
.mf3-item input[type="color"] {
  @apply w-7 p-0
}
.mf3-item input[type="color"] + label {
  @apply ml-2 order-3
}
.mf3-item input[type="range"] {
  @apply px-0 py-1 appearance-auto outline-none
}
.mf3-item input[type="checkbox"], .mf3-item input[type="radio"] {
  @apply inline-block mr-2 w-3.5 h-3.5
}
.mf3-input__file input, .mf3-input__image input {
  @apply pr-7
}
.mf3-input__file button, .mf3-input__image button {
  @apply absolute z-10 right-0.5 bottom-1 h-7 p-0 flex items-center justify-center border-none bg-transparent
}
.mf3-input__file > .mf3-items > div button, .mf3-input__image > .mf3-items > div button {
  @apply -right-0.5 top-0
}
.mf3-input__file button::before {
  @apply content-[""] absolute left-0 top-1 bottom-1 border-none border-l opacity-50
}
.mf3-input__file button i {
  @apply flex relative justify-center mx-2.5 w-3 h-4 overflow-hidden bg-blue-300 rounded-[2px] rounded-tr-[5px] border border-solid border-black opacity-65 pointer-events-none
}
.mf3-input__file button:hover i {
  @apply opacity-85
}
.mf3-input__file button i::before {
  @apply content-[""] absolute left-0.5 top-1.5 w-1.5 h-1 border-solid border-t border-b border-l-0 border-r-0 border-black
}
.mf3-input__file button i::after {
  @apply content-[""] absolute -right-0.5 -top-0.5 w-1.5 h-1.5 bg-white border border-solid border-black
}
.mf3-input__image button::before {
  @apply content-[""] absolute left-0 top-1 bottom-1 border-none border-l opacity-50
}
.mf3-input__image button i {
  @apply flex relative justify-center mx-2 w-4 h-4 overflow-hidden bg-cyan-200 rounded-[2px] border border-solid border-black opacity-65 pointer-events-none
}
.mf3-input__image button:hover i {
  @apply opacity-85
}
.mf3-input__image button i::before {
  @apply content-[""] absolute left-1 top-2 w-3 h-3 border border-solid border-black bg-red-500 rotate-45
}
.mf3-input__image button i::after {
  @apply content-[""] absolute left-0.5 top-0.5 w-1 h-1 border border-solid border-amber-800 bg-amber-400 rounded-full
}
</style>
