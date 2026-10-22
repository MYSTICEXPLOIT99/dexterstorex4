<div class="p-4 p-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="section-eyebrow">// PROMOTION & DISCOUNTS</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-percent text-danger me-2"></i> โค้ดส่วนลดสินค้า
            </h3>
            <p class="text-muted small mb-0">สร้างและจัดการโค้ดส่วนลดสำหรับลูกค้าใช้สั่งซื้อสินค้า</p>
        </div>
        <button class="btn-cyber-primary" id="open_discount_insert">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มโค้ดส่วนลด
        </button>
    </div>

    <div class="admin-inner-card">
        <div class="table-responsive">
            <table id="discount_table" class="admin-table">
                <thead>
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>รหัสโค้ด</th>
                        <th class="text-center">ส่วนลด</th>
                        <th class="text-center">ใช้แล้ว / จำกัด</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center pe-3">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $get_codes = dd_q("SELECT * FROM discount_codes ORDER BY id DESC");
                    if ($get_codes && $get_codes->rowCount() > 0):
                        while ($row = $get_codes->fetch(PDO::FETCH_ASSOC)):
                            $discount_display = $row['discount_type'] == 'percent'
                                ? number_format($row['discount_value']) . '%'
                                : '฿' . number_format($row['discount_value']);
                            $usage_display = $row['usage_limit']
                                ? $row['used_count'] . ' / ' . $row['usage_limit']
                                : $row['used_count'] . ' / ไม่จำกัด';
                            $is_active = $row['is_active'] == 1;
                            ?>
                            <tr>
                                <td class="ps-3 fw-bold text-white">#<?= $row['id'] ?></td>
                                <td>
                                    <code class="px-3 py-1 rounded bg-dark border border-secondary border-opacity-50 text-info fw-bold"
                                        style="font-size: 0.9rem; font-family:var(--font-cyber);">
                                        <?= htmlspecialchars($row['code']) ?>
                                    </code>
                                </td>
                                <td class="text-center text-success fw-bold" style="font-family:var(--font-cyber);">
                                    <?= $discount_display ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary bg-opacity-25 text-light px-3 rounded-pill"><?= $usage_display ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($is_active): ?>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-3 rounded-pill">เปิดใช้งาน</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 px-3 rounded-pill">ปิดใช้งาน</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn-cyber-edit"
                                            onclick="editDiscount(<?= $row['id'] ?>)">
                                            <i class="fa-solid fa-pen-to-square"></i> แก้ไข
                                        </button>
                                        <button class="btn-cyber-del"
                                            onclick="deleteDiscount(<?= $row['id'] ?>, '<?= htmlspecialchars($row['code']) ?>')">
                                            <i class="fa-solid fa-trash"></i> ลบ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-ticket fa-2x mb-3 opacity-50"></i>
                                <p class="mb-1">ยังไม่มีโค้ดส่วนลดในระบบ</p>
                                <p class="small">กดปุ่ม "เพิ่มโค้ดส่วนลด" เพื่อสร้างโค้ดใหม่</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal เพิ่มโค้ดส่วนลด -->
<div class="modal fade" id="discount_insert_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-plus text-danger me-2"></i> สร้างโค้ดส่วนลดใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="admin-label">รหัสโค้ด (Discount Code)</label>
                    <input type="text" id="new_code" class="admin-input form-control" placeholder="เช่น SAVE20, CYGNUSX10">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="admin-label">ประเภทส่วนลด</label>
                        <select id="new_type" class="admin-input form-select">
                            <option value="percent">เปอร์เซ็นต์ (%)</option>
                            <option value="fixed">จำนวนเงิน (฿)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="admin-label">มูลค่าส่วนลด</label>
                        <input type="number" id="new_value" class="admin-input form-control" placeholder="10">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="admin-label">ยอดซื้อขั้นต่ำ (฿)</label>
                        <input type="number" id="new_min" class="admin-input form-control" value="0">
                    </div>
                    <div class="col-6">
                        <label class="admin-label">จำกัดจำนวนใช้ (ครั้ง)</label>
                        <input type="number" id="new_limit" class="admin-input form-control" placeholder="ปล่อยว่าง = ไม่จำกัด">
                    </div>
                </div>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="new_active" checked style="background-color: #07080c; border-color: rgba(255,255,255,0.2);">
                    <label class="form-check-label text-white small" for="new_active">เปิดใช้งานโค้ดทันที</label>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn-cyber-primary w-100 py-2" id="save_new_discount">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกโค้ดส่วนลด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal แก้ไขโค้ดส่วนลด -->
<div class="modal fade" id="discount_edit_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-pen-to-square text-warning me-2"></i> แก้ไขโค้ดส่วนลด
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="edit_id">
                <div class="mb-3">
                    <label class="admin-label">รหัสโค้ด (Discount Code)</label>
                    <input type="text" id="edit_code" class="admin-input form-control">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="admin-label">ประเภทส่วนลด</label>
                        <select id="edit_type" class="admin-input form-select">
                            <option value="percent">เปอร์เซ็นต์ (%)</option>
                            <option value="fixed">จำนวนเงิน (฿)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="admin-label">มูลค่าส่วนลด</label>
                        <input type="number" id="edit_value" class="admin-input form-control">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="admin-label">ยอดซื้อขั้นต่ำ (฿)</label>
                        <input type="number" id="edit_min" class="admin-input form-control">
                    </div>
                    <div class="col-6">
                        <label class="admin-label">จำกัดจำนวนใช้ (ครั้ง)</label>
                        <input type="number" id="edit_limit" class="admin-input form-control" placeholder="ปล่อยว่าง = ไม่จำกัด">
                    </div>
                </div>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="edit_active" style="background-color: #07080c; border-color: rgba(255,255,255,0.2);">
                    <label class="form-check-label text-white small" for="edit_active">เปิดใช้งานโค้ด</label>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn-cyber-primary w-100 py-2" id="update_discount">
                    <i class="fa-solid fa-floppy-disk me-1"></i> อัปเดตโค้ดส่วนลด
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Move modals to body
        $('#discount_insert_modal, #discount_edit_modal').appendTo('body');
    });

    $("#open_discount_insert").click(() => {
        new bootstrap.Modal('#discount_insert_modal').show();
    });

    // Save new discount code
    $("#save_new_discount").click(function () {
        const data = {
            code: $("#new_code").val(),
            type: $("#new_type").val(),
            value: $("#new_value").val(),
            min: $("#new_min").val(),
            limit: $("#new_limit").val(),
            active: $("#new_active").is(':checked') ? 1 : 0
        };

        if (!data.code || !data.value) {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'กรุณากรอกรหัสโค้ดและมูลค่าส่วนลด' });
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'system/backend/discount_insert.php',
            data: data,
            dataType: 'json'
        }).done(function (res) {
            if (res.status === 'success') {
                Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message }).then(() => window.seamlessReload ? window.seamlessReload() : window.seamlessReload ? window.seamlessReload() : location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message });
            }
        });
    });

    // Edit discount
    function editDiscount(id) {
        $.ajax({
            type: 'POST',
            url: 'system/backend/call/discount_detail.php',
            data: { id: id },
            dataType: 'json'
        }).done(function (res) {
            $("#edit_id").val(res.id);
            $("#edit_code").val(res.code);
            $("#edit_type").val(res.discount_type);
            $("#edit_value").val(res.discount_value);
            $("#edit_min").val(res.min_purchase);
            $("#edit_limit").val(res.usage_limit);
            $("#edit_active").prop('checked', res.is_active == 1);
            new bootstrap.Modal('#discount_edit_modal').show();
        });
    }

    // Update discount
    $("#update_discount").click(function () {
        const data = {
            id: $("#edit_id").val(),
            code: $("#edit_code").val(),
            type: $("#edit_type").val(),
            value: $("#edit_value").val(),
            min: $("#edit_min").val(),
            limit: $("#edit_limit").val(),
            active: $("#edit_active").is(':checked') ? 1 : 0
        };

        $.ajax({
            type: 'POST',
            url: 'system/backend/discount_update.php',
            data: data,
            dataType: 'json'
        }).done(function (res) {
            if (res.status === 'success') {
                Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message }).then(() => window.seamlessReload ? window.seamlessReload() : window.seamlessReload ? window.seamlessReload() : location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message });
            }
        });
    });

    // Delete discount
    function deleteDiscount(id, code) {
        Swal.fire({
            title: 'ลบโค้ดส่วนลด?',
            text: 'ยืนยันลบโค้ด: ' + code,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ใช่, ลบเลย',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#ef4444'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/discount_delete.php',
                    data: { id: id },
                    dataType: 'json'
                }).done(function (res) {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message }).then(() => window.seamlessReload ? window.seamlessReload() : window.seamlessReload ? window.seamlessReload() : location.reload());
                    }
                });
            }
        });
    }
</script>


