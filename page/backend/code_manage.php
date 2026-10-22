<div class="p-4 p-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="section-eyebrow">// GIFT & REDEEM CODES</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-ticket text-info me-2"></i> จัดการโค้ดแลกพ้อยท์
            </h3>
            <p class="text-muted small mb-0">สร้างโค้ดแจกพ้อยท์ฟรี พร้อมระบบจำกัดจำนวนผู้ใช้งาน</p>
        </div>
        <button class="btn-cyber-primary" id="open_insert">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มโค้ดใหม่
        </button>
    </div>

    <div class="admin-inner-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>รหัสโค้ด</th>
                        <th class="text-center">มูลค่า (Points)</th>
                        <th class="text-center">ใช้งานแล้ว</th>
                        <th class="text-center">จำกัดสูงสุด</th>
                        <th class="text-center pe-3">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $get_user = dd_q("SELECT * FROM redeem ORDER BY id DESC");
                    if ($get_user && $get_user->rowCount() > 0) {
                        while ($row = $get_user->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <tr>
                            <td class="ps-3 fw-bold text-white">#<?php echo $row['id']; ?></td>
                            <td>
                                <code class="px-3 py-1 rounded bg-dark border border-secondary border-opacity-50 text-info fw-bold"
                                    style="font-size: 0.9rem; font-family:var(--font-cyber);">
                                    <?php echo htmlspecialchars($row['code']); ?>
                                </code>
                            </td>
                            <td class="text-center text-success fw-bold" style="font-family:var(--font-cyber);">
                                ฿<?php echo number_format($row['prize'], 2); ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-25 text-light px-3 rounded-pill"><?php echo number_format($row['count']); ?> ครั้ง</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-25 text-info px-3 rounded-pill fw-bold"><?php echo number_format($row['max_count']); ?> ครั้ง</span>
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn-cyber-edit"
                                        onclick="get_detail(<?php echo $row['id']; ?>)">
                                        <i class="fa-solid fa-pen-to-square"></i> แก้ไข
                                    </button>
                                    <button class="btn-cyber-del"
                                        onclick="del('<?php echo $row['id']; ?>','<?php echo htmlspecialchars($row['code']); ?>')">
                                        <i class="fa-solid fa-trash"></i> ลบ
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">ยังไม่มีโค้ดแลกพ้อยท์</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal เพิ่มโค้ด -->
<div class="modal fade" id="code_insert" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-plus text-info me-2"></i> เพิ่มโค้ดใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="admin-label">รหัสโค้ด (Code)</label>
                    <input type="text" id="p_code" class="admin-input form-control" placeholder="เช่น CYGNUSX-FREE-100">
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="admin-label">รางวัลพ้อยท์ (Points)</label>
                        <input type="number" id="p_prize" class="admin-input form-control" placeholder="100">
                    </div>
                    <div class="col-6">
                        <label class="admin-label">จำกัดจำนวนคนใช้</label>
                        <input type="number" id="p_max" class="admin-input form-control" placeholder="10">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn-cyber-primary w-100 py-2" id="insert_btn">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกโค้ด
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal แก้ไขโค้ด -->
<div class="modal fade" id="code_detail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-pen-to-square text-warning me-2"></i> แก้ไขโค้ด
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="u_id">
                <div class="mb-3">
                    <label class="admin-label">รหัสโค้ด</label>
                    <input type="text" id="u_code" class="admin-input form-control">
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="admin-label">รางวัลพ้อยท์</label>
                        <input type="number" id="u_prize" class="admin-input form-control">
                    </div>
                    <div class="col-6">
                        <label class="admin-label">จำกัดจำนวนคนใช้</label>
                        <input type="number" id="u_max" class="admin-input form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn-cyber-primary w-100 py-2" id="save_btn">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการแก้ไข
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$("#open_insert").click(function () {
    const myModal = new bootstrap.Modal($('#code_insert'), { keyboard: false });
    myModal.show();
});

$("#insert_btn").click(function () {
    var formData = new FormData();
    formData.append('code', $("#p_code").val());
    formData.append('prize', $("#p_prize").val());
    formData.append('max', $("#p_max").val());

    $.ajax({
        type: 'POST',
        url: 'system/backend/code_insert.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: res.message,
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#4b5d78'
            }).then(() => {
                window.seamlessReload ? window.seamlessReload() : location.reload();
            });
        },
        error: function (err) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: err.responseJSON ? err.responseJSON.message : 'เกิดข้อผิดพลาด',
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#4b5d78'
            });
        }
    });
});

function get_detail(id) {
    var formData = new FormData();
    formData.append('id', id);
    $.ajax({
        type: 'POST',
        url: 'system/backend/call/code_detail.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            $("#u_id").val(res.id || id);
            $("#u_code").val(res.code);
            $("#u_prize").val(res.prize);
            $("#u_max").val(res.max_count);
            const myModal = new bootstrap.Modal($('#code_detail'), { keyboard: false });
            myModal.show();
        }
    });
}

$("#save_btn").click(function () {
    var formData = new FormData();
    formData.append('id', $("#u_id").val());
    formData.append('code', $("#u_code").val());
    formData.append('prize', $("#p_prize").val() || $("#u_prize").val());
    formData.append('max', $("#u_max").val());

    $.ajax({
        type: 'POST',
        url: 'system/backend/code_update.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: res.message,
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#4b5d78'
            }).then(() => {
                location.reload();
            });
        },
        error: function (err) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: err.responseJSON ? err.responseJSON.message : 'เกิดข้อผิดพลาด',
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#4b5d78'
            });
        }
    });
});

function del(id, name) {
    Swal.fire({
        title: 'ยืนยันการลบโค้ด?',
        text: `คุณต้องการลบโค้ด ${name} หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ลบโค้ด',
        cancelButtonText: 'ยกเลิก',
        background: '#0a0a0f',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            var formData = new FormData();
            formData.append('id', id);
            $.ajax({
                type: 'POST',
                url: 'system/backend/code_del.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบสำเร็จ',
                        text: res.message,
                        background: '#0a0a0f',
                        color: '#ffffff',
                        confirmButtonColor: '#4b5d78'
                    }).then(() => {
                        window.seamlessReload ? window.seamlessReload() : location.reload();
                    });
                }
            });
        }
    });
}
</script>

