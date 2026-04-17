<?php
  require_once('../conn.php');
  require_once('../utils.php');

  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Headers: Content-Type, X-User-Token, X-App-Token");
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
  header('Content-Type: application/json');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
  }

  // 檢查 jwtToken
  $appJwtData = validateJWTToken('app');
  $appKey = $appJwtData['appKey'];

  $userJwtData = validateJWTToken();
  $username = $userJwtData['username'];

  $identifier = trim($_POST['identifier'] ?? '');
  if ($identifier === '') {
    echo json_encode(["ok" => false, "message" => "identifier 不得為空"]);
    exit();
  }

  $id = trim($_POST['id'] ?? '');
  if ($id === '') {
    echo json_encode(["ok" => false, "message" => "id 不得為空"]);
    exit();
  }

  $content = trim($_POST['content'] ?? '');
  if ($content === '') {
    echo json_encode(["ok" => false, "message" => "content 不得為空"]);
    exit();
  }

  // 檢查是不是自己的評論
  $sql = "SELECT * FROM `comments` WHERE id = ? AND app_key = ? AND identifier = ? AND username=?";
  $result = executeQuery($conn, $sql, 'isss', (int)$id, $appKey, $identifier, $username);
  if (!$result || $result->num_rows === 0) {
    echo json_encode(["ok" => false, "message" => "你沒有權限修改此評論"]);
    exit();
  }

  // 修改留言
  $sql = "UPDATE `comments` SET content = ? WHERE id = ?";
  $result = executeUpdate($conn, $sql, 'si', $content, (int)$id);
  if (!$result['success'] || $result['affected_rows'] === 0) {
    echo json_encode(["ok" => false, "message" => "系統錯誤"]);
    exit();
  }

  // 撈更新的留言
  $sql = "
    SELECT * FROM `comments`
    LEFT JOIN `users` on comments.username = users.username
    WHERE comments.id = ?
  ";
  $result = executeQuery($conn, $sql, 'i', (int)$id);
  if (!$result) {
    echo json_encode(["ok" => false, "message" => "系統錯誤"]);
    exit();    
  }
  $editedComment = $result->fetch_assoc();  

  // 整理資料
  $data = [
    "id"         => $editedComment['id'],
    "appKey"     => $editedComment['app_key'],
    "identifier" => $editedComment['identifier'],
    "nickname"   => $editedComment['nickname'] ?? '',
    "username"   => $editedComment['username'],
    "authorname" => $editedComment['authorname'],
    "content"    => $editedComment['content'],
    "createdAt"  => $editedComment['createdAt']
  ];

  echo json_encode([
    "ok"   => true,
    "data" => $data,
  ]);
  exit();
?>