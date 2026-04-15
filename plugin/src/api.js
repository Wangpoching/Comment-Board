// api.js
import store from './store.js'
import { buildHeaders, generateCommentAreaErrorMsg, isJWTExpired, parseDate } from './utils.js'
import { generateComment, generateAnonymousComment } from './dom.js'

export async function loadComments(page = 1, reload = true) {
  const { BOARD_API, identifier, container } = store
	try {
    const commentsEl = store.container.querySelector('.comments')
		const res = await fetch(`${BOARD_API}/comments.php?identifier=${identifier}&page=${page}`, {
			method: 'GET',
			headers: buildHeaders(true)
		})
		const data = await res.json()
		if (!data.ok) {
			generateCommentAreaErrorMsg('獲取留言失敗')
			return
		}

		if (reload) commentsEl.innerHTML = ''

		for (const comment of data.data) {
			if (comment.username) {
				const el = generateComment(
					comment.nickname, comment.username,
					parseDate(comment.createdAt), comment.content,
					comment.id, comment.identifier
				)
				if (comment.isOwn) el.querySelector('.comment__tools').classList.remove('hidden')
				commentsEl.append(el)
			} else {
				commentsEl.append(
					generateAnonymousComment(comment.authorname, parseDate(comment.createdAt), comment.content)
				)
			}
		}

		const { totalPages, currentPage } = data.pagination
		store.commentsCurrentPage = currentPage
		const loadMoreBtn = container.querySelector('.btn-load-more')
		if (totalPages > page) {
			loadMoreBtn.classList.remove('hidden')
		} else {
			loadMoreBtn.classList.add('hidden')
		}
	} catch (error) {
		generateCommentAreaErrorMsg('獲取留言失敗')
	}
}

export async function ensureAppToken() {
  if (store.appToken && !isJWTExpired(store.appToken)) return true

  try {
    const res = await fetch(store.getAppTokenUrl)
    const data = await res.json()
    if (data.token) {
      store.appToken = data.token
    }
    return true
  } catch (error) {
    return false
  }
}

export async function apiAddComment(identifier, content, authorname) {
  const { BOARD_API } = store
  const params = new URLSearchParams({ identifier, content, authorname })
  try {
    const res = await fetch(`${BOARD_API}/add_comment.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', ...buildHeaders(true) },
      body: params
    })
    const data = await res.json()
    if (!data.ok) {
      generateCommentAreaErrorMsg('新增留言失敗，請稍後再試')
      return false
    }
    return true
  } catch (error) {
    generateCommentAreaErrorMsg('新增留言失敗，請稍後再試')
    return false
  }
}

export async function apiEditComment(identifier, id, content) {
  const { BOARD_API } = store
  const params = new URLSearchParams({ identifier, id, content })
  try {
    const res = await fetch(`${BOARD_API}/edit_comment.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', ...buildHeaders(true) },
      body: params
    })
    const data = await res.json()
    if (!data.ok) {
      generateCommentAreaErrorMsg('修改留言失敗，請稍後再試')
      return false
    }
    return true
  } catch (error) {
    generateCommentAreaErrorMsg('修改留言失敗，請稍後再試')
    return false
  }
}

export async function apiDeleteComment(identifier, id) {
  const { BOARD_API } = store
  const params = new URLSearchParams({ identifier, id })
  try {
    const res = await fetch(`${BOARD_API}/delete_comment.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', ...buildHeaders(true) },
      body: params
    })
    const data = await res.json()
    if (!data.ok) {
      generateCommentAreaErrorMsg('刪除留言失敗，請稍後再試')
      return false
    }
    return true
  } catch (error) {
    generateCommentAreaErrorMsg('刪除留言失敗，請稍後再試')
    return false
  }
}