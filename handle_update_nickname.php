<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');
  verifyCsrfToken();

  // 登入檢查
  $row = requireLogin();

  // 檢查有沒有被水桶
  if (!verifyPermission($row, EDIT_NICKNAME_PERMISSION_ID)) {
      $_SESSION['flash'] = '你沒有編輯暱稱的權限';
      header('Location: index.php');
      exit();    
  }  

  // 參數檢查
  $nickname = trim($_POST['nickname'] ?? '');
  if ($nickname === '') {
      $_SESSION['flash'] = '暱稱不可為空';
      header('Location: index.php');
      exit();
  }

  $username = $row['username'];

  $sql = "UPDATE `users` SET `nickname` = ? WHERE `username` = ?";
  $result = executeUpdate($conn, $sql, 'ss', $nickname, $username);
  if (!$result['success'] || $result['affected_rows'] === 0) {
      $_SESSION['flash'] = '編輯暱稱失敗';
  }
  $_SESSION['flash'] = '暱稱更新成功';
  $_SESSION['flash_type'] = 'success';
  header('Location: index.php');
?>