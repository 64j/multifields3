import { createApp } from 'vue'
import Mf from './components/Mf.vue'

import('../css/mf.css')

window['mf3Elements'] = {}

Object.entries(import.meta.glob('./components/Elements/*.vue', { eager: true })).forEach(([, { default: module }]) => {
  window['mf3Elements'][module.name || module.__name] = module
})

document.querySelectorAll('.mf3-data').forEach(dataEl => {
  const el = document.createElement('div')

  if (window['mf3Config'][dataEl.name]) {
    createApp(Mf, {
      config: window['mf3Config'][dataEl.name],
      dataEl
    }).mount(el)
  } else {
    el.innerHTML = `Config for "${dataEl.name}" is empty or not found`
  }

  dataEl.after(el)
})
