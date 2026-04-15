<?php
    require_once('config.php');
    require_once('conn.php');
    define('ADMIN_ROLE_NAME', 'admin');
    define('NORMAL_ROLE_ID', 1);
    define('ADD_COMMENT_PERMISSION_ID', 1);
    define('EDIT_COMMENT_PERMISSION_ID', 2);
    define('DELETE_COMMENT_PERMISSION_ID', 3);
    define('EDIT_NICKNAME_PERMISSION_ID', 4);

    function getUserFromSession($username) {
        global $conn;
        $sql = "SELECT users.id as userId, username, nickname, roles.name as roleName, roles.id as roleId,
                GROUP_CONCAT(rp.permissionId) as permissionIds
        FROM `users` 
        LEFT JOIN `roles` ON users.roleId = roles.id
        LEFT JOIN `role_permissions` rp ON roles.id = rp.roleId
        WHERE username = ?
        GROUP BY users.id
        ";
        $result = executeQuery($conn, $sql, 's', $username);
        if (!$result) return null;
        if ($result->num_rows === 0) return null;
        $row = $result->fetch_assoc();
        // 把權限 id 字串轉成陣列 [1, 2, 3]
        $row['permissions'] = $row['permissionIds']
            ? array_map('intval', explode(',', $row['permissionIds']))
            : [];
        return $row;
    }

    /* 單雙引號都轉譯 */
    function escape($str) {
        return htmlspecialchars($str, ENT_QUOTES);
    }

    function executeQuery($conn, $sql, $types, ...$params) {
        $stmt = $conn->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) return null;
        return $stmt->get_result();
    }

    function executeUpdate($conn, $sql, $types, ...$params) {
        $stmt = $conn->prepare($sql);
        if (!$stmt) return [
            'success' => false,
            'errno' => 0,
            'affected_rows' => 0,
            'insert_id' => null
        ];
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            return [
                'success' => false,
                'errno' => $conn->errno,
                'affected_rows' => 0,
                'insert_id' => null
            ];
        }
        return [
            'success' => true,
            'errno' => 0,
            'affected_rows' => $stmt->affected_rows,
            'insert_id' => $conn->insert_id // 回傳第一筆新增的 id, 更新或刪除就沒有
        ];
    }

    function requireLogin() {
        // 沒登入回首頁
        if (empty($_SESSION['username'])) {
            header('Location: index.php');
            exit();
        }
        // session 與資料庫的資料不一致, 需要清除 cookie
        $row = getUserFromSession($_SESSION['username']);
        if (!$row) {
            header('Location: logout.php');
            exit();
        }
        return $row;
    }

    function generateCsrfToken() {
        // 同一位使用者的 csrf token 使用同一組
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    function verifyCsrfToken() {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            header('Location: index.php');
            exit();
        }
    }

    function base64url($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // 驗證 JWT Token
    function verifyJWT($token) {
      $parts = explode('.', $token);
      if (count($parts) !== 3) return null;

      [$header, $payload, $signature] = $parts;

      // 重新計算簽章比對
      $expectedSig = base64url(hash_hmac('sha256', "$header.$payload", SECRET_KEY, true));
      if ($signature !== $expectedSig) return null;

      // 解碼 payload
      $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

      // 檢查是否過期
      if ($data['exp'] < time()) return null;

      return $data;
    }

    function getFlash() {
        if (!isset($_SESSION['flash'])) {
            return ['message' => null, 'type' => 'error', 'appSecret' => ''];
        }
        $result = [
            'message'   => $_SESSION['flash'],
            'type'      => $_SESSION['flash_type'] ?? 'error',
            'appSecret' => $_SESSION['app_secret'] ?? ''
        ];
        unset($_SESSION['flash'], $_SESSION['flash_type'], $_SESSION['app_secret']);
        return $result;
    }

    function renderAnnouncement($text) {
        if (!$text) return;
        echo '<header class="warning"><div class="warning__wrapper"><span class="warning__text marquee__content">' . escape($text) . '</span></div></header>';
    }

    function getAnnouncements() {
        global $conn;
        $sql = "SELECT * FROM `announcements` WHERE startedAt <= NOW()
    AND (endAt IS NULL OR endAt >= NOW())
    ORDER BY startedAt DESC";
        $result = $conn->query($sql);
        if (!$result) {
            return null;
        }
        $announceText = '';
        if ($result->num_rows > 0) {
            $parts = [];
            while ($announcement = $result->fetch_assoc()) {
                $parts[] = '📢 ' . $announcement['content'];
            }
            $announceText = implode(' ', $parts);
        }
        return  $announceText;
    }

    function verifyPermission($user, $permissionId) {
        return in_array($permissionId, $user['permissions']);
    }

    function getCommentAndVerifyOwner($commentId, $username) {
        global $conn;
        $sql = "SELECT * FROM `comments` WHERE id = ? AND app_key IS NULL";
        $result = executeQuery($conn, $sql, 'i', $commentId);
        if (!$result) return null;
        $comment = $result->fetch_assoc();
        if (!$comment || $comment['username'] !== $username) return null;
        return $comment;
    }

    function getAppAndVerifyOwner($appId, $username) {
        global $conn;
        $sql = "SELECT * FROM `app_keys` WHERE id = ?";
        $result = executeQuery($conn, $sql, 'i', $appId);
        if (!$result) return null;
        $app = $result->fetch_assoc();
        if (!$app || $app['username'] !== $username) return null;
        return $app;
    }

    function validateJWTToken($type = 'user') {
        $authHeader = $type === 'app'
            ? getCustomHeader('X-App-Token')
            : getCustomHeader('X-User-Token');

        if (!str_starts_with($authHeader, 'Bearer ')) {
            http_response_code(401);
            echo json_encode(["ok" => false, "message" => "Unauthorized"]);
            exit();
        }
        $token   = substr($authHeader, 7);
        $jwtData = verifyJWT($token);
        if (!$jwtData || $jwtData['type'] !== ($type === 'app' ? 'appToken' : 'userToken')) {
            http_response_code(401);
            echo json_encode(["ok" => false, "message" => "Unauthorized"]);
            exit();
        }
        return $jwtData;
    }

    function getCustomHeader($name) {
        $headers = getallheaders();
        return $headers[$name] ?? '';
    }
?>