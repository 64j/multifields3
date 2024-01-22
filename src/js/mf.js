import { createApp } from 'vue'
import Mf from './components/Mf.vue'

import('../css/mf.css')

window['mf3Elements'] = {}

Object.entries(import.meta.glob('./components/Elements/*.vue', { eager: true })).forEach(([, { default: module }]) => {
  window['mf3Elements'][module.name || module.__name] = module
})

document.querySelectorAll('.mf3-data').forEach(dataEl => {
  const el = document.createElement('div')

  createApp(Mf, {
    dataEl,
    tvId: dataEl.dataset['tvId'],
    tvName: dataEl.dataset['tvName']
  }).mount(el)
  dataEl.after(el)
})
