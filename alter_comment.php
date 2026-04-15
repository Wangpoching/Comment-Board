<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');

  // 登入檢查
  $row = requireLogin();

  // 水桶檢查
  if (!verifyPermission($row, EDIT_COMMENT_PERMISSION_ID)) {
      $_SESSION['flash'] = '你沒有編輯留言的權限';
      header('Location: index.php');
      exit();
  };

  // 權限檢查
  $result = getCommentAndVerifyOwner($_GET['comment_id'], $row['username']);
  if (!$result) {
    $_SESSION['flash'] = '你沒有編輯留言的權限';
    header('Location: index.php');
    exit();
  }

  // Get Announcements
  $announceText = getAnnouncements($conn);

  // 讀取 flash 訊息
  $flashData = getFlash();
  $flash = $flashData['message'];
  $flashType = $flashData['type'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>留言板-修改留言</title>
  <link href="style.css" rel="stylesheet" />
</head>
<body>
  <?php renderAnnouncement($announceText) ?>
  <?php if ($flash): ?>
    <div class="flash-msg <?= $flashType === 'success' ? 'flash-success' : 'flash-error' ?>">
      <span><?= $flashType === 'success' ? '✓' : '⚠' ?></span>
      <?= escape($flash) ?>
    </div>
  <?php endif; ?>
  <main>
    <h1>Edit comment</h1>
      <form class="write-area" method="post" action="handle_alter_comment.php?comment_id=<?= $_GET['comment_id']?>">
        <div class="write-area__submit">
          <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
          <textarea id="comment" name="comment" rows="5" cols="33"><?= escape($result['content']) ?></textarea>
          <input class="button" type="submit" value="送出" />
        </div>
      </form>
      <a href="index.php" class="login-btn button">回留言板</a>
  </main>
</body>
<script src="flash.js"></script>
<script src="marquee.js"></script>
</html>
