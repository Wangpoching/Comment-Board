// index.js
import {
  ensureAppToken,
  loadComments,
  apiAddComment,
  apiEditComment,
  apiDeleteComment,
} from './api.js'

import { loginState } from './state.js'
import { isJWTExpired } from './utils.js'
import { commentsAreaTemplate } from './template.js'
import store from './store.js'


async function init({
	container, // 留言板要注入的容器 id
	identifier,
	getAppTokenUrl, // 提供取得 appToken 的 API 讓 plugin 可以自己管理 appToken 的生命週期
	loginParams, // 留言板使用者登入的網址專案想要另外提供的參數(物件)
	appKey,
	userToken // userToken 由引入的專案提供
}) {
	const loginQueryString = new URLSearchParams({
		...loginParams,
		appKey,
	}).toString()
	const loginUrl = `http://localhost/board/login.php?${loginQueryString}`
	const BOARD_API = 'http://localhost/board/api'

  store.container = container
  store.userToken = userToken
  store.identifier = identifier
  store.commentsCurrentPage = 1
  store.getAppTokenUrl = getAppTokenUrl
  store.BOARD_API = BOARD_API
  store.appToken = null

	// 在 Container 寫入 commtents area
	const commentArea = document.createElement('div')
	commentArea.innerHTML = commentsAreaTemplate
	commentArea.querySelector('.alert a').setAttribute('href', loginUrl)
	container.replaceChildren(commentArea)
	const commentsEl = container.querySelector('.comments')


	// 取得 appToken
	const renewAppToken = await ensureAppToken()
	if (!renewAppToken) {
		throw new Error('載入留言板失敗')
	}

	// 檢查登入狀態
	if (userToken && !isJWTExpired(userToken)) loginState.isLogin = true

	// 載入留言
	await loadComments(1, true)

  // --- Event listeners ---
  container.querySelector('.btn-send-comment').addEventListener('click', async (e) => {
    e.preventDefault()
    const authorname = container.querySelector('.name-input input')?.value ?? ''
    const textarea = container.querySelector('.comment-area textarea')
    const content = textarea.value
    if (!content) return

    const success = await apiAddComment(identifier, content, authorname)
    if (success) {
      loadComments(1, true)
      textarea.value = ''
    }
  })

  container.querySelector('.comment-area').addEventListener('click', async (e) => {
    if (e.target.closest('.btn-edit-comment')) {
      e.preventDefault()
      const commentEl = e.target.closest('.comment')
      commentEl.querySelector('.comment__body').classList.add('hidden')
      const editEl = commentEl.querySelector('.comment__body__edit')
      editEl.classList.remove('hidden')
      const inputEl = editEl.querySelector('input')
      inputEl.value = commentEl.querySelector('.comment__content').innerText
      inputEl.focus()
    }

    if (e.target.closest('.btn-cancel-edit-comment')) {
      e.preventDefault()
      const commentEl = e.target.closest('.comment')
      commentEl.querySelector('.comment__body').classList.remove('hidden')
      commentEl.querySelector('.comment__body__edit').classList.add('hidden')
    }

    if (e.target.closest('.btn-send-edit-comment')) {
      e.preventDefault()
      const commentEl = e.target.closest('.comment')
      const success = await apiEditComment(commentEl.dataset.identifier, commentEl.dataset.id, commentEl.querySelector('.input-content').value)
      if (success) loadComments(1, true)
    }

    if (e.target.closest('.btn-delete-comment')) {
      e.preventDefault()
      const commentEl = e.target.closest('.comment')
      const success = await apiDeleteComment(commentEl.dataset.identifier, commentEl.dataset.id)
      if (success) loadComments(1, true)
    }

		if (e.target.closest('.btn-logout')) {
		  store.userToken = null
      userToken = null
		  loginState.isLogin = false
		}
  })

  container.querySelector('.btn-load-more').addEventListener('click', e => {
    if (!e.target.classList.contains('hidden')) {
      loadComments(store.commentsCurrentPage + 1, false)
    }
  })
}

export { init }