<script>
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
    value: {
      type: [null, String, Number, Boolean, Array],
      default (props) {
        return props.default
      }
    },
    elements: [String, Object, Array],
    default: [Boolean, String, Number, Array],
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
    'item.style': [String, Object],
    'item.class': [String, Array],
    'item.attr': Object,
    'items.style': [String, Object],
    'items.class': [String, Array],
    'items.attr': Object
  },
  data () {
    return {
      id: 'v-' + crypto.getRandomValues(new Uint32Array(1))[0].toString(36),
    }
  },
  computed: {
    '@title' () {
      const template = this.title

      if (template) {
        return template.replace(/\{([\w.]*)}/g, (str, key) => {
          const value = typeof this.$props[key] !== undefined ? this.$props[key] : ''
          return (value === null || value === undefined) ? '' : value.toString().
              replace(/&/g, '&amp;').
              replace(/</g, '&lt;').
              replace(/>/g, '&gt;').
              replace(/"/g, '&quot;').
              replace(/'/g, '&#039;')
        })
      }

      return this.label ?? this.name
    }
  }
}
</script>
