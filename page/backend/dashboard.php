<?php
date_default_timezone_set("Asia/Bangkok");
$midnight = strtotime("today 00:00");
$date_day = date('Y-m-d H:i:s', $midnight);

// Topup today
$q_1 = dd_q("SELECT sum(amount) AS total FROM topup_his WHERE date > ?", [$date_day]);
$day = $q_1 ? $q_1->fetch(PDO::FETCH_ASSOC) : ['total' => '0.00'];
if ($day["total"] == null) $day["total"] = "0.00";

// Topup all
$q_3 = dd_q("SELECT sum(amount) AS total FROM topup_his");
$topup = $q_3 ? $q_3->fetch(PDO::FETCH_ASSOC) : ['total' => '0.00'];
if ($topup["total"] == null) $topup["total"] = "0.00";

// Total Users
$q_u = dd_q("SELECT count(*) as total FROM users WHERE username NOT IN ('admin')");
$total_users = $q_u ? ($q_u->fetch(PDO::FETCH_ASSOC)['total'] ?? 0) : 0;

// Total Orders
$q_o = dd_q("SELECT count(*) as total FROM history_buy");
$total_orders = $q_o ? ($q_o->fetch(PDO::FETCH_ASSOC)['total'] ?? 0) : 0;

// Total Stock Remaining
$q_s = dd_q("SELECT count(*) as total FROM box_item");
$total_stock = $q_s ? ($q_s->fetch(PDO::FETCH_ASSOC)['total'] ?? 0) : 0;
?>

<div class="p-4 p-lg-5">
    <div class="mb-4">
        <div class="section-eyebrow">// DASHBOARD OVERVIEW</div>
        <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
            <i class="fa-solid fa-chart-pie text-info me-2"></i> แผงควบคุมระบบ (Dashboard)
        </h3>
        <p class="text-muted small mb-0">ภาพรวมสถิติการใช้งาน รายได้ และรายการสั่งซื้อภายในร้านค้าของคุณ</p>
    </div>

    <!-- 4 Stat Cards Grid -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Topup -->
        <div class="col-sm-6 col-xl-3">
            <div class="admin-stat-box">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="admin-label text-muted">ยอดเติมเงินรวม</span>
                    <div class="stat-icon">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                </div>
                <div class="admin-stat-num text-info">฿<?= number_format((float)$topup["total"], 2) ?></div>
                <small class="text-muted" style="font-size:0.75rem;">วันนี้: ฿<?= number_format((float)$day["total"], 2) ?></small>
            </div>
        </div>

        <!-- 2. Total Users -->
        <div class="col-sm-6 col-xl-3">
            <div class="admin-stat-box">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="admin-label text-muted">สมาชิกทั้งหมด</span>
                    <div class="stat-icon" style="background:rgba(34,197,94,0.15);border-color:rgba(34,197,94,0.3);color:#4ade80;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="admin-stat-num text-success"><?= number_format($total_users) ?> <span style="font-size:1rem;color:#64748b;">คน</span></div>
                <small class="text-muted" style="font-size:0.75rem;">บัญชีที่ลงทะเบียนในระบบ</small>
            </div>
        </div>

        <!-- 3. Total Orders -->
        <div class="col-sm-6 col-xl-3">
            <div class="admin-stat-box">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="admin-label text-muted">ยอดการสั่งซื้อ</span>
                    <div class="stat-icon" style="background:rgba(234,179,8,0.15);border-color:rgba(234,179,8,0.3);color:#facc15;">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                </div>
                <div class="admin-stat-num text-warning"><?= number_format($total_orders) ?> <span style="font-size:1rem;color:#64748b;">ครั้ง</span></div>
                <small class="text-muted" style="font-size:0.75rem;">ออเดอร์สินค้าสำเร็จทั้งหมด</small>
            </div>
        </div>

        <!-- 4. Stock Remaining -->
        <div class="col-sm-6 col-xl-3">
            <div class="admin-stat-box">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="admin-label text-muted">สต็อกคงเหลือ</span>
                    <div class="stat-icon" style="background:rgba(168,85,247,0.15);border-color:rgba(168,85,247,0.3);color:#c084fc;">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
                <div class="admin-stat-num" style="color:#c084fc;"><?= number_format($total_stock) ?> <span style="font-size:1rem;color:#64748b;">ชิ้น</span></div>
                <small class="text-muted" style="font-size:0.75rem;">คีย์ที่พร้อมส่งมอบ</small>
            </div>
        </div>
    </div>

    <!-- 2 Tables: Recent Topup & Recent Orders -->
    <div class="row g-4">
        <!-- Recent Topups -->
        <div class="col-lg-6">
            <div class="admin-inner-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                        <i class="fa-solid fa-clock-rotate-left text-info me-2"></i> รายการเติมเงินล่าสุด
                    </h6>
                    <a href="?page=backend_topup_history" class="btn btn-sm btn-cyber-edit px-3" style="font-size:0.72rem;padding:4px 10px !important;">
                        ดูทั้งหมด <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ผู้ใช้งาน</th>
                                <th>จำนวน</th>
                                <th class="text-end">วันที่</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $f = dd_q("SELECT * FROM topup_his ORDER BY id DESC LIMIT 5");
                            if ($f && $f->rowCount() > 0):
                                while ($row = $f->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-white text-truncate d-inline-block" style="max-width:130px;vertical-align:middle;" title="<?= htmlspecialchars($row['uname']) ?>">
                                            <i class="fa-solid fa-user-astronaut text-muted me-1" style="font-size:0.75rem;"></i><?= htmlspecialchars($row['uname']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-40 px-2 py-1" style="font-family:'Space Grotesk',sans-serif;font-size:0.75rem;border-radius:6px;">
                                            +฿<?= number_format($row['amount'], 2) ?>
                                        </span>
                                    </td>
                                    <td class="text-end text-muted" style="font-size:0.72rem;white-space:nowrap;font-family:'Space Grotesk',sans-serif;">
                                        <?= htmlspecialchars($row['date']) ?>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4" style="font-family:var(--font-cyber);font-size:0.8rem;">
                                        <i class="fa-solid fa-inbox me-1"></i> ยังไม่มีรายการเติมเงิน
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-lg-6">
            <div class="admin-inner-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                        <i class="fa-solid fa-bag-shopping text-warning me-2"></i> รายการสั่งซื้อล่าสุด
                    </h6>
                    <a href="?page=backend_buy_history" class="btn btn-sm btn-cyber-edit px-3" style="font-size:0.72rem;padding:4px 10px !important;">
                        ดูทั้งหมด <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ผู้ซื้อ</th>
                                <th>สินค้า</th>
                                <th class="text-end">วันที่</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $f_b = dd_q("SELECT * FROM history_buy ORDER BY id DESC LIMIT 5");
                            if ($f_b && $f_b->rowCount() > 0):
                                while ($row = $f_b->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-white text-truncate d-inline-block" style="max-width:130px;vertical-align:middle;" title="<?= htmlspecialchars($row['uname']) ?>">
                                            <i class="fa-solid fa-user text-muted me-1" style="font-size:0.75rem;"></i><?= htmlspecialchars($row['uname']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-info text-truncate d-inline-block" style="max-width:140px;vertical-align:middle;" title="<?= htmlspecialchars($row['item_name'] ?? 'สินค้า') ?>">
                                            <?= htmlspecialchars($row['item_name'] ?? 'สินค้า') ?>
                                        </span>
                                    </td>
                                    <td class="text-end text-muted" style="font-size:0.72rem;white-space:nowrap;font-family:'Space Grotesk',sans-serif;">
                                        <?= htmlspecialchars($row['date']) ?>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4" style="font-family:var(--font-cyber);font-size:0.8rem;">
                                        <i class="fa-solid fa-box-open me-1"></i> ยังไม่มีรายการสั่งซื้อ
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
