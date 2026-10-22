<div class="p-4 p-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="section-eyebrow">// CATEGORY MANAGEMENT</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-layer-group text-warning me-2"></i> จัดการหมวดหมู่สินค้า
            </h3>
            <p class="text-muted small mb-0">สร้าง แก้ไข หรือลบหมวดหมู่เพื่อจัดระเบียบสินค้าในร้าน</p>
        </div>
        <button class="btn-cyber-primary" id="open_insert">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มหมวดหมู่ใหม่
        </button>
    </div>

    <div class="admin-inner-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>ภาพแบนเนอร์</th>
                        <th>ชื่อหมวดหมู่</th>
                        <th>คำอธิบาย</th>
                        <th class="text-center pe-3">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $get_user = dd_q("SELECT * FROM category ORDER BY c_id DESC");
                    if ($get_user && $get_user->rowCount() > 0) {
                        while ($row = $get_user->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <tr>
                            <td class="ps-3 fw-bold text-white">#<?php echo $row['c_id']; ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($row['img']); ?>" class="rounded-3 shadow-sm border border-secondary border-opacity-25"
                                    width="70px" height="44px" style="object-fit: cover;" onerror="this.src='assets/icon/box.png'">
                            </td>
                            <td>
                                <span class="text-white fw-bold" style="font-family:var(--font-cyber);"><?php echo htmlspecialchars($row['c_name']); ?></span>
                            </td>
                            <td>
                                <span class="text-muted small"><?php echo htmlspecialchars($row['des']); ?></span>
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn-cyber-edit"
                                        onclick="get_detail(<?php echo $row['c_id']; ?>)">
                                        <i class="fa-solid fa-pen-to-square"></i> แก้ไข
                                    </button>
                                    <button class="btn-cyber-del"
                                        onclick="del('<?php echo $row['c_id']; ?>','<?php echo htmlspecialchars($row['c_name']); ?>')">
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
                            <td colspan="5" class="text-center text-muted py-4">ยังไม่มีหมวดหมู่สินค้า</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal เพิ่มหมวดหมู่ -->
<div class="modal fade" id="category_insert" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-plus text-warning me-2"></i> เพิ่มหมวดหมู่ใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="admin-label">ชื่อหมวดหมู่ (Category Title)</label>
                    <input type="text" id="p_name" class="admin-input form-control" placeholder="เช่น PROGRAM PRODUCT">
                </div>
                <div class="mb-3">
                    <label class="admin-label">URL รูปภาพแบนเนอร์ (Banner URL)</label>
                    <input type="text" id="p_img" class="admin-input form-control" placeholder="https://...">
                </div>
                <input type="hidden" id="p_type" value="0">
                <div class="mb-0">
                    <label class="admin-label">คำอธิบายหมวดหมู่</label>
                    <textarea id="p_des" rows="3" class="admin-input form-control" placeholder="คำอธิบายสั้นๆ..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn-cyber-primary w-100 py-2" id="insert_btn">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกหมวดหมู่
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal แก้ไขหมวดหมู่ -->
<div class="modal fade" id="category_detail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-pen-to-square text-warning me-2"></i> แก้ไขหมวดหมู่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="u_c_id">
                <div class="mb-3">
                    <label class="admin-label">ชื่อหมวดหมู่</label>
                    <input type="text" id="u_name" class="admin-input form-control">
                </div>
                <div class="mb-3">
                    <label class="admin-label">URL รูปภาพแบนเนอร์</label>
                    <input type="text" id="u_img" class="admin-input form-control">
                </div>
                <div class="mb-0">
                    <label class="admin-label">คำอธิบาย</label>
                    <textarea id="u_des" rows="3" class="admin-input form-control"></textarea>
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
    const myModal = new bootstrap.Modal($('#category_insert'), { keyboard: false });
    myModal.show();
});

$("#insert_btn").click(function () {
    var name = $("#p_name").val().trim();
    if (!name) {
        Swal.fire({
            icon: 'warning',
            title: 'แจ้งเตือน',
            text: 'กรุณากรอกชื่อหมวดหมู่สินค้า',
            background: '#0a0a0f',
            color: '#ffffff',
            confirmButtonColor: '#4b5d78'
        });
        return;
    }

    var formData = new FormData();
    formData.append('c_name', name);
    formData.append('name', name);
    formData.append('img', $("#p_img").val().trim());
    formData.append('des', $("#p_des").val().trim());
    formData.append('c_type', 0);
    formData.append('type', 0);

    $.ajax({
        type: 'POST',
        url: 'system/backend/category_insert.php',
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
                text: err.responseJSON ? err.responseJSON.message : 'เกิดข้อผิดพลาดในการบันทึกข้อมูล',
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#4b5d78'
            });
        }
    });
});

function get_detail(id) {
    var formData = new FormData();
    formData.append('c_id', id);
    $.ajax({
        type: 'POST',
        url: 'system/backend/call/category_detail.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            $("#u_c_id").val(id);
            $("#u_name").val(res.c_name);
            $("#u_img").val(res.img);
            $("#u_des").val(res.des);
            const myModal = new bootstrap.Modal($('#category_detail'), { keyboard: false });
            myModal.show();
        }
    });
}

$("#save_btn").click(function () {
    var name = $("#u_name").val().trim();
    if (!name) {
        Swal.fire({
            icon: 'warning',
            title: 'แจ้งเตือน',
            text: 'กรุณากรอกชื่อหมวดหมู่สินค้า',
            background: '#0a0a0f',
            color: '#ffffff',
            confirmButtonColor: '#4b5d78'
        });
        return;
    }

    var formData = new FormData();
    formData.append('c_id', $("#u_c_id").val());
    formData.append('c_name', name);
    formData.append('name', name);
    formData.append('img', $("#u_img").val().trim());
    formData.append('des', $("#u_des").val().trim());
    formData.append('c_type', 0);
    formData.append('type', 0);

    $.ajax({
        type: 'POST',
        url: 'system/backend/category_update.php',
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
        title: 'ยืนยันการลบหมวดหมู่?',
        text: `คุณต้องการลบหมวดหมู่ ${name} หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ลบหมวดหมู่',
        cancelButtonText: 'ยกเลิก',
        background: '#0a0a0f',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            var formData = new FormData();
            formData.append('c_id', id);
            $.ajax({
                type: 'POST',
                url: 'system/backend/category_del.php',
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
