<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');
  verifyCsrfToken();

  // 登入檢查
  $row = requireLogin();

  // 水桶檢查
  if (!verifyPermission($row, DELETE_COMMENT_PERMISSION_ID)) {
      $_SESSION['flash'] = '你沒有刪除留言的權限';
      header('Location: index.php');
      exit();
  }

  // 權限檢查
  $comment = getCommentAndVerifyOwner($_POST['comment_id'], $row['username']);
  if (!$comment) {
    $_SESSION['flash'] = '這不是你的留言';
    header("Location: index.php");
    exit();    
  }

  // 刪除留言
  $sql = "DELETE FROM `comments` WHERE id=? AND app_key IS NULL";
  $result = executeUpdate($conn, $sql, 'i', $_POST['comment_id']);
  if (!$result['success'] || $result['affected_rows'] === 0) {
      $_SESSION['flash'] = '刪除評論失敗';
      header("Location: index.php");
      exit();
  }
  $_SESSION['flash'] = '刪除留言成功';
  $_SESSION['flash_type'] = 'success';
  header('Location: index.php');
  exit();
?>