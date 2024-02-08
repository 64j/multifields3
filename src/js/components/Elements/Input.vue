<script>
import axios from 'axios'
import Loader from '../Loader.vue'
import Element from '../Element.vue'
import Actions from '../Actions.vue'

export default {
  name: 'mf:input',
  __isStatic: true,
  extends: Element,
  components: { Loader, Actions },
  props: {
    type: {
      type: String,
      default: 'text'
    },
    min: [String, Number],
    max: [String, Number],
    minlength: [String, Number],
    maxlength: [String, Number],
    size: [String, Number],
    step: [String, Number],
    pattern: String,
    multi: Boolean,
    thumb: String
  },
  data () {
    return {
      data: this.elements ? [] : null,
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
              values = [...this.data.filter(i => value.includes(i.value))]
            } else {
              values = { ...this.data.filter(i => i.value === value)[0] || {} }
            }
          }
        }

        if (Array.isArray(value)) {
          if (this.type === 'number') {
            value = value.map(i => {
              if (i !== '') {
                return parseFloat(i)
              }
            })
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
    showValue () {
      switch (this.type) {
        case 'color':
        case 'range':
          return true
      }
    }
  },
  watch: {
    data: {
      handler (data) {
        if (!['radio', 'checkbox'].includes(this.type)) {
          this.model = [...data.map(i => i.value)]
        }
      },
      deep: true
    }
  },
  created () {
    if (this.elements) {
      if (typeof this.elements === 'object') {
        if (Array.isArray(this.elements)) {
          this.data = this.elements
        } else {
          for (const i in this.elements) {
            if (typeof this.elements[i] === 'object') {
              this.data.push({ key: i, ...this.elements[i] })
            } else {
              this.data.push({ key: i, value: this.elements[i] })
            }
          }
        }

        return
      }

      if (this.elements[0] === '@') {
        this.loading = true
        axios.post('?a=mf3&action=elements', { elements: this.elements }).then(({ data }) => {
          this.data = data
          this.$nextTick(this.initDatepicker)
        }).finally(() => this.loading = false)

        return
      }

      this.elements.split('||').forEach((i, k) => {
        let key = i.split('==')[0] ?? null
        let value = i.split('==')[1] ?? null

        if (!['radio', 'checkbox'].includes(this.type) && this.value?.[k] !== undefined) {
          value = this.value[k]
        }

        this.data.push({
          key: key,
          value: value ?? key
        })
      })
    }
  },
  mounted () {
    this.initDatepicker()
  },
  methods: {
    action (action, values) {
      this.$emit('action', action, values)
    },
    onChange (event, i, k) {
      if (['file', 'image', 'datepicker'].includes(this.type)) {
        if (i) {
          i.value = event.target.value
        } else {
          this.model = event.target.value
        }
      }
    },
    select (event) {
      if (this.type === 'image') {
        if (window['BrowseServer']) {
          BrowseServer(event.target.previousElementSibling.id)
          if (this.multi || this.elements) {
            this.MultiBrowseServer()
          }
        } else {
          alert('Method BrowseServer not found!')
        }
      } else if (this.type === 'file') {
        if (window['BrowseFileServer']) {
          BrowseFileServer(event.target.previousElementSibling.id)
        } else {
          alert('Method BrowseFileServer not found!')
        }
      } else if (this.type === 'datepicker') {
        event.target.previousElementSibling.value = ''
        event.target.previousElementSibling.dispatchEvent(new Event('change'))
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
                self.data[i].value = files[i]
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
    },
    initDatepicker () {
      if (this.type === 'datepicker') {
        if (window['DatePicker']) {
          this.$el.querySelectorAll('.mf3-items input').forEach(i => {
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
    }
  }
}
</script>

<template>
  <div class="mf3-item" :class="`mf3-${element}__${type}`" v-bind="attr">
    <actions @action="action" :actions="actions"/>

    <div class="mf3-items" :class="$props['items.class']" :style="$props['items.style']" v-bind="$props['items.attr']">
      <template v-if="data">
        <div v-if="label">
          <span>{{ label }}</span>
        </div>
        <loader v-if="loading" class="mf3-loader"/>
        <div v-for="(i, k) in data">
          <template v-if="['radio', 'checkbox'].includes(type)">
            <input :id="id + '-' + k"
                   :name="id + '-' + k"
                   :type="type"
                   :value="i.value"
                   :required="i.required"
                   :class="$props['item.class']"
                   v-bind="$props['item.attr']"
                   v-model="model">
          </template>
          <template v-else>
            <input :id="id + '-' + k"
                   :name="id + '-' + k"
                   :type="inputType"
                   :value="i.value"
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
                   :class="$props['item.class']"
                   v-bind="$props['item.attr']"
                   v-model="i.value"
                   @change="onChange($event, i, k)">

            <button v-if="['file', 'image', 'datepicker'].includes(type)" type="button" @click="select">
              <i/>
            </button>
          </template>

          <label v-if="i.key" :for="id + '-' + k">
            <span>{{ i.key }}</span>
            <span v-if="showValue && !(i.value === undefined || i.value === '')">{{ i.value }}</span>
            <i v-if="i.help" data-tooltip :title="i.help" class="fa fa-question-circle"/>
          </label>
        </div>
      </template>
      <template v-else>
        <input :id="id"
               :name="id"
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
               :class="$props['item.class']"
               v-bind="$props['item.attr']"
               v-model="model"
               @change="onChange">

        <button v-if="['file', 'image', 'datepicker'].includes(type)" type="button" @click="select">
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
.mf3-item > .mf3-items > div {
  @apply relative order-4 flex items-center w-full
}
.mf3-item > .mf3-items > div + div {
  @apply mt-1
}
.mf3-item .mf3-loader {
  @apply relative order-4
}
.mf3-item label {
  @apply order-3 m-0 shrink-0 inline-flex items-center
}
.mf3-input.mf3-input__checkbox label, .mf3-input.mf3-input__radio label {
  @apply w-auto
}
.mf3-item input {
  @apply order-2 w-full h-7 border
}
.mf3-item > .mf3-items > input ~ label {
  @apply order-1
}
.mf3-item > .mf3-items > div > input ~ label, .mf3-item.mf3-input__range > .mf3-items > input ~ label {
  @apply order-1 mr-2 w-40
}
.mf3-item > .mf3-items > div > input ~ label span, .mf3-item > .mf3-items input ~ label span {
  @apply first:mr-2 first:opacity-100 opacity-75 truncate
}
.mf3-item.mf3-input__checkbox > .mf3-items input ~ label, .mf3-item.mf3-input__radio > .mf3-items input ~ label, .mf3-item.mf3-input__color > .mf3-items input ~ label, .mf3-item.mf3-input__color > .mf3-items > div > input ~ label {
  @apply order-3 mr-0 p-0 w-fit
}
.mf3-item.mf3-input__color input {
  @apply w-7 p-0
}
.mf3-item.mf3-input__color input + label {
  @apply ml-2 order-3
}
.mf3-item.mf3-input__range input {
  @apply grow p-0 h-auto w-auto appearance-auto outline-none
}
.mf3-item.mf3-input__checkbox input, .mf3-item.mf3-input__radio input {
  @apply inline-block mr-2 w-3.5 h-3.5
}
.mf3-item.mf3-input__file input, .mf3-item.mf3-input__image input {
  @apply pr-7
}
.mf3-item.mf3-input__file button, .mf3-item.mf3-input__image button, .mf3-item.mf3-input__datepicker button {
  @apply absolute z-10 bottom-1.5 right-1.5 h-7 p-0 flex items-center justify-center border-none bg-transparent
}
.mf3-item.mf3-input__file > .mf3-items > div button, .mf3-item.mf3-input__image > .mf3-items > div button, .mf3-item.mf3-input__datepicker > .mf3-items > div button {
  @apply right-0 top-0
}
.mf3-item.mf3-input__file button::before {
  @apply content-[""] absolute left-0 top-1 bottom-1 border-none border-l opacity-50
}
.mf3-item.mf3-input__file button i {
  @apply flex relative justify-center mx-2.5 w-3 h-4 overflow-hidden bg-blue-300 rounded-[2px] rounded-tr-[5px] border border-solid border-black opacity-65 pointer-events-none
}
.mf3-item.mf3-input__file button:hover i {
  @apply opacity-85
}
.mf3-item.mf3-input__file button i::before {
  @apply content-[""] absolute left-0.5 top-1.5 w-1.5 h-1 border-solid border-t border-b border-l-0 border-r-0 border-black
}
.mf3-item.mf3-input__file button i::after {
  @apply content-[""] absolute -right-0.5 -top-0.5 w-1.5 h-1.5 bg-white border border-solid border-black
}
.mf3-item.mf3-input__image button::before {
  @apply content-[""] absolute left-0 top-1 bottom-1 border-none border-l opacity-50
}
.mf3-item.mf3-input__image button i {
  @apply flex relative justify-center mx-2 w-4 h-4 overflow-hidden bg-cyan-200 rounded-[2px] border border-solid border-black opacity-65 pointer-events-none
}
.mf3-item.mf3-input__image button:hover i {
  @apply opacity-85
}
.mf3-item.mf3-input__image button i::before {
  @apply content-[""] absolute left-1 top-2 w-3 h-3 border border-solid border-black bg-red-500 rotate-45
}
.mf3-item.mf3-input__image button i::after {
  @apply content-[""] absolute left-0.5 top-0.5 w-1 h-1 border border-solid border-amber-800 bg-amber-400 rounded-full
}
.mf3-item.mf3-input__date input {
  @apply !w-full
}
.mf3-item.mf3-input__datepicker button {
  @apply rounded-none
}
.mf3-item.mf3-input__datepicker button::before, .mf3-item.mf3-input__datepicker button::after {
  @apply content-[""] absolute top-1.5 left-2.5 w-0.5 h-0.5 bg-black
}
.mf3-item.mf3-input__datepicker button::after {
  @apply left-auto right-2.5
}
.mf3-item.mf3-input__datepicker button i {
  @apply flex relative justify-center items-center mx-2 w-3 h-3 overflow-hidden rounded-[2px] border border-t-2 border-solid border-black opacity-65 pointer-events-none
}
.mf3-item.mf3-input__datepicker button i::before, .mf3-item.mf3-input__datepicker button i::after {
  @apply content-[""] absolute w-1.5 h-[1px] bg-black rotate-45
}
.mf3-item.mf3-input__datepicker button i::before {
  @apply -rotate-45
}
.darkness .mf3-item.mf3-input__datepicker button::before, .darkness .mf3-item.mf3-input__datepicker button::after, .darkness .mf3-item.mf3-input__datepicker button i::before, .darkness .mf3-item.mf3-input__datepicker button i::after {
  @apply bg-white
}
.darkness .mf3-item.mf3-input__datepicker button i {
  @apply border-white
}
</style>
