// state.js
import store from './store.js'
import { getJWTPayload } from './utils.js'
import { loadComments } from './api.js'

// 登入狀態 Proxy
export const loginState = new Proxy({ isLogin: false }, {
	set(target, key, value) {
		target[key] = value
		if (key === 'isLogin') onLoginChange(value)
		return true
	}
})

function onLoginChange(isLogin) {
	const container = store.container
	container.querySelector('.comment-area .badge-status').innerText = isLogin ? '已登入' : '未登入'
	container.querySelector('.comment-area .warning').classList.toggle('hidden', isLogin)
	container.querySelector('.comment-area .alert').classList.toggle('hidden', isLogin)
	container.querySelector('.comment-area .name-input').classList.toggle('hidden', isLogin)

	const existingAvatar = container.querySelector('.login-avatar')
	if (isLogin) {
		if (existingAvatar) return
		const { username, nickname } = getJWTPayload(store.userToken)
		const loginAvatar = document.createElement('div')
		loginAvatar.classList.add('login-avatar')
		loginAvatar.innerHTML = `
			<div class="comment__author__avatar"><img href="#" /></div>
			<div class="comment__author__name"></div>
			<div class="btn btn-logout">登出</div>
		`
		loginAvatar.querySelector('.comment__author__name').textContent = `${nickname} (${username})`
		const form = container.querySelector('.comment-area form')
		form.insertBefore(loginAvatar, form.querySelector('textarea'))
	} else {
		if (existingAvatar) existingAvatar.classList.add('hidden')
		loadComments(1, true)
	}
}