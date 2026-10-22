<?php
if (!isset($_SESSION['id'])) {
    echo "<script>window.navTo ? window.navTo('') : (location.href = '');</script>";
    exit;
}

$uid = $_SESSION['id'];
$u_q = dd_q("SELECT * FROM users WHERE id = ?", [$uid]);
$user = $u_q->fetch(PDO::FETCH_ASSOC);

// Base url for affiliate link
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
$base_url = $protocol . "://" . $host . explode('?', $uri)[0];
$ref_link = $base_url . "?page=register&ref=" . $user['id'];

// Get referral records
$refs_q = dd_q("
    SELECT u.username, u.date, r.bonus_point, r.created_at 
    FROM users u 
    LEFT JOIN referral_history r ON r.referee_id = u.id 
    WHERE u.ref_by = ? 
    ORDER BY u.id DESC
", [$uid]);

$ref_list = $refs_q ? $refs_q->fetchAll(PDO::FETCH_ASSOC) : [];
$total_referred = count($ref_list);
$total_earned = floatval($user['ref_earnings'] ?? 0);
?>

<style>
/* ── MICKEY AFFILIATE / INVITE FRIENDS ── */
.mickey-ref-wrap {
    max-width: 960px;
    margin: 0 auto;
    padding: 10px 0 40px;
}
.mickey-ref-header {
    text-align: center;
    margin-bottom: 28px;
}
.mickey-ref-card {
    background: #0d0d10;
    border: 1px solid #1f1f26;
    border-radius: 18px;
    padding: 28px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.6);
    margin-bottom: 24px;
}
.mickey-ref-copy-box {
    background: #121216;
    border: 1px solid #23232c;
    border-radius: 12px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.mickey-ref-input {
    background: transparent;
    border: none;
    color: #fff;
    font-size: 0.92rem;
    flex: 1;
    outline: none;
}
.mickey-ref-stat-card {
    background: #121216;
    border: 1px solid #23232c;
    border-radius: 14px;
    padding: 20px;
    text-align: center;
}
.mickey-ref-stat-val {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--primary);
    font-family: 'Kanit', sans-serif;
}
.mickey-ref-stat-lbl {
    font-size: 0.85rem;
    color: #888888;
}
</style>

<div class="mickey-ref-wrap" data-aos="fade-up">
    <!-- Header -->
    <div class="mickey-ref-header">
        <h3 class="fw-bold text-white mb-1"><i class="fa-solid fa-users text-danger me-2"></i> ระบบชวนเพื่อน (Affiliate Program)</h3>
        <p class="text-muted small mb-0">แชร์ลิงก์ให้เพื่อนสมัครสมาชิก รับส่วนแบ่งรายได้และแต้มพิเศษทันทีเมื่อเพื่อนเติมเงิน</p>
    </div>

    <!-- Referral Link Box -->
    <div class="mickey-ref-card">
        <h5 class="fw-bold text-white mb-2"><i class="fa-solid fa-link text-primary me-2"></i> ลิงก์แนะนำเพื่อนของคุณ</h5>
        <p class="text-muted small mb-3">คัดลอกลิงก์นี้ส่งให้เพื่อนของคุณเพื่อเริ่มรับโบนัส</p>
        
        <div class="mickey-ref-copy-box mb-3">
            <input type="text" class="mickey-ref-input" id="ref_link_input" value="<?= htmlspecialchars($ref_link) ?>" readonly>
            <button class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" onclick="copyRefLink()" style="background: var(--primary); border: none;">
                <i class="fa-solid fa-copy me-1"></i> คัดลอกลิงก์
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="mickey-ref-stat-card">
                <i class="fa-solid fa-user-plus display-6 text-primary mb-2"></i>
                <div class="mickey-ref-stat-val"><?= number_format($total_referred) ?> คน</div>
                <div class="mickey-ref-stat-lbl">เพื่อนที่สมัครผ่านลิงก์ของคุณ</div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="mickey-ref-stat-card">
                <i class="fa-solid fa-coins display-6 text-warning mb-2"></i>
                <div class="mickey-ref-stat-val">฿<?= number_format($total_earned, 2) ?></div>
                <div class="mickey-ref-stat-lbl">รายได้รวมที่ได้รับจากระบบชวนเพื่อน</div>
            </div>
        </div>
    </div>

    <!-- Referred Users Table -->
    <div class="mickey-ref-card">
        <h5 class="fw-bold text-white mb-3"><i class="fa-solid fa-list-check text-primary me-2"></i> รายชื่อเพื่อนที่แนะนำ</h5>
        <?php if (empty($ref_list)): ?>
            <div class="text-center py-4">
                <p class="text-muted small mb-0">ยังไม่มีเพื่อนสมัครผ่านลิงก์ของคุณในขณะนี้</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0" style="background: transparent;">
                    <thead>
                        <tr class="text-muted small">
                            <th>ชื่อผู้ใช้</th>
                            <th>วันที่สมัคร</th>
                            <th class="text-end">สถานะโบนัส</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ref_list as $r): ?>
                            <tr>
                                <td class="text-white fw-bold">
                                    <i class="fa-solid fa-circle-user text-muted me-2"></i><?= htmlspecialchars($r['username']) ?>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars($r['date'] ?? '-') ?></td>
                                <td class="text-end">
                                    <span class="badge bg-success small">+฿<?= number_format($r['bonus_point'] ?? 0, 2) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function copyRefLink() {
    var copyText = document.getElementById("ref_link_input");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    Swal.fire({
        icon: 'success',
        title: 'คัดลอกลิงก์เรียบร้อย!',
        text: 'ส่งลิงก์นี้ให้เพื่อนของคุณได้เลย',
        timer: 1800,
        showConfirmButton: false
    });
}
</script>

