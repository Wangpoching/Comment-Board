<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');

  // Get Announcements
  $announceText = getAnnouncements($conn);

  // 計算分頁
  $currentPage = (int)($_GET['page'] ?? 1);
  $currentPage = max(1, $currentPage); // 先夾住 >= 1，等算完 totalPages 再夾上限
  $perPage = 5;
  $sql = "SELECT count(id) as count FROM `comments` WHERE app_key IS NULL";
  $countResult = $conn->query($sql);
  if (!$countResult) {
    die('Error' . $conn->error);
  }
  $row = $countResult->fetch_assoc();
  $total = $row['count'];
  if ($total !== 0) {
    $totalPages = ceil($total / $perPage);
    $currentPage = min($currentPage, max(1, $totalPages)); // 防止超出總頁數
    $prevDisabled = $currentPage <= 1 ? 'disabled' : '';
    $nextDisabled = $currentPage >= $totalPages ? 'disabled' : '';
    $offset = ($currentPage - 1)*$perPage;

    $sql = "
      SELECT comments.*, users.nickname 
      FROM `comments`
      LEFT JOIN `users` ON comments.username = users.username
      WHERE app_key IS NULL
      ORDER BY comments.createdAt DESC
      LIMIT  ? OFFSET ?";
    $commentsResult = executeQuery($conn, $sql, 'ii', $perPage, $offset);
    if (!$commentsResult) {
      die('Error' . $conn->error);
    }
  }

  // 讀取 flash 訊息
  $flashData = getFlash();
  $flash = $flashData['message'];
  $flashType = $flashData['type'];
  $isLogin = false;
  $isAdmin = false;
  $nickName = null;
  $userName = null;
  $commentEditable = false;
  $commentDeletable = false;

  // Cookie 根本不存在; Cookie 存在但是空字串 都通用
  if (!empty($_SESSION['username'])) {
    $row = getUserFromSession($_SESSION['username']);
    if ($row) {
      if ($row['roleName'] === ADMIN_ROLE_NAME) {
        $isAdmin = true;
      }
      if (verifyPermission($row, EDIT_COMMENT_PERMISSION_ID)) $commentEditable = true;
      if (verifyPermission($row, DELETE_COMMENT_PERMISSION_ID)) $commentDeletable = true;
      $isLogin = true;
      $nickName = $row['nickname'];
      $userName = $row['username'];
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>留言板-首頁</title>
  <link href="style.css" rel="stylesheet" />
</head>
<body>
  <?php renderAnnouncement($announceText) ?>
  <main>
    <h1>Comments</h1>
    <?php if ($flash): ?>
      <div class="flash-msg <?= $flashType === 'success' ? 'flash-success' : 'flash-error' ?>">
        <span><?= $flashType === 'success' ? '✓' : '⚠' ?></span>
        <?= escape($flash) ?>
      </div>
    <?php endif; ?>
    <?php if ($isLogin) { ?>
      <div class="btns">
        <button class="update-nickname-btn button" href="#">編輯暱稱</button>
        <a class="admin-btn button" href="apps.php">管理 app</a>
        <?php if ($isAdmin) { ?> 
          <a class="logout-btn button" href="admin_role.php">管理後台</a>
        <?php } ?>
        <a class="admin-btn button" href="logout.php">會員登出</a>
      </div>
      <section class="update-nickname-section hidden">
        <form class="update-nickname-form" method="post" action="handle_update_nickname.php">
          <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
          <label>新暱稱: <input type="text" name="nickname" /></label>
          <input class="button" type="submit" value="送出" />
          <div class="button close-update-nickname-btn">取消</div>
        </form>
        <hr />
      </section>
      <form class="write-area" method="post" action="add.php">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
        <label for="comment">
          <div class="write-area__author">
            <?= escape($nickName); ?>
          </div>
        有甚麼想說的嗎?
        </label>
        <div class="write-area__submit">
          <textarea id="comment" name="comment" rows="5" cols="33" placeholder="It was a dark and stormy night..."></textarea>
          <input class="button" type="submit" value="送出" />
        </div>
      </form>
    <?php } else { ?>
      <a href="login.php" class="login-btn button">會員登入</a>
    <?php } ?>
    <hr />
    <?php if ($total > 0) { ?>
      <section>
        <?php while($comment = $commentsResult->fetch_assoc()) { ?>
          <div class="card">
            <div class="avatar">
              <div class="avatar__wrapper"></div>
            </div>
            <div class="comment-detail">
              <div class="comment-time"><span class="comment-time__author"><?= escape($comment['nickname']); ?> (@<?= escape($comment['username']); ?>)</span><span class="comment-time__time"> &middot; <?= date_format(new DateTime($comment['createdAt']), 'Y-m-d H:i:s'); ?></span></div>
              <div class="comment-content"><?= escape($comment['content']); ?></div>
            </div>
            <?php if ($comment['username'] === $userName)  { ?>
              <div class="tools">
                <?php if ($commentEditable) { ?>
                  <div class="tool-wrapper">
                    <a href="alter_comment.php?comment_id=<?= $comment['id'] ?>"><img src="images/pencil.png" /></a>
                  </div>
                <?php } ?>
                <?php if ($commentDeletable) { ?>
                  <div class="tool-wrapper">
                    <form method="post" action="handle_delete_comment.php">
                      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
                      <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>" />
                      <button type="submit">
                        <img src="images/delete.png" />
                      </button>
                    </form>
                  </div>
                <?php } ?>
              </div>
            <?php } ?>
          </div>
        <?php } ?>
      </section>
      <hr />
      <div class="pagination">
        <a href="?page=<?= $currentPage - 1 ?>" class="arrow <?= $prevDisabled ?>">&#8249;</a>
        <?php if ($currentPage >= 3) { ?>
          <a href="?page=1" class="page">1</a>
        <?php } ?>
        <?php if ($currentPage >= 4) { ?>
          <span class="dots">...</span>
        <?php } ?>
        <?php if ($currentPage >= 2) { ?>
          <a href="?page=<?= $currentPage - 1 ?>" class="page"><?= $currentPage - 1 ?></a>
        <?php } ?>
        <a href="?page=<?= $currentPage ?>" class="page active"><?= $currentPage ?></a>
        <?php if ($currentPage < $totalPages) { ?>
          <a href="?page=<?= $currentPage + 1 ?>" class="page"><?= $currentPage + 1 ?></a>
        <?php } ?>
        <?php if ($currentPage + 3 <= $totalPages) { ?>
          <span class="dots">...</span>
        <?php } ?>
        <?php if ($currentPage + 2 <= $totalPages) { ?>
          <a href="?page=<?= $totalPages ?>" class="page"><?= $totalPages ?></a>
        <?php } ?>
        <a href="?page=<?= $currentPage + 1 ?>" class="arrow <?= $nextDisabled ?>">&#8250;</a>
      </div>
    <?php } else { ?>
      <div class="no-comment">暫無留言</div>
    <?php } ?> 
  </main>
</body>
<script src="flash.js"></script>
<script>
  // toggle 編輯暱稱顯示
  const updateNicknameBtn = document.querySelector('.update-nickname-btn')
  if (updateNicknameBtn) {
    updateNicknameBtn.addEventListener('click', (e) => {
      document.querySelector('.update-nickname-section').classList.remove('hidden')
    })
  }
  const closeUpdateNicknameBtn = document.querySelector('.close-update-nickname-btn')
  if (closeUpdateNicknameBtn) {
    closeUpdateNicknameBtn.addEventListener('click', (e) => {
      document.querySelector('.update-nickname-section').classList.add('hidden')
    })
  }
</script>
<script src="marquee.js"></script>
</html>
