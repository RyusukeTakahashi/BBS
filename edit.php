<?php
// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$message_id = null;
$mysqli = null;
$sql = null;
$res = null;
$error_message = array();
$message_data = array();

session_start();

if(!isset($_SESSION['user'])) {
	header("Location: login.php");
  }
if( !empty($_GET['message_id']) && empty($_POST['message_id']) ) {
	$message_id = (int)htmlspecialchars($_GET['message_id'], ENT_QUOTES);

	// DBとの接続
	include_once 'dbconnect.php';

	// 接続エラーの確認
	if( $mysqli->connect_errno ) {
		$error_message[] = 'データベースの接続に失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
	} else {
		// データの読み込み
		$sql = "SELECT * FROM message WHERE id = $message_id";

		$res = $mysqli->query($sql);

		if( $res ) {
			$message_data = $res->fetch_assoc();
		} else {
			// データが読み込めなかったら一覧に戻る
			header("Location: ./admin.php");
		}
		$mysqli->close();
	}

} elseif( !empty($_POST['message_id']) ) {
	$message_id = (int)htmlspecialchars( $_POST['message_id'], ENT_QUOTES);
	if( empty($_POST['view_name']) ) {
		$error_message[] = '表示名を入力してください。';
	} else {
		$message_data['view_name'] = htmlspecialchars($_POST['view_name'], ENT_QUOTES);
	}

	if( empty($_POST['message']) ) {
		$error_message[] = 'メッセージを入力してください。';
	} else {
		$message_data['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);
	}

	if( empty($error_message) ) {
		// DBとの接続
		include_once 'dbconnect.php';

		// 接続エラーの確認
		if( $mysqli->connect_errno ) {
			$error_message[] = 'データベースの接続に失敗しました。 エラー番号 ' . $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
		} else {
			$sql = "UPDATE message SET view_name = '$message_data[view_name]', message= '$message_data[message]' WHERE id =  $message_id";
			$res = $mysqli->query($sql);
		}

		$mysqli->close();

		// 更新に成功したら一覧に戻る
		if( $res ) {
			header("Location: ./admin.php");
		}
	}
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>投稿の編集</title>
</head>
<body>
<h1>投稿の編集</h1>
<?php if( !empty($error_message) ): ?>
	<ul class="error_message">
		<?php foreach( $error_message as $value ): ?>
			<li>・<?php echo $value; ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
<form method="post">
		<label for="view_name">表示名</label>
		<input id="view_name" type="text" name="view_name" value="<?php if( !empty($message_data['view_name']) ){ echo $message_data['view_name']; } ?>">
		<label for="message">ひと言メッセージ</label>
		<textarea id="message" name="message"><?php if( !empty($message_data['message']) ){ echo $message_data['message']; } ?></textarea>
	<a class="btn_cancel" href="admin.php">キャンセル</a>
	<input type="submit" name="btn_submit" value="更新">
	<input type="hidden" name="message_id" value="<?php echo $message_data['id']; ?>">
</form>
</body>
</html>