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

  $username = '';
  if (getCustomHeader('X-User-Token') !== '') {
    $userJwtData = validateJWTToken();
    $username = $userJwtData['username'];
  }

  // 參數
  $identifier = trim($_GET['identifier'] ?? '');
  if ($identifier === '') {
    echo json_encode(["ok" => false, "message" => "identifier 不得為空"]);
    exit();
  }

  // 分頁參數
  $perPage = 5;
  $page = (int)($_GET['page'] ?? 1);
  $page = max(1, $page);

  // 取得總筆數
  $sql = "SELECT COUNT(*) as count FROM comments WHERE app_key = ? AND identifier = ?";
  $countResult = executeQuery($conn, $sql, 'ss', $appKey, $identifier);
  if (!$countResult) {
    echo json_encode(["ok" => false, "message" => "系統錯誤"]);
    exit();
  }
  $total = (int)$countResult->fetch_assoc()['count'];
  if ($total === 0) {
    echo json_encode([
      "ok"   => true,
      "data" => [],
      "pagination" => [
        "total"       => 0,
        "currentPage" => 1,
        "totalPages"  => 0,
        "perPage"     => $perPage
      ]
    ]);
    exit();
  }
  $totalPages = ceil($total / $perPage);
  $page = min($page, max(1, $totalPages));
  $offset = ($page - 1) * $perPage;

  // 撈留言
  $sql = "
    SELECT
      comments.authorname,
      comments.id, 
      comments.content,
      comments.createdAt,
      comments.app_key,
      comments.identifier,
      users.username,
      users.nickname
    FROM `comments`
    LEFT JOIN `users` ON comments.username = users.username
    WHERE app_key = ? AND identifier = ?
    ORDER BY comments.createdAt DESC
    LIMIT ? OFFSET ?
  ";
  $result = executeQuery($conn, $sql, 'ssii', $appKey, $identifier, $perPage, $offset);
  if (!$result) {
    echo json_encode(["ok" => false, "message" => "系統錯誤"]);
    exit();
  }

  // 整理資料
  $data = [];
  while ($row = $result->fetch_assoc()) {
    $data[] = [
      "id"        => $row['id'],
      "identifier"   => $row['identifier'],
      "nickname"  => $row['nickname'],
      "username"  => $row['username'],
      "authorname" => $row['authorname'],
      "content"   => $row['content'],
      "isOwn"     => $username ? $row['username'] === $username : false,
      "createdAt" => $row['createdAt']
    ];
  }

  echo json_encode([
    "ok"   => true,
    "data" => $data,
    "pagination" => [
      "total"       => (int)$total,
      "currentPage" => $page,
      "totalPages"  => $totalPages,
      "perPage"     => $perPage
    ]
  ]);
  exit();
?>