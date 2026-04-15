<?php
  session_start();
  require_once('conn.php');
  require_once('utils.php');

  $userName = trim($_POST['username'] ?? '');
  if ($userName === '') {
      $_SESSION['flash'] = '帳號不可為空';
      header('Location: register.php');
      exit();
  }

  $nickName = trim($_POST['nickname'] ?? '');
  if ($nickName === '') {
      $_SESSION['flash'] = '暱稱不可為空';
      header('Location: register.php');
      exit();
  }

  $password = trim($_POST['password'] ?? '');
  if ($pasword === '') {
      $_SESSION['flash'] = '密碼不可為空';
      header('Location: register.php');
      exit();
  }

  $passwordHash = password_hash($password, PASSWORD_DEFAULT);

  $sql = "INSERT INTO `users` (`username`, `nickname`, `password_hash`, `roleId`) VALUES (?, ?, ?, ?)";
  $result = executeUpdate($conn, $sql, 'sssi', $userName, $nickName, $passwordHash, NORMAL_ROLE_ID);
  if (!$result['success'] || $result['affected_rows'] === 0) {
    if ($result['errno'] === 1062) {
        $_SESSION['flash'] = '帳號已存在';
    } else {
        $_SESSION['flash'] = '系統錯誤';
    }
    header('Location: register.php');
    exit();
  }

  // 發放 Cookie
  $_SESSION['username'] = $userName;
  header('Location: index.php');
  exit();
?>