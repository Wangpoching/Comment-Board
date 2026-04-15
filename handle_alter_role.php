<?php
    session_start();
    require_once('conn.php');
    require_once('utils.php');

    verifyCsrfToken();

    // 需要登入
    $row = requireLogin();

    // 驗證是不是 Admin
    if ($row['roleName'] !== ADMIN_ROLE_NAME) {
        header('Location: index.php');
        exit();
    }

    // 驗證輸入
    if (empty($_POST['roleId'])) {
        $_SESSION['flash'] = '查無此身分';
        header('Location: admin_role.php');
        exit();        
    }

    $roleId = (int)$_POST['roleId'];
    if ($roleId <= 0) {
        $_SESSION['flash'] = '查無此身分';
        header('Location: admin_role.php');
        exit();        
    }

    $roleName = trim($_POST['roleName'] ?? '');
    if ($roleName === '') {
        $_SESSION['flash'] = '身分名稱不可為空';
        header("Location: admin_role_edit.php?id={$roleId}");
        exit();
    }

    if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
        $permissions = $_POST['permissions'];
        $permissions = array_map('intval', $permissions);
        $permissions = array_filter($permissions, fn($id) => $id > 0);
    } else {
        $permissions = [];
    }

    // Transaction 開始
    $conn->begin_transaction();

    try {
        // Step 1: 更改角色名稱
        $sql = "UPDATE `roles` SET `name` = ? WHERE id = ?";
        $result = executeUpdate($conn, $sql, 'si', $roleName, $roleId);
        // 不檢查 AFFECTED_ROWS 因為名字可以相同
        if (!$result['success']) {
            throw new Exception('編輯身分失敗');
        }

        // Step 2: 刪除原始身分權限
        $sql = "DELETE FROM `role_permissions` WHERE roleId = ?";
        $result = executeUpdate($conn, $sql, 'i', $roleId);
        if (!$result['success']) {
            throw new Exception('編輯身分失敗');
        }

        // Step 3: 新增權限
        if (!empty($permissions)) {
            $placeholders = implode(',', array_fill(0, count($permissions), '(?,?)'));
            $types = str_repeat('ii', count($permissions));
            $sql = "INSERT INTO `role_permissions` (`roleId`, `permissionId`) VALUES {$placeholders}";

            $params = [];
            foreach ($permissions as $permId) {
                $params[] = $roleId;
                $params[] = (int)$permId;
            }

            $result = executeUpdate($conn, $sql, $types, ...$params);
            if (!$result['success'] || $result['affected_rows'] === 0) {
                throw new Exception('編輯身分權限失敗');
            }
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['flash'] = $e->getMessage();
        header("Location: admin_role_edit.php?id={$roleId}");
        exit();
    }
    $_SESSION['flash'] = '編輯身分成功';
    $_SESSION['flash_type'] = 'success';
    header('Location: admin_role.php');
    exit();
?>