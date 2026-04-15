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

  // 重設 app secret
  $appSecret = bin2hex(random_bytes(32));
  $appSecretHash = password_hash($appSecret, PASSWORD_DEFAULT);  
  $sql = "
  	UPDATE `app_keys` SET
  	`secret_key_hash` = ?
  	WHERE id = ?
  ";
  $result = executeUpdate(
  	$conn,
  	$sql,
  	'si',
  	$appSecretHash,
  	(int)$_POST['id'],
  );
  if (!$result['success'] || $result['affected_rows'] === 0) {
      $_SESSION['flash'] = 'secret key 更新失敗';
      header("Location: apps.php");
      exit();
  }
  $_SESSION['flash'] = 'secret key 更新成功，請妥善保存';
  $_SESSION['flash_type'] = 'success';
  $_SESSION['app_secret'] = $appSecret;
  header('Location: apps.php');
  exit();
?>