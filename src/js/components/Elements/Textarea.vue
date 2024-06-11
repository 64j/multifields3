<script>
import Element from '../Element.vue'
import Actions from '../Actions.vue'
import Loader from '../Loader.vue'
import axios from 'axios'

export default {
  name: 'mf:textarea',
  __isStatic: true,
  extends: Element,
  components: { Loader, Actions },
  props: {
    rows: {
      type: Number,
      default: 3
    }
  },
  data () {
    return {
      data: this.elements ? [] : null,
      loading: false
    }
  },
  watch: {
    data: {
      handler (data) {
        this.model = [...data.map(i => i.value)]
      },
      deep: true
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

      this.elements.split('||').forEach(i => {
        let key = i.split('==')[0] ?? null
        let value = i.split('==')[1] ?? null

        this.data.push({
          key: key,
          value: value ?? key
        })
      })
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
  <div class="mf3-item" :class="[`mf3-${element}__${type}`, !actions ? 'mf3-item__without-actions' : '']"
       v-bind="attr">
    <actions @action="action" :actions="actions"/>

    <div class="mf3-items" :class="$props['items.class']" :style="$props['items.style']" v-bind="$props['items.attr']">
      <template v-if="data">
        <div v-if="label">
          <span>{{ label }}</span>
        </div>
        <loader v-if="loading" class="mf3-loader"/>
        <div v-for="(i, k) in data">
            <textarea
                :id="id + '-' + k"
                :name="id + '-' + k"
                :required="required"
                :readonly="readonly"
                :disabled="disabled"
                :placeholder="placeholder"
                :class="$props['item.class']"
                v-bind="$props['item.attr']"
                v-model="i.value"
                :rows="rows"/>

          <label v-if="i.key" :for="id + '-' + k">
            <span>{{ i.key }}</span>
            <i v-if="i.help" data-tooltip :title="i.help" class="fa far fa-question-circle"/>
          </label>
        </div>
      </template>
      <template v-else>
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

        <label v-if="label" :for="id">
          <span>{{ label }}</span>
          <i v-if="help" data-tooltip :title="help" class="fa far fa-question-circle"/>
        </label>
      </template>
    </div>
  </div>
</template>

<style scoped>
.mf3-item label {
  @apply order-1 m-0
}
.mf3-item textarea {
  @apply order-2
}
.mf3-item > .mf3-items > div label {
  @apply order-1 mr-2 w-40
}
.mf3-item > .mf3-items > div textarea {
  @apply order-2 w-full border
}
.mf3-item > .mf3-items > div {
  @apply relative order-4 flex items-center mb-1 last-of-type:mb-0 w-full
}
</style>
