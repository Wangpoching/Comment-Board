// utils.js
import store from './store.js'

export function generateCommentAreaErrorMsg(msg) {
  const container = store.container
  let errorMsgEle = container.querySelector('.error-msg')
  if (!errorMsgEle) {
    errorMsgEle = document.createElement('div')
    errorMsgEle.classList.add('error-msg')
    const parentDiv = container.querySelector('.comment-area')
    const siblingDiv = container.querySelector('.alert')
    parentDiv.insertBefore(errorMsgEle, siblingDiv)
  }
  errorMsgEle.innerText = msg
}

export function getJWTPayload(token) {
	return JSON.parse(atob(token.split('.')[1]))
}

export function buildHeaders(includeUser = false) {
  const headers = { 'X-App-Token': `Bearer ${store.appToken}` }
  if (includeUser && store.userToken) headers['X-User-Token'] = `Bearer ${store.userToken}`
  return headers
}

export function isJWTExpired(token) {
  const exp = JSON.parse(atob(token.split('.')[1])).exp
  return Date.now() > exp * 1000
}

export function parseDate(dateString) {
  const d = new Date(dateString)
  const pad = n => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`
}