<script>
import axios from 'axios'

export default {
  name: 'Element',
  props: {
    name: String,
    element: {
      type: String,
      required: true
    },
    type: String,
    title: [String, Number],
    label: [String, Number],
    help: [String, Number],
    default: {
      type: [Boolean, String, Number, Array],
      default: undefined
    },
    value: {
      type: [null, String, Number, Boolean, Array],
      default (props) {
        return props.default
      }
    },
    values: [null, Array, Object],
    elements: [String, Object, Array],
    placeholder: [String, Number],
    required: Boolean,
    readonly: Boolean,
    disabled: Boolean,
    actions: {
      type: [null, Boolean, Array],
      default: ['add', 'move', 'del']
    },
    items: Array,
    url: String,
    method: {
      type: String,
      default: 'post'
    },
    attrs: {
      type: Object,
      default: {}
    },
    itemAttrs: {
      type: Object,
      default: {}
    },
    itemsAttrs: {
      type: Object,
      default: {}
    },
    nameKey: {
      type: String,
      default: 'key'
    },
    nameValue: {
      type: String,
      default: 'value'
    }
  },
  data () {
    return {
      id: 'v-' + crypto.getRandomValues(new Uint32Array(1))[0].toString(36),
      data: this.elements ? [] : null,
      loading: false,
      load: true
    }
  },
  created () {
    if (typeof this.elements === 'string' && this.elements[0] === '@' || this.url) {
      return this.load && this.getData()
    }

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

    if (typeof this.elements === 'string') {
      this.elements.split('||').forEach((i, k) => {
        let key = i.split('==')[0] ?? null
        let value = i.split('==')[1] ?? null

        if (!['radio', 'checkbox'].includes(this.type) && this.value?.[k] !== undefined) {
          value = this.value[k]
        }

        this.data.push({
          key,
          value: value ?? key
        })
      })
    }
  },
  computed: {
    '@title' () {
      if (this['title']) {
        return this['title'].replace(/\{([\w.]*)}/g, (str, key) => {
          const value = typeof this.$props[key] !== undefined ? this.$props[key] : ''
          return (value === null || value === undefined) ? '' : value.toString().
              replace(/&/g, '&amp;').
              replace(/</g, '&lt;').
              replace(/>/g, '&gt;').
              replace(/"/g, '&quot;').
              replace(/'/g, '&#039;')
        })
      }
    },
    className () {
      const className = ['mf3-' + this.element + (this.type ? '__' + this.type : '')].concat(
          this.attrs?.class?.split(' '))

      if (!this.actions) {
        className.push('mf3-item__without-actions')
      }

      return className
    }
  },
  methods: {
    getData () {
      this.loading = true
      axios[this.method](this.url || '?a=mf3&action=elements', { elements: this.elements }).then(({ data }) => {
        this.data = this.parseData(data)
        this.$nextTick(this.initDatepicker)
      }).finally(() => this.loading = false)
    },
    parseData (data) {
      if (!data) {
        return []
      }

      if (Array.isArray(data)) {
        return data.map(i => {
          if (typeof i === 'string') {
            i = {
              key: i,
              value: i
            }

            return i
          }

          return {
            key: i[this.nameKey],
            value: i[this.nameValue]
          }
        })
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
