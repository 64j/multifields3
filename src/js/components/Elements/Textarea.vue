<script>
import Element from '../Element.vue'
import Actions from '../Actions.vue'

export default {
  name: 'mf:textarea',
  __isStatic: true,
  extends: Element,
  components: { Actions },
  props: {
    rows: {
      type: Number,
      default: 3
    }
  },
  computed: {
    model: {
      set (value) {
        this.$emit('update:value', value)
      },
      get () {
        return this.value
      }
    }
  },
  methods: {
    action (action) {
      this.$emit('action', action)
    }
  }
}
</script>

<template>
  <div class="mf3-item" :class="[`mf3-${element}`, !actions ? 'mf3-item__without-actions' : '']" v-bind="attr">
    <actions @action="action" :actions="actions"/>
    <div class="mf3-items" :class="$props['items.class']" :style="$props['items.style']" v-bind="$props['items.attr']">
      <label v-if="label" :for="id">
        <span>{{ label }}</span>
      </label>
      <textarea
          :id="id"
          :name="id"
          :required="required"
          :readonly="readonly"
          :disabled="disabled"
          :placeholder="placeholder"
          :class="$props['item.class']"
          v-bind="$props['item.attr']"
          v-model="model"
          :rows="rows"/>
    </div>
  </div>
</template>

<style scoped>
.mf3-item label {
  @apply m-0
}
</style>
