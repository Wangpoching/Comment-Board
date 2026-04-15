<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');
  verifyCsrfToken();

  // 登入檢查
  $row = requireLogin();

	// 驗證輸入
	$name = trim($_POST['name'] ?? '');
	if ($name === '') {
	    $_SESSION['flash'] = 'App 名稱不可為空';
	    header('Location: add_app.php');
	    exit();
	}
	$redirectUri = trim($_POST['redirect_uri'] ?? '');
	if ($redirectUri === '') {
	    $_SESSION['flash'] = '重新導向網址不可為空';
	    header('Location: add_app.php');
	    exit();
	}
	if (!isset($_POST['isActive'])) {
	    $_SESSION['flash'] = '請選擇 App 運行狀態';
	    header('Location: add_app.php');
	    exit();	
	}
	if ($_POST['isActive'] != 1 && $_POST['isActive'] != 0) {
	    $_SESSION['flash'] = '不存在的 App 運行狀態';
	    header('Location: add_app.php');
	    exit();	
	}

  // 新增 app
	$appKey = bin2hex(random_bytes(32));
	$appSecret = bin2hex(random_bytes(32));
	$appSecretHash = password_hash($appSecret, PASSWORD_DEFAULT);  
  $sql = "
  	INSERT INTO `app_keys` 
  	(`name`, `app_key`, `username`, `secret_key_hash`, `isActive`, `redirect_uri`)
  	VALUES (?, ?, ?, ?, ?, ?)
  ";
  $result = executeUpdate(
  	$conn,
  	$sql,
  	'ssssis',
  	$_POST['name'],
  	$appKey,
  	$_SESSION['username'],
  	$appSecretHash,
  	(int)$_POST['isActive'],
  	$redirectUri
  );
  if (!$result['success'] || $result['affected_rows'] === 0) {
      $_SESSION['flash'] = '新增 app 失敗';
      header("Location: apps.php");
      exit();
  }
  $_SESSION['flash'] = '新增 app 成功';
  $_SESSION['flash_type'] = 'success';
  $_SESSION['app_secret'] = $appSecret;
  header('Location: apps.php');
  exit();
?>