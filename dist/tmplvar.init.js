document.addEventListener('DOMContentLoaded', () => {
  const select = document.querySelector('[name="type"]')
  const option = document.createElement('option')
  option.value = 'custom_tv:multifields3'
  option.innerText = 'multifields3'
  select.children[1].appendChild(option)
})
