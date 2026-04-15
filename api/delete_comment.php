<?php
  require_once('../conn.php');
  require_once('../utils.php');

  header('Content-Type: application/json');

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

  // 檢查是不是自己的評論
  $sql = "SELECT * FROM `comments` WHERE id = ? AND app_key = ? AND identifier = ? AND username=?";
  $result = executeQuery($conn, $sql, 'isss', (int)$id, $appKey, $identifier, $username);
  if (!$result || $result->num_rows === 0) {
    echo json_encode(["ok" => false, "message" => "你沒有權限修改此評論"]);
    exit();
  }

  // 修改留言
  $sql = "DELETE FROM `comments` WHERE id = ?";
  $result = executeUpdate($conn, $sql, 'i', (int)$id);
  if (!$result['success'] || $result['affected_rows'] === 0) {
    echo json_encode(["ok" => false, "message" => "系統錯誤"]);
    exit();
  }

  echo json_encode([
    "ok"   => true
  ]);
  exit();
?>