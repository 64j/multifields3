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
  methods: {
    action (action) {
      this.$emit('action', action)
    }
  }
}
</script>

<template>
  <div class="mf3-item" :class="className" v-bind="attrs">
    <actions @action="action" :actions="actions"/>

    <div class="mf3-items" v-bind="itemsAttrs">
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
                v-bind="itemAttrs"
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
            v-bind="itemAttrs"
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
