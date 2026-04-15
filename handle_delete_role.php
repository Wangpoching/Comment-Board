<?php
    session_start();
    require_once('conn.php');
    require_once('utils.php');

    verifyCsrfToken();

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

    $sql = "DELETE FROM `roles` WHERE id = ?";
    $result = executeUpdate($conn, $sql, 'i', $roleId);
    if (!$result['success'] || $result['affected_rows'] == 0) {
        $_SESSION['flash'] = '刪除角色失敗';
        header('Location: admin_role.php');   
        exit();
    }
    $_SESSION['flash'] = '刪除身分成功';
    $_SESSION['flash_type'] = 'success';
    header('Location: admin_role.php');
    exit();
?>