<div class="p-4 p-lg-5">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <div class="section-eyebrow">// PURCHASE LOGS</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-receipt text-warning me-2"></i> ประวัติการสั่งซื้อสินค้า
            </h3>
            <p class="text-muted small mb-0">บันทึกรายการสั่งซื้อทั้งหมดของลูกค้า พร้อมคีย์ที่ระบบส่งมอบ</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-danger btn-sm px-3 py-2 fw-bold" id="btnClearBuyHistory">
                <i class="fa-solid fa-trash-can me-1"></i> ล้างประวัติทั้งหมด
            </button>
        </div>
    </div>

    <div class="admin-inner-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>ผู้ซื้อ</th>
                        <th>สินค้า / คีย์ที่ได้รับ</th>
                        <th class="text-center">หมวดหมู่</th>
                        <th class="text-end pe-3">วันที่สั่งซื้อ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $get_user = dd_q("SELECT * FROM history_buy ORDER BY id DESC");
                    if (!$get_user || $get_user->rowCount() == 0) {
                        $get_user = dd_q("SELECT * FROM boxlog ORDER BY id DESC");
                    }
                    if ($get_user && $get_user->rowCount() > 0) {
                        while ($row = $get_user->fetch(PDO::FETCH_ASSOC)) {
                            $prod_title = $row['item_name'] ?? $row['prize_name'] ?? $row['name'] ?? 'สินค้า';
                            $buyer_uname = $row['uname'] ?? $row['username'] ?? 'User';
                    ?>
                        <tr>
                            <td class="ps-3 fw-bold text-white">#<?php echo $row['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-dark border border-secondary border-opacity-50" style="width:28px;height:28px;">
                                        <i class="fa-solid fa-user text-warning" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <span class="text-white fw-bold" style="font-family:var(--font-cyber);"><?php echo htmlspecialchars($buyer_uname); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="text-info fw-bold d-block"><?php echo htmlspecialchars($prod_title); ?></span>
                                <?php if (!empty($row['item_data'] ?? $row['data'] ?? '')): ?>
                                    <small class="text-muted font-monospace"><?php echo htmlspecialchars($row['item_data'] ?? $row['data']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-25 text-info border border-primary border-opacity-50">
                                    <?php echo htmlspecialchars($row['category'] ?? 'PROGRAM PRODUCT'); ?>
                                </span>
                            </td>
                            <td class="text-end pe-3 text-muted" style="font-size:0.75rem;">
                                <?php echo htmlspecialchars($row['date']); ?>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">ยังไม่มีประวัติการสั่งซื้อ</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$('#btnClearBuyHistory').on('click', function() {
    Swal.fire({
        title: 'ยืนยันล้างประวัติการซื้อ?',
        text: 'ประวัติการสั่งซื้อและประวัติซื้อล่าสุด (Live Feed) ทั้งหมดจะถูกลบอย่างถาวร',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#374151',
        confirmButtonText: 'ใช่, ล้างข้อมูลทั้งหมด',
        cancelButtonText: 'ยกเลิก',
        background: '#090a0f',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'system/backend/clear_buy_history.php',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'ล้างข้อมูลสำเร็จ!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#090a0f',
                            color: '#ffffff'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด',
                            text: res.message,
                            background: '#090a0f',
                            color: '#ffffff'
                        });
                    }
                }
            });
        }
    });
});
</script>