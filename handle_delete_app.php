<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');
  verifyCsrfToken();

  // 登入檢查
  $row = requireLogin();

  // 權限檢查
  $app = getAppAndVerifyOwner($_POST['app_id'], $row['username']);
  if (!$app) {
    $_SESSION['flash'] = '這不是你的 app';
    header("Location: index.php");
    exit();    
  }

  // 刪除 app
  $sql = "DELETE FROM `app_keys` WHERE id=?";
  $result = executeUpdate($conn, $sql, 'i', $_POST['app_id']);
  if (!$result['success'] || $result['affected_rows'] === 0) {
      $_SESSION['flash'] = '刪除 app 失敗';
      header("Location: apps.php");
      exit();
  }
  $_SESSION['flash'] = '刪除 app 成功';
  $_SESSION['flash_type'] = 'success';
  header('Location: apps.php');
  exit();
?>