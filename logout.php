<?php
  require_once('conn.php');
  session_start();
  // 清掉記憶體裡的 session
  $_SESSION = [];
  // 讓 Cookie 失效
  session_destroy();
  header('Location: index.php');
  exit();
?>