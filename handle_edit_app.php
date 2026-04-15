<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');
  verifyCsrfToken();

  // 登入檢查
  $row = requireLogin();

  // 檢查是不是本人的 app
  if (!isset($_POST['id'])) {
    header('Location: index.php');
    exit();
  }
  $appId = (int)$_POST['id'];
  $app = getAppAndVerifyOwner($appId, $row['username']);
  if (!$app) {
    header('Location: index.php');
    exit();    
  }

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
  $sql = "
  	UPDATE `app_keys` SET
  	`name` = ?, `isActive` = ?, `redirect_uri` = ?
  	WHERE id = ?
  ";
  $result = executeUpdate(
  	$conn,
  	$sql,
  	'sisi',
  	$_POST['name'],
  	(int)$_POST['isActive'],
  	$redirectUri,
  	(int)$_POST['id'],
  );
  if (!$result['success'] || $result['affected_rows'] === 0) {
      $_SESSION['flash'] = '修改 app 失敗';
      header("Location: apps.php");
      exit();
  }
  $_SESSION['flash'] = '修改 app 成功';
  $_SESSION['flash_type'] = 'success';
  $_SESSION['app_secret'] = $appSecret;
  header('Location: apps.php');
  exit();
?>