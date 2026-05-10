<?php
// contact_wine.php
mb_language("Japanese");
mb_internal_encoding("UTF-8");

if (!empty($_POST['website'])) {
  http_response_code(400);
  exit('Bad Request');
}
function field($key){ return isset($_POST[$key]) ? trim($_POST[$key]) : ''; }

$name      = field('name');
$email     = field('email');
$people    = field('people');
$phone     = field('phone');
$date1     = field('date_pref');
$date2     = field('date_alt');
$tasting   = field('tasting');
$message   = field('message');
$topicsArr = isset($_POST['topics']) && is_array($_POST['topics']) ? $_POST['topics'] : [];

if ($name === '' || $email === '' || $people === '' || $date1 === '' || $tasting === '' || count($topicsArr) === 0) {
  http_response_code(400);
  echo '必須項目が未入力です。';
  exit;
}

$to = 'okajo@le-chatnoir.jp';
$subject = '受講の申し込み (Le Chat Noir)';
$topics = implode(', ', $topicsArr);

$body = "【お名前】\n{$name}\n\n"
      . "【人数】\n{$people}\n\n"
      . "【希望日程】\n第一希望: {$date1}\n第二希望: {$date2}\n\n"
      . "【希望講習内容】\n{$topics}\n\n"
      . "【テイスティング】\n{$tasting}\n\n"
      . "【電話番号】\n{$phone}\n\n"
      . "【メール】\n{$email}\n\n"
      . "【補足】\n{$message}\n\n"
      . "----\n送信元: Le Chat Noir サイト フォーム\n";

$headers  = "From: Le Chat Noir <no-reply@le-chatnoir.jp>\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";

$ok = @mb_send_mail($to, $subject, $body, $headers);

if ($ok) {
  echo '<!doctype html><meta charset="utf-8"><title>送信完了</title><style>body{font-family:-apple-system,system-ui,"Noto Sans JP",sans-serif;padding:24px;}</style><h1>送信が完了しました</h1><p>ありがとうございました。内容を確認後、折り返しご連絡いたします。</p><p><a href="/">トップへ戻る</a></p>';
} else {
  http_response_code(500);
  echo '送信に失敗しました。恐れ入りますが、メールでご連絡ください：okajo@le-chatnoir.jp';
}
?>