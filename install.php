<?php error_reporting(E_ERROR | E_PARSE); ?>
<html>
<head>
  <meta charset="UTF-8">
  <title>اسکریپت فرم پرداخت پِی</title>
  <link rel="stylesheet" href="./install.css">
</head>
<body>
<div id="app">
  <div class="container">
    <header class="navbar">
      <section class="navbar-section">
        <a href="https://formpardakht.com" class="navbar-brand mr-2" target="_blank">اسکریپت فرم پرداخت پِی</a>
      </section>
      <section class="navbar-section">
        <a href="https://formpardakht.com/blog/help" class="btn btn-link" target="_blank">راهنمای نصب آسان</a>
      </section>
    </header>
  </div>
  <div class="container">
    <div class="columns">
      <div class="column col-3"></div>
      <div class="column col-6">
        <form method="post" id="form">
          <div class="card">
            <div class="card-header">
              <div class="card-title h5">نصب آسان</div>
            </div>
            <div class="card-body">
              <div id="message">
              <?php
              if (isset($_POST['site_url'])) {
                  install();
              }
              ?>
              </div>
              <div class="columns mt-2">
                <div class="column col-lg-6">
                  <div class="form-group">
                    <label class="form-label" for="txt-url">آدرس سایت</label>
                    <input class="form-input ltr" type="text" id="txt-url" placeholder="http://" name="site_url" value="<?= $_POST['site_url'] ?>">
                  </div>
                </div>
                <div class="column col-lg-6">
                  <div class="form-group">
                    <label class="form-label" for="txt-title">عنوان سایت</label>
                    <input class="form-input" type="text" id="txt-title" name="site_title" value="<?= $_POST['site_title'] ?>">
                  </div>
                </div>
              </div>
              <div class="columns mt-2">
                <div class="column col-lg-6">
                  <div class="form-group">
                    <label class="form-label" for="txt-db-host">آدرس دیتابیس</label>
                    <input class="form-input ltr" type="text" id="txt-db-host" name="db_host" value="<?= $_POST['db_host'] ? $_POST['db_host'] : 'localhost' ?>">
                  </div>
                </div>
                <div class="column col-lg-6">
                  <div class="form-group">
                    <label class="form-label" for="txt-db-name">نام دیتابیس</label>
                    <input class="form-input ltr" type="text" id="txt-db-name" name="db_name" value="<?= $_POST['db_name'] ?>">
                  </div>
                </div>
              </div>
              <div class="columns mt-2">
                <div class="column col-lg-6">
                  <div class="form-group">
                    <label class="form-label" for="txt-db-username">نام کاربری دیتابیس</label>
                    <input class="form-input ltr" type="text" id="txt-db-username" name="db_username" value="<?= $_POST['db_username'] ?>">
                  </div>
                </div>
                <div class="column col-lg-6">
                  <div class="form-group">
                    <label class="form-label" for="txt-db-password">کلمه عبور دیتابیس</label>
                    <input class="form-input ltr" type="text" id="txt-db-password" name="db_password" value="<?= $_POST['db_password'] ?>">
                  </div>
                </div>
              </div>
              <div class="columns">
                <div class="column col-lg-6">
                  <div class="form-group">
                    <label class="form-label" for="txt-admin-email">آدرس ایمیل مدیر سایت</label>
                    <input class="form-input ltr" type="text" id="txt-admin-email" name="admin_email" value="<?= $_POST['admin_email'] ?>">
                  </div>
                </div>
                <div class="column col-lg-6">
                  <div class="form-group">
                    <label class="form-label" for="txt-admin-password">کلمه عبور مدیر سایت</label>
                    <input class="form-input ltr" type="password" id="txt-admin-password" name="admin_password" value="<?= $_POST['admin_password'] ?>">
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button id="btn-submit" type="submit" class="btn btn-primary" name="install" onclick="submitForm()">شروع نصب</button>
            </div>
          </div>
        </form>
      </div>
      <div class="column col-3"></div>
    </div>
  </div>
</div>
<script>
  function submitForm() {
    document.getElementById('btn-submit').disabled = true;
    document.getElementById('btn-submit').innerText = 'لطفا صبر کنید...';
    document.getElementById('message').innerHTML = "<div class='toast toast-primary mb-2'>اسکریپت در حال نصب می باشد. عملیات نصب ممکن است چند دقیقه طول بکشد.</div>";
    document.getElementById('form').submit();
  }
</script>
</body>
</html>

<?php

function install()
{
    ini_set('max_execution_time', '3000');

    $input = $_POST;
    if (!$input['site_url'] || !$input['site_title'] || !$input['db_host'] || !$input['db_name'] || !$input['db_username'] || !$input['db_password'] || !$input['admin_email'] || !$input['admin_password']) {
        echo "<div class='toast toast-error'>لطفا همه ی فیلد ها را پر کنید</div>";
        return;
    }

    $conn = connectToDB($input['db_host'], $input['db_username'], $input['db_password'], $input['db_name']);

    if ($conn->connect_error) {
        echo "<div class='toast toast-error'>اطلاعات دیتابیس اشتباه می باشد</div>";
        return;
    }

    $file = file_get_contents('http://formpardakht.com/latest.zip', false);
    file_put_contents(__DIR__ . '/latest.zip', $file);
    $zip = new ZipArchive;
    if ($zip->open(__DIR__ . '/latest.zip')) {
        $zip->extractTo(__DIR__);
        $zip->close();
    }
    $sampleConfig = require(__DIR__ . '/core/config-sample.php');

    foreach ($sampleConfig as $key => $value) {
        $sampleConfig[$key] = '"' . $value . '",';
    }
    $sampleConfig['APP_URL'] = '"' . $input['site_url'] . '",';
    $sampleConfig['DB_HOST'] = '"' . $input['db_host'] . '",';
    $sampleConfig['DB_DATABASE'] = '"' . $input['db_name'] . '",';
    $sampleConfig['DB_USERNAME'] = '"' . $input['db_username'] . '",';
    $sampleConfig['DB_PASSWORD'] = '"' . $input['db_password'] . '",';

    $sampleConfig = print_r($sampleConfig, true);
    $sampleConfig = str_replace("[", '"', $sampleConfig);
    $sampleConfig = str_replace("]", '"', $sampleConfig);

    file_put_contents(__DIR__ . '/core/config.php', '<?php return ' . $sampleConfig . ';');

    if (file_exists(__DIR__ . '/core/.env')) {
        unlink(__DIR__ . '/core/.env');
    }

    echo "<div class='toast toast-primary'>در حال هدایت به صفحه تکمیل نصب اسکریپت...</div>";
    echo "<script>setTimeout(function() {window.location.href = './install/complete-ez?" . http_build_query($input) . "'}, 2000)</script>";
    return;
}

function connectToDB($host, $username, $password, $database)
{
    return new mysqli($host, $username, $password, $database);
}

?>