<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');

  // 第三方登入會攜帶要 state，需要保留
  $errorRrdirectPath = 'login.php?' . $_SERVER['QUERY_STRING'];

  // 檢查欄位
  $userName = trim($_POST['username'] ?? '');
  if ($userName === '') {
      $_SESSION['flash'] = '帳號不可為空';
      header('Location: ' . $errorRrdirectPath);
      exit();
  }

  $password = trim($_POST['password'] ?? '');
  if ($password === '') {
      $_SESSION['flash'] = '密碼不可為空';
      header('Location: ' . $errorRrdirectPath);
      exit();
  }

  $sql = "SELECT `username`, `password_hash` FROM `users` WHERE username=?";
  $result = executeQuery($conn, $sql, 's', $userName);
  if (!$result) {
    $_SESSION['flash'] = '系統錯誤';
    header('Location: ' . $errorRrdirectPath);
    exit();
  }
  $row = $result->fetch_assoc();
  if (!$row || !password_verify($password, $row['password_hash'])) {
    $_SESSION['flash'] = '帳號密碼錯誤';
    header('Location: ' . $errorRrdirectPath);
    exit();
  }

  // 發放 Cookie or 重新導向發放 authorizationCode
  $appKey = trim($_GET['appKey'] ?? '');
  if ($appKey === '') {
    $_SESSION['username'] = $row['username'];
    header('Location: index.php');
    exit();
  } else {
    $sql = "SELECT redirect_uri FROM app_keys WHERE app_key = ? AND isActive = 1";
    $result = executeQuery($conn, $sql, 's', $appKey);
    if (!$result || $result->num_rows === 0) {
      $_SESSION['flash'] = $result->num_rows;
      header('Location: index.php');
      exit();
    }
    $redirectUrl = $result->fetch_assoc()['redirect_uri'];
    $code = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', time() + 3600 * 24); // 1 天後過期
    $sql = "INSERT INTO auth_codes (code, userName, app_key, expiresAt) VALUES (?, ?, ?, ?)";
    $result = executeUpdate($conn, $sql, 'ssss', $code, $userName, $appKey, $expiresAt);
    if (!$result['success'] || $result['affected_rows'] === 0) {
      $_SESSION['flash'] = '發生錯誤';
      header('Location: index.php');
      exit();
    }
    $redirectPath = $redirectUrl . '?code=' . $code;
    $state = trim($_GET['state'] ?? '');
    if ($state !== '') {
      $redirectPath .= '&state=' . urlencode($state);
    }

    header('Location: ' . $redirectPath);
    exit();
  }
?>