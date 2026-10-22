<div class="p-4 p-lg-5" data-aos="fade-up">
    <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="section-eyebrow" style="color:#38bdf8;">// [ MODULE: USER_AUTHENTICATION_MGMT ]</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-users-gear text-info me-2"></i> ระบบจัดการฐานข้อมูลสมาชิก
            </h3>
            <p class="text-muted small mb-0">ตรวจสอบบัญชีผู้ใช้ ปรับยอดเงินคงเหลือ เปลี่ยนรหัสผ่าน หรือจัดการสิทธิ์แอดมิน</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge px-3 py-1.5" style="background:rgba(56,189,248,0.1); border:1px solid rgba(56,189,248,0.3); color:#38bdf8; font-family:'Space Mono',monospace; font-size:0.7rem; border-radius:999px;">
                <i class="fa-solid fa-server me-1"></i> DB_NODE: ACTIVE
            </span>
        </div>
    </div>

    <div class="admin-inner-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="ps-3" style="width:70px;">ID</th>
                        <th>ข้อมูลผู้ใช้งาน</th>
                        <th>ระดับสิทธิ์ (Role)</th>
                        <th class="text-center">คงเหลือ (Balance)</th>
                        <th class="text-center">ยอดเติมรวม</th>
                        <th class="text-center pe-3" style="width:160px;">คำสั่ง (Actions)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $get_user = dd_q("SELECT * FROM users WHERE username NOT IN ('admin') ORDER BY id DESC");
                    if ($get_user && $get_user->rowCount() > 0) {
                        while ($row = $get_user->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <tr>
                            <td class="ps-3 fw-bold text-muted" style="font-family:'Space Mono',monospace;">#<?= $row['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px; background:#07080e; border:1.5px solid <?= (($row['rank'] ?? 0) == 1) ? '#38bdf8' : 'rgba(255,255,255,0.15)' ?>; box-shadow:0 0 10px <?= (($row['rank'] ?? 0) == 1) ? 'rgba(56,189,248,0.3)' : 'transparent' ?>;">
                                        <i class="fa-solid <?= (($row['rank'] ?? 0) == 1) ? 'fa-user-shield text-info' : 'fa-user text-muted' ?>" style="font-size:0.85rem;"></i>
                                    </div>
                                    <div>
                                        <div class="text-white fw-bold" style="font-family:var(--font-cyber); font-size:0.9rem; letter-spacing:0.5px;"><?= htmlspecialchars($row['username']) ?></div>
                                        <div class="text-muted" style="font-size:0.68rem; font-family:'Space Mono',monospace;">UID: <?= $row['id'] ?> • <?= date('d/m/Y', strtotime($row['date'] ?? 'now')) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if (($row['rank'] ?? 0) == 1): ?>
                                    <span class="badge-admin-mech"><i class="fa-solid fa-shield-halved"></i> ADMIN</span>
                                <?php else: ?>
                                    <span class="badge-member-mech"><i class="fa-solid fa-user"></i> MEMBER</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-info" style="font-family:'Space Grotesk', 'Rajdhani', sans-serif !important; font-size:1rem; letter-spacing:0.3px;">฿<?= number_format($row['point'], 2) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="text-white fw-bold" style="font-family:'Space Grotesk', 'Rajdhani', sans-serif !important; font-size:0.92rem; color:#94a3b8 !important;">฿<?= number_format($row['total'], 2) ?></span>
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn-cyber-edit"
                                        onclick="get_detail(<?= $row['id'] ?>)">
                                        <i class="fa-solid fa-pen-to-square"></i> แก้ไข
                                    </button>
                                    <button class="btn-cyber-del"
                                        onclick="del('<?= $row['id'] ?>','<?= htmlspecialchars($row['username']) ?>')">
                                        <i class="fa-solid fa-trash-can"></i> ลบ
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5" style="font-family:var(--font-cyber);">
                                <i class="fa-solid fa-users-slash fs-3 mb-2 d-block opacity-50"></i>
                                ยังไม่มีสมาชิกในระบบ
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal แก้ไขข้อมูลผู้ใช้ -->
<div class="modal fade" id="product_detail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-user-pen text-info me-2"></i> แก้ไขข้อมูลผู้ใช้งาน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="user_id">
                <div class="mb-3">
                    <label class="admin-label">ชื่อผู้ใช้งาน (Username)</label>
                    <input type="text" id="username" class="admin-input form-control" style="background:#030407 !important; color:#94a3b8 !important;" disabled>
                </div>
                <div class="mb-3">
                    <label class="admin-label">รหัสผ่านใหม่ (ปล่อยว่างหากไม่ต้องการเปลี่ยน)</label>
                    <input type="text" id="password" class="admin-input form-control" placeholder="พิมพ์รหัสผ่านใหม่ที่นี่...">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="admin-label">เงินคงเหลือ (฿)</label>
                        <input type="number" step="0.01" id="points" class="admin-input form-control">
                    </div>
                    <div class="col-6">
                        <label class="admin-label">ยอดเติมเงินรวม (฿)</label>
                        <input type="number" step="0.01" id="total" class="admin-input form-control">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="admin-label">ระดับสิทธิ์ (Role)</label>
                    <select id="user_rank" class="admin-input form-select">
                        <option value="0">MEMBER (ผู้ใช้ทั่วไป)</option>
                        <option value="1">ADMIN (ผู้ดูแลระบบ)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn-cyber-primary w-100 py-2" id="save_btn">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#product_detail').appendTo('body');
});

function get_detail(id) {
    var formData = new FormData();
    formData.append('id', id);
    $.ajax({
        type: 'POST',
        url: 'system/backend/call/user_detail.php',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (res) {
            $("#user_id").val(res.id);
            $("#username").val(res.username);
            $("#password").val('');
            $("#points").val(res.point);
            $("#total").val(res.total);
            $("#user_rank").val(res.rank || 0);
            new bootstrap.Modal('#product_detail').show();
        },
        error: function(err) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: err.responseJSON ? err.responseJSON.message : 'ไม่สามารถดึงข้อมูลผู้ใช้ได้',
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#4b5d78'
            });
        }
    });
}

$("#save_btn").click(function () {
    var formData = new FormData();
    formData.append('id', $("#user_id").val());
    formData.append('password', $("#password").val());
    formData.append('point', $("#points").val());
    formData.append('total', $("#total").val());
    formData.append('rank', $("#user_rank").val());

    $.ajax({
        type: 'POST',
        url: 'system/backend/user_setting.php',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
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
                text: err.responseJSON ? err.responseJSON.message : 'เกิดข้อผิดพลาดในการบันทึกข้อมูล',
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#4b5d78'
            });
        }
    });
});

function del(id, name) {
    Swal.fire({
        title: 'ยืนยันการลบสมาชิก?',
        text: `คุณต้องการลบผู้ใช้ ${name} ออกจากระบบหรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ลบสมาชิก',
        cancelButtonText: 'ยกเลิก',
        background: '#0a0a0f',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            var formData = new FormData();
            formData.append('id', id);
            $.ajax({
                type: 'POST',
                url: 'system/backend/user_del.php',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
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
                },
                error: function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: err.responseJSON ? err.responseJSON.message : 'ไม่สามารถลบข้อมูลได้',
                        background: '#0a0a0f',
                        color: '#ffffff',
                        confirmButtonColor: '#4b5d78'
                    });
                }
            });
        }
    });
}
</script>
