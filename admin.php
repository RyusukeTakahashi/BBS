<?php
// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$clean = array();

session_start();

include_once 'dbconnect.php';

// ユーザーIDからユーザー名を取り出す
$query = "SELECT * FROM users WHERE user_id=".$_SESSION['user']."";
$result = $mysqli->query($query);

$result = $mysqli->query($query);
if (!$result) {
  print('クエリーが失敗しました。' . $mysqli->error);
  $mysqli->close();
  exit();
}

// ユーザー情報の取り出し
while ($row = $result->fetch_assoc()) {
  $username = $row['username'];
  $email = $row['email'];
}

// データベースの切断
$result->close();

if(!isset($_SESSION['user'])) {
	header("Location: login.php");
  }

  if( !empty($_POST['btn_submit']) ) {
	// 表示名の入力チェック
	if( empty($_POST['view_name']) ) {
		$error_message[] = '表示名を入力してください。';
	} else {
		$clean['view_name'] = htmlspecialchars( $_POST['view_name'], ENT_QUOTES);

		// セッションに表示名を保存
		$_SESSION['view_name'] = $clean['view_name'];
	}

	// メッセージの入力チェック
	if( empty($_POST['message']) ) {
		$error_message[] = 'ひと言メッセージを入力してください。';
	} else {
		$clean['message'] = htmlspecialchars( $_POST['message'], ENT_QUOTES);
	}

	if( empty($error_message) ) {
		// DBとの接続
		include_once 'dbconnect.php';

		// 接続エラーの確認
		if( $mysqli->connect_errno ) {
			$error_message[] = '書き込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
		} else {
			// 文字コード設定
			$mysqli->set_charset('utf8');

			// 書き込み日時を取得
			$now_date = date("Y-m-d H:i:s");

			// データを登録するSQL作成
			$sql = "INSERT INTO message (email,username,view_name, message, post_date) VALUES ('$email','$username', '$clean[view_name]', '$clean[message]', '$now_date')";

			// データを登録
			$res = $mysqli->query($sql);

			if( $res ) {
				$_SESSION['success_message'] = 'メッセージを書き込みました。';
			} else {
				$error_message[] = '書き込みに失敗しました。';
			}

			// データベースの接続を閉じる
			$mysqli->close();
		}
		header('Location: ./admin.php');
	}
}

// データベースに接続


// 接続エラーの確認
if( $mysqli->connect_errno ) {
	$error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {
	$sql = "SELECT id,email,username,view_name,message,post_date FROM message ORDER BY post_date DESC";
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
<link rel="stylesheet" href="stylesheet2.css">
<title>BBS</title>
</head>
<body>
<header>
  <div class="header-right">
	<a href="logout.php?logout">ログアウト</a>
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

 	<div class='pos'>
	<form method="post">
		<label for="view_name">記事名</label>
		<input id="view_name" type="text" name="view_name" value="<?php if( !empty($_SESSION['view_name']) ){ echo $_SESSION['view_name']; } ?>">
		<label for="message">記事</label>
		<textarea id="message" name="message"></textarea>
		<input type="submit" name="btn_submit" value="投稿">
	</form>
	</div>

	<?php if( !empty($error_message) ): ?>
	<ul class="error_message">
		<?php foreach( $error_message as $value ): ?>
			<li>・<?php echo $value; ?></li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
	<?php if( empty($_POST['btn_submit']) && !empty($_SESSION['success_message']) ): ?>
    <p class="success_message"><?php echo $_SESSION['success_message']; ?></p>
    <?php unset($_SESSION['success_message']); ?>
	<?php endif; ?>

</div>
<hr>
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
		<?php if($value['email'] == $email): ?>
		<p><a href="edit.php?message_id=<?php echo $value['id']; ?>">編集</a>&nbsp;&nbsp;<a href="delete.php?message_id=<?php echo $value['id']; ?>">削除</a></p>
		<?php endif; ?>
	<p><?php echo nl2br($value['message']); ?></p>
</div>
</article>
<?php } ?>
<?php } ?>
</section>
</body>
</html>