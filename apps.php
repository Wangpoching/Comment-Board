<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');

  // 檢查登入
  if (empty($_SESSION['username'])) {
    header('Location: login.php');
    exit();
  }

  // Get Announcements
  $announceText = getAnnouncements($conn);

  // 計算分頁
  $currentPage = (int)($_GET['page'] ?? 1);
  $currentPage = max(1, $currentPage); // 先夾住 >= 1，等算完 totalPages 再夾上限
  $perPage = 5;
  $sql = "SELECT count(id) as count FROM `app_keys` WHERE username = ?";
  $countResult = executeQuery($conn, $sql, 's', $_SESSION['username']);
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
      SELECT id, app_key, name, isActive, created_at, redirect_uri
      FROM `app_keys`
      WHERE username = ?
      ORDER BY created_at DESC
      LIMIT  ? OFFSET ?";
    $appsResult = executeQuery($conn, $sql, 'sii', $_SESSION['username'], $perPage, $offset);
    if (!$appsResult) {
      die('Error' . $conn->error);
    }
    $apps = $appsResult->fetch_all(MYSQLI_ASSOC);
  } else {
    $apps = [];
  } 

  // 讀取 flash 訊息
  $flashData = getFlash();
  $flash = $flashData['message'];
  $flashType = $flashData['type'];
  $appSecret = $flashData['appSecret'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>留言板- APP 管理</title>
  <link href="style.css" rel="stylesheet" />
</head>
<body>
  <?php renderAnnouncement($announceText) ?>
  <main class="lg">
    <h1>Manage Apps</h1>
    <?php if ($flash): ?>
      <div class="flash-msg <?= $flashType === 'success' ? 'flash-success' : 'flash-error' ?>">
        <span><?= $flashType === 'success' ? '✓' : '⚠' ?></span>
        <?= escape($flash) ?>
        <div><?= escape($appSecret) ?></div>
      </div>
    <?php endif; ?>
    <div class="btns">
      <a class="admin-btn button" href="add_app.php">新增 App</a>
      <a class="admin-btn button" href="index.php">回到留言板</a>
      <a class="admin-btn button" href="logout.php">會員登出</a>
    </div>
    <table>
      <thead>
        <tr>
          <th>名稱</th>
          <th>appkey</th>
          <th>重新導向網址</th>
          <th>狀態</th>
          <th>創建時間</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($apps as $app): ?>
          <tr>
            <td><?= escape($app['name']) ?></td>
            <td><div class="td__app-key"><div class="restricted-box"><?= escape($app['app_key']) ?></div><div class="action-btn copy">複製</div></div></td>
            <td><div class="restricted-box"><?= escape($app['redirect_uri']) ?></div></td>
            <td>
              <?= $app['isActive'] == 1 ?
                '<div class="status-badge running">運行中</div>' :
                '<div class="status-badge stop">已停用</div>'
              ?>
            </td>
            <td><?= date_format(new DateTime($app['created_at']), 'Y-m-d H:i:s') ?></td>
            <td>
              <div class="actions">
                <a href="edit_app.php?id=<?= escape($app['id']) ?>" class="action-btn edit">編輯</a>
                <form method="post" action="handle_delete_app.php">
                  <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
                  <input type="hidden" name="app_id" value="<?= escape($app['id']) ?>" />
                  <input type="submit" class="action-btn delete" value="刪除" />
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if ($total > 0 && $totalPages > 1): ?>
      <div class="pagination">
        <a href="?page=<?= $currentPage - 1 ?>" class="arrow <?= $prevDisabled ?>">&#8249;</a>
        <?php if ($currentPage >= 3): ?>
          <a href="?page=1" class="page">1</a>
        <?php endif; ?>
        <?php if ($currentPage >= 4): ?>
          <span class="dots">...</span>
        <?php endif; ?>
        <?php if ($currentPage >= 2): ?>
          <a href="?page=<?= $currentPage - 1 ?>" class="page"><?= $currentPage - 1 ?></a>
        <?php endif; ?>
        <a href="?page=<?= $currentPage ?>" class="page active"><?= $currentPage ?></a>
        <?php if ($currentPage < $totalPages): ?>
          <a href="?page=<?= $currentPage + 1 ?>" class="page"><?= $currentPage + 1 ?></a>
        <?php endif; ?>
        <?php if ($currentPage + 3 <= $totalPages): ?>
          <span class="dots">...</span>
        <?php endif; ?>
        <?php if ($currentPage + 2 <= $totalPages): ?>
          <a href="?page=<?= $totalPages ?>" class="page"><?= $totalPages ?></a>
        <?php endif; ?>
        <a href="?page=<?= $currentPage + 1 ?>" class="arrow <?= $nextDisabled ?>">&#8250;</a>
      </div>
    <?php endif; ?>
  </main>
</body>
<script src="marquee.js"></script>
<script src="flash.js"></script>
<script>
  document.querySelector('table').addEventListener('click', (e) => {
    if (e.target.classList.contains('copy')) {
      const text = e.target.closest('td').querySelector('.restricted-box').innerText
      navigator.clipboard.writeText(text).then(() => {
        e.target.innerText = '已複製！'
        setTimeout(() => e.target.innerText = '複製', 1500)
      })
    }
  })
</script>
</html>
