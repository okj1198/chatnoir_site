<?php
// /mail_relay.php
declare(strict_types=1);

/* ===== ここだけあなたの環境に合わせてください ===== */
$FROM_ADDRESS = "info@le-chatnoir.jp";        // 送信元（コントロールパネルで作成済みの実在アドレス）
$TO_ADDRESS   = "info@le-chatnoir.jp";        // 送信先（まずは同じアドレス推奨。転送は管理画面で）
$THANKS_URL   = "https://www.le-chatnoir.jp/thanks.html"; // 送信後に遷移するページ
/* ==================================================== */

mb_language("Japanese");
mb_internal_encoding("UTF-8");

function postv(string $k, string $def=""): string { return isset($_POST[$k]) ? trim((string)$_POST[$k]) : $def; }
function sanitize_header(string $s): string { return str_replace(["\r","\n"], " ", $s); }
function norm_newlines(string $s): string { return preg_replace("/\r\n|\r|\n/u", "\n", $s); }
// JISに無い文字（絵文字など）を置換して文字化け回避
function strip_emoji(string $s): string { return preg_replace('/[\x{1F000}-\x{1FAFF}\x{200D}\x{FE0F}]/u', '□', $s); }

// --- ハニーポット（Botなら無言で終了）
if (postv('website') !== '') { http_response_code(204); exit; }

// --- フォームの実項目名で受け取り（※ e-mail はハイフンつき）
$name     = postv('name');
$email    = postv('e-mail');
$person   = postv('person');
$requests = postv('special requests');
$day      = postv('day');
$month    = postv('month');
$year     = postv('year');
$hour     = postv('hour');

// --- 必須チェック
$errs = [];
if ($name === '') $errs[] = 'name';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errs[] = 'e-mail';
if ($person === '') $errs[] = 'person';
if ($day === '' || $month === '' || $year === '' || $hour === '') $errs[] = 'date/time';

if ($errs) {
  header('Content-Type: text/html; charset=UTF-8');
  http_response_code(400);
  echo "<h2>必須項目が入力されていません。</h2><p>" . htmlspecialchars(implode(', ', $errs), ENT_QUOTES, 'UTF-8') . "</p>";
  exit;
}

$dt_formatted = sprintf('%04d-%02d-%02d %s', (int)$year, (int)$month, (int)$day, $hour);

// 付帯情報（任意）
$ip  = $_SERVER['REMOTE_ADDR']     ?? '';
$ua  = $_SERVER['HTTP_USER_AGENT'] ?? '';
$ref = $_SERVER['HTTP_REFERER']    ?? '';

/* --------- 本文（UTF-8）→ ISO-2022-JPへ変換して送信 --------- */
$body_utf8 = norm_newlines(
"[Reservation]

name : {$name}
e-mail : {$email}
person : {$person}
special requests : {$requests}
date : {$dt_formatted}

-- meta --
IP: {$ip}
User Agent: {$ua}
Referrer: {$ref}
");
$body_utf8 = strip_emoji($body_utf8);

// 件名：フォームから来た subject を優先、未指定なら既定
$subject_utf8 = postv('subject', '[Le Chat Noir] Reservation / ご予約');

// 文字コード変換
$subject_jis = mb_encode_mimeheader($subject_utf8, "ISO-2022-JP-MS");
$body_jis    = mb_convert_encoding($body_utf8, "ISO-2022-JP-MS", "UTF-8");

// ヘッダ
$headers  = "From: " . sanitize_header($FROM_ADDRESS) . "\r\n";
$headers .= "Reply-To: " . sanitize_header($email) . "\r\n";
$headers .= "Content-Type: text/plain; charset=ISO-2022-JP-MS\r\n";
$headers .= "Content-Transfer-Encoding: 7bit\r\n";

// 共有サーバでは -f（エンベロープFrom）を実在アドレスにする必要があることが多い
$additional_params = "-f {$FROM_ADDRESS}";

// 送信
$ok = @mb_send_mail($TO_ADDRESS, $subject_jis, $body_jis, $headers, $additional_params);

if ($ok) {
  header("Location: {$THANKS_URL}", true, 303);
  exit;
} else {
  header('Content-Type: text/html; charset=UTF-8');
  http_response_code(500);
  echo "<h2>送信に失敗しました。</h2>";
  echo "<p>サーバのフォームメール許可設定（送信元/送信先の登録）が完了しているかをご確認ください。</p>";
  exit;
}