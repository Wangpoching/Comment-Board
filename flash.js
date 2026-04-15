setTimeout(() => {
  document.querySelectorAll('.flash-msg, .error-msg, .success-msg').forEach(el => {
    el.style.transition = 'opacity 0.5s'
    el.style.opacity = '0'
    setTimeout(() => el.classList.add('hidden'), 500)
  })
}, 2500)
