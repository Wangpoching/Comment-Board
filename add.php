<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');
  verifyCsrfToken();

  // 登入檢查
  $row = requireLogin();

  // 檢查是否被水桶
  if (!verifyPermission($row, ADD_COMMENT_PERMISSION_ID)) {
      $_SESSION['flash'] = '你沒有發布留言的權限';
      header('Location: index.php');
      exit();    
  }

  // 將留言寫入資料庫(不用 empty 的原因是讓 0 可以寫入)
  $content = trim($_POST['comment'] ?? '');
  if ($content === '') {
      $_SESSION['flash'] = '留言內容不可為空';
      header('Location: index.php');
      exit();
  }

  $username = $row['username'];

  $sql = "INSERT INTO `comments` (`username`, `content`) VALUES (?, ?)";
  $result = executeUpdate($conn, $sql, 'ss', $username, $content);
  if (!$result['success'] || $result['affected_rows'] === 0) {
    $_SESSION['flash'] = '新增留言失敗';
  }

  header('Location: index.php');
  exit();
?>