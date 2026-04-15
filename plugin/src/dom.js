/* dom.js */
import { commentTemplate, anonymousCommentTemplate } from './template.js'

export function generateComment(nickname, username, date, content, id, identifier) {
  const el = document.createElement('div')
  el.classList.add('comment')
  el.dataset.id = id
  el.dataset.identifier = identifier
  el.innerHTML = commentTemplate
  el.querySelector('.comment__author__name').textContent = `${nickname} (${username})`
  el.querySelector('.comment__date').textContent = date
  el.querySelector('.comment__content').textContent = content
  return el
}

export function generateAnonymousComment(authorname, date, content) {
  const el = document.createElement('div')
  el.classList.add('comment')
  el.innerHTML = anonymousCommentTemplate
  el.querySelector('.comment__author__name').textContent = authorname
  el.querySelector('.comment__date').textContent = date
  el.querySelector('.comment__content').textContent = content
  return el
}