<?php
// Fetch Top 10 users by total topup
$top_users_q = dd_q("
    SELECT id, username, total_topup, total, point, vip_level, date 
    FROM users 
    WHERE total_topup > 0 AND username NOT IN ('admin')
    ORDER BY total_topup DESC, total DESC 
    LIMIT 10
");

$rank_icons = [
    1 => ['badge' => 'bg-warning text-dark', 'icon' => 'fa-crown', 'color' => '#f59e0b', 'title' => 'อันดับ 1'],
    2 => ['badge' => 'bg-light text-dark', 'icon' => 'fa-medal', 'color' => '#94a3b8', 'title' => 'อันดับ 2'],
    3 => ['badge' => 'bg-danger text-white', 'icon' => 'fa-award', 'color' => '#d97706', 'title' => 'อันดับ 3'],
];

$vip_colors = [
    'Iron' => '#6b7280', 'Bronze' => '#d97706', 'Silver' => '#94a3b8',
    'Gold' => '#f59e0b', 'Platinum' => '#38bdf8', 'Diamond' => '#00c9ff',
    'Ascendant' => '#10b981', 'Immortal' => '#ec4899', 'Radiant' => '#ffd700'
];
?>

<style>
/* ── MICKEY STORE TOP 10 RANKING STYLES ── */
.mickey-rank-wrap {
    max-width: 900px;
    margin: 0 auto;
    padding: 10px 0 40px;
}
.mickey-rank-box {
    background: #0d0d10;
    border: 1px solid #1f1f26;
    border-radius: 18px;
    padding: 28px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.6);
}
.mickey-rank-header-box {
    margin-bottom: 24px;
}
.mickey-rank-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 4px;
}
.mickey-rank-subtitle {
    color: #888888;
    font-size: 0.85rem;
    margin-bottom: 0;
}
.mickey-rank-empty {
    text-align: center;
    padding: 60px 20px;
    color: #777777;
    font-size: 0.95rem;
}
.mickey-rank-table-row {
    background: #121216;
    border: 1px solid #202028;
    border-radius: 12px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    transition: all 0.2s ease;
}
.mickey-rank-table-row:hover {
    border-color: rgba(var(--primary-rgb), 0.4);
    background: #16161c;
}
.mickey-rank-user-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #251010;
    color: var(--primary);
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(var(--primary-rgb), 0.3);
}
</style>

<div class="mickey-rank-wrap" data-aos="fade-up">
    <div class="mickey-rank-box">
        <!-- Header -->
        <div class="mickey-rank-header-box">
            <div class="mickey-rank-title">
                <i class="fa-solid fa-trophy" style="color: #f59e0b;"></i> Top 10 ผู้เติมเงินสูงสุด
            </div>
            <p class="mickey-rank-subtitle">10 อันดับสมาชิกที่มียอดเติมสะสมสูงสุด</p>
        </div>

        <!-- Content Area -->
        <?php if (!$top_users_q || $top_users_q->rowCount() == 0): ?>
            <div class="mickey-rank-empty">
                <i class="fa-solid fa-trophy display-4 opacity-25 mb-3" style="color: #f59e0b;"></i>
                <div>ยังไม่มีข้อมูลอันดับ</div>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-2">
                <?php 
                $pos = 1;
                while ($u = $top_users_q->fetch(PDO::FETCH_ASSOC)):
                    $vip = $u['vip_level'] ?: 'Iron';
                    $vip_col = $vip_colors[$vip] ?? '#6b7280';
                ?>
                    <div class="mickey-rank-table-row">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 40px; text-align: center;">
                                <?php if ($pos === 1): ?>
                                    <span class="badge bg-warning text-dark px-2 py-1"><i class="fa-solid fa-crown me-1"></i>#1</span>
                                <?php elseif ($pos === 2): ?>
                                    <span class="badge bg-light text-dark px-2 py-1"><i class="fa-solid fa-medal me-1"></i>#2</span>
                                <?php elseif ($pos === 3): ?>
                                    <span class="badge bg-danger text-white px-2 py-1"><i class="fa-solid fa-award me-1"></i>#3</span>
                                <?php else: ?>
                                    <span class="text-muted fw-bold small">#<?= $pos ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="mickey-rank-user-avatar">
                                <?= strtoupper(substr($u['username'], 0, 1)) ?>
                            </div>

                            <div>
                                <div class="text-white fw-bold" style="font-size: 0.95rem;">
                                    <?= htmlspecialchars($u['username']) ?>
                                </div>
                                <div style="font-size: 0.75rem; color: <?= $vip_col ?>;">
                                    <i class="fa-solid fa-crown me-1"></i><?= htmlspecialchars($vip) ?> VIP
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <div style="font-size: 0.75rem; color: #777;">ยอดเติมสะสม</div>
                            <div style="color: var(--primary); font-weight: 800; font-size: 1.1rem; font-family: 'Kanit', sans-serif;">
                                ฿<?= number_format($u['total_topup'], 2) ?>
                            </div>
                        </div>
                    </div>
                <?php 
                    $pos++;
                endwhile; 
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

