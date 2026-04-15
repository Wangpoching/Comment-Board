<?php
  require_once('../conn.php');
  require_once('../utils.php');

  header('Content-Type: application/json');

  // 檢查 jwtToken
  $appJwtData = validateJWTToken('app');
  $appKey = $appJwtData['appKey'];

  $username = '';
  if (getCustomHeader('X-User-Token') !== '') {
    $userJwtData = validateJWTToken();
    $username = $userJwtData['username'];
  }

  $identifier = trim($_POST['identifier'] ?? '');
  if ($identifier === '') {
    echo json_encode(["ok" => false, "message" => "identifier 不得為空"]);
    exit();
  }

  $content = trim($_POST['content'] ?? '');
  if ($content === '') {
    echo json_encode(["ok" => false, "message" => "content 不得為空"]);
    exit();
  }

  // 寫匿名留言
  $insertId = '';
  if (!$username) {
    $sql = "INSERT INTO `comments` (content, app_key, identifier, authorname) VALUES (?, ?, ?, ?)";
    $result = executeUpdate($conn, $sql, 'ssss', $content, $appKey, $identifier, $_POST['authorname']);
    if (!$result['success'] || $result['affected_rows'] === 0) {
      echo json_encode(["ok" => false, "message" => "系統錯誤"]);
      exit();
    }
    $insertId = $result['insert_id'];
  } else {
    // 登入後寫留言
    $sql = "INSERT INTO `comments` (username, content, app_key, identifier) VALUES (?, ?, ?, ?)";
    $result = executeUpdate($conn, $sql, 'ssss', $username, $content, $appKey, $identifier);
    if (!$result['success'] || $result['affected_rows'] === 0) {
      echo json_encode(["ok" => false, "message" => "系統錯誤"]);
      exit();
    }
    $insertId = $result['insert_id'];
  }

  // 撈新增的留言
  $sql = "
    SELECT * FROM `comments`
    LEFT JOIN `users` on comments.username = users.username
    WHERE comments.id = ?
  ";
  $result = executeQuery($conn, $sql, 'i', (int)$insertId);
  if (!$result) {
    echo json_encode(["ok" => false, "message" => "系統錯誤"]);
    exit();    
  }
  $newComment = $result->fetch_assoc();

  // 整理資料
  $data = [
    "id"         => $newComment['id'],
    "appKey"     => $newComment['app_key'],
    "identifier" => $newComment['identifier'],
    "nickname"   => $newComment['nickname'] ?? '',
    "username"   => $newComment['username'],
    "authorname" => $newComment['authorname'],
    "content"    => $newComment['content'],
    "createdAt"  => $newComment['createdAt']
  ];

  echo json_encode([
    "ok"   => true,
    "data" => $data,
  ]);
  exit();
?>