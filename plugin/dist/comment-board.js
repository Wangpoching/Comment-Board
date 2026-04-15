/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["CommentBoard"] = factory();
	else
		root["CommentBoard"] = factory();
})(this, () => {
return /******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/api.js"
/*!********************!*\
  !*** ./src/api.js ***!
  \********************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   apiAddComment: () => (/* binding */ apiAddComment),\n/* harmony export */   apiDeleteComment: () => (/* binding */ apiDeleteComment),\n/* harmony export */   apiEditComment: () => (/* binding */ apiEditComment),\n/* harmony export */   ensureAppToken: () => (/* binding */ ensureAppToken),\n/* harmony export */   loadComments: () => (/* binding */ loadComments)\n/* harmony export */ });\n/* harmony import */ var _store_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./store.js */ \"./src/store.js\");\n/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils.js */ \"./src/utils.js\");\n/* harmony import */ var _dom_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./dom.js */ \"./src/dom.js\");\n// api.js\r\n\r\n\r\n\r\n\r\nasync function loadComments(page = 1, reload = true) {\r\n  const { BOARD_API, identifier, container } = _store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\r\n\ttry {\r\n    const commentsEl = _store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"].container.querySelector('.comments')\r\n\t\tconst res = await fetch(`${BOARD_API}/comments.php?identifier=${identifier}&page=${page}`, {\r\n\t\t\tmethod: 'GET',\r\n\t\t\theaders: (0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.buildHeaders)(true)\r\n\t\t})\r\n\t\tconst data = await res.json()\r\n\t\tif (!data.ok) {\r\n\t\t\t(0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.generateCommentAreaErrorMsg)('獲取留言失敗')\r\n\t\t\treturn\r\n\t\t}\r\n\r\n\t\tif (reload) commentsEl.innerHTML = ''\r\n\r\n\t\tfor (const comment of data.data) {\r\n\t\t\tif (comment.username) {\r\n\t\t\t\tconst el = (0,_dom_js__WEBPACK_IMPORTED_MODULE_2__.generateComment)(\r\n\t\t\t\t\tcomment.nickname, comment.username,\r\n\t\t\t\t\t(0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.parseDate)(comment.createdAt), comment.content,\r\n\t\t\t\t\tcomment.id, comment.identifier\r\n\t\t\t\t)\r\n\t\t\t\tif (comment.isOwn) el.querySelector('.comment__tools').classList.remove('hidden')\r\n\t\t\t\tcommentsEl.append(el)\r\n\t\t\t} else {\r\n\t\t\t\tcommentsEl.append(\r\n\t\t\t\t\t(0,_dom_js__WEBPACK_IMPORTED_MODULE_2__.generateAnonymousComment)(comment.authorname, (0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.parseDate)(comment.createdAt), comment.content)\r\n\t\t\t\t)\r\n\t\t\t}\r\n\t\t}\r\n\r\n\t\tconst { totalPages, currentPage } = data.pagination\r\n\t\t_store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"].commentsCurrentPage = currentPage\r\n\t\tconst loadMoreBtn = container.querySelector('.btn-load-more')\r\n\t\tif (totalPages > page) {\r\n\t\t\tloadMoreBtn.classList.remove('hidden')\r\n\t\t} else {\r\n\t\t\tloadMoreBtn.classList.add('hidden')\r\n\t\t}\r\n\t} catch (error) {\r\n\t\t(0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.generateCommentAreaErrorMsg)('獲取留言失敗')\r\n\t}\r\n}\r\n\r\nasync function ensureAppToken() {\r\n  if (_store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"].appToken && !(0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.isJWTExpired)(_store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"].appToken)) return true\r\n\r\n  try {\r\n    const res = await fetch(_store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"].getAppTokenUrl)\r\n    const data = await res.json()\r\n    if (data.token) {\r\n      _store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"].appToken = data.token\r\n    }\r\n    return true\r\n  } catch (error) {\r\n    return false\r\n  }\r\n}\r\n\r\nasync function apiAddComment(identifier, content, authorname) {\r\n  const { BOARD_API } = _store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\r\n  const params = new URLSearchParams({ identifier, content, authorname })\r\n  try {\r\n    const res = await fetch(`${BOARD_API}/add_comment.php`, {\r\n      method: 'POST',\r\n      headers: { 'Content-Type': 'application/x-www-form-urlencoded', ...(0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.buildHeaders)(true) },\r\n      body: params\r\n    })\r\n    const data = await res.json()\r\n    if (!data.ok) {\r\n      (0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.generateCommentAreaErrorMsg)('新增留言失敗，請稍後再試')\r\n      return false\r\n    }\r\n    return true\r\n  } catch (error) {\r\n    (0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.generateCommentAreaErrorMsg)('新增留言失敗，請稍後再試')\r\n    return false\r\n  }\r\n}\r\n\r\nasync function apiEditComment(identifier, id, content) {\r\n  const { BOARD_API } = _store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\r\n  const params = new URLSearchParams({ identifier, id, content })\r\n  try {\r\n    const res = await fetch(`${BOARD_API}/edit_comment.php`, {\r\n      method: 'POST',\r\n      headers: { 'Content-Type': 'application/x-www-form-urlencoded', ...(0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.buildHeaders)(true) },\r\n      body: params\r\n    })\r\n    const data = await res.json()\r\n    if (!data.ok) {\r\n      (0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.generateCommentAreaErrorMsg)('修改留言失敗，請稍後再試')\r\n      return false\r\n    }\r\n    return true\r\n  } catch (error) {\r\n    (0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.generateCommentAreaErrorMsg)('修改留言失敗，請稍後再試')\r\n    return false\r\n  }\r\n}\r\n\r\nasync function apiDeleteComment(identifier, id) {\r\n  const { BOARD_API } = _store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\r\n  const params = new URLSearchParams({ identifier, id })\r\n  try {\r\n    const res = await fetch(`${BOARD_API}/delete_comment.php`, {\r\n      method: 'POST',\r\n      headers: { 'Content-Type': 'application/x-www-form-urlencoded', ...(0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.buildHeaders)(true) },\r\n      body: params\r\n    })\r\n    const data = await res.json()\r\n    if (!data.ok) {\r\n      (0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.generateCommentAreaErrorMsg)('刪除留言失敗，請稍後再試')\r\n      return false\r\n    }\r\n    return true\r\n  } catch (error) {\r\n    (0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.generateCommentAreaErrorMsg)('刪除留言失敗，請稍後再試')\r\n    return false\r\n  }\r\n}\n\n//# sourceURL=webpack://CommentBoard/./src/api.js?\n}");

/***/ },

/***/ "./src/dom.js"
/*!********************!*\
  !*** ./src/dom.js ***!
  \********************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   generateAnonymousComment: () => (/* binding */ generateAnonymousComment),\n/* harmony export */   generateComment: () => (/* binding */ generateComment)\n/* harmony export */ });\n/* harmony import */ var _template_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./template.js */ \"./src/template.js\");\n/* dom.js */\r\n\r\n\r\nfunction generateComment(nickname, username, date, content, id, identifier) {\r\n  const el = document.createElement('div')\r\n  el.classList.add('comment')\r\n  el.dataset.id = id\r\n  el.dataset.identifier = identifier\r\n  el.innerHTML = _template_js__WEBPACK_IMPORTED_MODULE_0__.commentTemplate\r\n  el.querySelector('.comment__author__name').textContent = `${nickname} (${username})`\r\n  el.querySelector('.comment__date').textContent = date\r\n  el.querySelector('.comment__content').textContent = content\r\n  return el\r\n}\r\n\r\nfunction generateAnonymousComment(authorname, date, content) {\r\n  const el = document.createElement('div')\r\n  el.classList.add('comment')\r\n  el.innerHTML = _template_js__WEBPACK_IMPORTED_MODULE_0__.anonymousCommentTemplate\r\n  el.querySelector('.comment__author__name').textContent = authorname\r\n  el.querySelector('.comment__date').textContent = date\r\n  el.querySelector('.comment__content').textContent = content\r\n  return el\r\n}\n\n//# sourceURL=webpack://CommentBoard/./src/dom.js?\n}");

/***/ },

/***/ "./src/index.js"
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   init: () => (/* binding */ init)\n/* harmony export */ });\n/* harmony import */ var _api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./api.js */ \"./src/api.js\");\n/* harmony import */ var _state_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./state.js */ \"./src/state.js\");\n/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils.js */ \"./src/utils.js\");\n/* harmony import */ var _template_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./template.js */ \"./src/template.js\");\n/* harmony import */ var _store_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./store.js */ \"./src/store.js\");\n// index.js\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nasync function init({\r\n\tcontainer, // 留言板要注入的容器 id\r\n\tidentifier,\r\n\tgetAppTokenUrl, // 提供取得 appToken 的 API 讓 plugin 可以自己管理 appToken 的生命週期\r\n\tloginParams, // 留言板使用者登入的網址專案想要另外提供的參數(物件)\r\n\tappKey,\r\n\tuserToken // userToken 由引入的專案提供\r\n}) {\r\n\tconst loginQueryString = new URLSearchParams({\r\n\t\t...loginParams,\r\n\t\tappKey,\r\n\t}).toString()\r\n\tconst loginUrl = `${\"http://localhost/board\"}/login.php?${loginQueryString}`\r\n\tconst BOARD_API = `${\"http://localhost/board\"}/api`\r\n\r\n  _store_js__WEBPACK_IMPORTED_MODULE_4__[\"default\"].container = container\r\n  _store_js__WEBPACK_IMPORTED_MODULE_4__[\"default\"].userToken = userToken\r\n  _store_js__WEBPACK_IMPORTED_MODULE_4__[\"default\"].identifier = identifier\r\n  _store_js__WEBPACK_IMPORTED_MODULE_4__[\"default\"].commentsCurrentPage = 1\r\n  _store_js__WEBPACK_IMPORTED_MODULE_4__[\"default\"].getAppTokenUrl = getAppTokenUrl\r\n  _store_js__WEBPACK_IMPORTED_MODULE_4__[\"default\"].BOARD_API = BOARD_API\r\n  _store_js__WEBPACK_IMPORTED_MODULE_4__[\"default\"].appToken = null\r\n\r\n\t// 在 Container 寫入 commtents area\r\n\tconst commentArea = document.createElement('div')\r\n\tcommentArea.innerHTML = _template_js__WEBPACK_IMPORTED_MODULE_3__.commentsAreaTemplate\r\n\tcommentArea.querySelector('.alert a').setAttribute('href', loginUrl)\r\n\tcontainer.replaceChildren(commentArea)\r\n\tconst commentsEl = container.querySelector('.comments')\r\n\r\n\r\n\t// 取得 appToken\r\n\tconst renewAppToken = await (0,_api_js__WEBPACK_IMPORTED_MODULE_0__.ensureAppToken)()\r\n\tif (!renewAppToken) {\r\n\t\tthrow new Error('載入留言板失敗')\r\n\t}\r\n\r\n\t// 檢查登入狀態\r\n\tif (userToken && !(0,_utils_js__WEBPACK_IMPORTED_MODULE_2__.isJWTExpired)(userToken)) _state_js__WEBPACK_IMPORTED_MODULE_1__.loginState.isLogin = true\r\n\r\n\t// 載入留言\r\n\tawait (0,_api_js__WEBPACK_IMPORTED_MODULE_0__.loadComments)(1, true)\r\n\r\n  // --- Event listeners ---\r\n  container.querySelector('.btn-send-comment').addEventListener('click', async (e) => {\r\n    e.preventDefault()\r\n    const authorname = container.querySelector('.name-input input')?.value ?? ''\r\n    const textarea = container.querySelector('.comment-area textarea')\r\n    const content = textarea.value\r\n    if (!content) return\r\n\r\n    const success = await (0,_api_js__WEBPACK_IMPORTED_MODULE_0__.apiAddComment)(identifier, content, authorname)\r\n    if (success) {\r\n      (0,_api_js__WEBPACK_IMPORTED_MODULE_0__.loadComments)(1, true)\r\n      textarea.value = ''\r\n    }\r\n  })\r\n\r\n  container.querySelector('.comment-area').addEventListener('click', async (e) => {\r\n    if (e.target.closest('.btn-edit-comment')) {\r\n      e.preventDefault()\r\n      const commentEl = e.target.closest('.comment')\r\n      commentEl.querySelector('.comment__body').classList.add('hidden')\r\n      const editEl = commentEl.querySelector('.comment__body__edit')\r\n      editEl.classList.remove('hidden')\r\n      const inputEl = editEl.querySelector('input')\r\n      inputEl.value = commentEl.querySelector('.comment__content').innerText\r\n      inputEl.focus()\r\n    }\r\n\r\n    if (e.target.closest('.btn-cancel-edit-comment')) {\r\n      e.preventDefault()\r\n      const commentEl = e.target.closest('.comment')\r\n      commentEl.querySelector('.comment__body').classList.remove('hidden')\r\n      commentEl.querySelector('.comment__body__edit').classList.add('hidden')\r\n    }\r\n\r\n    if (e.target.closest('.btn-send-edit-comment')) {\r\n      e.preventDefault()\r\n      const commentEl = e.target.closest('.comment')\r\n      const success = await (0,_api_js__WEBPACK_IMPORTED_MODULE_0__.apiEditComment)(commentEl.dataset.identifier, commentEl.dataset.id, commentEl.querySelector('.input-content').value)\r\n      if (success) (0,_api_js__WEBPACK_IMPORTED_MODULE_0__.loadComments)(1, true)\r\n    }\r\n\r\n    if (e.target.closest('.btn-delete-comment')) {\r\n      e.preventDefault()\r\n      const commentEl = e.target.closest('.comment')\r\n      const success = await (0,_api_js__WEBPACK_IMPORTED_MODULE_0__.apiDeleteComment)(commentEl.dataset.identifier, commentEl.dataset.id)\r\n      if (success) (0,_api_js__WEBPACK_IMPORTED_MODULE_0__.loadComments)(1, true)\r\n    }\r\n\r\n\t\tif (e.target.closest('.btn-logout')) {\r\n\t\t  _store_js__WEBPACK_IMPORTED_MODULE_4__[\"default\"].userToken = null\r\n      userToken = null\r\n\t\t  _state_js__WEBPACK_IMPORTED_MODULE_1__.loginState.isLogin = false\r\n\t\t}\r\n  })\r\n\r\n  container.querySelector('.btn-load-more').addEventListener('click', e => {\r\n    if (!e.target.classList.contains('hidden')) {\r\n      (0,_api_js__WEBPACK_IMPORTED_MODULE_0__.loadComments)(_store_js__WEBPACK_IMPORTED_MODULE_4__[\"default\"].commentsCurrentPage + 1, false)\r\n    }\r\n  })\r\n}\r\n\r\n\n\n//# sourceURL=webpack://CommentBoard/./src/index.js?\n}");

/***/ },

/***/ "./src/state.js"
/*!**********************!*\
  !*** ./src/state.js ***!
  \**********************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   loginState: () => (/* binding */ loginState)\n/* harmony export */ });\n/* harmony import */ var _store_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./store.js */ \"./src/store.js\");\n/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils.js */ \"./src/utils.js\");\n/* harmony import */ var _api_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./api.js */ \"./src/api.js\");\n// state.js\r\n\r\n\r\n\r\n\r\n// 登入狀態 Proxy\r\nconst loginState = new Proxy({ isLogin: false }, {\r\n\tset(target, key, value) {\r\n\t\ttarget[key] = value\r\n\t\tif (key === 'isLogin') onLoginChange(value)\r\n\t\treturn true\r\n\t}\r\n})\r\n\r\nfunction onLoginChange(isLogin) {\r\n\tconst container = _store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"].container\r\n\tcontainer.querySelector('.comment-area .badge-status').innerText = isLogin ? '已登入' : '未登入'\r\n\tcontainer.querySelector('.comment-area .warning').classList.toggle('hidden', isLogin)\r\n\tcontainer.querySelector('.comment-area .alert').classList.toggle('hidden', isLogin)\r\n\tcontainer.querySelector('.comment-area .name-input').classList.toggle('hidden', isLogin)\r\n\r\n\tconst existingAvatar = container.querySelector('.login-avatar')\r\n\tif (isLogin) {\r\n\t\tif (existingAvatar) return\r\n\t\tconst { username, nickname } = (0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.getJWTPayload)(_store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"].userToken)\r\n\t\tconst loginAvatar = document.createElement('div')\r\n\t\tloginAvatar.classList.add('login-avatar')\r\n\t\tloginAvatar.innerHTML = `\r\n\t\t\t<div class=\"comment__author__avatar\"><img href=\"#\" /></div>\r\n\t\t\t<div class=\"comment__author__name\"></div>\r\n\t\t\t<div class=\"btn btn-logout\">登出</div>\r\n\t\t`\r\n\t\tloginAvatar.querySelector('.comment__author__name').textContent = `${nickname} (${username})`\r\n\t\tconst form = container.querySelector('.comment-area form')\r\n\t\tform.insertBefore(loginAvatar, form.querySelector('textarea'))\r\n\t} else {\r\n\t\tif (existingAvatar) existingAvatar.classList.add('hidden')\r\n\t\t;(0,_api_js__WEBPACK_IMPORTED_MODULE_2__.loadComments)(1, true)\r\n\t}\r\n}\n\n//# sourceURL=webpack://CommentBoard/./src/state.js?\n}");

/***/ },

/***/ "./src/store.js"
/*!**********************!*\
  !*** ./src/store.js ***!
  \**********************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n// store.js\r\nconst store = {\r\n  container: null,\r\n  appToken: null,\r\n  userToken: null,\r\n  identifier: null,\r\n  getAppTokenUrl: null,\r\n  BOARD_API: null,\r\n  commentsCurrentPage: 1,\r\n}\r\n\r\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (store);\n\n//# sourceURL=webpack://CommentBoard/./src/store.js?\n}");

/***/ },

/***/ "./src/template.js"
/*!*************************!*\
  !*** ./src/template.js ***!
  \*************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   anonymousCommentTemplate: () => (/* binding */ anonymousCommentTemplate),\n/* harmony export */   commentTemplate: () => (/* binding */ commentTemplate),\n/* harmony export */   commentsAreaTemplate: () => (/* binding */ commentsAreaTemplate)\n/* harmony export */ });\n// template.js\r\nconst commentsAreaTemplate =`\r\n<div class=\"comment-area\">\r\n\t<div class=\"counts\">Comments (3)<span class=\"badge badge-status\">未登入</span></div>\r\n\t<div class=\"alert\">Want to manage your comments?  <a href=\"\">login to comment board</a></div>\r\n\t<form class=\"add-comment-form\">\r\n\t\t<div class=\"name-input\">\r\n\t\t\t<input id=\"name\" name=\"name\" placeholder=\"暱稱（選填）\" />\r\n\t\t</div>\r\n\t\t<textarea name=\"content\" placeholder=\"寫下你的留言\" rows=\"7\"></textarea>\r\n\t\t<div class=\"warning\">匿名留言無法編輯或刪除</div>\r\n\t\t<div class=\"btn btn-send-comment\">送出留言</div>\r\n\t</form>\r\n\t<div class=\"comments\">\r\n\t</div>\r\n\t<button class=\"btn btn-load-more hidden\">more comments</button>\r\n</div>\r\n`\r\n\r\n\r\nconst commentTemplate = `<div class=\"comment__info\">\r\n      <div class=\"comment__author\">\r\n        <div class=\"comment__author__avatar\"><img href=\"#\" /></div>\r\n        <div class=\"comment__author__name\"></div>\r\n      </div>\r\n      <div class=\"comment__date\"></div>\r\n    </div>\r\n    <div class=\"comment__body\">\r\n      <div class=\"comment__content\"></div>\r\n      <div class=\"comment__tools hidden\">\r\n        <div class=\"btn btn-delete-comment\"><img src=\"images/bin.png\"/></div>\r\n        <div class=\"btn btn-edit-comment\"><img src=\"images/pencil.png\"/></div>\r\n      </div>\r\n    </div>\r\n    <div class=\"comment__body__edit hidden\">\r\n      <input class=\"input-content\" name=\"content\" type=\"text\"></input>\r\n      <div class=\"comment__tools\">\r\n        <div class=\"btn btn-send-edit-comment\">送出</div>\r\n        <div class=\"btn btn-cancel-edit-comment\">取消</div>\r\n      </div>\r\n    </div>`\r\n\r\nconst anonymousCommentTemplate = `<div class=\"comment__info\">\r\n      <div class=\"comment__author\">\r\n        <div class=\"comment__author__avatar\"><img href=\"#\" /></div>\r\n        <div class=\"comment__author__name\"></div>\r\n        <span class=\"tag-anonymous\">匿名</span>\r\n      </div>\r\n      <div class=\"comment__date\"></div>\r\n    </div>\r\n    <div class=\"comment__content\"></div>`\n\n//# sourceURL=webpack://CommentBoard/./src/template.js?\n}");

/***/ },

/***/ "./src/utils.js"
/*!**********************!*\
  !*** ./src/utils.js ***!
  \**********************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   buildHeaders: () => (/* binding */ buildHeaders),\n/* harmony export */   generateCommentAreaErrorMsg: () => (/* binding */ generateCommentAreaErrorMsg),\n/* harmony export */   getJWTPayload: () => (/* binding */ getJWTPayload),\n/* harmony export */   isJWTExpired: () => (/* binding */ isJWTExpired),\n/* harmony export */   parseDate: () => (/* binding */ parseDate)\n/* harmony export */ });\n/* harmony import */ var _store_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./store.js */ \"./src/store.js\");\n// utils.js\r\n\r\n\r\nfunction generateCommentAreaErrorMsg(msg) {\r\n  const container = _store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"].container\r\n  let errorMsgEle = container.querySelector('.error-msg')\r\n  if (!errorMsgEle) {\r\n    errorMsgEle = document.createElement('div')\r\n    errorMsgEle.classList.add('error-msg')\r\n    const parentDiv = container.querySelector('.comment-area')\r\n    const siblingDiv = container.querySelector('.alert')\r\n    parentDiv.insertBefore(errorMsgEle, siblingDiv)\r\n  }\r\n  errorMsgEle.innerText = msg\r\n}\r\n\r\nfunction getJWTPayload(token) {\r\n\treturn JSON.parse(atob(token.split('.')[1]))\r\n}\r\n\r\nfunction buildHeaders(includeUser = false) {\r\n  const headers = { 'X-App-Token': `Bearer ${_store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"].appToken}` }\r\n  if (includeUser && _store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"].userToken) headers['X-User-Token'] = `Bearer ${_store_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"].userToken}`\r\n  return headers\r\n}\r\n\r\nfunction isJWTExpired(token) {\r\n  const exp = JSON.parse(atob(token.split('.')[1])).exp\r\n  return Date.now() > exp * 1000\r\n}\r\n\r\nfunction parseDate(dateString) {\r\n  const d = new Date(dateString)\r\n  const pad = n => String(n).padStart(2, '0')\r\n  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`\r\n}\n\n//# sourceURL=webpack://CommentBoard/./src/utils.js?\n}");

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = __webpack_require__("./src/index.js");
/******/ 	
/******/ 	return __webpack_exports__;
/******/ })()
;
});