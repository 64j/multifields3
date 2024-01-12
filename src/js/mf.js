import axios from 'axios'
import { createApp } from 'vue'
import Mf from './components/Mf.vue'

import('../css/mf.css')

window['mf3Components'] = {}

Object.entries(import.meta.glob('./components/*/*.vue', { eager: true })).forEach(([, { default: module }]) => {
  if (module?.['__isStatic'] || module?.extends?.['__isStatic']) {
    window['mf3Components'][module.name || module.__name] = module
  }
})

document.querySelectorAll('.mf3-data').forEach(async i => {
  let response = {}
  try {
    response = await axios.get('./assets/plugins/multifields3/config/' + i.dataset['tvName'] + '.json')
  } catch (error) {}

  const el = document.createElement('div')
  createApp(Mf, {
    el: i,
    ...eval(i.dataset || ''),
    settings: response?.data?.['settings'],
    templates: response?.data?.['templates'],
  }).mount(el)
  i.after(el)
})
