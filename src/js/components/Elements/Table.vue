<script>
import Element from '../Element.vue'
import Actions from '../Actions.vue'
import Loader from '../Loader.vue'

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
    return {
      data: this.$attrs['data'] ?? [],
      templateItems: null
    }
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
  methods: {
    action (action) {
      this.$emit('action', action)
    },
    toolbarNew (event) {
      this.modalOpen()
    },
    toolbarExport () {

    },
    toolbarImport () {

    },
    toolbarClear () {

    },
    modalOpen (data = {}, k) {
      this.templateItems = JSON.parse(JSON.stringify(this.template.items)).map(i => {
        if (data[i.name] !== undefined) {
          i.value = data[i.name]
        }

        return i
      })

      this.$root['modal'] = {
        component: this.$root.getElements(this.templateItems),
        open: true,
        onConfirm: () => {
          if (!this.data) {
            this.data = []
          }

          for (const i of this.templateItems) {
            data[i.name] = i.value
          }

          if (k === undefined) {
            this.data.push(data)
          }

          this.$emit('update:props', 'data', this.data)
        }
      }
    }
  }
}
</script>

<template>
  <div class="mf3-table mf3-item" :class="className" v-bind="attrs">
    <actions @action="action" :actions="actions"/>

    <div v-if="label">
      <span>{{ label }}</span>
      <i v-if="help" data-tooltip :title="help" class="fa far fa-question-circle"/>
    </div>

    <div v-if="propToolbar" class="py-1">
      <button v-for="i in propToolbar" class="btn btn-sm mr-1" type="button" @click="i.click">
        <i v-if="i.icon" :class="i.icon"/>
        {{ i.label }}
      </button>
    </div>

    <div class="mf3-items table-responsive" v-bind="itemsAttrs">
      <table class="table data nowrap" style="table-layout: fixed;">
        <thead>
        <tr v-if="columns">
          <th style="width: 3rem">#</th>
          <th v-for="i in columns">
            {{ i['title'] }}
          </th>
        </tr>
        <tr v-else-if="items?.[0]">
          <th style="width: 3rem">#</th>
          <th v-for="i in items[0]">
            {{ i }}
          </th>
        </tr>
        </thead>
        <tbody v-if="data?.length">
        <tr v-for="(i, k) in data" @click="modalOpen(i, k)">
          <template v-if="columns">
            <th>{{ k + 1 }}</th>
            <td v-for="j in columns">
              <div class="text-truncate">
                {{ i[j['key']] }}
              </div>
            </td>
          </template>
          <template v-else>
            <th>{{ k + 1 }}</th>
            <td v-for="j in i">
              <div class="text-truncate">{{ j }}</div>
            </td>
          </template>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>

</style>
