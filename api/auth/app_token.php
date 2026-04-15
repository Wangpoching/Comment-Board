<?php
  require_once('../../conn.php');
  require_once('../../utils.php');
  header('Content-Type: application/json');

  $appKey = trim($_POST['appKey'] ?? '');
  $appSecret = trim($_POST['appSecret'] ?? '');

  if ($appKey === '') {
    echo json_encode(["ok" => false, "message" => "appKey 不得為空"]);
    exit();
  }
  if ($appSecret === '') {
    echo json_encode(["ok" => false, "message" => "appSecret 不得為空"]);
    exit();
  }

  // 驗證 appKey
  $sql = "SELECT * FROM app_keys WHERE app_key = ? AND isActive = 1";
  $result = executeQuery($conn, $sql, 's', $appKey);
  if (!$result || $result->num_rows === 0) {
    echo json_encode(["ok" => false, "message" => "驗證失敗"]);
    exit();
  }

  $app = $result->fetch_assoc();

  // 驗證 appSecret
  if (!password_verify($appSecret, $app['secret_key_hash'])) {
    echo json_encode(["ok" => false, "message" => "驗證失敗"]);
    exit();
  }

  // 發放 App JWT
  $header = base64url(json_encode(["alg" => "HS256", "typ" => "JWT"]));
  $payload = base64url(json_encode([
    "type"    => "appToken",      // ← 跟 userToken 區分
    "appKey"  => $app['app_key'],
    "appName" => $app['name'],
    "exp"     => time() + 3600 * 24    // 1天
  ]));
  $signature = base64url(hash_hmac('sha256', "$header.$payload", SECRET_KEY, true));
  $token = "$header.$payload.$signature";

  echo json_encode([
    "ok"    => true,
    "token" => $token
  ]);
  exit();
?>