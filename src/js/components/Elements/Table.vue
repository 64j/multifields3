<script>
import Element from '../Element.vue'
import Actions from '../Actions.vue'
import Loader from '../Loader.vue'
import draggable from 'vuedraggable'

export default {
  name: 'mf:table',
  __isStatic: true,
  extends: Element,
  components: { Loader, Actions, draggable },
  props: {
    columns: Array,
    toolbar: Array,
    pagination: Boolean,
    limit: Number,
    template: Object
  },
  data () {
    return {
      templateItems: null,
      data: this.$attrs['data'] ?? []
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
    toolbarNew () {
      this.modalOpen()
    },
    toolbarExport () {

    },
    toolbarImport () {

    },
    toolbarClear () {
      this.updateData([])
    },
    modalOpen (data = {}, k) {
      this.templateItems = JSON.parse(JSON.stringify(this.template.items)).map(i => {
        i.actions = false

        if (data[i.name] !== undefined) {
          i.value = data[i.name]
        }

        return i
      })

      this.$root['modal'] = {
        component: this.$root.getElements(this.templateItems),
        open: true,
        btnConfirm: true,
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

          this.updateData()
        }
      }
    },
    updateData (data) {
      if (data) {
        this.data = data
      }

      this.$emit('update:props', 'data', this.data)
    },
    onRemove (i) {
      this.data.splice(i, 1)
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
      <table class="table table-sm data nowrap" style="table-layout: fixed;">
        <thead v-if="columns">
        <tr>
          <th style="width: 4rem"></th>
          <th style="width: 3rem">#</th>
          <th v-for="i in columns" :style="{ ...(i['style'] ?? {}) }">
            {{ i['title'] }}
          </th>
        </tr>
        </thead>
        <draggable
            v-model="data"
            group="people"
            @end="updateData()"
            item-key="id"
            handle=".fa-bars"
            tag="tbody">
          <template #item="{element, index}">
            <tr @click="modalOpen(element, index)">
              <th>
                <i class="fa fa-bars mx-1"/>
                <i class="fa fa-trash mx-1 text-danger" @click.stop="onRemove(index)"/>
              </th>
              <th>{{ index + 1 }}</th>
              <template v-if="columns">
                <td v-for="i in columns">
                  <div class="text-truncate">{{ element[i.key] }}</div>
                </td>
              </template>
              <template v-else>
                <td v-for="j in element">
                  <div class="text-truncate">{{ j }}</div>
                </td>
              </template>
            </tr>
          </template>
        </draggable>
      </table>
    </div>
  </div>
</template>

<style scoped>

</style>
