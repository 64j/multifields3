<script>
export default {
  name: 'Element',
  props: {
    name: {
      type: String,
      required: true
    },
    type: String,
    title: [String, Number],
    label: [String, Number],
    value: {
      type: [null, String, Number, Boolean, Array],
      default: undefined
    },
    elements: [String, Object, Array],
    default: [String, Number, Array],
    placeholder: [String, Number],
    required: Boolean,
    readonly: Boolean,
    disabled: Boolean,
    actions: {
      type: [null, Boolean, Array],
      default: ['add', 'move', 'del']
    },
    items: Array,
    attr: Object,
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
  }
}
</script>
