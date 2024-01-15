<script>
import Select from './Select.vue'

export default {
  name: 'mf:listbox',
  extends: Select,
  props: {
    load: {
      type: Boolean,
      default: true
    }
  }
}
</script>

<template>
  <div class="mf3-item">
    <actions @action="action"/>
    <select v-model="model" @mousedown="getOptions"
            :size="8"
            @focus="() => focus = true"
            @blur="() => focus = false">
      <option v-for="i in options" :value="i.key">{{ i.value }}</option>
    </select>
    <loader v-if="loading" class="absolute left-2 top-2.5"/>
  </div>
</template>

<style scoped>
.mf3-item select {
  @apply w-full block
}
.mf3-item select option {
  @apply checked:bg-blue-600 checked:text-white
}
</style>
