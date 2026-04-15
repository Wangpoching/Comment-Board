<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');

  // 登入檢查
  $row = requireLogin();

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
  <title>留言板-新增 APP</title>
  <link href="style.css" rel="stylesheet" />
</head>
<body>
  <?php renderAnnouncement($announceText) ?>
  <main>
    <h1>Create App</h1>
    <?php if ($flash): ?>
      <div class="flash-msg <?= $flashType === 'success' ? 'flash-success' : 'flash-error' ?>">
        <span><?= $flashType === 'success' ? '✓' : '⚠' ?></span>
        <?= escape($flash) ?>
      </div>
    <?php endif; ?>
    <div class="btns">
      <a class="admin-btn button" href="index.php">回到留言板</a>
      <a class="admin-btn button" href="apps.php">回到 App 管理</a>
      <a class="admin-btn button" href="logout.php">會員登出</a>
    </div>
    <div class="form-card">
      <form method="POST" action="handle_add_app.php">
        <div class="form-group">
          <label class="form-label" for="name">App 名稱</label>
          <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
          <input class="form-input" type="text" id="name" name="name" />
        </div>

        <div class="form-group">
          <label class="form-label" for="redirect_uri">重新導向網址</label>
          <input class="form-input" type="text" id="redirect_uri" name="redirect_uri" />
        </div>

        <div class="form-group">
          <label class="form-label" for="isActive">狀態</label>
	          <select id="isActive" name="isActive" class="role-select">
	          	<option value="">請選擇</option>
              <option value="1">運行</option>
              <option value="0">停用</option>
	          </select>
        </div>

        <div class="form-actions">
          <button type="submit" class="add-btn">新增 App</button>
          <a href="apps.php" class="cancel-btn">取消</a>
        </div>
      </form>
    </div>
  </main>
</body>
<script src="marquee.js"></script>
<script src="flash.js"></script>
</html>