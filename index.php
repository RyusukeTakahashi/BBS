<?php
session_start();
if( isset($_SESSION['user']) != "") {
  // ログイン済みの場合はリダイレクト
  header("Location: admin.php");
}

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$message_array = array();
$error_message = array();

// DBとの接続
include_once 'dbconnect.php';

// 接続エラーの確認
if( $mysqli->connect_errno ) {
	$error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {
	$sql = "SELECT username,view_name,message,post_date FROM message ORDER BY post_date DESC";
	$res = $mysqli->query($sql);
    if( $res ) {
		$message_array = $res->fetch_all(MYSQLI_ASSOC);
    }
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="stylesheet.css">
<title>BBS</title>
</head>
<body>
<header>
  <div class="header-right">
    <a href="register.php">新規登録</a>
    <a href="login.php" class="login">ログイン</a>
  </div>
  <div class="header-left">
    <p>DBBS</p>
  </div>
</header>
<div class='Top'>
  <p>Let me share this whole new world with you..</p>
</div>
<div class='Middle'>
    <div class='greeting'>
    <h1>DBBS</h1>
    <p class='greeting1'>-Disney Bulletin Board System-</p>
    <p> </p>
    <p class='greeting2'>ディズニー関連記事専用のひとこと掲示板になります。</p>
    <p class='greeting3'>マナーを守ってお楽しみください。</p>
  </div>
</div>
<hr>
  <?php if( !empty($error_message) ): ?>
    <ul class="error_message">
		<?php foreach( $error_message as $value ): ?>
            <li>・<?php echo $value; ?></li>
		<?php endforeach; ?>
    </ul>
  <?php endif; ?>

<section>
<?php if( !empty($message_array) ){ ?>
<?php foreach( $message_array as $value ){ ?>
<article>
<div class="kakomi-smart1">
  <div class="title-smart1">
    <p><?php echo $value['view_name']; ?></p>
  </div>
    <p><?php echo $value['username']; ?></p>
    <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
    <p><?php echo nl2br($value['message']); ?></p>
</div>
</article>
<?php } ?>
<?php } ?>
</section>
</body>