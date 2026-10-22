<?php
// Product Detail Page (Black & Gold Cyber Edition - Exact Match Screenshots 1, 2, 3, 4)

$prod_param = trim($_GET['id'] ?? $_GET['name'] ?? '1');

// 1. Query Database directly from box_product
$db_p_q = null;
if (is_numeric($prod_param)) {
    $db_p_q = dd_q("SELECT * FROM box_product WHERE id = ? LIMIT 1", [$prod_param]);
}
if (!$db_p_q || $db_p_q->rowCount() == 0) {
    $db_p_q = dd_q("SELECT * FROM box_product WHERE name LIKE ? LIMIT 1", ["%$prod_param%"]);
}
if (!$db_p_q || $db_p_q->rowCount() == 0) {
    $db_p_q = dd_q("SELECT * FROM box_product ORDER BY id ASC LIMIT 1");
}

$p_data = $db_p_q ? $db_p_q->fetch(PDO::FETCH_ASSOC) : [];
$p_id = $p_data['id'] ?? 1;

// Count live stock
$stk_cnt_q = dd_q("SELECT COUNT(*) as c FROM box_stock WHERE p_id = ?", [$p_id]);
$live_stock = $stk_cnt_q ? intval($stk_cnt_q->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) : 0;

$is_unlimited = (isset($p_data['is_unlimited']) && intval($p_data['is_unlimited']) === 1) || (isset($p_data['stock_enabled']) && intval($p_data['stock_enabled']) === 0);
$is_random = (isset($p_data['type']) && intval($p_data['type']) === 0);
$win_percent = isset($p_data['percent']) ? max(0, min(100, intval($p_data['percent']))) : 100;
$salt_prize = !empty($p_data['salt_prize']) ? $p_data['salt_prize'] : 'เสียใจด้วยนะ ไม่ได้รับรางวัล';
$warranty_text = !empty($p_data['warranty']) ? $p_data['warranty'] : '1 ชั่วโมง (มีปัญหากดรายงานได้ทันที)';

// Format description
$des_text = $p_data['des'] ?? 'สินค้าคุณภาพสูงจากร้านค้า จัดส่งรวดเร็วผ่านระบบอัตโนมัติ ใช้งานได้ทันที';

// Query sold count from boxhis
$sold_cnt_q = dd_q("SELECT COUNT(*) as c FROM boxhis WHERE p_id = ?", [$p_id]);
$sold_count = $sold_cnt_q ? intval($sold_cnt_q->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) : 0;
if ($sold_count === 0) {
    $sold_count = !empty($p_data['sales_count']) ? intval($p_data['sales_count']) : rand(15, 60);
}

$item = [
    'id' => $p_id,
    'name' => $p_data['name'] ?? 'สินค้า',
    'category' => $p_data['c_type'] ?? 'PROGRAM PRODUCT',
    'price' => (float)($p_data['price'] ?? 45.00),
    'stock' => $live_stock,
    'is_unlimited' => $is_unlimited,
    'is_random' => $is_random,
    'percent' => $win_percent,
    'salt_prize' => $salt_prize,
    'warranty' => $warranty_text,
    'sold' => $sold_count,
    'type' => $p_data['c_type'] ?? 'โปรแกรม / แอพพลิเคชั่น',
    'img' => !empty($p_data['img']) ? $p_data['img'] : 'assets/icon/box.png',
    'des' => $des_text,
    'date' => $p_data['date'] ?? ($p_data['created_at'] ?? date('Y-m-d H:i:s'))
];

// Query recent orders for user if logged in (for report modal order picker)
$user_orders = [];
if (isset($_SESSION['id'])) {
    $uo_q = dd_q("SELECT * FROM boxhis WHERE uid = ? ORDER BY id DESC LIMIT 15", [$_SESSION['id']]);
    if ($uo_q) {
        $user_orders = $uo_q->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<div class="cx-pdp-shell" data-aos="fade-up">
    <!-- ══════════════════════════════════════════
         TOP BAR: BACK BUTTON & BREADCRUMB
    ══════════════════════════════════════════ -->
    <div class="cx-pdp-topbar">
        <a href="javascript:history.back()" class="cx-pdp-back-btn">
            <i class="fa-solid fa-arrow-left"></i> ย้อนกลับ
        </a>
        <div class="cx-pdp-breadcrumb">
            <a href="?page=home">หน้าแรก</a> / 
            <a href="?page=shop"><?= htmlspecialchars($item['category']) ?></a> / 
            <span><?= htmlspecialchars($item['name']) ?></span>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         TOP HERO GRID: IMAGE (LEFT) & BUY PANEL (RIGHT - SCREENSHOT 1)
    ══════════════════════════════════════════ -->
    <div class="cx-pdp-hero-grid">
        <!-- Left Column: Product Image Box & Video Bar -->
        <div>
            <div class="cx-pdp-img-container">
                <div class="cx-pdp-img-tag"><?= htmlspecialchars($item['category']) ?></div>
                <img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cx-pdp-main-img" onerror="this.src='assets/icon/box.png'">
                <div class="cx-pdp-img-banner-title"><?= htmlspecialchars(strtoupper($item['name'])) ?></div>
                <div class="cx-pdp-img-buy-tag">BUY NOW</div>
            </div>

            <!-- Video Teaser Bar -->
            <div class="cx-pdp-video-bar" onclick="playPdpVideo()">
                <div class="d-flex align-items-center">
                    <div class="cx-pdp-video-icon">
                        <i class="fa-solid fa-play"></i>
                    </div>
                    <div class="cx-pdp-video-text">
                        <div class="cx-pdp-video-title">คลิปวิดีโอสินค้า</div>
                        <div class="cx-pdp-video-sub">กดเพื่อเปิดดูคลิปวิดีโอสินค้า</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-muted small"></i>
            </div>
        </div>

        <!-- Right Column: Product Info & Purchase Controls -->
        <div>
            <!-- Title -->
            <h1 class="cx-pdp-title"><?= htmlspecialchars($item['name']) ?></h1>

            <!-- Warranty Card -->
            <div class="cx-pdp-warranty-card">
                <i class="fa-solid fa-shield-halved cx-pdp-warranty-icon"></i>
                <div>
                    <div class="cx-pdp-warranty-title">รับประกันสินค้า</div>
                    <div class="cx-pdp-warranty-sub"><?= htmlspecialchars($item['warranty']) ?></div>
                </div>
            </div>

            <?php if ($item['is_random']): ?>
            <!-- Gacha Mystery Box Chance Card -->
            <div class="p-3 mb-3 rounded-3" style="background: rgba(250, 204, 21, 0.08); border: 1px solid rgba(250, 204, 21, 0.35);">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-warning fw-bold"><i class="fa-solid fa-dice me-1"></i> สินค้าแบบสุ่ม (Mystery Box)</span>
                    <span class="badge bg-warning text-dark fw-bold">โอกาสได้ <?= $item['percent'] ?>%</span>
                </div>
                <div class="small text-muted">
                    🎯 โอกาสได้รับรางวัล: <b class="text-warning"><?= $item['percent'] ?>%</b> | 🧂 โอกาสเกลือ: <b class="text-secondary"><?= (100 - $item['percent']) ?>%</b>
                </div>
            </div>
            <?php endif; ?>

            <!-- Price Row -->
            <div class="cx-pdp-price-row">
                <span class="cx-pdp-price-label"><?= $item['is_random'] ? 'ราคาสุ่มต่อครั้ง' : 'ราคาต่อชิ้น' ?></span>
                <span class="cx-pdp-price-val"><?= number_format($item['price'], 2) ?> ฿</span>
            </div>

            <!-- Quantity Stepper -->
            <div class="cx-pdp-qty-wrap">
                <div class="cx-pdp-qty-label"><?= $item['is_random'] ? 'จำนวนครั้งที่สุ่ม' : 'จำนวนสินค้า' ?></div>
                <div class="cx-pdp-stepper">
                    <button type="button" class="cx-pdp-stepper-btn" onclick="changePdpQty(-1)">−</button>
                    <input type="text" id="pdpQtyInput" value="1" readonly class="cx-pdp-stepper-input">
                    <button type="button" class="cx-pdp-stepper-btn" onclick="changePdpQty(1)">+</button>
                </div>
                <div class="cx-pdp-stock-text">
                    <i class="fa-solid fa-box-open text-warning me-1"></i> เหลือสินค้าอีก <?= htmlspecialchars(max(2, $item['stock'])) ?> ชิ้น
                </div>
            </div>

            <!-- Terms Agreement -->
            <div class="cx-pdp-terms-wrap">
                <div class="cx-pdp-terms-label">ยืนยันข้อตกลง <span class="text-danger">•</span></div>
                <label class="cx-pdp-terms-check">
                    <input type="checkbox" id="pdpAgreeTerms" checked>
                    <span>ฉันได้อ่านและยอมรับข้อตกลงและเงื่อนไขการสั่งซื้อแล้ว</span>
                </label>
            </div>

            <!-- Action Buttons Row -->
            <div class="cx-pdp-actions-row">
                <button type="button" class="btn-pdp-icon-info" onclick="scrollToPdpSpecs()" title="ดูข้อมูลจำเพาะ">
                    <i class="fa-solid fa-table-list"></i>
                </button>

                <?php if (!isset($_SESSION['id'])): ?>
                    <button type="button" class="btn-pdp-buy-now" onclick="openAuthModal('login')">
                        <i class="fa-solid fa-right-to-bracket"></i> กรุณาเข้าสู่ระบบ
                    </button>
                <?php else: ?>
                    <button type="button" class="btn-pdp-buy-now" onclick="executePdpBuy()">
                        <?php if ($item['is_random']): ?>
                            <i class="fa-solid fa-dice me-1"></i> สุ่มของรางวัล
                        <?php else: ?>
                            <i class="fa-solid fa-bolt me-1"></i> สั่งซื้อสินค้า
                        <?php endif; ?>
                    </button>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- ══════════════════════════════════════════
         BOTTOM SECTION: SPECS, DETAILS & NOTICES (SCREENSHOT 2)
    ══════════════════════════════════════════ -->
    <div class="cx-pdp-details-section" id="pdpSpecsSection">
        <!-- 1. ข้อมูลจำเพาะ -->
        <div class="cx-pdp-card-box">
            <div class="cx-pdp-card-head">
                <i class="fa-solid fa-table-list text-warning"></i> ข้อมูลจำเพาะ
            </div>
            <div class="cx-pdp-specs-grid">
                <div class="cx-pdp-spec-row">
                    <span class="cx-pdp-spec-key">ชื่อ</span>
                    <span class="cx-pdp-spec-val"><?= htmlspecialchars($item['name']) ?></span>
                </div>
                <div class="cx-pdp-spec-row">
                    <span class="cx-pdp-spec-key">ประกัน</span>
                    <span class="cx-pdp-spec-val text-warning">1 ชั่วโมง (มีปัญหากดรายงานได้ทันที)</span>
                </div>
                <div class="cx-pdp-spec-row">
                    <span class="cx-pdp-spec-key">สต็อก</span>
                    <span class="cx-pdp-spec-val"><?= htmlspecialchars($item['stock']) ?> ชิ้น</span>
                </div>
                <div class="cx-pdp-spec-row">
                    <span class="cx-pdp-spec-key">ยอดขาย</span>
                    <span class="cx-pdp-spec-val"><?= htmlspecialchars($item['sold']) ?> ชิ้น</span>
                </div>
                <div class="cx-pdp-spec-row">
                    <span class="cx-pdp-spec-key">สร้างเมื่อ</span>
                    <span class="cx-pdp-spec-val"><?= date('d M Y H:i', strtotime($item['date'])) ?></span>
                </div>
            </div>
        </div>

        <!-- 2. รายละเอียดสินค้า -->
        <div class="cx-pdp-card-box">
            <div class="cx-pdp-card-head">
                <i class="fa-solid fa-circle-info text-warning"></i> รายละเอียด
            </div>
            <div class="cx-pdp-desc-content">
                <?= nl2br(htmlspecialchars($item['des'])) ?>
            </div>
        </div>

        <!-- 3. หมายเหตุ -->
        <div class="cx-pdp-card-box" style="border-color: rgba(239, 68, 68, 0.25);">
            <div class="cx-pdp-card-head" style="color: #ef4444;">
                <i class="fa-solid fa-triangle-exclamation text-danger"></i> หมายเหตุ
            </div>
            <div class="cx-pdp-notice-text">
                กรุณาอัดคลิปวิดีโอก่อนทำการซื้อสินค้าเพื่อเป็นหลักฐานหากระบบผิดพลาด ต้องใช้วิดีโอในการยืนยัน ไม่มีการคืนเงิน ทุกกรณี
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     REPORT / TICKET MODAL (SCREENSHOTS 3 & 4)
══════════════════════════════════════════ -->
<div class="modal fade" id="cxReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered cx-report-modal-dialog">
        <div class="modal-content cx-report-modal-content">
            <!-- Modal Header -->
            <div class="cx-report-modal-header">
                <div class="cx-report-header-left">
                    <div class="cx-report-flag-badge">
                        <i class="fa-solid fa-flag"></i>
                    </div>
                    <div>
                        <div class="cx-report-title">ติดต่อหรือรายงานปัญหาของท่าน</div>
                        <div class="cx-report-subtitle">รายงานปัญหาหรือเคลมประกันสินค้านี้</div>
                    </div>
                </div>
                <button type="button" class="cx-report-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Form -->
            <form id="cxReportForm" onsubmit="submitReportTicket(event)">
                <!-- 1. ประเภท & หัวข้อ -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="cx-report-label">ประเภท <span class="req">•</span></label>
                        <select id="rpt_category" class="cx-report-select form-select" required>
                            <option value="code_not_working">รหัสใช้ไม่ได้ / สินค้ามีปัญหา</option>
                            <option value="warranty_claim">ต้องการเคลมประกันสินค้า</option>
                            <option value="wrong_item">ได้ของผิด / ไม่ครบ</option>
                            <option value="topup_issue">ปัญหาเติมเงิน</option>
                            <option value="product_inquiry">สอบถามข้อมูลเพิ่มเติม</option>
                            <option value="other">อื่นๆ</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="cx-report-label">หัวข้อ <span class="req">•</span></label>
                        <input type="text" id="rpt_subject" class="cx-report-input form-control" placeholder="หัวข้อรายงาน" value="แจ้งปัญหา: <?= htmlspecialchars($item['name']) ?>" required>
                    </div>
                </div>

                <!-- 2. สินค้าที่เกี่ยวข้อง -->
                <div class="cx-report-form-group">
                    <label class="cx-report-label">สินค้าที่เกี่ยวข้อง <span class="text-muted fw-normal">(ไม่จำเป็น)</span></label>
                    <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:#050608;border:1px solid rgba(255,255,255,0.12);">
                        <i class="fa-solid fa-box text-warning ms-2"></i>
                        <input type="text" id="rpt_product_name" class="border-0 bg-transparent text-white small flex-grow-1" value="<?= htmlspecialchars($item['name']) ?>" readonly>
                        <input type="hidden" id="rpt_product_id" value="<?= $item['id'] ?>">
                        <input type="hidden" id="rpt_order_id" value="">
                        <?php if (!empty($user_orders)): ?>
                            <button type="button" class="btn btn-sm text-warning" onclick="openOrderPickerModal()" style="font-size:0.75rem;background:rgba(250,204,21,0.1);border:1px solid rgba(250,204,21,0.3);border-radius:8px;">
                                เลือกจากประวัติซื้อ
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 3. รายละเอียด -->
                <div class="cx-report-form-group">
                    <label class="cx-report-label">รายละเอียด <span class="req">•</span></label>
                    <textarea id="rpt_message" rows="3" class="cx-report-textarea form-control" placeholder="อธิบายรายละเอียดปัญหาของคุณอย่างละเอียด..." required></textarea>
                </div>

                <!-- 4. ช่องทางติดต่อเพิ่มเติม -->
                <div class="cx-report-form-group">
                    <label class="cx-report-label">ช่องทางติดต่อเพิ่มเติม <span class="text-muted fw-normal">(ไม่จำเป็น)</span></label>
                    <input type="text" id="rpt_contact" class="cx-report-input form-control" placeholder="Discord Tag / เบอร์โทร / LINE ID / Facebook">
                </div>

                <!-- 5. รูปภาพ (สูงสุด 5 ภาพ ไฟล์ละไม่เกิน 5 MB) -->
                <div class="cx-report-form-group">
                    <label class="cx-report-label">รูปภาพ (สูงสุด 5 ภาพ ไฟล์ละไม่เกิน 5 MB)</label>
                    <div class="cx-report-dropzone" onclick="document.getElementById('rpt_images_input').click()">
                        <i class="fa-solid fa-cloud-arrow-up cx-report-dropzone-icon"></i>
                        <div class="cx-report-dropzone-text">คลิกหรือลากไฟล์มาวางที่นี่</div>
                        <div class="cx-report-dropzone-sub">สูงสุด 5 ภาพ • ไฟล์ละไม่เกิน 5 MB</div>
                        <input type="file" id="rpt_images_input" multiple accept="image/*" style="display:none;" onchange="handleReportImages(this)">
                    </div>
                    <div id="rpt_preview_container" class="cx-report-previews"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-cx-submit-report" id="btnSubmitReport">
                    <i class="fa-solid fa-paper-plane me-1"></i> ส่งรายงาน
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     SELECT ORDER MODAL (SCREENSHOT 4)
══════════════════════════════════════════ -->
<div class="modal fade" id="cxSelectOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content cx-report-modal-content p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-box-archive text-warning fs-5"></i>
                    <div>
                        <div class="fw-bold text-white small">เลือกสินค้า</div>
                        <div class="text-muted" style="font-size:0.72rem;">เลือกรายการจากประวัติการซื้อของคุณ (ไม่บังคับ)</div>
                    </div>
                </div>
                <button type="button" class="cx-report-close-btn" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="list-group mb-3" style="max-height: 250px; overflow-y: auto;">
                <?php if (empty($user_orders)): ?>
                    <div class="text-center py-4 text-muted small">ไม่มีประวัติการสั่งซื้อ</div>
                <?php else: ?>
                    <?php foreach ($user_orders as $uo): ?>
                        <button type="button" class="list-group-item list-group-item-action text-white d-flex align-items-center justify-content-between p-2.5 mb-1.5 rounded-3" 
                                style="background:#090b12;border:1px solid rgba(255,255,255,0.08);"
                                onclick="pickOrderForReport('<?= htmlspecialchars(addslashes($uo['product'] ?? '')) ?>', <?= intval($uo['p_id'] ?? 0) ?>, <?= intval($uo['id']) ?>)">
                            <div>
                                <div class="fw-bold small"><?= htmlspecialchars($uo['product'] ?? '') ?></div>
                                <div class="text-muted" style="font-size:0.7rem;"><?= date('d/m/Y H:i', strtotime($uo['date'] ?? 'now')) ?> • ฿<?= number_format((float)($uo['price'] ?? 0)) ?></div>
                            </div>
                            <i class="fa-solid fa-check text-warning opacity-0 check-mark"></i>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary w-50 rounded-3 small" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning w-50 fw-bold rounded-3 small text-dark" data-bs-dismiss="modal">ยืนยัน</button>
            </div>
        </div>
    </div>
</div>

<script>
// Quantity Stepper
function changePdpQty(delta) {
    const input = document.getElementById('pdpQtyInput');
    let current = parseInt(input.value) || 1;
    let max = <?= $item['stock'] ?>;
    current += delta;
    if (current < 1) current = 1;
    if (current > max && max > 0) current = max;
    input.value = current;
}

function scrollToPdpSpecs() {
    const el = document.getElementById('pdpSpecsSection');
    if (el) el.scrollIntoView({ behavior: 'smooth' });
}

function playPdpVideo() {
    Swal.fire({
        title: 'วิดีโอรีวิวสินค้า',
        html: '<div class="ratio ratio-16x9"><iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1" allowfullscreen></iframe></div>',
        showCloseButton: true,
        showConfirmButton: false,
        background: '#08090d',
        color: '#ffffff',
        width: '700px'
    });
}

function addToCartPdp(pid) {
    const qty = parseInt(document.getElementById('pdpQtyInput').value) || 1;
    Swal.fire({
        icon: 'success',
        title: 'เพิ่มลงรถเข็นแล้ว!',
        text: `เพิ่ม <?= htmlspecialchars($item['name']) ?> จำนวน ${qty} ชิ้น เรียบร้อย`,
        background: '#08090d',
        color: '#ffffff',
        timer: 1500,
        showConfirmButton: false
    });
}

function executePdpBuy() {
    <?php if (!isset($_SESSION['id'])): ?>
        openAuthModal('login');
        return;
    <?php endif; ?>

    const agree = document.getElementById('pdpAgreeTerms').checked;
    if (!agree) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณายอมรับข้อตกลง',
            text: 'กรุณาติ๊กยอมรับข้อตกลงและเงื่อนไขการสั่งซื้อก่อนดำเนินการต่อ',
            background: '#08090d',
            color: '#ffffff',
            confirmButtonColor: '#facc15'
        });
        return;
    }

    const qty = parseInt(document.getElementById('pdpQtyInput').value) || 1;
    const price = <?= $item['price'] ?>;
    const totalPrice = price * qty;
    const isRandom = <?= $item['is_random'] ? 'true' : 'false' ?>;

    const confirmTitle = isRandom ? 'ยืนยันการสุ่มของรางวัล?' : 'ยืนยันการสั่งซื้อ?';
    const confirmText = isRandom ? 
        `คุณต้องการสุ่มกล่อง "<?= htmlspecialchars($item['name']) ?>" จำนวน ${qty} ครั้ง รวม ฿${totalPrice.toLocaleString()} ใช่หรือไม่?` :
        `คุณต้องการสั่งซื้อ <?= htmlspecialchars($item['name']) ?> จำนวน ${qty} ชิ้น รวม ฿${totalPrice.toLocaleString()} ใช่หรือไม่?`;
    const confirmBtnText = isRandom ? '🎲 สุ่มเลย!' : 'ยืนยันสั่งซื้อ';

    Swal.fire({
        title: confirmTitle,
        text: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#facc15',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmBtnText,
        cancelButtonText: 'ยกเลิก',
        background: '#08090d',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: isRandom ? 'กำลังสุ่มของรางวัล...' : 'กำลังทำรายการ...',
                text: isRandom ? 'กรุณารอสักครู่ ระบบกำลังหมุนและสุ่มผลรางวัล' : 'กรุณารอสักครู่ ระบบกำลังตัดสต็อกและส่งมอบสินค้า',
                allowOutsideClick: false,
                background: '#08090d',
                color: '#ffffff',
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                type: 'POST',
                url: 'system/buybox.php',
                data: { id: <?= $item['id'] ?>, count: qty },
                dataType: 'json',
                success: function(res) {
                    const isSalt = (res.salt === 'salt');
                    Swal.fire({
                        icon: isSalt ? 'info' : 'success',
                        title: isSalt ? 'ผลการสุ่ม' : (isRandom ? 'ยินดีด้วย!' : 'สั่งซื้อสำเร็จ!'),
                        text: res.message || (isSalt ? 'เสียใจด้วยนะ คุณไม่ได้รับรางวัล' : 'ระบบได้ส่งมอบสินค้าไปยังประวัติการซื้อของคุณแล้ว'),
                        confirmButtonColor: isSalt ? '#6b7280' : '#facc15',
                        confirmButtonText: 'ดูประวัติการซื้อ / รางวัล',
                        background: '#08090d',
                        color: '#ffffff'
                    }).then(() => {
                        window.navTo('?page=buyhis');
                    });
                },
                error: function(err) {
                    const msg = err.responseJSON ? err.responseJSON.message : 'เกิดข้อผิดพลาดในการทำรายการ';
                    Swal.fire({
                        icon: 'error',
                        title: 'ทำรายการไม่สำเร็จ',
                        text: msg,
                        confirmButtonColor: '#ef4444',
                        background: '#08090d',
                        color: '#ffffff'
                    });
                }
            });
        }
    });
}

// ── Report / Ticket Functions ──
function openReportModal() {
    <?php if (!isset($_SESSION['id'])): ?>
        openAuthModal('login');
        return;
    <?php endif; ?>
    const modal = new bootstrap.Modal(document.getElementById('cxReportModal'));
    modal.show();
}

function openOrderPickerModal() {
    const modal = new bootstrap.Modal(document.getElementById('cxSelectOrderModal'));
    modal.show();
}

function pickOrderForReport(pName, pId, orderId) {
    document.getElementById('rpt_product_name').value = pName;
    document.getElementById('rpt_product_id').value = pId;
    document.getElementById('rpt_order_id').value = orderId;
    document.getElementById('rpt_subject').value = `แจ้งปัญหา: ${pName} (ออเดอร์ #${orderId})`;
    bootstrap.Modal.getInstance(document.getElementById('cxSelectOrderModal')).hide();
}

let selectedReportFiles = [];
function handleReportImages(input) {
    const container = document.getElementById('rpt_preview_container');
    container.innerHTML = '';
    selectedReportFiles = Array.from(input.files).slice(0, 5);

    selectedReportFiles.forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'cx-report-prev-thumb';
            container.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}

function submitReportTicket(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitReport');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังส่งรายงาน...';

    const fd = new FormData();
    fd.append('category', document.getElementById('rpt_category').value);
    fd.append('subject', document.getElementById('rpt_subject').value);
    fd.append('product_name', document.getElementById('rpt_product_name').value);
    fd.append('product_id', document.getElementById('rpt_product_id').value);
    fd.append('order_id', document.getElementById('rpt_order_id').value);
    fd.append('contact', document.getElementById('rpt_contact').value);
    fd.append('message', document.getElementById('rpt_message').value);

    selectedReportFiles.forEach(f => {
        fd.append('images[]', f);
    });

    fetch('system/ticket_create.php', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> ส่งรายงาน';

        if (res.status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('cxReportModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'ส่งรายงานสำเร็จ!',
                text: 'ทีมงานได้รับข้อมูลรายงานแล้ว และจะติดต่อกลับโดยเร็วที่สุดครับ',
                background: '#08090d',
                color: '#ffffff',
                confirmButtonColor: '#facc15'
            }).then(() => {
                if (res.data && res.data.ticket_id) {
                    window.navTo(`?page=profile&subpage=ticket_detail&tid=${res.data.ticket_id}`);
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'ส่งรายงานไม่สำเร็จ',
                text: res.message || 'เกิดข้อผิดพลาด',
                background: '#08090d',
                color: '#ffffff',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> ส่งรายงาน';
        Swal.fire({
            icon: 'error',
            title: 'ผิดพลาด',
            text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้',
            background: '#08090d',
            color: '#ffffff'
        });
    });
}
</script>
