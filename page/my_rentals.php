<?php
if (defined('IS_PARENT_STORE') && !IS_PARENT_STORE) {
    http_response_code(403);
    die("<script>window.location.href = '?page=home';</script>");
}

$user_id = intval($_SESSION['id'] ?? ($_SESSION['user']['id'] ?? 0));

// Auto-create tables if missing
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
          `wheel_unlocked` tinyint(1) DEFAULT 1,
          `backup_unlocked` tinyint(1) DEFAULT 0,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

        try { $conn->exec("ALTER TABLE `rented_websites` ADD COLUMN `wheel_unlocked` TINYINT(1) DEFAULT 1"); } catch(Exception $e) {}
        try { $conn->exec("ALTER TABLE `rented_websites` ADD COLUMN `backup_unlocked` TINYINT(1) DEFAULT 0"); } catch(Exception $e) {}
        try { $conn->exec("ALTER TABLE `setting` ADD COLUMN `addon_wheel_price` DECIMAL(10,2) DEFAULT 150.00"); } catch(Exception $e) {}
        try { $conn->exec("ALTER TABLE `setting` ADD COLUMN `addon_backup_price` DECIMAL(10,2) DEFAULT 99.00"); } catch(Exception $e) {}
        try { $conn->exec("ALTER TABLE `setting` ADD COLUMN `renewal_price_1d` DECIMAL(10,2) DEFAULT 10.00"); } catch(Exception $e) {}
        try { $conn->exec("ALTER TABLE `setting` ADD COLUMN `renewal_price_3d` DECIMAL(10,2) DEFAULT 25.00"); } catch(Exception $e) {}
        try { $conn->exec("ALTER TABLE `setting` ADD COLUMN `renewal_price_7d` DECIMAL(10,2) DEFAULT 50.00"); } catch(Exception $e) {}
        try { $conn->exec("ALTER TABLE `setting` ADD COLUMN `renewal_price_15d` DECIMAL(10,2) DEFAULT 90.00"); } catch(Exception $e) {}
        try { $conn->exec("ALTER TABLE `setting` ADD COLUMN `renewal_price_30d` DECIMAL(10,2) DEFAULT 150.00"); } catch(Exception $e) {}
    } catch(Exception $e) {}
}

// Fetch master pricing
$set_q = dd_q("SELECT * FROM setting LIMIT 1");
$m_setting = ($set_q && $set_q instanceof PDOStatement) ? $set_q->fetch(PDO::FETCH_ASSOC) : [];
$addon_wheel_price  = floatval($m_setting['addon_wheel_price'] ?? 150);
$addon_backup_price = floatval($m_setting['addon_backup_price'] ?? 99);
$renewal_price_7d   = floatval($m_setting['renewal_price_7d'] ?? 50);
$renewal_price_15d  = floatval($m_setting['renewal_price_15d'] ?? 90);
$renewal_price_30d  = floatval($m_setting['renewal_price_30d'] ?? 150);

// 1. Fetch rented websites for this user
$rentals_q = dd_q("SELECT r.*, p.name as pkg_name, p.image as pkg_image 
                   FROM rented_websites r 
                   LEFT JOIN web_rental_packages p ON r.package_id = p.id 
                   WHERE r.user_id = ? 
                   ORDER BY r.id DESC", [$user_id]);
$rentals = ($rentals_q && $rentals_q instanceof PDOStatement) ? $rentals_q->fetchAll(PDO::FETCH_ASSOC) : [];

// 2. Fetch all active packages available for rent
$available_pkgs_q = dd_q("SELECT * FROM web_rental_packages WHERE status = 'active' ORDER BY price ASC");
$available_pkgs = ($available_pkgs_q && $available_pkgs_q instanceof PDOStatement) ? $available_pkgs_q->fetchAll(PDO::FETCH_ASSOC) : [];
?>

<div class="py-3" data-aos="fade-up">

    <!-- ═══ SECTION 1: PACKAGES FOR RENT (เว็บที่ลงขาย) ═══ -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <span class="badge px-3 py-1.5 rounded-pill mb-2" style="background: rgba(250, 204, 21, 0.12); color: #facc15; border: 1.5px solid rgba(250, 204, 21, 0.35); font-family: var(--font-cyber); letter-spacing: 0.5px;">
                    <i class="fa-solid fa-bolt-lightning me-1"></i> [ SYS.PROVISION: 10_SECONDS_INSTANT_DEPLOY ]
                </span>
                <h3 class="fw-bold text-white mb-1" style="font-family: var(--font-cyber); font-size: 1.85rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-globe text-warning me-2"></i> CLOUD STORE // <span style="color:#facc15;">PROVISIONING</span>
                </h3>
                <p class="text-white-50 small mb-0">เลือกแพ็กเกจที่คุณต้องการเพื่อเปิดร้านค้าออนไลน์ของคุณเองได้ทันที — ระบบติดตั้งและเปิดอัตโนมัติ</p>
            </div>
            <?php if (!empty($rentals)): ?>
                <a href="#my-stores" class="btn btn-outline-warning rounded-pill px-4 py-2 fw-bold small" style="font-family:var(--font-cyber);letter-spacing:0.5px;">
                    <i class="fa-solid fa-server me-1"></i> ดูร้านค้าของฉัน (<?= count($rentals) ?> ร้าน) ➔
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($available_pkgs)): ?>
            <div class="card-ryzex p-4 text-center my-3" style="background: #080a12; border: 1px dashed rgba(250, 204, 21, 0.3); border-radius: 20px;">
                <i class="fa-solid fa-store-slash text-warning fs-2 mb-2 d-block"></i>
                <h5 class="text-white fw-bold mb-1" style="font-family:var(--font-cyber);">[ SYSTEM_NOTICE: NO_ACTIVE_PACKAGES ]</h5>
                <p class="text-white-50 small mb-0">ยังไม่มีแพ็กเกจเช่าเว็บไซต์เปิดขายในขณะนี้ โปรดติดตามหรือติดต่อผู้ดูแลระบบ</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($available_pkgs as $pkg): 
                    $pkg_tpl = $pkg['template_type'] ?? 'php_classic';
                    $duration = intval($pkg['duration_days']);
                    $is_lifetime = ($duration >= 9999 || $duration >= 3650);
                    $is_plus = ($duration >= 90 && !$is_lifetime);
                    $tier_class = $is_lifetime ? 'tier-lifetime-gold' : ($is_plus ? 'tier-plus-purple' : 'tier-standard-cyan');
                    $badge_class = $is_lifetime ? 'badge-tier-gold' : ($is_plus ? 'badge-tier-purple' : 'badge-tier-cyan');
                    $badge_text = $is_lifetime ? '⭐ OMEGA LIFETIME // ตลอดชีพ' : ($is_plus ? '🔥 POPULAR // 90 วัน' : '⚡ STARTER // 30 วัน');
                    $btn_class = $is_lifetime ? 'btn-rent-tier-gold' : ($is_plus ? 'btn-rent-tier-purple' : 'btn-rent-tier-cyan');
                    
                    // Parse description lines
                    $desc_raw = str_replace(["\r\n", "\r"], "\n", $pkg['description'] ?? '');
                    $desc_lines = array_filter(array_map('trim', explode("\n", $desc_raw)));
                    $icons = ['fa-solid fa-bolt-lightning', 'fa-solid fa-shield-halved', 'fa-solid fa-qrcode', 'fa-solid fa-wand-magic-sparkles', 'fa-solid fa-sliders', 'fa-solid fa-headset', 'fa-solid fa-gem'];
                ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-ryzex cyber-pkg-card <?= $tier_class ?> p-4 h-100 d-flex flex-column justify-content-between position-relative">
                            <?php if ($is_lifetime): ?>
                                <div class="pkg-ribbon-gold"><span>BEST VALUE</span></div>
                            <?php endif; ?>
                            
                            <div>
                                <div class="position-relative mb-3 rounded-4 overflow-hidden pkg-thumb-box" style="aspect-ratio: 16/9;">
                                    <img src="<?= htmlspecialchars($pkg['image'] ?? 'https://image.fivempro.xyz/storage/86474fabad3b223270ec5b045a1a14de.png') ?>" 
                                         class="w-100 h-100 pkg-thumb-img" 
                                         style="object-fit: cover;" 
                                         onerror="this.src='https://image.fivempro.xyz/storage/86474fabad3b223270ec5b045a1a14de.png'">
                                    
                                    <div class="position-absolute d-flex gap-1.5" style="top: 12px; left: 12px;">
                                        <span class="badge <?= $badge_class ?> px-3 py-1.5 rounded-pill shadow-sm">
                                            <?= $badge_text ?>
                                        </span>
                                    </div>

                                    <span class="badge bg-dark bg-opacity-75 border border-warning border-opacity-50 position-absolute px-3 py-1.5 rounded-pill text-white" style="top: 12px; right: 12px; font-size: 0.8rem; backdrop-filter: blur(8px); font-family:var(--font-cyber);">
                                        <i class="fa-solid fa-clock text-warning me-1"></i> <?= $is_lifetime ? 'PERPETUAL' : $duration . ' DAYS' ?>
                                    </span>
                                </div>
                                <h4 class="pkg-title fw-bold mb-2"><?= htmlspecialchars($pkg['name']) ?></h4>
                                
                                <ul class="cyber-pkg-feature-list mb-3">
                                    <?php 
                                     $i_idx = 0;
                                     foreach ($desc_lines as $line): 
                                         $clean_line = ltrim($line, "•- \t");
                                         if (empty($clean_line)) continue;
                                         $ic = $icons[$i_idx % count($icons)];
                                         $i_idx++;
                                    ?>
                                         <li>
                                             <span class="feature-icon-wrap"><i class="<?= $ic ?>"></i></span>
                                             <span class="feature-text"><?= htmlspecialchars($clean_line) ?></span>
                                         </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3 pt-3 border-top border-secondary border-opacity-25">
                                    <div>
                                        <span class="text-white-50 x-small d-block" style="font-family:var(--font-cyber);letter-spacing:1px;">PRICE // อัตราค่าบริการ</span>
                                        <span class="pkg-price-text fw-bold fs-4">฿<?= number_format($pkg['price'], 2) ?></span>
                                        <span class="text-white-50 x-small">/ <?= $is_lifetime ? 'ตลอดชีพ' : $duration . ' วัน' ?></span>
                                    </div>
                                    <div>
                                        <?php if (!empty($pkg['demo_url']) && $pkg['demo_url'] !== '#'): ?>
                                            <a href="<?= htmlspecialchars($pkg['demo_url']) ?>" target="_blank" class="btn btn-outline-warning btn-sm rounded-pill px-3 me-1 btn-demo-hover" style="font-family:var(--font-cyber);font-size:0.75rem;">
                                                <i class="fa-solid fa-eye me-1"></i> LIVE DEMO
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <button type="button" class="btn <?= $btn_class ?> w-100 py-2.5 rounded-pill fw-bold" onclick="openRentModal(<?= $pkg['id'] ?>, '<?= htmlspecialchars(addslashes($pkg['name'])) ?>', <?= $pkg['price'] ?>, <?= $pkg['duration_days'] ?>)">
                                    <i class="fa-solid fa-bolt-lightning me-1"></i> เช่าร้านค้านี้ทันที ➔
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ═══ SECTION 2: MY RENTED STORES (เว็บไซต์ของฉัน) ═══ -->
    <div class="pt-4 border-top border-secondary border-opacity-25" id="my-stores">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold text-white mb-1"><i class="fa-solid fa-server text-info me-2"></i> เว็บไซต์ร้านค้าของฉัน</h3>
                <p class="text-white-50 small mb-0">รายการร้านค้าออนไลน์ที่คุณเป็นเจ้าของ สามารถเข้าจัดการหลังบ้านและต่ออายุได้ตลอดเวลา</p>
            </div>
            <span class="badge bg-primary bg-opacity-20 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill">
                <i class="fa-solid fa-store me-1"></i> ครอบครอง <?= count($rentals) ?> ร้านค้า
            </span>
        </div>

        <?php if (empty($rentals)): ?>
            <div class="card-ryzex p-5 text-center my-4" style="background: rgba(15, 23, 42, 0.7); border: 1px dashed rgba(0, 201, 255, 0.2); border-radius: 20px;">
                <i class="fa-solid fa-store-slash text-muted fs-1 mb-3 opacity-50 d-block"></i>
                <h4 class="text-white fw-bold mb-2">คุณยังไม่มีร้านค้าออนไลน์</h4>
                <p class="text-white-50 mb-4">เลือกแพ็กเกจที่คุณชื่นชอบด้านบนเพื่อเปิดร้านค้าออนไลน์ของคุณเองได้ใน 10 วินาที</p>
                <a href="#top" class="btn btn-login rounded-pill px-4 py-2 fw-bold">
                    <i class="fa-solid fa-plus me-1"></i> สั่งเช่าเว็บไซต์ทันที
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($rentals as $r): 
                    $shop_slug = !empty($r['shop_name']) ? $r['shop_name'] : ($r['subdomain'] ?? 'store_'.$r['id']);
                    $store_front_url = !empty($r['store_url']) ? $r['store_url'] : (SYSTEM_PROTOCOL . $shop_slug . "." . SYSTEM_DOMAIN . "/");
                    $admin_pass = $r['admin_password'] ?? 'admin';
                    $store_admin_url = $store_front_url . "?page=autologin&token=" . urlencode($admin_pass);
                    $r_tpl = $r['template_type'] ?? 'php_classic';
                    
                    // Time calculation
                    $end_ts = !empty($r['end_date']) ? strtotime($r['end_date']) : strtotime('+30 days', strtotime($r['created_at']));
                    $now = time();
                    $diff_sec = max(0, $end_ts - $now);
                    $days = floor($diff_sec / 86400);
                    $hours = floor(($diff_sec % 86400) / 3600);
                    $mins = floor(($diff_sec % 3600) / 60);
                    $is_expired = ($end_ts < $now);

                    // Add-on states
                    $wheel_unlocked = !empty($r['wheel_unlocked']);
                    $backup_unlocked = !empty($r['backup_unlocked']);
                ?>
                    <div class="col-12 col-lg-6">
                        <div class="card-ryzex p-4 h-100 position-relative" style="background: rgba(15, 23, 42, 0.85); border: 1px solid <?= $is_expired ? 'rgba(239, 68, 68, 0.4)' : 'rgba(0, 201, 255, 0.3)' ?>; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.4);">
                            
                            <!-- Header Info -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary bg-opacity-20 text-info border border-info border-opacity-25 px-2.5 py-1 rounded-pill font-monospace small">
                                            #<?= $r['id'] ?> - <?= htmlspecialchars($r['pkg_name'] ?? 'Store') ?>
                                        </span>
                                        <span class="badge bg-dark border border-secondary text-info px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                            <i class="fa-solid fa-server me-1"></i> เว็บร้านค้า
                                        </span>
                                    </div>
                                    <h5 class="text-white fw-bold mb-1">
                                        <i class="fa-solid fa-store text-info me-1"></i> ร้านค้า: <span class="text-info font-monospace"><?= htmlspecialchars($shop_slug) ?></span>
                                    </h5>
                                </div>
                                <span class="badge <?= $is_expired ? 'bg-danger text-danger' : 'bg-success text-success' ?> bg-opacity-20 border <?= $is_expired ? 'border-danger' : 'border-success' ?> border-opacity-25 px-3 py-1.5 rounded-pill small">
                                    <?= $is_expired ? '🔴 หมดอายุ' : '🟢 ออนไลน์' ?>
                                </span>
                            </div>

                            <!-- Live Countdown Banner -->
                            <div class="p-2.5 px-3 rounded-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2" 
                                 style="background: <?= $is_expired ? 'rgba(239, 68, 68, 0.1)' : 'rgba(0, 201, 255, 0.08)' ?>; border: 1px solid <?= $is_expired ? 'rgba(239, 68, 68, 0.3)' : 'rgba(0, 201, 255, 0.25)' ?>;">
                                <div>
                                    <div class="text-white-50 x-small"><i class="fa-solid fa-hourglass-half text-info me-1"></i> อายุการเช่าเว็บไซต์ที่เหลือ:</div>
                                    <div class="fw-bold small">
                                        <?php if ($is_expired): ?>
                                            <span class="text-danger"><i class="fa-solid fa-circle-exclamation me-1"></i> หมดอายุการใช้งานแล้ว</span>
                                        <?php else: ?>
                                            <span class="text-info"><i class="fa-solid fa-clock me-1"></i> เหลืออีก <?= $days ?> วัน <?= $hours ?> ชั่วโมง <?= $mins ?> นาที</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-warning fw-bold px-3 rounded-pill text-dark btn-open-renew" 
                                        data-id="<?= $r['id'] ?>" data-shop="<?= htmlspecialchars($shop_slug) ?>">
                                    <i class="fa-solid fa-rotate me-1"></i> ต่ออายุเว็บไซต์
                                </button>
                            </div>

                            <!-- URL Box -->
                            <div class="p-3 rounded-3 mb-3" style="background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255,255,255,0.06);">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-white-50 x-small"><i class="fa-solid fa-globe me-1"></i> ลิงก์โดเมนร้านค้า:</span>
                                    <button type="button" class="btn btn-link text-info p-0 text-decoration-none x-small" onclick="copyToClip('<?= htmlspecialchars($store_front_url) ?>')">
                                        <i class="fa-solid fa-copy"></i> คัดลอก
                                    </button>
                                </div>
                                <a href="<?= htmlspecialchars($store_front_url) ?>" target="_blank" class="text-info font-monospace fw-bold small text-decoration-none d-block text-truncate">
                                    <?= htmlspecialchars($store_front_url) ?> <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 0.75rem;"></i>
                                </a>
                            </div>

                            <!-- Admin Credentials Box -->
                            <div class="p-3 rounded-3 mb-3" style="background: rgba(0, 201, 255, 0.05); border: 1px dashed rgba(0, 201, 255, 0.3);">
                                <div class="row g-2 align-items-center">
                                    <div class="col-6">
                                        <span class="text-white-50 x-small d-block"><i class="fa-solid fa-user-shield me-1"></i> บัญชีแอดมิน:</span>
                                        <span class="text-white font-monospace fw-bold small">admin</span>
                                    </div>
                                    <div class="col-6 text-end">
                                        <span class="text-white-50 x-small d-block"><i class="fa-solid fa-key me-1"></i> รหัสผ่านแอดมิน:</span>
                                        <span class="text-warning font-monospace fw-bold small me-2" id="pass_<?= $r['id'] ?>"><?= htmlspecialchars($admin_pass) ?></span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary p-1 px-2 rounded-2" onclick="copyToClip('<?= htmlspecialchars(addslashes($admin_pass)) ?>')">
                                            <i class="fa-solid fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add-on Feature Marketplace Bar -->
                            <div class="p-2.5 px-3 rounded-3 mb-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                                <div class="text-white-50 x-small mb-2 fw-bold"><i class="fa-solid fa-puzzle-piece text-warning me-1"></i> ฟังก์ชันเสริมของร้านค้านี้:</div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <!-- Wheel Add-on -->
                                    <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-30 py-1.5 px-2.5 rounded-pill small">
                                        <i class="fa-solid fa-circle-check me-1"></i> 🎡 วงล้อสุ่ม (เปิดใช้งานฟรี)
                                    </span>

                                    <!-- Backup Add-on -->
                                    <?php if ($backup_unlocked): ?>
                                        <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-30 py-1.5 px-2.5 rounded-pill small">
                                            <i class="fa-solid fa-circle-check me-1"></i> 💾 สำรองข้อมูล (เปิดแล้ว)
                                        </span>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-xs btn-outline-info rounded-pill py-1 px-2.5 btn-buy-addon" data-id="<?= $r['id'] ?>" data-addon="backup" data-price="<?= $addon_backup_price ?>">
                                            <i class="fa-solid fa-cart-shopping me-1"></i> 💾 ซื้อระบบสำรองข้อมูล (฿<?= number_format($addon_backup_price) ?>)
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Actions Buttons -->
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="<?= htmlspecialchars($store_front_url) ?>" target="_blank" class="btn btn-outline-info w-100 rounded-pill fw-bold py-2 small">
                                        <i class="fa-solid fa-desktop me-1"></i> หน้าร้านค้า
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="<?= htmlspecialchars($store_admin_url) ?>" target="_blank" class="btn xez-btn-cyan w-100 rounded-pill fw-bold py-2 small">
                                        <i class="fa-solid fa-gauge me-1"></i> จัดการหลังร้าน
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal สำหรับสั่งเช่าเว็บไซต์ -->
<div class="modal fade" id="rentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white" style="background: #0f172a; border: 1px solid rgba(0, 201, 255, 0.3); border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-server text-info me-2"></i> สั่งเช่าเว็บไซต์ร้านค้า</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="rent_package_id">
                <div class="mb-3 p-3 rounded-3" style="background: rgba(255,255,255,0.05);">
                    <div class="d-flex justify-content-between">
                        <span class="text-white-50">แพ็กเกจ:</span>
                        <span class="fw-bold text-info" id="rent_pkg_name">-</span>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-white-50">ราคา:</span>
                        <span class="fw-bold text-success" id="rent_pkg_price">฿0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-white-50">ระยะเวลา:</span>
                        <span class="fw-bold" id="rent_pkg_duration">- วัน</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white fw-bold small"><i class="fa-solid fa-globe text-info me-1"></i> ตั้งชื่อลิงก์โดเมนร้านค้า (Subdomain ภาษาอังกฤษ/ตัวเลข)</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.1); color: #94a3b8;"><?= SYSTEM_PROTOCOL ?></span>
                        <input type="text" id="rent_shop_name" class="form-control text-white fw-bold" placeholder="เช่น myshop หรือ shop99" style="background: rgba(255,255,255,0.05); border-color: rgba(0,201,255,0.3);">
                        <span class="input-group-text" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.1); color: #94a3b8;">.<?= SYSTEM_DOMAIN ?></span>
                    </div>
                    <div class="form-text text-white-50 small mt-2">
                        ✨ ลิงก์ร้านของคุณจะเป็น: <code class="text-info"><?= SYSTEM_PROTOCOL ?>[ชื่อที่กรอก].<?= SYSTEM_DOMAIN ?></code><br>
                        <span class="text-muted x-small">*ใส่เฉพาะภาษาอังกฤษตัวพิมพ์เล็กและตัวเลข (สามารถเปลี่ยนชื่อร้านภาษาไทยได้ในระบบหลังบ้าน)</span>
                    </div>
                </div>

                <div class="p-3 rounded-3 mb-3" style="background: rgba(0, 201, 255, 0.08); border: 1px dashed rgba(0, 201, 255, 0.3);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-key text-info"></i>
                        <span class="text-white small fw-bold">รหัสผ่านแอดมิน:</span>
                    </div>
                    <p class="text-white-50 small mb-0 mt-1">ระบบจะทำการสุ่มรหัสผ่านแอดมินที่มีความปลอดภัยสูงให้โดยอัตโนมัติ คุณสามารถเข้าสู่ระบบและเปลี่ยนรหัสผ่านเองได้ในหลังบ้านร้านค้า</p>
                </div>

                <button class="btn btn-login w-100 py-3 fw-bold mt-2" id="btn_confirm_rent">
                    <i class="fa-solid fa-check-circle me-1"></i> ยืนยันการสั่งซื้อและสร้างร้านทันที
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับต่ออายุเว็บไซต์ -->
<div class="modal fade" id="renewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white" style="background: #0f172a; border: 1px solid rgba(245, 158, 11, 0.4); border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-rotate text-warning me-2"></i> ต่ออายุเว็บไซต์ร้านค้า</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="renew_rental_id">
                <div class="mb-3 p-3 rounded-3" style="background: rgba(255,255,255,0.05);">
                    <span class="text-white-50 small">ร้านค้าที่ต้องการต่ออายุ:</span>
                    <h5 class="text-warning fw-bold mb-0 font-monospace" id="renew_shop_display">-</h5>
                </div>

                <label class="form-label text-white fw-bold small">เลือกระยะเวลาที่ต้องการต่ออายุ:</label>
                <div class="row g-2 mb-4">
                    <div class="col-4">
                        <label class="p-3 text-center rounded-3 border d-block cursor-pointer renew-option" style="border-color: rgba(255,255,255,0.1); background: rgba(255,255,255,0.02);">
                            <input type="radio" name="renew_days" value="7" class="d-none">
                            <div class="fw-bold text-white fs-5">7 วัน</div>
                            <div class="text-warning fw-bold small">฿<?= number_format($renewal_price_7d) ?></div>
                        </label>
                    </div>
                    <div class="col-4">
                        <label class="p-3 text-center rounded-3 border d-block cursor-pointer renew-option" style="border-color: rgba(255,255,255,0.1); background: rgba(255,255,255,0.02);">
                            <input type="radio" name="renew_days" value="15" class="d-none">
                            <div class="fw-bold text-white fs-5">15 วัน</div>
                            <div class="text-warning fw-bold small">฿<?= number_format($renewal_price_15d) ?></div>
                        </label>
                    </div>
                    <div class="col-4">
                        <label class="p-3 text-center rounded-3 border d-block cursor-pointer renew-option active" style="border-color: rgba(245,158,11,0.6) !important; background: rgba(245,158,11,0.1);">
                            <input type="radio" name="renew_days" value="30" checked class="d-none">
                            <div class="fw-bold text-white fs-5">30 วัน</div>
                            <div class="text-warning fw-bold small">฿<?= number_format($renewal_price_30d) ?></div>
                        </label>
                    </div>
                </div>

                <button class="btn btn-warning text-dark w-100 py-3 fw-bold" id="btn_confirm_renew">
                    <i class="fa-solid fa-check-circle me-1"></i> ยืนยันการต่ออายุเว็บไซต์
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClip(text) {
    navigator.clipboard.writeText(text).then(function() {
        Swal.fire({
            icon: 'success',
            title: 'คัดลอกเรียบร้อย!',
            timer: 1200,
            showConfirmButton: false
        });
    });
}

function openRentModal(id, name, price, days) {
    $('#rent_package_id').val(id);
    $('#rent_pkg_name').text(name);
    $('#rent_pkg_price').text('฿' + parseFloat(price).toFixed(2));
    $('#rent_pkg_duration').text(days + ' วัน');
    new bootstrap.Modal(document.getElementById('rentModal')).show();
}

// Open Renew Modal
$('.btn-open-renew').click(function() {
    let id = $(this).data('id');
    let shop = $(this).data('shop');
    $('#renew_rental_id').val(id);
    $('#renew_shop_display').text(shop + '.<?= SYSTEM_DOMAIN ?>');
    new bootstrap.Modal(document.getElementById('renewModal')).show();
});

// Renew Option selection UI
$('.renew-option').click(function() {
    $('.renew-option').removeClass('active').css({
        'border-color': 'rgba(255,255,255,0.1)',
        'background': 'rgba(255,255,255,0.02)'
    });
    $(this).addClass('active').css({
        'border-color': 'rgba(245,158,11,0.6)',
        'background': 'rgba(245,158,11,0.1)'
    });
    $(this).find('input[type="radio"]').prop('checked', true);
});

// Confirm Renew
$('#btn_confirm_renew').click(function() {
    let rentalId = $('#renew_rental_id').val();
    let days = $('input[name="renew_days"]:checked').val();

    Swal.fire({ 
        title: 'กำลังต่ออายุเว็บไซต์...', 
        background: '#0a0d18',
        color: '#ffffff',
        allowOutsideClick: false, 
        didOpen: () => { Swal.showLoading(); } 
    });

    $.ajax({
        type: 'POST',
        url: 'system/rent_website.php',
        data: { action: 'renew', rental_id: rentalId, days: days },
        dataType: 'json'
    }).done(function(res) {
        if (res.status === 'success' || res.success) {
            Swal.fire({ 
                icon: 'success', 
                iconColor: '#00c9ff',
                title: 'ต่ออายุสำเร็จ! 🎉', 
                text: res.message,
                background: '#0a0d18',
                color: '#ffffff',
                confirmButtonColor: '#3b82f6'
            }).then(() => location.reload());
        } else {
            Swal.fire({ 
                icon: 'error', 
                title: 'ผิดพลาด', 
                text: res.message,
                background: '#0a0d18',
                color: '#ffffff',
                confirmButtonColor: '#ef4444'
            });
        }
    }).fail(function() {
        Swal.fire({ 
            icon: 'error', 
            title: 'ผิดพลาด', 
            text: 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์',
            background: '#0a0d18',
            color: '#ffffff',
            confirmButtonColor: '#ef4444'
        });
    });
});

// Buy Addon
$('.btn-buy-addon').click(function() {
    let rentalId = $(this).data('id');
    let addon = $(this).data('addon');
    let price = $(this).data('price');
    let addonTitle = (addon === 'backup') ? 'ระบบสำรองข้อมูล (Backup & Restore)' : 'ระบบวงล้อสุ่มของรางวัล (Lucky Wheel)';

    Swal.fire({
        title: '<span style="color:#fff;">สั่งซื้อฟังก์ชันเสริม?</span>',
        html: '<span style="color:#94a3b8;">ต้องการปลดล็อก <strong>' + addonTitle + '</strong> ในราคา <strong class="text-info">฿' + price + '</strong> หรือไม่?</span>',
        icon: 'question',
        iconColor: '#00c9ff',
        background: '#0a0d18',
        color: '#ffffff',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'ยืนยันสั่งซื้อ',
        cancelButtonText: 'ยกเลิก'
    }).then((res) => {
        if (res.isConfirmed) {
            Swal.fire({ 
                title: 'กำลังสั่งซื้อ...', 
                background: '#0a0d18',
                color: '#ffffff',
                allowOutsideClick: false, 
                didOpen: () => { Swal.showLoading(); } 
            });
            $.ajax({
                type: 'POST',
                url: 'system/rent_website.php',
                data: { action: 'buy_addon', rental_id: rentalId, addon: addon },
                dataType: 'json'
            }).done(function(resp) {
                if (resp.status === 'success' || resp.success) {
                    Swal.fire({ 
                        icon: 'success', 
                        iconColor: '#00c9ff',
                        title: 'สำเร็จ! 🚀', 
                        text: resp.message,
                        background: '#0a0d18',
                        color: '#ffffff',
                        confirmButtonColor: '#3b82f6'
                    }).then(() => location.reload());
                } else {
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'ผิดพลาด', 
                        text: resp.message,
                        background: '#0a0d18',
                        color: '#ffffff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            }).fail(function() {
                Swal.fire({ 
                    icon: 'error', 
                    title: 'ผิดพลาด', 
                    text: 'เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์',
                    background: '#0a0d18',
                    color: '#ffffff',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
});

// Rent Website Submit
$('#btn_confirm_rent').click(function(e) {
    e.preventDefault();
    var pkgId = $('#rent_package_id').val();
    var shopName = $('#rent_shop_name').val().trim();

    if (!shopName) {
        Swal.fire({
            icon: 'warning',
            title: 'ข้อมูลไม่ครบถ้วน',
            text: 'กรุณาระบุชื่อลิงก์โดเมนร้านค้า (ภาษาอังกฤษ/ตัวเลข)',
            background: '#0a0d18',
            color: '#ffffff',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }

    Swal.fire({
        title: '<span style="color:#fff;">กำลังสร้างร้านค้าและฐานข้อมูล...</span>',
        html: '<div class="spinner-border text-info my-3"></div><br><small class="text-white-50">ระบบกำลังสร้าง Database และติดตั้งร้านค้าให้คุณทันที กรุณารอสักครู่ (ประมาณ 1-2 วินาที)</small>',
        background: '#0a0d18',
        color: '#ffffff',
        allowOutsideClick: false,
        showConfirmButton: false
    });

    $.ajax({
        type: 'POST',
        url: 'system/rent_website.php',
        data: {
            package_id: pkgId,
            shop_name: shopName
        },
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success' || res.status === 200 || res.salt === 'prize' || res.success) {
                var finalUrl = res.store_url || (res.data ? res.data.store_url : '') || ('<?= SYSTEM_PROTOCOL ?>' + shopName + '.<?= SYSTEM_DOMAIN ?>/');
                var adminPass = res.admin_password || (res.data ? res.data.admin_password : '');
                
                var successHtml = '<div class="text-start p-3 rounded-4 mb-3" style="background: rgba(15, 23, 42, 0.95); border: 1px solid rgba(0, 201, 255, 0.25);">' +
                    '<div class="mb-2.5 d-flex align-items-center justify-content-between flex-wrap gap-1">' +
                        '<span class="text-white-50 small"><i class="fa-solid fa-globe text-info me-1.5"></i> ลิงก์หน้าร้าน:</span>' +
                        '<a href="' + finalUrl + '" target="_blank" class="text-info font-monospace text-decoration-none fw-bold small">' + finalUrl + ' <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.7rem;"></i></a>' +
                    '</div>' +
                    '<div class="mb-2.5 d-flex align-items-center justify-content-between">' +
                        '<span class="text-white-50 small"><i class="fa-solid fa-user-shield text-primary me-1.5"></i> บัญชีแอดมิน:</span>' +
                        '<span class="badge bg-dark border border-secondary text-white font-monospace">admin</span>' +
                    '</div>' +
                    '<div class="d-flex align-items-center justify-content-between">' +
                        '<span class="text-white-50 small"><i class="fa-solid fa-key text-warning me-1.5"></i> รหัสผ่านแอดมิน:</span>' +
                        '<span class="badge bg-dark font-monospace text-warning border border-warning border-opacity-40 px-2.5 py-1 fs-6">' + adminPass + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="d-grid gap-2">' +
                    '<a href="' + finalUrl + '" target="_blank" class="btn btn-success fw-bold py-2.5 rounded-pill shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">' +
                        '<i class="fa-solid fa-rocket me-1.5"></i> เปิดดูหน้าร้านของคุณ <i class="fa-solid fa-arrow-right ms-1"></i>' +
                    '</a>' +
                '</div>';

                Swal.fire({
                    title: '<span style="color:#fff; font-family:var(--font-cyber);">สร้างร้านค้าสำเร็จ! 🚀</span>',
                    html: successHtml,
                    icon: 'success',
                    iconColor: '#00c9ff',
                    background: '#0a0d18',
                    color: '#ffffff',
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fa-solid fa-list-check me-1"></i> ดูรายการเว็บไซต์ของฉัน',
                    confirmButtonColor: '#3b82f6',
                    customClass: {
                        popup: 'rounded-4 border border-info border-opacity-25 shadow-lg'
                    }
                }).then((r) => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด',
                    text: res.message || 'เกิดข้อผิดพลาดในการสร้างร้านค้า',
                    background: '#0a0d18',
                    color: '#ffffff',
                    confirmButtonColor: '#ef4444'
                });
            }
        },
        error: function(xhr) {
            var msg = 'เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์';
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.message) msg = res.message;
            } catch(e) {}
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                text: msg,
                background: '#0a0d18',
                color: '#ffffff',
                confirmButtonColor: '#ef4444'
            });
        }
    });
});
</script>