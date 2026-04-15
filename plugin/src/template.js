// template.js
export const commentsAreaTemplate =`
<div class="comment-area">
	<div class="counts">Comments (3)<span class="badge badge-status">未登入</span></div>
	<div class="alert">Want to manage your comments?  <a href="">login to comment board</a></div>
	<form class="add-comment-form">
		<div class="name-input">
			<input id="name" name="name" placeholder="暱稱（選填）" />
		</div>
		<textarea name="content" placeholder="寫下你的留言" rows="7"></textarea>
		<div class="warning">匿名留言無法編輯或刪除</div>
		<div class="btn btn-send-comment">送出留言</div>
	</form>
	<div class="comments">
	</div>
	<button class="btn btn-load-more hidden">more comments</button>
</div>
`


export const commentTemplate = `<div class="comment__info">
      <div class="comment__author">
        <div class="comment__author__avatar"><img href="#" /></div>
        <div class="comment__author__name"></div>
      </div>
      <div class="comment__date"></div>
    </div>
    <div class="comment__body">
      <div class="comment__content"></div>
      <div class="comment__tools hidden">
        <div class="btn btn-delete-comment"><img src="images/bin.png"/></div>
        <div class="btn btn-edit-comment"><img src="images/pencil.png"/></div>
      </div>
    </div>
    <div class="comment__body__edit hidden">
      <input class="input-content" name="content" type="text"></input>
      <div class="comment__tools">
        <div class="btn btn-send-edit-comment">送出</div>
        <div class="btn btn-cancel-edit-comment">取消</div>
      </div>
    </div>`

export const anonymousCommentTemplate = `<div class="comment__info">
      <div class="comment__author">
        <div class="comment__author__avatar"><img href="#" /></div>
        <div class="comment__author__name"></div>
        <span class="tag-anonymous">匿名</span>
      </div>
      <div class="comment__date"></div>
    </div>
    <div class="comment__content"></div>`