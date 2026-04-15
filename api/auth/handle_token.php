<?php
  require_once('../../conn.php');
  require_once('../../utils.php');

  header('Content-Type: application/json');

  // 檢查欄位
  $appKey = trim($_POST['appKey'] ?? '');
  if ($appKey === '') {
      echo json_encode(["ok" => false, "message" => "應用程式金鑰不得為空"]);
      exit();
  }

  $appSecret = trim($_POST['appSecret'] ?? '');
  if ($appSecret === '') {
      echo json_encode(["ok" => false, "message" => "應用程式密碼不得為空"]);
      exit();
  }

  $code = trim($_POST['code'] ?? '');
  if ($code === '') {
      echo json_encode(["ok" => false, "message" => "通行碼不得為空"]);
      exit();
  }
  // 撈出"讀者"的 username, 不是站長的!
  $sql = "SELECT u.nickname, ac.username as username, secret_key_hash, ak.app_key as app_key
    FROM `auth_codes` as ac
    LEFT JOIN `app_keys` as ak
    on ak.app_key = ac.app_key
    LEFT JOIN `users` as u
    on ac.username = u.username
    WHERE ac.code=? AND ak.isActive=1 AND ak.app_key=? AND ac.expiresAt > NOW()
  ";
  $result = executeQuery($conn, $sql, 'ss', $code, $appKey);
  if (!$result || $result->num_rows === 0) {
    echo json_encode(["ok" => false, "message" => "驗證不通過"]);
    exit();
  }
  $row = $result->fetch_assoc();
  if (!password_verify($appSecret, $row['secret_key_hash'])) {
    echo json_encode(["ok" => false, "message" => "通行碼錯誤"]);
    exit();
  }

  // 發放 JWT 之前要先刪掉 code！
  $deleteSql = "DELETE FROM auth_codes WHERE code = ?";
  $deleteResult = executeUpdate($conn, $deleteSql, 's', $code);
  if (!$deleteResult['success'] || $deleteResult['affected_rows'] === 0) {
    echo json_encode(["ok" => false, "message" => "系統發生錯誤"]);
    exit();    
  }

  // 發放 jwtToken
  $header  = base64url(json_encode(["alg" => "HS256", "typ" => "JWT"]));
  $payload = base64url(json_encode([
    "type"     => 'userToken',
    "username" => $row['username'],
    "nickname" => $row['nickname'],
    "appKey"   => $row['app_key'],
    "exp"      => time() + 3600 * 24  // 1天後過期
  ]));
  $signature = base64url(hash_hmac('sha256', "$header.$payload", SECRET_KEY, true));

  $token = "$header.$payload.$signature";

  echo json_encode([
    "ok" => true,
    "token" => $token
  ]);
  exit();
?>