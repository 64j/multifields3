<script>
import Actions from '../Actions.vue'

export default {
  name: 'mf:textarea',
  __isStatic: true,
  components: { Actions },
  props: ['key', 'type', 'name', 'title', 'value', 'actions', 'rows'],
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
  <div class="mf3-item">
    <actions @action="action" :actions="actions"/>
    <div class="mf3-items">
      <textarea v-model="model" :rows="rows"/>
    </div>
  </div>
</template>
