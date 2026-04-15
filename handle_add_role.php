<?php
    session_start();
    require_once('conn.php');
    require_once('utils.php');
    verifyCsrfToken();

    // 登入檢查
    $row = requireLogin();

    // 驗證是不是 Admin
    if ($row['roleName'] !== ADMIN_ROLE_NAME) {
        header('Location: index.php');
        exit();
    }

    // 驗證輸入
    $roleName = trim($_POST['roleName'] ?? '');
    if ($roleName === '') {
        $_SESSION['flash'] = '身分名稱不可為空';
        header('Location: admin_role_add.php');
        exit();
    }

    if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
        // 清洗 permissionIds
        $permissions = $_POST['permissions'];
        $permissions = array_map('intval', $permissions);
        $permissions = array_filter($permissions, fn($id) => $id > 0);
    } else {
        $permissions = [];
    }

    // Transaction 開始
    $conn->begin_transaction();

    try {
        // Step 1: 新增角色
        $sql = "INSERT INTO `roles` (`name`) VALUES (?)";
        $result = executeUpdate($conn, $sql, 's', $roleName);
        if (!$result['success'] || $result['affected_rows'] === 0) {
            if ($result['errno'] === 1062) {  // MySQL duplicate entry
                throw new Exception('此身分名稱已存在');
            }
            throw new Exception('新增身分失敗');
        }
        $newRoleId = $result['insert_id'];

        // Step 2: 新增角色權限
        if (!empty($permissions)) {
            $placeholders = implode(',', array_fill(0, count($permissions), '(?,?)'));
            $types = str_repeat('ii', count($permissions));
            $sql = "INSERT INTO `role_permissions` (`roleId`, `permissionId`) VALUES {$placeholders}";

            $params = [];
            foreach ($permissions as $permId) {
                $params[] = $newRoleId;
                $params[] = (int)$permId;
            }

            $result = executeUpdate($conn, $sql, $types, ...$params);
            if (!$result['success'] || $result['affected_rows'] === 0) {
                throw new Exception('新增身分失敗');
            }
        }

        $conn->commit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['flash'] = $e->getMessage();
        header('Location: admin_role_add.php');
        exit();        
    }
    $_SESSION['flash'] = '新增身分成功';
    $_SESSION['flash_type'] = 'success';
    header('Location: admin_role.php');
    exit();
?>