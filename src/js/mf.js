import axios from 'axios'
import { createApp } from 'vue'
import Mf from './components/Mf.vue'

import('../css/mf.css')

window['mfComponents'] = {}

Object.entries(import.meta.glob('./components/*/*.vue', { eager: true })).forEach(([, { default: module }]) => {
  if (module?.['__isStatic']) {
    window['mfComponents'][module.name || module.__name] = module
  }
})

document.querySelectorAll('.mf3-data').forEach(i => {
  axios.get('./assets/plugins/multifields3/config/' + i.dataset['tvName'] + '.json').then(({ data }) => {
    const el = document.createElement('div')
    createApp(Mf, {
      ...eval(i.dataset || ''),
      el: i,
      config: data
    }).mount(el)
    i.after(el)
  })
})
