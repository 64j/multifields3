import { createApp } from 'vue'
import Mf from './components/Mf.vue'

import('../css/mf.css')

window['mf3Components'] = {}

Object.entries(import.meta.glob('./components/*/*.vue', { eager: true })).forEach(([, { default: module }]) => {
  if (module?.['__isStatic'] || module?.extends?.['__isStatic']) {
    window['mf3Components'][module.name || module.__name] = module
  }
})

document.querySelectorAll('.mf3-data').forEach(dataEl => {
  const el = document.createElement('div')

  createApp(Mf, {
    dataEl,
    data: mf3Config[dataEl.dataset['tvName']] ?? {},
    ...eval(dataEl.dataset || '')
  }).mount(el)
  dataEl.after(el)
})
