<div class="p-4 p-lg-5">
    <div class="mb-4">
        <div class="section-eyebrow">// TOPUP LOGS</div>
        <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
            <i class="fa-solid fa-wallet text-success me-2"></i> ประวัติการเติมเงิน
        </h3>
        <p class="text-muted small mb-0">ตรวจสอบรายการเติมเงินทั้งหมดของลูกค้าผ่าน TrueMoney Wallet และสลิปธนาคาร</p>
    </div>

    <div class="admin-inner-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>ผู้ใช้งาน</th>
                        <th>ข้อมูลอ้างอิง / ลิงก์</th>
                        <th class="text-center">จำนวนเงิน</th>
                        <th class="text-end pe-3">วันที่ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $get_user = dd_q("SELECT * FROM topup_his ORDER BY id DESC");
                    if ($get_user && $get_user->rowCount() > 0) {
                        while ($row = $get_user->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <tr>
                            <td class="ps-3 fw-bold text-white">#<?php echo $row['id']; ?></td>
                            <td>
                                <span class="text-white fw-bold" style="font-family:var(--font-cyber);"><?php echo htmlspecialchars($row['uname']); ?></span>
                            </td>
                            <td>
                                <span class="text-muted small text-truncate d-inline-block" style="max-width: 250px;">
                                    <?php echo htmlspecialchars($row['link']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50" style="font-size:0.88rem; font-family:'Space Grotesk', 'Rajdhani', sans-serif !important; font-weight:800;">
                                    +฿<?php echo number_format($row['amount'], 2); ?>
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
                            <td colspan="5" class="text-center text-muted py-4">ยังไม่มีประวัติการเติมเงิน</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>