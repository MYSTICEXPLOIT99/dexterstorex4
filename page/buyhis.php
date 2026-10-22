<?php
// Buy History Page (CYGNUSX STORE - Exact Match Screenshot 5)

$search = trim($_GET['search'] ?? '');
$current_uid = $_SESSION['id'] ?? 0;
$current_uname = $user['username'] ?? '';

$sql = "SELECT * FROM boxlog WHERE (uid = ? OR username = ?)";
$params = [$current_uid, $current_uname];

if (!empty($search)) {
    $sql .= " AND (prize_name LIKE ? OR category LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= " ORDER BY id DESC";
$q = dd_q($sql, $params);
$orders = $q ? $q->fetchAll(PDO::FETCH_ASSOC) : [];
?>

<div class="buyhis-shell" data-aos="fade-up">
    <!-- Header -->
    <div class="text-center mb-4">
        <div class="section-eyebrow" style="letter-spacing:3px;">// ประวัติการซื้อ</div>
        <h1 class="section-main-heading" style="font-size: 2.2rem; margin-bottom: 6px;">ประวัติการซื้อ</h1>
        <p class="section-subtext">รายการสินค้าที่คุณเคยสั่งซื้อ พร้อมข้อมูลคีย์ / รหัสส่งมอบ</p>
    </div>

    <div class="buyhis-card">
        <!-- Search Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <span class="text-muted small" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-clock-rotate-left text-info me-1"></i> รายการทั้งหมด: <b class="text-white"><?= count($orders) ?></b> รายการ
            </span>
            <div class="buyhis-search-wrap m-0">
                <form action="?page=buyhis" method="GET" class="m-0 p-0">
                    <input type="hidden" name="page" value="buyhis">
                    <input type="text" name="search" class="buyhis-search-input form-control" placeholder="ค้นหาชื่อสินค้า..." value="<?= htmlspecialchars($search) ?>">
                </form>
            </div>
        </div>

        <!-- Table or Empty State -->
        <?php if (count($orders) == 0): ?>
            <div class="empty-history-box my-4">
                <div class="empty-history-icon">📦</div>
                <div class="empty-history-text">ยังไม่มีประวัติการซื้อ</div>
                <p class="text-muted small mt-2">เมื่อคุณสั่งซื้อสินค้าแล้ว ข้อมูลคีย์และรหัสส่งมอบจะแสดงที่นี่</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="buyhis-table">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 60px;">#</th>
                            <th>สินค้า / รายการ</th>
                            <th>ข้อมูลคีย์ / รหัสส่งมอบ</th>
                            <th class="text-center">ราคา</th>
                            <th class="text-end pe-3">วันที่สั่งซื้อ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($orders as $row): 
                            $prod_name = !empty($row['category']) ? $row['category'] : 'สินค้า';
                            $key_data = $row['prize_name'] ?? '';
                        ?>
                            <tr>
                                <td class="ps-3 fw-bold text-white">#<?= $i++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center border border-secondary border-opacity-25" style="width:32px;height:32px;background:#050608;">
                                            <i class="fa-solid fa-box-open text-info" style="font-size:0.75rem;"></i>
                                        </div>
                                        <span class="fw-bold text-white" style="font-family:var(--font-cyber);"><?= htmlspecialchars($prod_name) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="buyhis-key-badge">
                                        <span class="buyhis-key-text"><?= htmlspecialchars($key_data) ?></span>
                                        <button type="button" class="buyhis-copy-btn" title="คัดลอกคีย์" onclick="navigator.clipboard.writeText('<?= htmlspecialchars(addslashes($key_data)) ?>'); Swal.fire({icon:'success', title:'คัดลอกคีย์แล้ว!', toast:true, position:'top-end', showConfirmButton:false, timer:1500, background:'#0a0a0f', color:'#fff'});">
                                            <i class="fa-regular fa-copy"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-info" style="font-family:var(--font-cyber); font-size: 0.95rem;">
                                        ฿<?= number_format($row['price'] ?? 0, 2) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-3 text-muted" style="font-size:0.8rem; font-family:var(--font-cyber);">
                                    <?= htmlspecialchars($row['date']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Pagination Footer -->
        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-secondary border-opacity-25">
            <span class="text-muted small"><?= count($orders) == 0 ? 'ไม่มีรายการ' : 'แสดงทั้งหมด ' . count($orders) . ' รายการ' ?></span>
            <div class="d-flex gap-2">
                <button class="btn-cyber-primary py-1 px-3" style="font-size:0.75rem;" onclick="return false;">
                    <i class="fa-solid fa-angle-left"></i> ย้อนกลับ
                </button>
                <button class="btn-cyber-primary py-1 px-3" style="font-size:0.75rem;" onclick="return false;">
                    ถัดไป <i class="fa-solid fa-angle-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>
