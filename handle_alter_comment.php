<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');
  verifyCsrfToken();

  // 登入檢查
  $row = requireLogin();

  // 檢查有沒有被水桶
  if (!verifyPermission($row, EDIT_COMMENT_PERMISSION_ID)) {
      $_SESSION['flash'] = '你沒有編輯留言的權限';
      header('Location: index.php');
      exit();
  }

  // 權限檢查
  $comment = getCommentAndVerifyOwner($_GET['comment_id'], $row['username']);
  if (!$comment) {
    $_SESSION['flash'] = '這不是你的留言';
    header("Location: index.php");
    exit();    
  }

  // 修改留言
  $content = trim($_POST['comment'] ?? '');
  if ($content === '') {
    $_SESSION['flash'] = '留言不可為空';
    header("Location: alter_comment.php?comment_id={$_GET['comment_id']}");
    exit();
  }
  $sql = "UPDATE `comments` SET content=? WHERE id=? AND app_key IS NULL";
  $result = executeUpdate($conn, $sql, 'si', $content, (int)$_GET['comment_id']);
  // 確認留言已經存在, 不需要再檢查 affected_rows, 如果 affected_rows = 0 必定是因為內容與原來無異
  if (!$result['success']) {
      $_SESSION['flash'] = '編輯評論失敗';
      header("Location: alter_comment.php?comment_id={$_GET['comment_id']}");
      exit();
  }
  $_SESSION['flash'] = '編輯留言成功';
  $_SESSION['flash_type'] = 'success';
  header('Location: index.php');
  exit();
?>