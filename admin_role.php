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
  // 撈取所有角色跟權限
  $sql = "SELECT r.id as roleId, r.name as roleName, p.id as permissionId, p.name as permissionName
    FROM roles r 
    LEFT JOIN role_permissions rp ON r.id = rp.roleId
    LEFT JOIN permissions p ON rp.permissionId = p.id
  ";
  $rolesResult = $conn->query($sql);
  if (!$rolesResult) {
    die('系統出錯');
  }
  $roles = [];
  while ($roleRow = $rolesResult->fetch_assoc()) {
      $roleId = $roleRow['roleId'];
      if (!isset($roles[$roleId])) {
          $roles[$roleId] = [
              'id' => $roleId,
              'name' => $roleRow['roleName'],
              'permissions' => []
          ];
      }
      // 因為 left join 所以有可能有 role 是沒有權限的~
      if ($roleRow['permissionId']) {
          $roles[$roleId]['permissions'][] = [
              'id' => $roleRow['permissionId'],
              'name' => $roleRow['permissionName']
          ];
      }
  }
  // 撈取所有權限
  $sql = "SELECT * FROM `permissions`";
  $permissionsResult = $conn->query($sql);
  if (!$permissionsResult) {
    die('系統出錯');
  }
  $permissions = [];
  while ($permRow = $permissionsResult->fetch_assoc()) {
    $permissions[] = $permRow;
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
  <title>留言板-後台-身分管理</title>
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
        <div class="page-title">身分管理</div>
        <div class="page-subtitle">管理角色與對應的操作權限</div>
      </div>
      <a href="admin_role_add.php" class="add-btn">
        ＋ 新增角色
      </a>
    </div>
    <?php if ($flash): ?>
      <div class="<?= $flashType === 'success' ? 'success-msg' : 'error-msg' ?>">
        <span class="error-icon"><?= $flashType === 'success' ? '✓' : '⚠' ?></span>
        <?= escape($flash) ?>
      </div>
    <?php endif; ?>
    <div class="card">
      <table>
        <thead>
          <tr>
            <th>角色名稱</th>
            <?php foreach($permissions as $permission): ?>
              <th><?= escape($permission['name']) ?></th>
            <?php endforeach; ?>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($roles as $role): ?>
            <tr>
              <td>
                <div class="role-name"><?= $role['name'] ?></div>
              </td>
              <?php foreach ($permissions as $permission): ?>
                <td><span class="check">
                <?php
                  $rolePermissions = $role['permissions'];
                  $hasPermission = !empty(array_filter(
                    $rolePermissions,
                    fn($p) => $p['id'] === $permission['id']
                  ))
                ?>
                <?= $hasPermission ? '✓' : '✗' ?>
                </span></td>
              <?php endforeach; ?>
              <td>
                <div class="actions">
                  <a href="admin_role_edit.php?id=<?= escape($role['id']) ?>" class="action-btn edit">編輯</a>
                  <form method="post" action="handle_delete_role.php">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
                    <input type="hidden" name="roleId" value="<?= escape($role['id']) ?>" />
                    <input type="submit" class="action-btn delete" value="刪除" />
                  </form> 
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
<script src="flash.js"></script>
</html>