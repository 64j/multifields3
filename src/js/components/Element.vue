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
