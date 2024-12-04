<script>
import Element from '../Element.vue'
import Actions from '../Actions.vue'
import Loader from '../Loader.vue'
import { h } from 'vue'

export default {
  name: 'mf:table',
  __isStatic: true,
  extends: Element,
  components: { Loader, Actions },
  props: {
    columns: Array,
    toolbar: Array,
    pagination: Boolean,
    limit: Number,
    template: Object
  },
  data () {
    return {}
  },
  computed: {
    propToolbar () {
      const items = []

      for (const item of this.toolbar) {
        switch (item) {
          case 'new':
            items.push({
              label: 'New',
              icon: 'fa fa-plus text-success',
              click: this.toolbarNew
            })
            break
          case 'import':
            items.push({
              label: 'Import',
              icon: 'fa fa-download',
              click: this.toolbarImport
            })
            break
          case 'export':
            items.push({
              label: 'Export',
              icon: 'fa fa-upload',
              click: this.toolbarExport
            })
            break
          case 'clear':
            items.push({
              label: 'Clear',
              icon: 'fa fa-trash text-danger',
              click: this.toolbarClear
            })
            break
        }
      }

      return items
    }
  },
  created () {

  },
  methods: {
    action (action) {
      this.$emit('action', action)
    },
    toolbarNew (event) {
      this.$root['modal'] = {
        component: () => h(
            this.$root.getElements(this.template.items),
            {
             updateValue () {
                console.log(1)
              }
            }
        ),
        // component: h('div', {
        //   onVnodeUpdated () {
        //     console.log(arguments)
        //   }
        // }, [this.$root.getElements(this.template.items, this)]),
        open: true
      }
    },
    toolbarExport () {

    },
    toolbarImport () {

    },
    toolbarClear () {

    }
  }
}
</script>

<template>
  <div class="mf3-panel mf3-item" :class="className" v-bind="attrs">
    <actions @action="action" :actions="actions"/>

    <div v-if="label">
      <span>{{ label }}</span>
      <i v-if="help" data-tooltip :title="help" class="fa far fa-question-circle"/>
    </div>

    <div v-if="propToolbar">
      <button v-for="i in propToolbar" class="btn btn-sm mr-1" type="button" @click="i.click">
        <i v-if="i.icon" :class="i.icon"/>
        {{ i.label }}
      </button>
    </div>

    <div class="mf3-items" v-bind="itemsAttrs">
      <table>
        <thead>
        <tr v-if="columns">
          <th v-for="i in columns">
            {{ i['title'] }}
          </th>
        </tr>
        <tr v-else-if="items?.[0]">
          <th v-for="i in items[0]">
            {{ i }}
          </th>
        </tr>
        </thead>
      </table>
    </div>

    <div ref="modal" class="">
      <slot/>
    </div>
  </div>
</template>

<style scoped>

</style>
