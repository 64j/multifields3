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
    type: {
      type: String,
      default (props) {
        return props.element
      }
    },
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
    attr: {
      type: Object,
      default: {}
    },
    'item.attr': Object,
    'item.class': [String, Array],
    'item.style': [String, Object],
    'items.attr': Object,
    'items.class': [String, Array],
    'items.style': [String, Object]
  },
  data () {
    return {
      id: 'v-' + crypto.getRandomValues(new Uint32Array(1))[0].toString(36),
      data: this.elements ? [] : null,
      loading: false
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
    }
  }
}
</script>
