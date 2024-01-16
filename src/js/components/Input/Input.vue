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
    }
  }
}
</script>

<template>
  <div class="mf3-item">
    <actions @action="action"/>
    <template v-if="data">
      <div v-for="i in data">
        <label>
          <input v-model="model"
                 :type="type"
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
          {{ i.value }}
        </label>
      </div>
    </template>
    <div v-else>
      <label>
        <input v-model="model"
               :type="type"
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
               :false-value="falseValue">
        {{ label }}
      </label>
    </div>
  </div>
</template>

<style scoped>
.mf3-item label {
  @apply m-0 inline-flex items-center
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
  @apply mr-2 !w-3.5 !h-3.5
}
</style>
