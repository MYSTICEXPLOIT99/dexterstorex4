<?php
if (!isset($_SESSION['id'])) {
    echo "<script>window.navTo ? window.navTo('') : (location.href = '');</script>";
    exit;
}

$uid = $_SESSION['id'];
$u_q = dd_q("SELECT * FROM users WHERE id = ?", [$uid]);
$user = $u_q->fetch(PDO::FETCH_ASSOC);

$user_topup = floatval($user['total_topup'] ?? 0);
$user_vip = $user['vip_level'] ?: 'Iron';

// VIP Tier configs
$tiers = [
    ['name' => 'Iron', 'min' => 0, 'discount' => 0, 'color' => '#6b7280', 'icon' => 'fa-shield'],
    ['name' => 'Bronze', 'min' => 100, 'discount' => 1, 'color' => '#d97706', 'icon' => 'fa-shield-heart'],
    ['name' => 'Silver', 'min' => 500, 'discount' => 3, 'color' => '#94a3b8', 'icon' => 'fa-medal'],
    ['name' => 'Gold', 'min' => 1000, 'discount' => 5, 'color' => '#f59e0b', 'icon' => 'fa-award'],
    ['name' => 'Platinum', 'min' => 2500, 'discount' => 7, 'color' => '#38bdf8', 'icon' => 'fa-shield-halved'],
    ['name' => 'Diamond', 'min' => 5000, 'discount' => 10, 'color' => '#00c9ff', 'icon' => 'fa-gem'],
    ['name' => 'Ascendant', 'min' => 10000, 'discount' => 12, 'color' => '#10b981', 'icon' => 'fa-gem'],
    ['name' => 'Immortal', 'min' => 25000, 'discount' => 15, 'color' => '#ec4899', 'icon' => 'fa-chess-king'],
    ['name' => 'Radiant', 'min' => 50000, 'discount' => 20, 'color' => '#ffd700', 'icon' => 'fa-crown'],
];

// Determine active tier index
$current_idx = 0;
foreach ($tiers as $idx => $t) {
    if ($t['name'] == $user_vip || $user_topup >= $t['min']) {
        $current_idx = $idx;
    }
}
$next_tier = $tiers[min(count($tiers) - 1, $current_idx + 1)];
$needed = max(0, $next_tier['min'] - $user_topup);
$progress = ($next_tier['min'] > 0) ? min(100, round(($user_topup / $next_tier['min']) * 100)) : 100;
?>

<style>
/* ── VIP TIER PAGE ── */
.mickey-vip-wrap {
    max-width: 960px;
    margin: 0 auto;
    padding: 10px 0 40px;
}
.mickey-vip-header {
    text-align: center;
    margin-bottom: 28px;
}
.mickey-vip-card {
    background: #0d0d10;
    border: 1px solid #1f1f26;
    border-radius: 18px;
    padding: 28px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.6);
    margin-bottom: 24px;
}
.mickey-vip-badge-big {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(var(--primary-rgb), 0.15);
    border: 2px solid var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    color: var(--primary);
    margin: 0 auto 16px;
    box-shadow: 0 0 25px rgba(var(--primary-rgb), 0.4);
}
.mickey-vip-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
}
@media (max-width: 768px) {
    .mickey-vip-grid { grid-template-columns: 1fr; }
}
.mickey-tier-card {
    background: #121216;
    border: 1px solid #23232c;
    border-radius: 14px;
    padding: 20px 16px;
    text-align: center;
    position: relative;
    transition: all 0.25s ease;
}
.mickey-tier-card:hover {
    transform: translateY(-4px);
    border-color: rgba(var(--primary-rgb), 0.5);
}
.mickey-tier-card.active-tier {
    background: #190e0e;
    border-color: var(--primary);
    box-shadow: 0 0 20px rgba(var(--primary-rgb), 0.3);
}
.mickey-tier-icon {
    font-size: 1.8rem;
    margin-bottom: 8px;
}
.mickey-tier-name {
    font-size: 1.1rem;
    font-weight: 800;
    margin-bottom: 4px;
}
.mickey-tier-desc {
    font-size: 0.78rem;
    color: #888888;
    margin-bottom: 12px;
}
.mickey-tier-benefit {
    font-size: 0.85rem;
    font-weight: 700;
    color: #22c55e;
    background: rgba(34, 197, 94, 0.1);
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-block;
}
</style>

<div class="mickey-vip-wrap" data-aos="fade-up">
    <!-- Header -->
    <div class="mickey-vip-header">
        <h3 class="fw-bold text-white mb-1"><i class="fa-solid fa-crown text-warning me-2"></i> สิทธิพิเศษระดับ VIP & แต้มพิเศษ</h3>
        <p class="text-muted small mb-0">ยิ่งเติมสะสมมาก ยิ่งได้รับส่วนลดและแต้มพิเศษเพิ่มขึ้นอัตโนมัติ</p>
    </div>

    <!-- Current User Status Card -->
    <div class="mickey-vip-card text-center">
        <div class="mickey-vip-badge-big">
            <i class="fa-solid <?= $tiers[$current_idx]['icon'] ?>" style="color: <?= $tiers[$current_idx]['color'] ?>;"></i>
        </div>
        <h4 class="fw-bold text-white mb-1">ระดับของคุณ: <span style="color: <?= $tiers[$current_idx]['color'] ?>;"><?= $tiers[$current_idx]['name'] ?> VIP</span></h4>
        <p class="text-muted small mb-3">ยอดเติมเงินสะสมรวม: <strong class="text-primary">฿<?= number_format($user_topup, 2) ?></strong></p>

        <!-- Progress Bar -->
        <?php if ($current_idx < count($tiers) - 1): ?>
            <div class="mb-2 d-flex justify-content-between small text-muted">
                <span>ความคืบหน้าระดับถัดไป (<?= $next_tier['name'] ?>)</span>
                <span>ขาดอีก ฿<?= number_format($needed, 2) ?></span>
            </div>
            <div class="progress mb-3" style="height: 10px; background: #1a1a24; border-radius: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" style="width: <?= $progress ?>%; background: var(--primary) !important;"></div>
            </div>
        <?php else: ?>
            <span class="badge bg-warning text-dark px-3 py-2 fw-bold">คุณอยู่ในระดับสูงสุดแล้ว (MAX LEVEL)</span>
        <?php endif; ?>
    </div>

    <!-- Tiers Grid -->
    <div class="mickey-vip-card">
        <h5 class="fw-bold text-white mb-4"><i class="fa-solid fa-layer-group text-primary me-2"></i> ระดับชั้น VIP ทั้งหมด</h5>
        <div class="mickey-vip-grid">
            <?php foreach ($tiers as $idx => $t): 
                $is_curr = ($idx === $current_idx);
            ?>
                <div class="mickey-tier-card <?= $is_curr ? 'active-tier' : '' ?>">
                    <?php if ($is_curr): ?>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2 small" style="background: var(--primary) !important;">ระดับปัจจุบัน</span>
                    <?php endif; ?>
                    <i class="fa-solid <?= $t['icon'] ?> mickey-tier-icon" style="color: <?= $t['color'] ?>;"></i>
                    <div class="mickey-tier-name" style="color: <?= $t['color'] ?>;"><?= $t['name'] ?></div>
                    <div class="mickey-tier-desc">เติมสะสม ฿<?= number_format($t['min']) ?> ขึ้นไป</div>
                    <div class="mickey-tier-benefit">
                        <?= ($t['discount'] > 0) ? "ส่วนลดพิเศษ {$t['discount']}%" : "ระดับเริ่มต้น" ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

