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
        header('Location: admin_user.php');
        exit();        
    }
    $roleId = (int)$_POST['roleId'];

    if (empty($_POST['userId'])) {
        $_SESSION['flash'] = '查無此會員';
        header('Location: admin_user.php');
        exit();        
    }
    $userId = (int)$_POST['userId'];

    // 更新會員身分
    $sql = "UPDATE `users` SET roleId = ? WHERE id = ?";
    $result = executeUpdate($conn, $sql, 'ii', $roleId, $userId);
    // 不用檢查 affected_rows === 0, 因為輸入不存在的 roleId 以及 userId 會被外鍵檔下
    if (!$result['success']) {
        $_SESSION['flash'] = '更新會員身分失敗';
        header('Location: admin_user.php');   
        exit();
    }
    $_SESSION['flash'] = '更新會員身分成功';
    $_SESSION['flash_type'] = 'success';
    header('Location: admin_user.php');
    exit();
?>