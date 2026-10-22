<div class="p-4 p-lg-5">
    <div class="mb-5">
        <h3 class="fw-bold text-dark mb-1"><i class="fa-solid fa-list-check text-xdvz-primary me-2"></i>
            รายการสั่งซื้อเรท/แมพ Roblox</h3>
        <p class="text-muted small mb-0">ตรวจสอบและอัปเดตสถานะการส่งมอบบริการ Roblox Maps</p>
    </div>

    <div class="table-responsive">
        <table id="orders_table" class="table table-hover border-0 w-100">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 py-3 ps-4 text-muted small fw-bold">ID</th>
                    <th class="border-0 py-3 text-muted small fw-bold">วันที่สั่งซื้อ</th>
                    <th class="border-0 py-3 text-muted small fw-bold">ผู้ใช้ (ในเว็บ)</th>
                    <th class="border-0 py-3 text-muted small fw-bold">แมพ</th>
                    <th class="border-0 py-3 text-muted small fw-bold">Roblox USERNAME</th>
                    <th class="border-0 py-3 text-muted small fw-bold">Roblox PASSWORD</th>
                    <th class="border-0 py-3 text-muted small fw-bold">จำนวน</th>
                    <th class="border-0 py-3 text-muted small fw-bold">ราคารวม</th>
                    <th class="border-0 py-3 text-muted small fw-bold">สถานะ</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-center">เครื่องมือ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $get_orders = dd_q("SELECT o.*, u.username as web_user, m.name as map_name, m.rate_unit FROM roblox_orders o 
                                    LEFT JOIN users u ON o.uid = u.id 
                                    LEFT JOIN roblox_maps m ON o.map_id = m.id 
                                    ORDER BY o.id DESC");
                while ($row = $get_orders->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <tr class="align-middle border-bottom">
                        <td class="py-3 ps-4 text-dark small fw-bold">#<?php echo $row['id']; ?></td>
                        <td class="py-3 small text-muted"><?php echo $row['order_date']; ?></td>
                        <td class="py-3 small fw-bold text-dark"><?php echo htmlspecialchars($row['web_user'] ?? 'N/A'); ?></td>
                        <td class="py-3">
                            <span class="text-dark fw-bold small"><?php echo htmlspecialchars($row['map_name'] ?? 'ไม่ทราบแมพ'); ?></span>
                        </td>
                        <td class="py-3">
                            <div class="input-group input-group-sm" style="max-width: 150px;">
                                <input type="text" class="form-control form-control-sm" readonly value="<?php echo htmlspecialchars($row['username']); ?>" id="user_<?php echo $row['id']; ?>">
                                <button class="btn btn-outline-secondary" type="button" onclick="copyText('user_<?php echo $row['id']; ?>')"><i class="fa-solid fa-copy"></i></button>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="input-group input-group-sm" style="max-width: 150px;">
                                <input type="text" class="form-control form-control-sm" readonly value="<?php echo htmlspecialchars($row['password']); ?>" id="pass_<?php echo $row['id']; ?>">
                                <button class="btn btn-outline-secondary" type="button" onclick="copyText('pass_<?php echo $row['id']; ?>')"><i class="fa-solid fa-copy"></i></button>
                            </div>
                        </td>
                        <td class="py-3 text-dark small fw-bold"><?php echo $row['qty']; ?> <?php echo htmlspecialchars($row['rate_unit'] ?? 'M'); ?></td>
                        <td class="py-3 text-xdvz-primary small fw-bold">฿<?php echo number_format($row['price'], 2); ?></td>
                        <td class="py-3">
                            <?php if ($row['status'] == 'pending'): ?>
                                <span class="badge bg-warning text-dark small">รอดำเนินการ</span>
                            <?php elseif ($row['status'] == 'success'): ?>
                                <span class="badge bg-success small">สำเร็จ</span>
                            <?php else: ?>
                                <span class="badge bg-danger small">ยกเลิก/คืนเงิน</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 text-center">
                            <?php if ($row['status'] == 'pending'): ?>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-success btn-sm rounded-pill px-3 fw-bold"
                                        onclick="updateStatus(<?php echo $row['id']; ?>, 'complete', 'ยืนยันทำรายการสำเร็จและจัดส่งของแล้ว?')">
                                        <i class="fa-solid fa-check me-1"></i> เสร็จสิ้น
                                    </button>
                                    <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold"
                                        onclick="updateStatus(<?php echo $row['id']; ?>, 'cancel', 'ยืนยันยกเลิกและคืนเงินให้ลูกค้า?')">
                                        <i class="fa-solid fa-times me-1"></i> ยกเลิก/คืนเงิน
                                    </button>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#orders_table').DataTable({
            "order": [[0, "desc"]],
            "language": { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json" }
        });
    });

    function copyText(id) {
        var copyText = document.getElementById(id);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        Swal.fire({
            icon: 'success',
            title: 'คัดลอกสำเร็จ!',
            toast: true,
            position: 'top-end',
            timer: 1500,
            showConfirmButton: false
        });
    }

    function updateStatus(id, action, text) {
        Swal.fire({
            title: 'ยืนยันรายการ?',
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/roblox_orders_api.php',
                    data: { action: action, order_id: id }
                }).done(function (res) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message }).then(() => window.seamlessReload ? window.seamlessReload() : window.seamlessReload ? window.seamlessReload() : location.reload());
                }).fail(function(xhr) {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: xhr.responseJSON ? xhr.responseJSON.message : 'เกิดข้อผิดพลาด' });
                });
            }
        });
    }
</script>

