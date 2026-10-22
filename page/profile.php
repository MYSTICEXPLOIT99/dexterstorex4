<?php
// Profile Page (CYGNUSX STORE - Exact Match Screenshot 14)
if (!isset($_SESSION['id'])) {
    echo "<script>window.navTo ? window.navTo('') : (location.href = '');</script>";
    exit();
}

$user_initials = strtoupper(substr($user['username'] ?? 'U', 0, 2));
?>

<div class="container-sm py-4" data-aos="fade-up">
    <div class="acct-modal-wrap">
        <!-- Close Button (back to home) -->
        <a href="?page=home" class="acct-modal-close-btn" aria-label="Close">✕</a>

        <!-- Header -->
        <div class="text-center mb-3">
            <div class="section-eyebrow" style="letter-spacing:3px;">// บัญชีของฉัน</div>
            <h2 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);font-size:1.4rem;letter-spacing:1px;">
                MY ACCOUNT
            </h2>
        </div>

        <!-- 1. User Profile Box -->
        <div class="acct-profile-box">
            <div class="acct-avatar-circle">
                <span><?= htmlspecialchars($user_initials) ?></span>
                <span class="acct-avatar-tag"><?= $user['rank'] == '1' ? 'ADMIN' : 'MEMBER' ?></span>
            </div>
            <div>
                <div class="acct-user-name"><?= htmlspecialchars($user['username']) ?></div>
                <div class="acct-user-id">ID: member_<?= htmlspecialchars($user['id']) ?></div>
            </div>
        </div>

        <!-- 2. Wallet Balance Strip -->
        <div class="acct-wallet-strip">
            <span class="acct-wallet-label">ยอดคงเหลือในกระเป๋า</span>
            <span class="acct-wallet-amount">฿<?= number_format($user['point'] ?? 0, 2) ?></span>
        </div>

        <!-- 3. Action Grid (2x2) -->
        <div class="acct-actions-grid-2x2">
            <!-- 1. ซื้อสินค้า -->
            <a href="?page=shop" class="acct-action-btn-item">
                <div class="acct-action-icon-wrap">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div class="acct-action-btn-title">ซื้อสินค้า</div>
                <div class="acct-action-btn-sub">เลือกสินค้าในร้าน</div>
            </a>

            <!-- 2. ประวัติการซื้อ -->
            <a href="?page=buyhis" class="acct-action-btn-item">
                <div class="acct-action-icon-wrap">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <div class="acct-action-btn-title">ประวัติการซื้อ</div>
                <div class="acct-action-btn-sub">ดูรายการ & Key</div>
            </a>

            <!-- 3. เติมเงิน -->
            <a href="?page=topup" class="acct-action-btn-item">
                <div class="acct-action-icon-wrap">
                    <i class="fa-regular fa-credit-card"></i>
                </div>
                <div class="acct-action-btn-title">เติมเงิน</div>
                <div class="acct-action-btn-sub">โอน / สลิป / ซอง</div>
            </a>

            <!-- 4. ติดต่อ -->
            <a href="?page=contact" class="acct-action-btn-item">
                <div class="acct-action-icon-wrap">
                    <i class="fa-regular fa-comment-dots"></i>
                </div>
                <div class="acct-action-btn-title">ติดต่อ</div>
                <div class="acct-action-btn-sub">Discord & โซเชียล</div>
            </a>
        </div>

        <!-- 4. Change Password Row -->
        <div class="acct-pwd-row" onclick="togglePasswordForm()">
            <div class="acct-action-icon-wrap mb-0">
                <i class="fa-solid fa-lock"></i>
            </div>
            <div>
                <div class="acct-action-btn-title">เปลี่ยนรหัสผ่าน</div>
                <div class="acct-action-btn-sub">ใส่รหัสเก่าแล้วตั้งรหัสใหม่</div>
            </div>
        </div>

        <!-- Hidden Change Password Form -->
        <div id="pwdChangeFormWrap" style="display:none;background:#090a0f;border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:14px;margin-bottom:14px;">
            <div class="mb-2">
                <label class="auth-label-title">รหัสผ่านปัจจุบัน</label>
                <input type="password" id="p_old" class="auth-custom-input py-2" placeholder="กรอกรหัสผ่านเดิม...">
            </div>
            <div class="mb-2">
                <label class="auth-label-title">รหัสผ่านใหม่</label>
                <input type="password" id="p_new" class="auth-custom-input py-2" placeholder="กรอกรหัสผ่านใหม่ (อย่างน้อย 4 ตัว)...">
            </div>
            <div class="mb-3">
                <label class="auth-label-title">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" id="p_new2" class="auth-custom-input py-2" placeholder="ยืนยันรหัสผ่านใหม่อีกครั้ง...">
            </div>
            <button type="button" class="btn btn-primary w-100 py-2 fw-bold" onclick="submitChangePassword()" style="background:#ffffff;color:#050508;border-radius:8px;font-family:var(--font-cyber);">
                บันทึกรหัสผ่านใหม่
            </button>
        </div>

        <?php if ($user['rank'] == '1'): ?>
            <a href="?page=backend" class="btn btn-outline-warning w-100 py-2 mb-3 fw-bold" style="border-radius:10px;font-size:0.84rem;">
                <i class="fa-solid fa-shield-halved me-2"></i> จัดการระบบหลังบ้าน (Admin)
            </a>
        <?php endif; ?>

        <!-- 5. Logout Button -->
        <a href="?page=logout" class="btn-acct-logout">
            ออกจากระบบ
        </a>
    </div>
</div>

<script>
function togglePasswordForm() {
    const wrap = document.getElementById('pwdChangeFormWrap');
    if (wrap.style.display === 'none') {
        wrap.style.display = 'block';
    } else {
        wrap.style.display = 'none';
    }
}

function submitChangePassword() {
    const oldPass = $('#p_old').val().trim();
    const newPass = $('#p_new').val().trim();
    const newPass2 = $('#p_new2').val().trim();

    if (!oldPass || !newPass || !newPass2) {
        Swal.fire({ icon: 'warning', title: 'แจ้งเตือน', text: 'กรุณากรอกข้อมูลให้ครบทุกช่อง', confirmButtonColor: '#6a80a0' });
        return;
    }
    if (newPass !== newPass2) {
        Swal.fire({ icon: 'warning', title: 'แจ้งเตือน', text: 'รหัสผ่านใหม่ทั้งสองช่องไม่ตรงกัน', confirmButtonColor: '#6a80a0' });
        return;
    }

    const formData = new FormData();
    formData.append('old_pass', oldPass);
    formData.append('new_pass', newPass);
    formData.append('new_pass2', newPass2);

    $.ajax({
        type: 'POST',
        url: 'system/change_pwd.php',
        data: formData,
        contentType: false,
        processData: false
    }).done(function(res) {
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, confirmButtonColor: '#6a80a0' }).then(() => {
                window.seamlessReload ? window.seamlessReload() : location.reload();
            });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message || 'เกิดข้อผิดพลาด', confirmButtonColor: '#6a80a0' });
        }
    }).fail(function() {
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', confirmButtonColor: '#6a80a0' });
    });
}
</script>

