<?php
if (defined('IS_PARENT_STORE') && !IS_PARENT_STORE) {
    echo "<div class='p-5 text-center'><h3 class='text-muted'><i class='fa-solid fa-triangle-exclamation text-warning me-2'></i> ไม่พบหน้านี้หรือคุณไม่มีสิทธิ์เข้าถึง</h3></div>";
    return;
}
global $conn;
if (isset($conn) && $conn instanceof PDO) {
    try {
        $conn->exec("CREATE TABLE IF NOT EXISTS `web_rental_packages` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `description` text DEFAULT NULL,
          `price` decimal(10,2) NOT NULL DEFAULT 0.00,
          `duration_days` int(11) NOT NULL DEFAULT 30,
          `image` varchar(500) DEFAULT 'https://image.fivempro.xyz/storage/86474fabad3b223270ec5b045a1a14de.png',
          `demo_url` varchar(255) DEFAULT '/stores/demo/',
          `status` varchar(20) NOT NULL DEFAULT 'active',
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

        $conn->exec("CREATE TABLE IF NOT EXISTS `rented_websites` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `package_id` int(11) NOT NULL,
          `shop_name` varchar(100) NOT NULL,
          `subdomain` varchar(100) NOT NULL,
          `db_name` varchar(100) NOT NULL,
          `db_user` varchar(100) NOT NULL,
          `db_pass` varchar(100) NOT NULL,
          `admin_username` varchar(100) NOT NULL DEFAULT 'admin',
          `admin_password` varchar(100) NOT NULL,
          `status` varchar(20) NOT NULL DEFAULT 'active',
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          `end_date` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    } catch(Exception $e) {}
}

// Fetch all packages
$packages_q = dd_q("SELECT * FROM web_rental_packages ORDER BY id DESC");
$packages = ($packages_q && $packages_q instanceof PDOStatement) ? $packages_q->fetchAll(PDO::FETCH_ASSOC) : [];

// Fetch master add-on & renewal pricing
$m_set_q = dd_q("SELECT * FROM setting LIMIT 1");
$m_set = ($m_set_q && $m_set_q instanceof PDOStatement) ? $m_set_q->fetch(PDO::FETCH_ASSOC) : [];
$adm_addon_wheel  = floatval($m_set['addon_wheel_price'] ?? 150);
$adm_addon_backup = floatval($m_set['addon_backup_price'] ?? 99);
$adm_renew_7      = floatval($m_set['renewal_price_7d'] ?? 50);
$adm_renew_15     = floatval($m_set['renewal_price_15d'] ?? 90);
$adm_renew_30     = floatval($m_set['renewal_price_30d'] ?? 150);
?>

<div class="p-4 p-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-globe text-xez-primary me-2"></i> จัดการแพ็กเกจเช่าเว็บไซต์ (Web Rental Packages)</h3>
            <p class="text-muted small mb-0">กำหนดแพ็กเกจ ราคา และฟีเจอร์สำหรับบริการเปิดร้านค้าสำเร็จรูป</p>
        </div>
        <button type="button" class="btn xez-btn-cyan fw-bold px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addPackageModal">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มแพ็กเกจใหม่
        </button>
    </div>

    <!-- Addon & Renewal Pricing Settings Card -->
    <div class="admin-inner-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 flex-wrap gap-3" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div>
                <h5 class="fw-bold mb-1 text-white" style="font-family:var(--font-cyber);"><i class="fa-solid fa-tags text-warning me-2"></i> ตั้งค่าราคาฟังก์ชันเสริม & การต่ออายุเว็บไซต์ (Add-on & Renewal Pricing)</h5>
                <p class="text-muted small mb-0">กำหนดราคาขายฟังก์ชันเสริมและราคาค่าต่ออายุเว็บไซต์สำหรับลูกค้าเว็บลูก</p>
            </div>
            <button type="button" class="btn btn-warning fw-bold px-4 py-2 rounded-3" id="btnSaveAddonPrices" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการตั้งค่าราคา
            </button>
        </div>

        <form id="formAddonPrices">
            <input type="hidden" name="action" value="save_addon_prices">

            <!-- Section 1: Add-ons Pricing -->
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-primary bg-opacity-20 text-info border border-primary border-opacity-25 px-2.5 py-1.5 rounded-pill small fw-bold">
                        <i class="fa-solid fa-gem me-1"></i> ราคาขายฟังก์ชันเสริม (Add-on Features)
                    </span>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-4" style="background: rgba(10,10,15,0.4); border: 1px solid rgba(0, 201, 255, 0.15);">
                            <label class="form-label text-white small fw-bold d-flex align-items-center justify-content-between mb-2">
                                <span><i class="fa-solid fa-database text-info me-1.5"></i> ฟังก์ชันสำรองข้อมูล (Backup & Restore)</span>
                                <span class="badge bg-dark text-white-50">บาท / ปลดล็อก</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-white-50" style="border-color: rgba(0, 201, 255, 0.15) !important;">฿</span>
                                <input type="number" step="0.01" min="0" name="addon_backup_price" class="form-control bg-dark text-white fw-bold fs-6" style="border-color: rgba(0, 201, 255, 0.15) !important;" value="<?= $adm_addon_backup ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: rgba(10,10,15,0.4); border: 1px solid rgba(0, 201, 255, 0.15);">
                            <label class="form-label text-white small fw-bold d-flex align-items-center justify-content-between mb-2">
                                <span><i class="fa-solid fa-eye text-warning me-1.5"></i> แสดงปุ่ม "เช่าเว็บ" บนแถบนำทาง (Parent Tab)</span>
                                <span class="badge bg-dark text-white-50">เปิด-ปิด</span>
                            </label>
                            <div class="d-flex align-items-center justify-content-between px-2 py-1">
                                <span class="text-muted small">แสดงแถบเมนู "บอทดิสคอร์ส"</span>
                                <div class="form-check form-switch m-0 fs-5">
                                    <input class="form-check-input" type="checkbox" name="show_rental_tab" value="1" <?= intval($config['show_rental_tab'] ?? 1) === 1 ? 'checked' : '' ?> style="cursor: pointer; background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Website Renewal Tiers (7, 15, 30 Days) -->
            <div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded-pill small fw-bold">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i> ราคาต่ออายุเว็บไซต์ (Website Renewal Tiers)
                    </span>
                </div>
                <div class="row g-3">
                    <!-- 7 Days -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-4 text-center h-100 d-flex flex-column justify-content-between" style="background: rgba(10,10,15,0.4); border: 1px solid rgba(0, 201, 255, 0.15);">
                            <div class="mb-2">
                                <span class="badge bg-warning bg-opacity-25 text-warning mb-1 fw-bold fs-6">⏱️ ต่ออายุ 7 วัน</span>
                                <div class="text-muted small">ขยายเวลา 1 สัปดาห์</div>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-white-50" style="border-color: rgba(0, 201, 255, 0.15) !important;">฿</span>
                                <input type="number" step="0.01" min="0" name="renewal_price_7d" class="form-control bg-dark text-white fw-bold text-center fs-6" style="border-color: rgba(0, 201, 255, 0.15) !important;" value="<?= $adm_renew_7 ?>" required>
                            </div>
                        </div>
                    </div>
                    <!-- 15 Days -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-4 text-center h-100 d-flex flex-column justify-content-between" style="background: rgba(10,10,15,0.4); border: 1px solid rgba(0, 201, 255, 0.15);">
                            <div class="mb-2">
                                <span class="badge bg-purple bg-opacity-25 text-purple mb-1 fw-bold fs-6" style="background:rgba(168,85,247,0.2);color:#c084fc;">⏱️ ต่ออายุ 15 วัน</span>
                                <div class="text-muted small">ขยายเวลา ครึ่งเดือน</div>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-white-50" style="border-color: rgba(0, 201, 255, 0.15) !important;">฿</span>
                                <input type="number" step="0.01" min="0" name="renewal_price_15d" class="form-control bg-dark text-white fw-bold text-center fs-6" style="border-color: rgba(0, 201, 255, 0.15) !important;" value="<?= $adm_renew_15 ?>" required>
                            </div>
                        </div>
                    </div>
                    <!-- 30 Days -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-4 text-center h-100 d-flex flex-column justify-content-between" style="background: rgba(10,10,15,0.4); border: 1px solid rgba(0, 201, 255, 0.15);">
                            <div class="mb-2">
                                <span class="badge bg-success bg-opacity-25 text-success mb-1 fw-bold fs-6">⏱️ ต่ออายุ 30 วัน</span>
                                <div class="text-muted small">ขยายเวลา 1 เดือนเต็ม</div>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-white-50" style="border-color: rgba(0, 201, 255, 0.15) !important;">฿</span>
                                <input type="number" step="0.01" min="0" name="renewal_price_30d" class="form-control bg-dark text-white fw-bold text-center fs-6" style="border-color: rgba(0, 201, 255, 0.15) !important;" value="<?= $adm_renew_30 ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Packages Table -->
    <div class="table-responsive mb-5">
        <table class="table table-dark table-hover align-middle mb-0" style="background: rgba(15, 23, 42, 0.6); border-radius: 16px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <thead>
                <tr class="text-muted small" style="border-bottom: 2px solid rgba(0,201,255,0.2);">
                    <th style="width: 70px;">รูป</th>
                    <th>ชื่อแพ็กเกจ</th>
                    <th>ราคา</th>
                    <th>ระยะเวลา</th>
                    <th>ตัวอย่าง Demo</th>
                    <th>สถานะ</th>
                    <th class="text-end">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($packages)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-layer-group fs-2 mb-2 opacity-50 d-block"></i>
                            ยังไม่มีแพ็กเกจเช่าเว็บไซต์ในระบบ กรุณากดปุ่ม "เพิ่มแพ็กเกจใหม่" ด้านบน
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($packages as $pkg): 
                        $pkg_tpl = $pkg['template_type'] ?? 'php_classic';
                    ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td>
                                <img src="<?= htmlspecialchars($pkg['image'] ?? 'https://image.fivempro.xyz/storage/86474fabad3b223270ec5b045a1a14de.png') ?>" 
                                     alt="Package" 
                                     class="rounded-3 shadow-sm" 
                                     style="width: 50px; height: 50px; object-fit: cover;"
                                     onerror="this.src='https://image.fivempro.xyz/storage/86474fabad3b223270ec5b045a1a14de.png'">
                            </td>
                            <td>
                                <div class="fw-bold text-white"><?= htmlspecialchars($pkg['name']) ?></div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <?php if ($pkg_tpl === 'nextjs'): ?>
                                        <span class="badge bg-info bg-opacity-20 text-info border border-info border-opacity-30 rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                            <i class="fa-solid fa-bolt me-1"></i> ⚡ Next.js Modern
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-purple bg-opacity-20 text-light border border-secondary border-opacity-30 rounded-pill px-2 py-0.5" style="font-size: 0.68rem; background: rgba(147, 51, 234, 0.2); color: #d8b4fe !important;">
                                            <i class="fa-brands fa-php me-1"></i> 🐘 PHP Classic
                                        </span>
                                    <?php endif; ?>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">
                                        <?= htmlspecialchars(mb_substr($pkg['description'] ?? '', 0, 30)) ?>...
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-info">฿<?= number_format($pkg['price'], 2) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-soft text-light"><?= $pkg['duration_days'] ?> วัน</span>
                            </td>
                            <td>
                                <?php if (!empty($pkg['demo_url'])): ?>
                                    <a href="<?= htmlspecialchars($pkg['demo_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 small">
                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> ดูตัวอย่าง
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (($pkg['status'] ?? 'active') === 'active'): ?>
                                    <span class="badge bg-success-soft text-success"><i class="fa-solid fa-circle-check me-1"></i> เปิดใช้งาน</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-soft text-danger"><i class="fa-solid fa-circle-xmark me-1"></i> ปิดชั่วคราว</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-warning me-1 rounded-pill px-3 py-0.5 fw-bold" style="font-size: 0.75rem;" onclick="editPkg(<?= htmlspecialchars(json_encode($pkg)) ?>)">
                                    <i class="fa-solid fa-pen me-1"></i> แก้ไข
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-0.5 fw-bold" style="font-size: 0.75rem;" onclick="deletePkg(<?= $pkg['id'] ?>, '<?= htmlspecialchars(addslashes($pkg['name'])) ?>')">
                                    <i class="fa-solid fa-trash me-1"></i> ลบ
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
                </div>

                <!-- Quick select buttons -->
                <div class="mt-3">
                    <p class="text-white-50 small mb-2">เลือกด่วน:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3" onclick="setExtendQuick(0,1,0)"><i class="fa-regular fa-clock me-1"></i>+1 ชม.</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3" onclick="setExtendQuick(1,0,0)"><i class="fa-solid fa-calendar-days me-1"></i>+1 วัน</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3" onclick="setExtendQuick(7,0,0)"><i class="fa-solid fa-calendar-week me-1"></i>+7 วัน</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3" onclick="setExtendQuick(15,0,0)"><i class="fa-solid fa-calendar me-1"></i>+15 วัน</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3" onclick="setExtendQuick(30,0,0)"><i class="fa-solid fa-calendar me-1"></i>+30 วัน</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success fw-bold rounded-pill px-4" id="btnConfirmExtend">
                    <i class="fa-solid fa-clock me-1"></i> ยืนยันเพิ่มเวลา
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Package -->
<div class="modal fade" id="addPackageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content xez-card p-2" style="background: rgba(15, 23, 42, 0.98); border: 1px solid rgba(0,201,255,0.3);">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-plus text-xez-primary me-2"></i> เพิ่มแพ็กเกจเช่าเว็บไซต์</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPackageForm">
                <input type="hidden" name="action" value="add">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">ชื่อแพ็กเกจ <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="add_name" class="form-control bg-dark border-secondary text-white" placeholder="เช่น CYGNUS-X Store Standard" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-white small fw-bold">ราคา (บาท) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="price" id="add_price" class="form-control bg-dark border-secondary text-white" value="150.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-white small fw-bold">ระยะเวลา (วัน) <span class="text-danger">*</span></label>
                            <input type="number" min="1" name="duration_days" id="add_duration" class="form-control bg-dark border-secondary text-white" value="30" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">ประเภทเทมเพลตหน้าร้าน (Store Engine) <span class="text-danger">*</span></label>
                        <select name="template_type" id="add_template_type" class="form-select bg-dark border-secondary text-white fw-bold">
                            <option value="php_classic">🐘 PHP Classic Engine (เสถียร / เต็มรูปแบบ)</option>
                            <option value="nextjs">⚡ Next.js Modern UI / Tailwind CSS (ความเร็วสูง / ทันสมัย)</option>
                        </select>
                        <small class="text-white-50" style="font-size:0.75rem;">Next.js จะใช้เทมเพลตความเร็วสูง และสื่อสารผ่าน Headless REST API</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">ลิงก์รูปภาพแพ็กเกจ</label>
                        <input type="text" name="image" id="add_image" class="form-control bg-dark border-secondary text-white" value="https://image.fivempro.xyz/storage/86474fabad3b223270ec5b045a1a14de.png">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">ลิงก์ร้านค้าตัวอย่าง (Demo URL)</label>
                        <input type="text" name="demo_url" id="add_demo" class="form-control bg-dark border-secondary text-white" value="/stores/demo/">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">รายละเอียด / จุดเด่น (บรรทัดละ 1 ข้อ)</label>
                        <textarea name="description" id="add_desc" class="form-control bg-dark border-secondary text-white" rows="4" placeholder="• ธีม Dark Neon ล้ำสมัย&#10;• ระบบสต็อก ยอดขาย จัดการสินค้าครบ&#10;• ติดตั้งอัตโนมัติพร้อมใช้ใน 10 วินาที"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-25">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn xez-btn-cyan fw-bold px-4" id="btnAddSubmit">
                        <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Package -->
<div class="modal fade" id="editPackageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content xez-card p-2" style="background: rgba(15, 23, 42, 0.98); border: 1px solid rgba(245,158,11,0.3);">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-pen text-warning me-2"></i> แก้ไขแพ็กเกจเช่าเว็บไซต์</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPackageForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">ชื่อแพ็กเกจ <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control bg-dark border-secondary text-white" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-white small fw-bold">ราคา (บาท) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="price" id="edit_price" class="form-control bg-dark border-secondary text-white" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-white small fw-bold">ระยะเวลา (วัน) <span class="text-danger">*</span></label>
                            <input type="number" min="1" name="duration_days" id="edit_duration_days" class="form-control bg-dark border-secondary text-white" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">ประเภทเทมเพลตหน้าร้าน (Store Engine) <span class="text-danger">*</span></label>
                        <select name="template_type" id="edit_template_type" class="form-select bg-dark border-secondary text-white fw-bold">
                            <option value="php_classic">🐘 PHP Classic Engine (เสถียร / เต็มรูปแบบ)</option>
                            <option value="nextjs">⚡ Next.js Modern UI / Tailwind CSS (ความเร็วสูง / ทันสมัย)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">ลิงก์รูปภาพแพ็กเกจ</label>
                        <input type="text" name="image" id="edit_image" class="form-control bg-dark border-secondary text-white">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">ลิงก์ร้านค้าตัวอย่าง (Demo URL)</label>
                        <input type="text" name="demo_url" id="edit_demo_url" class="form-control bg-dark border-secondary text-white">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">สถานะการให้บริการ</label>
                        <select name="status" id="edit_status" class="form-select bg-dark border-secondary text-white">
                            <option value="active">เปิดให้บริการ (Active)</option>
                            <option value="inactive">ปิดให้บริการชั่วคราว (Inactive)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">รายละเอียด / จุดเด่น (บรรทัดละ 1 ข้อ)</label>
                        <textarea name="description" id="edit_description" class="form-control bg-dark border-secondary text-white" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-25">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4" id="btnEditSubmit">
                        <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Handle Add Package via AJAX
$('#addPackageForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#btnAddSubmit');
    btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังบันทึก...');

    $.ajax({
        url: 'system/backend/rental_packages_api.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i> บันทึกข้อมูล');
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
                Swal.fire('ข้อผิดพลาด', res.message || 'เกิดข้อผิดพลาดในการบันทึก', 'error');
            }
        },
        error: function(xhr) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i> บันทึกข้อมูล');
            var msg = 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้';
            try {
                var json = JSON.parse(xhr.responseText);
                if (json.message) msg = json.message;
            } catch(e){}
            Swal.fire('ข้อผิดพลาด', msg, 'error');
        }
    });
});

// Handle Edit Package via AJAX
$('#editPackageForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#btnEditSubmit');
    btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังบันทึก...');

    $.ajax({
        url: 'system/backend/rental_packages_api.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการแก้ไข');
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
                Swal.fire('ข้อผิดพลาด', res.message || 'เกิดข้อผิดพลาดในการบันทึก', 'error');
            }
        },
        error: function(xhr) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการแก้ไข');
            var msg = 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้';
            try {
                var json = JSON.parse(xhr.responseText);
                if (json.message) msg = json.message;
            } catch(e){}
            Swal.fire('ข้อผิดพลาด', msg, 'error');
        }
    });
});

function editPkg(pkg) {
    $('#edit_id').val(pkg.id);
    $('#edit_name').val(pkg.name);
    $('#edit_price').val(pkg.price);
    $('#edit_duration_days').val(pkg.duration_days);
    $('#edit_template_type').val(pkg.template_type || 'php_classic');
    $('#edit_image').val(pkg.image);
    $('#edit_demo_url').val(pkg.demo_url);
    $('#edit_status').val(pkg.status || 'active');
    $('#edit_description').val(pkg.description);
    new bootstrap.Modal(document.getElementById('editPackageModal')).show();
}

function deletePkg(id, name) {
    Swal.fire({
        title: 'ยืนยันการลบแพ็กเกจ?',
        text: 'คุณต้องการลบแพ็กเกจ "' + name + '" หรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'ลบแพ็กเกจ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'system/backend/rental_packages_api.php',
                type: 'POST',
                data: { action: 'delete', id: id },
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
                        Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถลบแพ็กเกจได้', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
}

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
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถทำรายการได้', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
}

// Save Addon & Renewal Prices
$('#btnSaveAddonPrices').click(function() {
    var formData = $('#formAddonPrices').serialize();
    Swal.fire({ title: 'กำลังบันทึกราคา...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

    $.ajax({
        url: 'system/backend/rental_packages_api.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire({ 
                    icon: 'success', 
                    title: 'บันทึกสำเร็จ!', 
                    text: res.message, 
                    timer: 1500, 
                    showConfirmButton: false 
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถบันทึกได้', 'error');
            }
        },
        error: function() {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
        }
    });
});
function extendStoreTime(rentalId, shopName, currentExpiry) {
    $('#extendRentalId').val(rentalId);
    $('#extendShopName').text(shopName);
    
    var expText = 'ไม่ระบุ';
    if (currentExpiry) {
        var d = new Date(currentExpiry.replace(' ', 'T'));
        if (!isNaN(d)) {
            expText = d.toLocaleDateString('th-TH', {year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit'});
        }
    }
    $('#extendCurrentExpiry').text(expText);
    $('#extendDays').val(0);
    $('#extendHours').val(0);
    $('#extendMinutes').val(0);
    var modal = new bootstrap.Modal(document.getElementById('extendTimeModal'));
    modal.show();
}

function setExtendQuick(days, hours, minutes) {
    $('#extendDays').val(days);
    $('#extendHours').val(hours);
    $('#extendMinutes').val(minutes);
}

$('#btnConfirmExtend').click(function() {
    var rentalId = $('#extendRentalId').val();
    var days    = parseInt($('#extendDays').val()) || 0;
    var hours   = parseInt($('#extendHours').val()) || 0;
    var minutes = parseInt($('#extendMinutes').val()) || 0;

    if (days <= 0 && hours <= 0 && minutes <= 0) {
        Swal.fire('ข้อผิดพลาด', 'กรุณาระบุเวลาที่ต้องการเพิ่ม (วัน/ชั่วโมง/นาที) อย่างน้อย 1 ค่า', 'warning');
        return;
    }

    var shopName = $('#extendShopName').text();
    var totalMins = days * 1440 + hours * 60 + minutes;
    var displayText = [];
    if (days > 0) displayText.push(days + ' วัน');
    if (hours > 0) displayText.push(hours + ' ชั่วโมง');
    if (minutes > 0) displayText.push(minutes + ' นาที');

    var extendModal = bootstrap.Modal.getInstance(document.getElementById('extendTimeModal'));
    if (extendModal) extendModal.hide();

    Swal.fire({
        title: 'ยืนยันการเพิ่มเวลา?',
        html: 'เพิ่มเวลา <strong class="text-success">' + displayText.join(' ') + '</strong><br>ให้ร้านค้า <strong class="text-info">' + shopName + '</strong>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: '✅ ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'กำลังดำเนินการ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            $.ajax({
                url: 'system/backend/rental_packages_api.php',
                type: 'POST',
                data: {
                    action: 'extend_time',
                    rental_id: rentalId,
                    days: days,
                    hours: hours,
                    minutes: minutes
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'เพิ่มเวลาสำเร็จ!',
                            html: res.message,
                            timer: 2500,
                            showConfirmButton: false
                        }).then(() => { window.location.reload(); });
                    } else {
                        Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถเพิ่มเวลาได้', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
});
</script>
