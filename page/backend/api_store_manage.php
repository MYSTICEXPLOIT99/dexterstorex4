<?php
// G:\xampp\htdocs\BOT\page\backend\api_store_manage.php
if (defined('IS_PARENT_STORE') && !IS_PARENT_STORE) {
    echo "<div class='p-5 text-center'><h3 class='text-muted'><i class='fa-solid fa-triangle-exclamation text-warning me-2'></i> ไม่พบหน้านี้หรือคุณไม่มีสิทธิ์เข้าถึง</h3></div>";
    return;
}
global $conn;

$cnt_stores_q = dd_q("SELECT COUNT(*) as c FROM rented_websites");
$cnt_stores = $cnt_stores_q ? ($cnt_stores_q->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) : 0;
?>

<style>
.store-table-header th {
    white-space: nowrap;
    color: #94a3b8 !important;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 14px 16px !important;
    border-bottom: 2px solid rgba(0, 201, 255, 0.25) !important;
}
.store-table-row td {
    padding: 14px 16px !important;
    vertical-align: middle;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
}
.action-btn-pill {
    padding: 5px 12px;
    font-size: 0.78rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.quick-btn {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #e2e8f0;
    font-size: 0.82rem;
    font-weight: 500;
    padding: 6px 14px;
    border-radius: 20px;
    transition: all 0.2s ease;
}
.quick-btn:hover {
    background: rgba(0, 201, 255, 0.2);
    border-color: rgba(0, 201, 255, 0.5);
    color: #38bdf8;
}
</style>

<div class="card border-0 shadow-lg p-4 rounded-4" style="background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(14px); border: 1px solid rgba(0, 201, 255, 0.2) !important;">
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 pb-3" style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
        <div>
            <h4 class="fw-bold mb-1 text-white d-flex align-items-center gap-2" style="font-family: var(--font-cyber); letter-spacing: 0.5px;">
                <i class="fa-solid fa-store text-info"></i> จัดการร้านค้าที่เช่าทั้งหมด (Rented Stores Management)
            </h4>
            <p class="text-white-50 small mb-0" style="font-size: 0.88rem;">
                ตรวจสอบรายชื่อผู้เช่า, ซับโดเมน, รหัสผ่านแอดมิน, จัดการวันหมดอายุ และเปิด-ปิดฟังก์ชันเสริม
            </p>
        </div>
        <span class="badge px-3.5 py-2 rounded-pill fs-6 fw-bold d-inline-flex align-items-center gap-2" 
              style="background: rgba(0, 201, 255, 0.12); border: 1px solid rgba(0, 201, 255, 0.35); color: #38bdf8;">
            <i class="fa-solid fa-server"></i> ร้านค้าทั้งหมด: <span class="text-white fw-bold"><?= $cnt_stores ?></span> ร้าน
        </span>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0" style="background: rgba(10, 15, 29, 0.7); border-radius: 16px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08);">
            <thead>
                <tr class="store-table-header">
                    <th style="min-width: 200px;">ชื่อร้านค้า & โดเมน</th>
                    <th style="min-width: 120px;">ผู้เช่า (User)</th>
                    <th style="min-width: 130px;">แพ็กเกจ</th>
                    <th style="min-width: 130px;">รหัสผ่านแอดมิน</th>
                    <th style="min-width: 160px;">วันหมดอายุ</th>
                    <th class="text-center" style="min-width: 160px;">ฟังก์ชันเสริม (Add-ons)</th>
                    <th class="text-center" style="min-width: 100px;">สถานะล็อก</th>
                    <th class="text-center" style="min-width: 200px;">การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rented_q = dd_q("SELECT r.*, u.username as owner_username, p.name as pkg_name 
                                 FROM rented_websites r 
                                 LEFT JOIN users u ON r.user_id = u.id 
                                 LEFT JOIN web_rental_packages p ON r.package_id = p.id 
                                 ORDER BY r.id DESC");
                $rented_list = ($rented_q && $rented_q instanceof PDOStatement) ? $rented_q->fetchAll(PDO::FETCH_ASSOC) : [];
                if (empty($rented_list)):
                ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-white-50">
                            <i class="fa-solid fa-inbox fs-1 mb-2 d-block opacity-40"></i>
                            ยังไม่มีร้านค้าที่ถูกสร้างในระบบ
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rented_list as $rw): 
                        $rw_name = !empty($rw['shop_name']) ? $rw['shop_name'] : ($rw['subdomain'] ?? 'ร้านค้า');
                        $rw_sub  = !empty($rw['subdomain']) ? $rw['subdomain'] : preg_replace('/[^a-zA-Z0-9_]/', '', $rw['shop_name']);
                        $rw_db   = !empty($rw['db_name']) ? $rw['db_name'] : ('store_' . preg_replace('/[^a-zA-Z0-9_]/', '', $rw_sub));
                        $is_locked = !empty($rw['is_locked']);
                        $has_w = !empty($rw['wheel_unlocked']);
                        $has_b = !empty($rw['backup_unlocked']);
                    ?>
                        <tr class="store-table-row">
                            <!-- Shop Name & Link -->
                            <td>
                                <div class="fw-bold text-white d-flex align-items-center gap-2 mb-1" style="font-size: 0.95rem;">
                                    <?php if ($is_locked): ?>
                                        <i class="fa-solid fa-lock text-warning" title="ร้านค้านี้ถูกล็อกอยู่" style="font-size: 0.85rem;"></i>
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($rw_name) ?></span>
                                </div>
                                <a href="<?= SYSTEM_PROTOCOL ?><?= htmlspecialchars($rw_sub) ?>.<?= SYSTEM_DOMAIN ?>/" target="_blank" 
                                   class="small text-info text-decoration-none d-inline-flex align-items-center gap-1 opacity-90 hover-opacity-100">
                                    <span><?= SYSTEM_PROTOCOL ?><?= htmlspecialchars($rw_sub) ?>.<?= SYSTEM_DOMAIN ?>/</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.7rem;"></i>
                                </a>
                            </td>

                            <!-- Owner User -->
                            <td>
                                <span class="badge bg-dark border border-secondary text-light font-monospace px-2.5 py-1" style="font-size: 0.78rem;">
                                    <i class="fa-solid fa-user me-1 text-info"></i> <?= htmlspecialchars($rw['owner_username'] ?? 'N/A') ?>
                                </span>
                            </td>

                            <!-- Package Name -->
                            <td>
                                <span class="text-white small fw-semibold"><?= htmlspecialchars($rw['pkg_name'] ?? 'Standard') ?></span>
                            </td>

                            <!-- Admin Password -->
                            <td>
                                <span class="badge bg-dark font-monospace text-warning border border-warning border-opacity-30 px-2.5 py-1.5" style="font-size: 0.82rem; letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($rw['admin_password']) ?>
                                </span>
                            </td>

                            <!-- Expiration Date -->
                            <td>
                                <?php 
                                if (!empty($rw['end_date'])) {
                                    $expire_time = strtotime($rw['end_date']);
                                    if ($expire_time > strtotime('2090-01-01')) {
                                        echo '<span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-40 px-2.5 py-1 rounded-pill"><i class="fa-solid fa-infinity me-1"></i> ตลอดชีพ</span>';
                                    } else {
                                        $diff = $expire_time - time();
                                        if ($diff <= 0) {
                                            echo '<span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-40 px-2.5 py-1 rounded-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i> หมดอายุ</span><br><small class="text-white-50" style="font-size:0.72rem;">' . date('d/m/Y H:i', $expire_time) . '</small>';
                                        } else {
                                            $r_days = floor($diff / 86400);
                                            $r_hours = floor(($diff % 86400) / 3600);
                                            $r_mins = floor(($diff % 3600) / 60);

                                            $time_str = "";
                                            if ($r_days > 0) $time_str .= $r_days . " วัน ";
                                            if ($r_hours > 0 || $r_days > 0) $time_str .= $r_hours . " ชม. ";
                                            $time_str .= $r_mins . " นาที";

                                            $badge_bg = ($r_days <= 3) ? 'bg-danger bg-opacity-25 text-danger border-danger' : (($r_days <= 7) ? 'bg-warning bg-opacity-25 text-warning border-warning' : 'bg-info bg-opacity-20 text-info border-info');
                                            echo '<span class="badge ' . $badge_bg . ' border border-opacity-40 px-2.5 py-1 rounded-pill" style="font-size:0.75rem;"><i class="fa-regular fa-clock me-1"></i> เหลือ ' . $time_str . '</span><br><small class="text-white-50" style="font-size:0.72rem;">หมดอายุ: ' . date('d/m/Y H:i', $expire_time) . '</small>';
                                        }
                                    }
                                } else {
                                    echo '<span class="badge bg-secondary-soft text-white-50">ไม่มีข้อมูล</span>';
                                }
                                ?>
                            </td>

                            <!-- Addons Toggle -->
                            <td class="text-center">
                                <div class="d-flex flex-column gap-1.5 align-items-center">
                                    <button type="button" 
                                            class="btn btn-sm <?= $has_w ? 'btn-success' : 'btn-outline-secondary text-white-50' ?> py-1 px-2.5 rounded-pill d-flex align-items-center justify-content-between" 
                                            style="font-size: 0.72rem; width: 135px; font-weight: 600;"
                                            onclick="toggleStoreAddon(<?= $rw['id'] ?>, 'wheel', <?= $has_w ? 0 : 1 ?>, '<?= htmlspecialchars(addslashes($rw_name)) ?>')">
                                        <span>🎡 วงล้อสุ่ม</span>
                                        <i class="fa-solid <?= $has_w ? 'fa-check text-white' : 'fa-xmark text-muted' ?>"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm <?= $has_b ? 'btn-info text-dark' : 'btn-outline-secondary text-white-50' ?> py-1 px-2.5 rounded-pill d-flex align-items-center justify-content-between" 
                                            style="font-size: 0.72rem; width: 135px; font-weight: 600;"
                                            onclick="toggleStoreAddon(<?= $rw['id'] ?>, 'backup', <?= $has_b ? 0 : 1 ?>, '<?= htmlspecialchars(addslashes($rw_name)) ?>')">
                                        <span>💾 สำรองข้อมูล</span>
                                        <i class="fa-solid <?= $has_b ? 'fa-check text-dark' : 'fa-xmark text-muted' ?>"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- Lock Switch -->
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block m-0 fs-5">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                           id="lockSwitch_<?= $rw['id'] ?>" 
                                           <?= $is_locked ? 'checked' : '' ?>
                                           onchange="toggleStoreLock(<?= $rw['id'] ?>, this.checked ? 1 : 0)"
                                           style="cursor: pointer; background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                                </div>
                                <div class="small fw-semibold <?= $is_locked ? 'text-warning' : 'text-muted' ?>" style="font-size: 0.72rem;">
                                    <?= $is_locked ? '🔒 ล็อกอยู่' : 'ปกติ' ?>
                                </div>
                            </td>

                            <!-- Action Buttons -->
                            <td class="text-center">
                                <div class="d-inline-flex gap-1.5 align-items-center justify-content-center flex-wrap">
                                    <!-- Extend Time -->
                                    <button type="button" class="btn btn-sm btn-outline-warning action-btn-pill" 
                                            onclick="openExtendModal(<?= $rw['id'] ?>, '<?= htmlspecialchars(addslashes($rw_name)) ?>', '<?= !empty($rw['end_date']) ? date('d/m/Y H:i', strtotime($rw['end_date'])) : 'ไม่มีข้อมูล' ?>')" 
                                            title="เพิ่ม/ขยายเวลาเช่าร้านค้า">
                                        <i class="fa-solid fa-clock-rotate-left"></i> ต่อเวลา
                                    </button>

                                    <!-- Reset Password -->
                                    <button type="button" class="btn btn-sm btn-outline-info action-btn-pill" 
                                            onclick="openResetPassModal(<?= $rw['id'] ?>, '<?= htmlspecialchars(addslashes($rw_name)) ?>', '<?= htmlspecialchars(addslashes($rw['admin_password'])) ?>')" 
                                            title="รีเซ็ตรหัสผ่านแอดมิน">
                                        <i class="fa-solid fa-key"></i> รหัสผ่าน
                                    </button>

                                    <!-- Delete Button -->
                                    <?php if ($is_locked): ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary action-btn-pill opacity-75" 
                                                onclick="Swal.fire({ icon: 'warning', title: 'ร้านค้าถูกล็อกอยู่!', text: 'ร้านค้านี้เปิดการล็อกความปลอดภัยไว้ กรุณาปิดสวิตช์ล็อกด้านข้างก่อนจึงจะสามารถกดลบได้', confirmButtonColor: '#f59e0b', background: '#0f172a', color: '#fff' })"
                                                title="ร้านค้านี้ถูกล็อกไว้ กรุณาปิดสวิตช์ล็อกก่อนลบ">
                                            <i class="fa-solid fa-lock text-warning"></i> ลบร้าน
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger action-btn-pill" 
                                                onclick="deleteRentedStore(<?= $rw['id'] ?>, '<?= htmlspecialchars(addslashes($rw_name)) ?>', '<?= htmlspecialchars(addslashes($rw_db)) ?>')" 
                                                title="ลบร้านค้าและ Database">
                                            <i class="fa-solid fa-trash"></i> ลบร้าน
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: เพิ่ม/ขยายเวลาเช่า (Extend Duration) พร้อมปุ่มด่วน -->
<div class="modal fade" id="extendStoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white rounded-4 shadow-lg p-2" style="background: rgba(15, 23, 42, 0.98); border: 1px solid rgba(0, 201, 255, 0.3);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-clock text-warning me-2"></i> เพิ่ม/ขยายเวลาเช่าร้านค้า</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formExtendStore">
                <input type="hidden" name="action" value="extend_store_duration">
                <input type="hidden" name="rental_id" id="extend_rental_id">
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label text-white-50 small mb-1">ชื่อร้านค้า:</label>
                        <input type="text" id="extend_shop_name" class="form-control bg-dark border-secondary text-white fw-bold" readonly style="background: rgba(0,0,0,0.3) !important;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50 small mb-1">วันหมดอายุเดิม:</label>
                        <input type="text" id="extend_current_end" class="form-control bg-dark border-secondary text-warning fw-bold font-monospace" readonly style="background: rgba(0,0,0,0.3) !important;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white fw-bold mb-1">จำนวนวันที่ต้องการเพิ่ม (วัน):</label>
                        <div class="input-group">
                            <input type="number" name="days" id="extendDaysInput" class="form-control bg-dark border-secondary text-white fw-bold text-center fs-5" value="30" min="1" required>
                            <span class="input-group-text bg-dark border-secondary text-white-50">วัน</span>
                        </div>
                    </div>

                    <!-- Quick select buttons (Image 1) -->
                    <div class="mt-3 pt-2" style="border-top: 1px solid rgba(255,255,255,0.08);">
                        <label class="text-white-50 small mb-2 d-block">เลือกเวลาด่วน:</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="quick-btn" onclick="setQuickDays(1)"><i class="fa-solid fa-calendar-day me-1"></i>+1 วัน</button>
                            <button type="button" class="quick-btn" onclick="setQuickDays(7)"><i class="fa-solid fa-calendar-week me-1"></i>+7 วัน</button>
                            <button type="button" class="quick-btn" onclick="setQuickDays(15)"><i class="fa-solid fa-calendar me-1"></i>+15 วัน</button>
                            <button type="button" class="quick-btn" onclick="setQuickDays(30)"><i class="fa-solid fa-calendar-check me-1"></i>+30 วัน</button>
                            <button type="button" class="quick-btn" onclick="setQuickDays(90)"><i class="fa-solid fa-calendar-plus me-1"></i>+90 วัน</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success fw-bold rounded-pill px-4" style="background: #10b981; border-color: #10b981;">
                        <i class="fa-solid fa-clock me-1"></i> ยืนยันเพิ่มเวลา
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: รีเซ็ตรหัสผ่านแอดมินร้านค้า (Reset Admin Pass) -->
<div class="modal fade" id="resetAdminPassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white rounded-4 shadow-lg p-2" style="background: rgba(15, 23, 42, 0.98); border: 1px solid rgba(0, 201, 255, 0.3);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-key text-info me-2"></i> รีเซ็ตรหัสผ่านแอดมินร้านค้า</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formResetAdminPass">
                <input type="hidden" name="action" value="reset_admin_password">
                <input type="hidden" name="rental_id" id="reset_rental_id">
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label text-white-50 small mb-1">ชื่อร้านค้า:</label>
                        <input type="text" id="reset_shop_name" class="form-control bg-dark border-secondary text-white fw-bold" readonly style="background: rgba(0,0,0,0.3) !important;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50 small mb-1">รหัสผ่านเดิม:</label>
                        <input type="text" id="reset_current_pass" class="form-control bg-dark border-secondary text-warning fw-bold font-monospace" readonly style="background: rgba(0,0,0,0.3) !important;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white fw-bold mb-1">รหัสผ่านใหม่:</label>
                        <input type="text" name="new_password" class="form-control bg-dark border-secondary text-white fw-bold" placeholder="กรอกรหัสผ่านใหม่สำหรับเข้าหลังบ้าน" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-info fw-bold text-dark rounded-pill px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกรหัสผ่านใหม่
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setQuickDays(d) {
    $('#extendDaysInput').val(d);
}

function openExtendModal(id, shopName, endDate) {
    $('#extend_rental_id').val(id);
    $('#extend_shop_name').val(shopName);
    $('#extend_current_end').val(endDate || 'ไม่มีข้อมูล');
    $('#extendDaysInput').val(30);
    new bootstrap.Modal(document.getElementById('extendStoreModal')).show();
}

function openResetPassModal(id, shopName, currentPass) {
    $('#reset_rental_id').val(id);
    $('#reset_shop_name').val(shopName);
    $('#reset_current_pass').val(currentPass || '');
    new bootstrap.Modal(document.getElementById('resetAdminPassModal')).show();
}

$('#formExtendStore').on('submit', function(e) {
    e.preventDefault();
    Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

    $.ajax({
        url: 'system/backend/rental_packages_api.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: res.message,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถขยายเวลาได้', 'error');
            }
        },
        error: function() {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
        }
    });
});

$('#formResetAdminPass').on('submit', function(e) {
    e.preventDefault();
    Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

    $.ajax({
        url: 'system/backend/rental_packages_api.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: res.message,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถรีเซ็ตรหัสผ่านได้', 'error');
            }
        },
        error: function() {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
        }
    });
});

function deleteRentedStore(id, shopName, dbName) {
    Swal.fire({
        title: 'ยืนยันการลบร้านค้าและ Database?',
        html: 'คุณต้องการลบร้านค้า <strong>' + shopName + '</strong><br>และทำลายฐานข้อมูล <code>' + dbName + '</code> ใช่หรือไม่?<br><span class="text-danger small">คำเตือน: ข้อมูลสินค้าและประวัติทั้งหมดในร้านค้านี้จะถูกลบถาวร</span>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '🗑️ ลบร้านค้าและ Database',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'กำลังลบข้อมูล...',
                text: 'กำลังลบไฟล์และฐานข้อมูล กรุณารอสักครู่',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: 'system/backend/rental_packages_api.php',
                type: 'POST',
                data: {
                    action: 'delete_rented_store',
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success' || res.status === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบสำเร็จ!',
                            text: res.message || 'ลบร้านค้าเรียบร้อยแล้ว',
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', res.message || 'ไม่สามารถลบร้านค้าได้', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
}

function toggleStoreLock(rentalId, targetLock) {
    let actionText = (targetLock === 1) ? 'ล็อกร้านค้าเพื่อป้องกันการเผลอลบ' : 'ปลดล็อกร้านค้า (จะสามารถกดลบได้)';
    Swal.fire({
        title: 'ยืนยันการเปลี่ยนสถานะล็อก?',
        text: 'คุณต้องการ' + actionText + ' หรือไม่',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: targetLock === 1 ? '#f59e0b' : '#3b82f6',
        cancelButtonColor: '#64748b',
        confirmButtonText: targetLock === 1 ? '🔒 ยืนยันล็อก' : '🔓 ยืนยันปลดล็อก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'กำลังดำเนินการ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            $.ajax({
                url: 'system/backend/rental_packages_api.php',
                type: 'POST',
                data: {
                    action: 'toggle_store_lock',
                    rental_id: rentalId,
                    locked: targetLock
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถเปลี่ยนสถานะได้', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
}

function toggleStoreAddon(rentalId, addonType, targetStatus, shopName) {
    let addonTitle = (addonType === 'wheel') ? 'วงล้อสุ่มของรางวัล (Lucky Wheel)' : 'ระบบสำรองข้อมูล (Backup & Restore)';
    let actionTitle = (targetStatus === 1) ? 'เปิดใช้งาน / เพิ่มฟังก์ชัน' : 'ปิดการใช้งาน / ลบฟังก์ชัน';
    let promptMsg = 'คุณต้องการ ' + actionTitle + ' <strong>' + addonTitle + '</strong> ให้กับร้าน <strong>' + shopName + '</strong> ใช่หรือไม่?';

    Swal.fire({
        title: 'ยืนยันการตั้งค่าฟังก์ชันเสริม?',
        html: promptMsg,
        icon: targetStatus === 1 ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: targetStatus === 1 ? '#10b981' : '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: targetStatus === 1 ? '✨ ยืนยันเปิดให้' : '🗑️ ยืนยันปิด/ลบออก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'กำลังดำเนินการ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            $.ajax({
                url: 'system/backend/rental_packages_api.php',
                type: 'POST',
                data: {
                    action: 'toggle_store_addon',
                    rental_id: rentalId,
                    addon: addonType,
                    status: targetStatus
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถเปลี่ยนสถานะได้', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
}
</script>
