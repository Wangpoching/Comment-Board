<?php
  session_start();
  require_once('utils.php');

  // Get Announcements
  $announceText = getAnnouncements($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>留言板-註冊</title>
  <link href="register-style.css" rel="stylesheet" />
</head>
<body>
  <?php if ($announceText) { ?>
    <header class="warning">
      <div class="warning__wrapper">
        <span class="warning__text marquee__content"><?= escape($announceText) ?></span>
      </div>
    </header>
  <?php } ?>
  <main>
    <div class="title">
      <h1>註冊</h1>
      <div class="buttons">
        <a class="back-btn button" href="index.php">回留言板</a>
        <a class="login-btn button" href="login.php">登入</a>
      </div>
    </div>
    <form class="register-area" method="post" action="handle_register.php">
      <div class="error-msg hidden">
        <?php if (isset($_SESSION['flash'])) {
          echo $_SESSION['flash'];
          unset($_SESSION['flash']);
        } ?>
      </div>
      <div class="explain">* 必填</div>
      <div class="must">
        <label for="username">使用者名稱：</label>
        <input type="text" id="username" name="username" >
      </div>
      <div class="must">
        <label for="nickname">暱稱：</label>
        <input type="text" id="nickname" name="nickname" >
      </div>
      <div class="must">
        <label for="password">密碼：</label>
        <input type="password" id="password" name="password" >
      </div>
      <div class="must">
        <label for="password_confirm">確認密碼：</label>
        <input type="password" id="password_confirm" name="password_confirm" >
      </div>
      <div>
        <input class="button" type="submit" value="提交" />
      </div>
    </form>
  </main>
</body>
<script>
  const error = document.querySelector('.error-msg')
  if (error.innerText !== '') {
    error.classList.remove('hidden')
    setTimeout(() => {
      error.classList.add('hidden')
    }, 2000)
  }

  document.querySelector('form').addEventListener('submit', (e) => {
    const pwEle = document.querySelector('input[name="password"]')
    const pwConfirmEle = document.querySelector('input[name="password_confirm"]')
    if (pwEle.value !== pwConfirmEle.value) {
      e.preventDefault()
      error.classList.remove('hidden')
      error.innerText = '確認密碼輸入錯誤'
      setTimeout(() => {
        error.classList.add('hidden')
      }, 2000)
    }
  })
</script>
<script src="marquee.js"></script>
</html>
