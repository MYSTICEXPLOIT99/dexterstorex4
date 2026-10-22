<?php
$q_conf = dd_q("SELECT * FROM setting LIMIT 1");
$config = $q_conf ? $q_conf->fetch(PDO::FETCH_ASSOC) : [];
$banner_interval = max(1, intval($config['banner_interval'] ?? 1));
?>
<div class="p-4 p-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="section-eyebrow">// HERO BANNER SLIDER</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-images text-info me-2"></i> จัดการป้ายสไลด์หน้าร้าน (Banners)
            </h3>
            <p class="text-muted small mb-0">เพิ่มหรือลบป้ายแบนเนอร์ขนาดใหญ่ที่แสดงสไลด์ด้านบนของหน้าแรก (ใส่ได้หลายรูป)</p>
        </div>
        <button class="btn-cyber-primary" id="open_add_banner">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มป้ายแบนเนอร์ใหม่
        </button>
    </div>

    <!-- 1. Slider Speed Setting Card -->
    <div class="admin-inner-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h6 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-stopwatch text-warning me-2"></i> ตั้งเวลาสลับภาพสไลด์อัตโนมัติ (Slide Speed)
                </h6>
                <p class="text-muted small mb-0">กำหนดเวลาให้ป้ายแบนเนอร์สลับหรือเลื่อนไปยังรูปถัดไป (ระบุเป็นวินาที)</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group" style="width: 170px;">
                    <input type="number" id="banner_interval" min="1" max="60" class="admin-input form-control text-center fw-bold font-monospace" value="<?= $banner_interval ?>" style="font-size: 1rem;">
                    <span class="input-group-text bg-dark border-secondary border-opacity-50 text-white font-monospace">วินาที</span>
                </div>
                <button class="btn btn-warning fw-bold px-3 py-2 rounded-3" id="btn_save_speed" style="font-size:0.84rem;font-family:var(--font-cyber);">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกเวลา
                </button>
            </div>
        </div>
    </div>

    <!-- 2. Banner Grid / List -->
    <div class="admin-inner-card mb-4">
        <h6 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
            <i class="fa-solid fa-list-check text-info me-2"></i> รายการป้ายแบนเนอร์ที่กำลังแสดงผล
        </h6>

        <div class="row g-3">
            <?php
            $get_banners = dd_q("SELECT * FROM banners ORDER BY id DESC");
            if ($get_banners && $get_banners->rowCount() > 0):
                while ($b = $get_banners->fetch(PDO::FETCH_ASSOC)):
            ?>
                <div class="col-12 col-md-6 col-xl-6">
                    <div class="admin-inner-card p-3 h-100 border border-secondary border-opacity-25 position-relative">
                        <div class="ratio ratio-21x9 mb-3 rounded-3 overflow-hidden border border-secondary border-opacity-25" style="background:#07080c;">
                            <img src="<?= htmlspecialchars($b['img']) ?>" alt="Banner" style="object-fit:cover;width:100%;height:100%;" onerror="this.src='https://img2.pic.in.th/d65f7b1e-8cdc-47f6-8949-025da3ed382e.png'">
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white fw-bold mb-1" style="font-family:var(--font-cyber);"><?= htmlspecialchars($b['title'] ?? 'Banner') ?></h6>
                                <small class="text-muted text-truncate d-block" style="max-width:260px;font-size:0.75rem;"><?= htmlspecialchars($b['img']) ?></small>
                            </div>
                            <button class="btn-cyber-del" onclick="del_banner(<?= $b['id'] ?>)">
                                <i class="fa-solid fa-trash"></i> ลบป้าย
                            </button>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            else:
            ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="fa-solid fa-image fs-1 mb-3"></i>
                    <h5>ยังไม่มีป้ายแบนเนอร์</h5>
                    <p class="small">กดปุ่ม "เพิ่มป้ายแบนเนอร์ใหม่" เพื่อเพิ่มป้ายหน้าร้าน</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal เพิ่มป้ายแบนเนอร์ -->
<div class="modal fade" id="banner_modal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
            <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-plus text-info me-2"></i> เพิ่มป้ายแบนเนอร์หน้าร้าน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="admin-label">ชื่อเรียกป้ายแบนเนอร์ (Title)</label>
                    <input type="text" id="b_title" class="admin-input form-control" placeholder="เช่น โปรโมชั่น FiveM ประจำเดือน">
                </div>
                <div class="mb-3">
                    <label class="admin-label">URL รูปภาพป้ายแบนเนอร์ (Image URL) *</label>
                    <input type="text" id="b_img" class="admin-input form-control" placeholder="https://... (แนะนำขนาด 1920x600 หรือ 1200x500)">
                </div>
                <div class="mb-0">
                    <label class="admin-label">ลิงก์เมื่อคลิกที่ป้าย (Click Link)</label>
                    <input type="text" id="b_link" class="admin-input form-control" placeholder="เช่น # หรือ ?page=shop" value="#">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn-cyber-primary w-100 py-2" id="btn_save_banner">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกป้ายแบนเนอร์
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#banner_modal').appendTo('body');
});

$("#open_add_banner").click(function () {
    $('#banner_modal').appendTo('body');
    const myModal = new bootstrap.Modal(document.getElementById('banner_modal'), { keyboard: true });
    myModal.show();
});

// Save Slide Speed Interval
$("#btn_save_speed").click(function () {
    var intervalVal = $("#banner_interval").val();
    var formData = new FormData();
    formData.append('banner_interval', intervalVal);

    $.ajax({
        type: 'POST',
        url: 'system/backend/banner_setting.php',
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

$("#btn_save_banner").click(function () {
    var imgVal = $("#b_img").val().trim();
    if (!imgVal) {
        Swal.fire({
            icon: 'warning',
            title: 'แจ้งเตือน',
            text: 'กรุณากรอก URL รูปภาพป้ายแบนเนอร์',
            background: '#0a0a0f',
            color: '#ffffff',
            confirmButtonColor: '#4b5d78'
        });
        return;
    }

    var formData = new FormData();
    formData.append('title', $("#b_title").val());
    formData.append('img', imgVal);
    formData.append('link', $("#b_link").val());

    $.ajax({
        type: 'POST',
        url: 'system/backend/banner_insert.php',
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

function del_banner(id) {
    Swal.fire({
        title: 'ยืนยันการลบป้าย?',
        text: 'คุณต้องการลบป้ายแบนเนอร์นี้หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ลบป้าย',
        cancelButtonText: 'ยกเลิก',
        background: '#0a0a0f',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            var formData = new FormData();
            formData.append('id', id);
            $.ajax({
                type: 'POST',
                url: 'system/backend/banner_del.php',
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
                }
            });
        }
    });
}
</script>

