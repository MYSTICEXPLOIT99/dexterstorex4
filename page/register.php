<div class="container-sm py-4" data-aos="fade-up">
    <div class="auth-split-card">
        <!-- Left Column (Celestial Orbit Logo & Actions) -->
        <aside class="auth-aside auth-aside-celestial">
            <!-- Telemetry Header -->
            <div class="auth-telemetry-strip">
                <span><span class="auth-telemetry-dot"></span>ระบบ // ยืนยันตัวตน</span>
                <span>ระดับความปลอดภัย // สูงสุด</span>
            </div>

            <div class="auth-celestial-wrap">
                <div class="auth-orbit-ring ring-1">
                    <span class="orbit-node node-1">✦</span>
                    <span class="orbit-node node-2">★</span>
                    <span class="orbit-node node-3">⬡</span>
                </div>
                <div class="auth-orbit-ring ring-2">
                    <span class="orbit-node node-4">✧</span>
                    <span class="orbit-node node-5">❖</span>
                </div>
                <div class="auth-orbit-ring ring-3"></div>
                <div class="auth-celestial-core">
                    <img src="<?= htmlspecialchars(!empty($config['logo']) ? $config['logo'] : 'https://image.fivempro.xyz/storage/e67c57d15b804ab9ad2df5406691aea7.png') ?>" alt="<?= htmlspecialchars($site_name ?? 'Store') ?>" class="auth-celestial-logo" onerror="this.src='assets/icon/store.png'">
                </div>
            </div>

            <!-- Telemetry Chips -->
            <div class="auth-telemetry-chips">
                <span class="auth-tele-chip"><i class="fa-solid fa-microchip me-1 text-warning"></i> CORE: 4.2GHz</span>
                <span class="auth-tele-chip"><i class="fa-solid fa-shield-halved me-1 text-info"></i> RSA: 4096-BIT</span>
                <span class="auth-tele-chip"><i class="fa-solid fa-signal me-1 text-success"></i> PING: 0.02ms</span>
            </div>

            <!-- 2 Buttons Underneath -->
            <div class="auth-aside-actions">
                <a href="?page=shop" class="btn-auth-aside-shop">
                    <i class="fa-solid fa-bag-shopping me-1"></i> เข้าสู่ร้านค้า
                </a>
                <?php if (!empty($config['contact_discord'])): ?>
                    <a href="<?= htmlspecialchars($config['contact_discord']) ?>" target="_blank" class="btn-auth-aside-contact">
                        <i class="fa-brands fa-discord me-1"></i> ติดต่อแอดมิน
                    </a>
                <?php else: ?>
                    <a href="?page=contact" class="btn-auth-aside-contact">
                        <i class="fa-solid fa-headset me-1"></i> ติดต่อแอดมิน
                    </a>
                <?php endif; ?>
            </div>
        </aside>

        <!-- Right Column (Hacker Terminal Form) -->
        <div class="auth-panel">
            <a href="?page=home" class="auth-modal-close-btn" aria-label="Close">✕</a>
            
            <div class="mb-3">
                <div class="auth-header-eyebrow">// ระบบเข้าสู่ระบบ & สมัครสมาชิก</div>
                <h3 class="auth-header-title" id="authTitle">สมัคร<span>สมาชิก</span></h3>
                <p class="auth-header-sub" id="authSub">กรอกข้อมูลเพื่อสร้างบัญชีผู้ใช้งานใหม่</p>
            </div>

            <!-- Tab Switcher -->
            <div class="auth-tabs-wrap">
                <button type="button" class="auth-tab-pill active" id="tabReg" onclick="setAuthTab('register')">สมัครสมาชิก</button>
                <button type="button" class="auth-tab-pill" id="tabLog" onclick="setAuthTab('login')">เข้าสู่ระบบ</button>
            </div>

            <form id="authForm" onsubmit="handleAuthSubmit(event)">
                <!-- Inline Error Alert Box -->
                <div id="authAlertBox" style="display:none; padding:12px 14px; border-radius:12px; margin-bottom:18px; font-size:0.875rem; font-weight:500; align-items:center; gap:10px; background:rgba(239, 68, 68, 0.15); border:1px solid rgba(239, 68, 68, 0.4); color:#fca5a5; box-shadow:0 4px 15px rgba(239,68,68,0.2);">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size:1.1rem; color:#ef4444; flex-shrink:0;"></i>
                    <span id="authAlertText" style="line-height:1.4;"></span>
                </div>

                <!-- Inline Success Alert Box -->
                <div id="authSuccessBox" style="display:none; padding:12px 14px; border-radius:12px; margin-bottom:18px; font-size:0.875rem; font-weight:500; align-items:center; gap:10px; background:rgba(34, 197, 94, 0.15); border:1px solid rgba(34, 197, 94, 0.4); color:#86efac; box-shadow:0 4px 15px rgba(34, 197, 94, 0.2);">
                    <i class="fa-solid fa-circle-check" style="font-size:1.1rem; color:#22c55e; flex-shrink:0;"></i>
                    <span id="authSuccessText" style="line-height:1.4;"></span>
                </div>

                <div class="auth-input-group">
                    <label class="auth-label-title">
                        <span>[01] // ชื่อผู้ใช้งาน</span>
                        <span class="auth-label-tag">ชื่อผู้ใช้</span>
                    </label>
                    <div class="auth-input-inner">
                        <i class="fa-solid fa-user-astronaut auth-input-icon"></i>
                        <input type="text" id="auth_user" class="auth-custom-input" placeholder="กรอกชื่อผู้ใช้ (อย่างน้อย 3 ตัวอักษร)" required autocomplete="username" oninput="hideAuthError(); hideAuthSuccess();">
                    </div>
                </div>

                <div class="auth-input-group">
                    <label class="auth-label-title">
                        <span>[02] // รหัสผ่าน</span>
                        <span class="auth-label-tag">รหัสผ่าน</span>
                    </label>
                    <div class="auth-input-inner">
                        <i class="fa-solid fa-shield-halved auth-input-icon"></i>
                        <input type="password" id="auth_pass" class="auth-custom-input" placeholder="กรอกรหัสผ่าน (อย่างน้อย 4 ตัวอักษร)" required autocomplete="new-password" oninput="hideAuthError(); hideAuthSuccess();">
                        <button type="button" class="auth-pass-toggle" onclick="toggleAuthPass('auth_pass', this)" tabindex="-1" aria-label="Toggle Password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="auth-input-group" id="authConfirmWrap">
                    <label class="auth-label-title">
                        <span>[03] // ยืนยันรหัสผ่าน</span>
                        <span class="auth-label-tag">ยืนยันรหัสผ่าน</span>
                    </label>
                    <div class="auth-input-inner">
                        <i class="fa-solid fa-key auth-input-icon"></i>
                        <input type="password" id="auth_pass2" class="auth-custom-input" placeholder="กรอกรหัสผ่านอีกครั้งเพื่อยืนยัน" required autocomplete="new-password" oninput="hideAuthError(); hideAuthSuccess();">
                        <button type="button" class="auth-pass-toggle" onclick="toggleAuthPass('auth_pass2', this)" tabindex="-1" aria-label="Toggle Password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-submit-btn" id="authBtn">
                    <i class="fa-solid fa-user-plus me-2"></i> สมัครสมาชิก
                </button>

                <div class="auth-switch-text" id="authSwitch">
                    มีบัญชีอยู่แล้วใช่หรือไม่? <a href="javascript:void(0)" onclick="setAuthTab('login')" class="auth-switch-link-action">เข้าสู่ระบบ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentAuthMode = 'register';

function showAuthError(msg) {
    hideAuthSuccess();
    $('#authAlertText').text(msg || 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
    $('#authAlertBox').css('display', 'flex').hide().stop(true, true).fadeIn(200);
}

function hideAuthError() {
    $('#authAlertBox').stop(true, true).fadeOut(150);
}

function showAuthSuccess(msg) {
    hideAuthError();
    $('#authSuccessText').text(msg || 'สำเร็จ');
    $('#authSuccessBox').css('display', 'flex').hide().stop(true, true).fadeIn(200);
}

function hideAuthSuccess() {
    $('#authSuccessBox').stop(true, true).fadeOut(150);
}

function toggleAuthPass(inputId, btnEl) {
    const inp = document.getElementById(inputId);
    if (!inp) return;
    if (inp.type === 'password') {
        inp.type = 'text';
        btnEl.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
    } else {
        inp.type = 'password';
        btnEl.innerHTML = '<i class="fa-solid fa-eye"></i>';
    }
}

function setAuthTab(mode) {
    hideAuthError();
    hideAuthSuccess();
    currentAuthMode = mode;
    const tabReg = document.getElementById('tabReg');
    const tabLog = document.getElementById('tabLog');
    const authTitle = document.getElementById('authTitle');
    const authSub = document.getElementById('authSub');
    const authConfirmWrap = document.getElementById('authConfirmWrap');
    const authBtn = document.getElementById('authBtn');
    const authSwitch = document.getElementById('authSwitch');

    if (mode === 'register') {
        tabReg.classList.add('active');
        tabLog.classList.remove('active');
        authTitle.innerHTML = 'สมัคร<span>สมาชิก</span>';
        authSub.innerText = 'กรอกข้อมูลเพื่อสร้างบัญชีผู้ใช้งานใหม่';
        authConfirmWrap.style.display = 'block';
        document.getElementById('auth_pass2').setAttribute('required', 'required');
        authBtn.innerHTML = '<i class="fa-solid fa-user-plus me-2"></i> สมัครสมาชิก';
        if (authSwitch) authSwitch.innerHTML = 'มีบัญชีอยู่แล้วใช่หรือไม่? <a href="javascript:void(0)" onclick="setAuthTab(\'login\')" class="auth-switch-link-action">เข้าสู่ระบบ</a>';
    } else {
        tabLog.classList.add('active');
        tabReg.classList.remove('active');
        authTitle.innerHTML = 'เข้าสู่<span>ระบบ</span>';
        authSub.innerText = 'กรอกชื่อผู้ใช้และรหัสผ่านเพื่อเข้าสู่ระบบ';
        authConfirmWrap.style.display = 'none';
        document.getElementById('auth_pass2').removeAttribute('required');
        authBtn.innerHTML = '<i class="fa-solid fa-right-to-bracket me-2"></i> เข้าสู่ระบบ';
        if (authSwitch) authSwitch.innerHTML = 'ยังไม่มีบัญชีใช่หรือไม่? <a href="javascript:void(0)" onclick="setAuthTab(\'register\')" class="auth-switch-link-action">สมัครสมาชิก</a>';
    }
}

function handleAuthSubmit(e) {
    e.preventDefault();
    hideAuthError();
    const user = $('#auth_user').val().trim();
    const pass = $('#auth_pass').val().trim();
    const pass2 = $('#auth_pass2').val().trim();
    const btn = $('#authBtn');

    if (!user || !pass) {
        showAuthError('กรุณากรอกข้อมูลให้ครบถ้วน');
        return;
    }

    if (currentAuthMode === 'register') {
        if (pass !== pass2) {
            showAuthError('รหัสผ่านทั้งสองช่องไม่ตรงกัน');
            return;
        }

        btn.attr('disabled', 'disabled').html('<i class="fa-solid fa-spinner fa-spin me-2"></i> กำลังสร้างบัญชี...');
        const formData = new FormData();
        formData.append('user', user);
        formData.append('pass', pass);
        formData.append('pass2', pass2);

        $.ajax({
            type: 'POST',
            url: 'system/register.php',
            data: formData,
            contentType: false,
            processData: false
        }).done(function (res) {
            btn.removeAttr('disabled').html('<i class="fa-solid fa-user-plus me-2"></i> สมัครสมาชิก');
            if (res.status === 'success') {
                if (window.bgMusic && typeof window.bgMusic.saveProgress === 'function') {
                    window.bgMusic.saveProgress();
                }
                showAuthSuccess('สมัครสมาชิกสำเร็จ! กำลังเข้าสู่ระบบ...');
                setTimeout(() => {
                    window.location.replace('./');
                }, 800);
            } else {
                showAuthError(res.message || 'เกิดข้อผิดพลาดในการสมัครสมาชิก');
            }
        }).fail(function () {
            btn.removeAttr('disabled').html('<i class="fa-solid fa-user-plus me-2"></i> สมัครสมาชิก');
            showAuthError('ไม่สามารถติดต่อเซิร์ฟเวอร์ได้');
        });
    } else {
        btn.attr('disabled', 'disabled').html('<i class="fa-solid fa-spinner fa-spin me-2"></i> กำลังเข้าสู่ระบบ...');
        const formData = new FormData();
        formData.append('user', user);
        formData.append('pass', pass);

        $.ajax({
            type: 'POST',
            url: 'system/login.php',
            data: formData,
            contentType: false,
            processData: false
        }).done(function (res) {
            btn.removeAttr('disabled').html('<i class="fa-solid fa-right-to-bracket me-2"></i> เข้าสู่ระบบ');
            if (res.status === 'success') {
                if (window.bgMusic && typeof window.bgMusic.saveProgress === 'function') {
                    window.bgMusic.saveProgress();
                }
                showAuthSuccess('ยินดีต้อนรับ! เข้าสู่ระบบสำเร็จ');
                setTimeout(() => {
                    window.location.replace('./');
                }, 800);
            } else {
                showAuthError(res.message || 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
            }
        }).fail(function () {
            btn.removeAttr('disabled').html('<i class="fa-solid fa-right-to-bracket me-2"></i> เข้าสู่ระบบ');
            showAuthError('ไม่สามารถติดต่อเซิร์ฟเวอร์ได้');
        });
    }
}
</script>
