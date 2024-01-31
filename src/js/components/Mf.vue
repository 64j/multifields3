<script setup>
import { getCurrentInstance, h, shallowReactive, watch } from 'vue'
import draggable from 'vuedraggable'
import Templates from './Templates.vue'

const $props = defineProps(['dataEl', 'config'])
const instance = getCurrentInstance()
//const elements = reactive(setElements())
const elements = shallowReactive([])

watch(
    () => elements,
    (elements) => {
      $props.dataEl.innerHTML = elements.length ? JSON.stringify(setData(elements)) : ''
    },
    { deep: true }
)

function setElements () {
  let value

  try {
    value = Object.values(JSON.parse($props.dataEl.value))
  } catch (errors) {
    value = []
  }

  return setElementFromTemplates(value, $props.config.templates)
}

function setElementFromTemplates (elements, templates) {
  elements.forEach(element => {
    let template = templates.find(i => i.key === element.key) ??
        $props.config.templates.find(i => i.key === element.key) ?? null

    if (template) {
      template = { ...template }

      const items = template.items

      if (items) {
        element.items = setElementFromTemplates(element.items || [], items)
        delete template.items
      }

      if (template.value !== undefined) {
        if (template.value === false) {
          delete element.value
        } else {
          delete template.value
        }
      }

      Object.assign(element, template)
    }
  })

  return elements
}

function getElements (elements, element) {
  return h(
      draggable,
      {
        tag: 'div',
        list: elements,
        itemKey: a => a,
        class: ['mf3-items', element?.['itemsClass']],
        style: element?.['itemsStyle'],
        handle: '.mf3-actions__move',
        ghostClass: 'mf3-draggable__active',
        chosenClass: 'mf3-draggable__chosen'
      },
      {
        item: ({ element, index }) => getElement(element, index, elements)
      }
  )
}

function getElement (element, index, elements) {
  const name = element.name ? 'mf:' + element.name : null
  let items

  if (element?.items) {
    items = () => getElements(element.items, element)
  }

  return mf3Elements[name] && h(
      mf3Elements[name],
      {
        ...element,
        'onAction': (a, ...args) => action(a, elements, index, ...args),
        'onUpdate:value': (...args) => updateValue(element, elements, ...args),
        'onSelect:template': (...args) => selectTemplate(element, ...args)
        //'onClick': select
      },
      items
  )
}

function action (action, data, index, values) {
  index ??= data.length

  switch (action) {
    case 'del':
      data.splice(index, 1)
      break

    case 'add':
      let element = {}
      if (data[index]['key'] && $props.config.templates[data[index]['key']]) {
        element = { ...$props.config.templates[data[index]['key']] }
        element.key = data[index]['key']
      } else {
        element = clearValue({ ...data[index] })
      }

      data.splice(index + 1, 0, element)

      if (values) {
        Object.assign(data[index + 1], values)
      }
      break
  }
}

function updateValue (element, elements, value, values, keys) {
  element.value = value
  //element.values = values

  if (keys !== undefined) {
    keys = typeof keys !== 'object' ? [keys] : keys

    elements.forEach(i => {
      if (keys.includes(i.key)) {
        i.value = value
      }
    })
  }
}

function select (event) {
  instance.vnode.el.querySelectorAll('.mf3-item.active').forEach(i => i.classList.remove('active'))
  event.currentTarget.classList.add('active')
  event.stopPropagation()
}

function clearValue (data) {
  if (data.value !== undefined) {
    data.value = data.value === false ? data.value : ''
  }

  if (data.items) {
    data.items = data.items.map(i => clearValue({ ...i }))
  }

  return data
}

function selectTemplate (element, template, key) {
  if (key !== undefined) {
    if (!element.items) {
      element.items = []
    }

    element.items.push({ ...template })
  } else {
    element.key ??= template
    elements.push({ ...element })
  }
}

function setData (elements) {
  return elements

  elements = Object.assign([], elements)

  for (let j in elements) {
    const element = Object.assign({}, elements[j])

    for (const i in element) {
      if (![
        'id',
        'key',
        'type',
        'name',
        'value',
        'values',
        'default',
        'items'
      ].includes(i)) {
        delete element[i]
      }
    }

    if (element?.items) {
      element.items = setData(element.items)
    }

    elements[j] = element
  }

  return elements
}
</script>

<template>
  <div class="mf3 mf3-group">
    <templates class="mf3-templates" :data="true" @select:template="selectTemplate"/>
    <component :is="() => getElements(elements)"/>
  </div>
</template>

<style scoped>
@tailwind base;
.mf3 {
  @apply relative flex flex-wrap mb-6 p-0 m-0 border-none
}
.mf3::before {
  @apply hidden
}
.mf3 > .mf3-templates {
  @apply -bottom-3
}
</style>
