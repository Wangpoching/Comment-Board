<?php
  session_start();
  require_once('utils.php');

  // Get Announcements
  $announceText = getAnnouncements($conn);

  $queryString = '';
  $appKey = $_GET['appKey'] ?? '';

  // 處理需要重新導向的外站
  if ($appKey !== '') {
    $state = $_GET['state'] ?? '';

    // 驗證導向
    $sql = "SELECT * FROM app_keys WHERE app_key = ? AND isActive = 1";
    $result = executeQuery($conn, $sql, 's', $appKey);
    if (!$result || $result->num_rows === 0) {
      exit('state 的網域必須與註冊的網域一致');
    }
    $registeredHost = parse_url($result->fetch_assoc()['redirect_uri'])['host'] ?? '';
    $stateHost = $state !== '' ? (parse_url($state)['host'] ?? '') : '';
    if ($stateHost === '' || $stateHost !== $registeredHost) {
      exit('state 的網域必須與註冊的網域一致');
    }
    $queryString = '?appKey=' . $_GET['appKey'];
    if ($state !== '') {
        $queryString .= '&state=' . urlencode($state);
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>留言板-登入</title>
  <link href="login-style.css" rel="stylesheet" />
</head>
<body>
  <?php if ($announceText) { ?>
    <header class="warning">
      <div class="warning__wrapper">
        <span class="warning__text marquee__content"><?= escape($announceText) ?></span>
      </div>
    </header>
  <?php } ?>
  <main>
    <div class="title">
      <h1>登入</h1>
      <div class="buttons">
        <a href="index.php" class="back-btn button">回留言板</a>
        <a href="register.php" class="login-btn button">註冊</a>
      </div>
    </div>
    <form class="login-area" method="post" action="handle_login.php<?= $queryString ?>">
      <div class="error-msg hidden">
        <?php if (isset($_SESSION['flash'])) {
          echo escape($_SESSION['flash']);
          unset($_SESSION['flash']);
        } ?>
      </div>
      <div>
        <label for="username">使用者名稱：</label>
        <input type="text" id="username" name="username" >
      </div>
      <div>
        <label for="password">密碼：</label>
        <input type="password" id="password" name="password" >
      </div>
      <div>
        <input class="button" type="submit" value="提交" />
      </div>
    </form>
  </main>
</body>
<script>
  const error = document.querySelector('.error-msg')
  if (error.innerText !== '') {
    error.classList.remove('hidden')
    setTimeout(() => {
      error.classList.add('hidden')
    }, 2000)
  }
</script>
<script src="marquee.js"></script>
</html>
