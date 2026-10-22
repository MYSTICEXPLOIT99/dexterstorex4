<div class="p-4 p-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="fa-solid fa-clock text-xdvz-primary me-2"></i>
                จัดการพรีออเดอร์</h3>
            <p class="text-muted small mb-0">ส่งมอบสินค้าหรือยกเลิกรายการสั่งซื้อแบบพรีออเดอร์ของสมาชิก</p>
        </div>
    </div>

    <div class="table-responsive">
        <table id="preorder_table" class="table table-hover border-0 w-100">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 py-3 ps-4 text-muted small fw-bold">ID</th>
                    <th class="border-0 py-3 text-muted small fw-bold">ผู้สั่งซื้อ</th>
                    <th class="border-0 py-3 text-muted small fw-bold">สินค้า</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-center">จำนวน</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-center">ราคารวม</th>
                    <th class="border-0 py-3 text-muted small fw-bold">หมายเหตุจากผู้ซื้อ</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-center">สถานะ</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-center">เครื่องมือ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $get_preorders = dd_q("SELECT po.*, u.username, p.name as product_name FROM preorder_orders po 
                                       JOIN users u ON po.uid = u.id 
                                       JOIN box_product p ON po.product_id = p.id 
                                       ORDER BY po.id DESC");
                while ($row = $get_preorders->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <tr class="align-middle border-bottom" id="preorder-row-<?php echo $row['id']; ?>">
                        <td class="py-3 ps-4 text-dark small fw-bold">#<?php echo $row['id']; ?></td>
                        <td class="py-3 small"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="py-3 small fw-bold"><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td class="py-3 text-center small"><?php echo number_format($row['qty']); ?></td>
                        <td class="py-3 text-center small text-main fw-bold">฿<?php echo number_format($row['price'], 2); ?></td>
                        <td class="py-3 small text-muted"><?php echo htmlspecialchars($row['note'] ?? '-'); ?></td>
                        <td class="py-3 text-center preorder-status-cell">
                            <?php if ($row['status'] == "pending"): ?>
                                <span class="badge bg-warning text-dark px-3 rounded-pill">รอส่งมอบ</span>
                            <?php elseif ($row['status'] == "delivered"): ?>
                                <span class="badge bg-success px-3 rounded-pill">ส่งมอบแล้ว</span>
                            <?php else: ?>
                                <span class="badge bg-danger px-3 rounded-pill">ยกเลิก (คืนพ้อยท์แล้ว)</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 text-center preorder-action-cell">
                            <?php if ($row['status'] == "pending"): ?>
                                <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold me-1" onclick="openDeliverModal(<?php echo $row['id']; ?>)">
                                    <i class="fa-solid fa-paper-plane me-1"></i>ส่งมอบ
                                </button>
                                <button class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" onclick="cancelPreorder(<?php echo $row['id']; ?>)">
                                    <i class="fa-solid fa-rotate-left me-1"></i>ยกเลิก+คืนเงิน
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" disabled>เสร็จสิ้น</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Deliver Modal -->
<div class="modal fade" id="deliverModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="fw-bold text-dark mb-0">ส่งมอบสินค้าพรีออเดอร์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="deliver_order_id">
                <div class="mb-3">
                    <label class="form-label small fw-bold">ระบุเนื้อหา/รหัสสินค้าที่จะส่งมอบ</label>
                    <textarea id="deliver_content" class="form-control" rows="5" placeholder="เช่น ไอดี:รหัสผ่าน หรือ ลิงก์ของรางวัล..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-dark w-100 py-3 fw-bold rounded-3 shadow-sm" onclick="submitDeliver()">
                    <i class="fa-solid fa-check me-1"></i> ยืนยันการส่งมอบสินค้า
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#preorder_table').DataTable({ 
        "language": { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json" },
        "order": [[0, "desc"]]
    });
});

function openDeliverModal(id) {
    $("#deliver_order_id").val(id);
    $("#deliver_content").val("");
    new bootstrap.Modal('#deliverModal').show();
}

function submitDeliver() {
    let id = $("#deliver_order_id").val();
    let content = $("#deliver_content").val().trim();

    if(content === "") {
        Swal.fire({ icon: 'warning', title: 'คำเตือน', text: 'กรุณากรอกของรางวัลที่ต้องการส่งมอบ' });
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'system/backend/preorder_deliver.php',
        data: {
            order_id: id,
            deliver_content: content,
            action: 'deliver'
        },
        dataType: 'json'
    }).done(function (res) {
        bootstrap.Modal.getInstance('#deliverModal').hide();
        Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message });
        
        // Update UI Row Async
        const row = $(`#preorder-row-${id}`);
        row.find('.preorder-status-cell').html('<span class="badge bg-success px-3 rounded-pill">ส่งมอบแล้ว</span>');
        row.find('.preorder-action-cell').html('<button class="btn btn-sm btn-outline-secondary rounded-pill px-3" disabled>เสร็จสิ้น</button>');
    }).fail(function (xhr) {
        let msg = "เกิดข้อผิดพลาดในการเชื่อมต่อ";
        try {
            msg = JSON.parse(xhr.responseText).message;
        } catch(e) {}
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: msg });
    });
}

function cancelPreorder(id) {
    Swal.fire({
        title: 'ยืนยันยกเลิกคำสั่งซื้อ?',
        text: "ระบบจะทำการยกเลิกออเดอร์และคืนเงินพ้อยท์ให้กับสมาชิกทันที",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ยืนยันยกเลิกและคืนเงิน',
        cancelButtonText: 'กลับ',
        confirmButtonColor: '#ef4444'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'system/backend/preorder_deliver.php',
                data: {
                    order_id: id,
                    action: 'cancel'
                },
                dataType: 'json'
            }).done(function (res) {
                Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message });
                
                // Update UI Row Async
                const row = $(`#preorder-row-${id}`);
                row.find('.preorder-status-cell').html('<span class="badge bg-danger px-3 rounded-pill">ยกเลิก (คืนพ้อยท์แล้ว)</span>');
                row.find('.preorder-action-cell').html('<button class="btn btn-sm btn-outline-secondary rounded-pill px-3" disabled>เสร็จสิ้น</button>');
            }).fail(function (xhr) {
                let msg = "เกิดข้อผิดพลาดในการเชื่อมต่อ";
                try {
                    msg = JSON.parse(xhr.responseText).message;
                } catch(e) {}
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: msg });
            });
        }
    });
}
</script>
