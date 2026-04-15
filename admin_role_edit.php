<?php
  session_start();
  require_once('utils.php');
  require_once('conn.php');

  // 驗證身份
  if (empty($_SESSION['username'])) {
      header('Location: logout.php');
      exit();
  }
  $row = getUserFromSession($_SESSION['username']);
  if (!$row) {
      header('Location: logout.php');
      exit();
  }

  // 驗證是不是 Admin
  if ($row['roleName'] !== ADMIN_ROLE_NAME) {
      header('Location: index.php');
      exit();
  }

  if (empty($_GET['id'])) {
    header('Location: admin_role.php');
    exit();    
  }
  $roleId = (int)$_GET['id'];

  // 撈取所有權限
  $sql = "SELECT * FROM `permissions`";
  $permissionsResult = $conn->query($sql);
  if (!$permissionsResult) {
    die('系統出錯');
  }
  $permissions = [];
  while ($row = $permissionsResult->fetch_assoc()) {
    $permissions[] = $row;
  }

  // 撈取該身分的權限
  $sql = "SELECT r.id as roleId, r.name as roleName, p.id as permissionId, p.name as permissionName
    FROM roles r 
    LEFT JOIN role_permissions rp ON r.id = rp.roleId
    LEFT JOIN permissions p ON rp.permissionId = p.id
    WHERE r.id = ?";
  $result = executeQuery($conn, $sql, 'i', $roleId);
  if (!$result) {
      header('Location: admin_role.php');
      exit();    
  }
  if ($result->num_rows === 0) {
      // 找不到這個身分
      header('Location: admin_role.php');
      exit();     
  }
  $role = [];
  while ($row = $result->fetch_assoc()) {
    if (!isset($role['name'])) {
      $role['name'] = $row['roleName'];
      $role['id'] = $row['roleId'];
      $role['permissions'] = [];
    }
    if ($row['permissionId']) {
        $role['permissions'][] = [
          'id' => $row['permissionId'],
          'name' => $row['permissionName']
        ];
    }
  }

  // 讀取 flash 訊息
  $flashData = getFlash();
  $flash = $flashData['message'];
  $flashType = $flashData['type'];
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>留言板-後台-編輯身分</title>
  <link href="admin.css" rel="stylesheet" />
</head>
<body>
  <div class="topbar">
    <div class="topbar-logo">管理<span>後台</span></div>
    <div class="topbar-user">
      <div class="avatar">A</div>
      <span>admin</span>
      <a href="logout.php" class="logout-btn">登出</a>
    </div>
  </div>

  <nav class="sidebar">
    <div class="sidebar-section">
      <div class="sidebar-label">管理功能</div>
      <a href="admin_role.php" class="sidebar-item active">
        <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM6 20v-1a6 6 0 0 1 12 0v1"/>
        </svg>
        身分管理
      </a>
      <a href="admin_user.php" class="sidebar-item">
        <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        會員管理
      </a>
    </div>
    <div class="sidebar-back">
      <a href="index.php">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/>
          <path d="M9 21V12h6v9"/>
        </svg>
        回到留言板
      </a>
    </div>
  </nav>

  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">編輯身分</div>
        <div class="page-subtitle">修改身分名稱與對應的操作權限</div>
      </div>
    </div>

    <div class="form-card">
      <form method="POST" action="handle_alter_role.php">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
        <input type="hidden" name="roleId" value="<?= (int)$role['id'] ?>" />

        <?php if ($flash): ?>
          <div class="<?= $flashType === 'success' ? 'success-msg' : 'error-msg' ?>">
            <span class="error-icon"><?= $flashType === 'success' ? '✓' : '⚠' ?></span>
            <?= escape($flash) ?>
          </div>
        <?php endif; ?>

        <div class="form-group">
            <label class="form-label" for="roleName">身分名稱</label>
            <input
              class="form-input"
              type="text"
              id="roleName"
              name="roleName"
              value="<?= escape($role['name']) ?>"
          />
        </div>

        <div class="form-group">
          <div class="form-label">權限設定</div>
          <div class="permission-list">
          <?php foreach ($permissions as $permission): ?>
          <?php
            $hasPermission = !empty(array_filter(
              $role['permissions'],
              fn($p) => $p['id'] == $permission['id']
            ))
          ?>
            <label class="permission-item">
              <input
                type="checkbox"
                name="permissions[]"
                value="<?= escape($permission['id']) ?>"
                <?= $hasPermission ? "checked" : '' ?>
              />
              <?= escape($permission['name']) ?>
            </label>
          <?php endforeach; ?>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="add-btn">儲存變更</button>
          <a href="admin_role.php" class="cancel-btn">取消</a>
        </div>
      </form>
    </div>
  </main>
</body>
<script src="flash.js"></script>
</html>