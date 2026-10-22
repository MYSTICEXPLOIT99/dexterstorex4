<?php
// 🤖 Discord Bot Hosting Platform (CYGNUSX STORE)
global $conn, $user;
if (!isset($conn) || !$conn) {
    require_once dirname(__DIR__) . '/system/a_func.php';
}

$user_id = intval($_SESSION['id'] ?? ($_SESSION['user']['id'] ?? ($user['id'] ?? 0)));
$user_point = isset($user['point']) ? floatval($user['point']) : 0;

// Fetch Package Settings
$pkg_stmt = $conn ? $conn->query("SELECT * FROM discord_bot_packages LIMIT 1") : null;
$pkg = ($pkg_stmt) ? $pkg_stmt->fetch(PDO::FETCH_ASSOC) : [
    'price' => 50.00,
    'duration_days' => 30,
    'disk_mb' => 1024,
    'addon_storage_gb_price' => 20.00
];

// Fetch User's Active Bots
$my_bots = [];
if ($user_id > 0) {
    $b_stmt = $conn->prepare("SELECT * FROM discord_bot_rentals WHERE user_id = ? ORDER BY id DESC");
    $b_stmt->execute([$user_id]);
    $my_bots = $b_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="mb-4 text-center" data-aos="fade-down">
    <div class="section-eyebrow">// DISCORD_BOT_CLUSTER // AUTONOMOUS_NODE_PROVISION</div>
    <h1 class="section-main-heading" style="font-size: 2.3rem;">DISCORD BOT // <span style="color:#00c9ff;">CLUSTER</span></h1>
    <p class="section-subtext mb-3">รันบอทออนไลน์ 24/7 เสถียร ไม่ดับ ไม่แล็ก — ควบคุมผ่าน Live Web Terminal และแก้ไขโค้ดสดได้ทันที</p>
    
    <!-- Balance & Bot Count Badge -->
    <div class="d-inline-flex align-items-center gap-3 flex-wrap justify-content-center">
        <div class="wheel-pill-badge d-inline-flex align-items-center gap-2" style="background:#090a12;border:1.5px solid rgba(250,204,21,0.3);padding:8px 22px;border-radius:999px;box-shadow:0 0 20px rgba(250,204,21,0.12);">
            <span style="font-family:var(--font-cyber);font-size:0.75rem;color:#94a3b8;letter-spacing:1px;">[ CREDIT_BALANCE ]</span>
            <span style="color:#facc15;font-family:var(--font-cyber);font-weight:900;font-size:1.1rem;letter-spacing:1px;">฿ <?= number_format($user_point, 2) ?></span>
        </div>
        <div class="wheel-pill-badge d-inline-flex align-items-center gap-2" style="background:#090a12;border:1.5px solid rgba(0,201,255,0.4);color:#00c9ff;padding:8px 22px;border-radius:999px;box-shadow:0 0 20px rgba(0,201,255,0.15);font-family:var(--font-cyber);">
            <i class="fa-solid fa-robot text-info"></i>
            <span>MY_BOT_SLOTS: <strong style="color:#ffffff;"><?= count($my_bots) ?></strong> NODES</span>
        </div>
    </div>
</div>

<div class="container-fluid px-0 px-md-3" style="max-width: 1200px;">
    
    <!-- ══════════════════════════════════════════
         SECTION 1: MY ACTIVE BOTS (IF ANY)
    ══════════════════════════════════════════ -->
    <?php if (!empty($my_bots)): ?>
    <div class="mb-5" data-aos="fade-up">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <h4 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);letter-spacing:0.5px;">
                <i class="fa-solid fa-server text-info me-2"></i> MY ACTIVE BOT NODES (<?= count($my_bots) ?> SLOTS)
            </h4>
            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-4 py-2 fw-bold" onclick="openRentBotModal()" style="font-family:var(--font-cyber);letter-spacing:0.5px;">
                <i class="fa-solid fa-plus me-1"></i> [ RENT NEW SLOT + ]
            </button>
        </div>

        <div class="row g-3">
            <?php foreach ($my_bots as $b): 
                $is_exp = (strtotime($b['expire_date']) < time());
                $status_color = $is_exp ? '#ef4444' : (($b['status'] == 'running') ? '#22c55e' : '#94a3b8');
                $status_text  = $is_exp ? 'EXPIRED' : (($b['status'] == 'running') ? 'ONLINE' : 'STOPPED');
            ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="ranvyx-cat-card p-4 h-100 d-flex flex-column justify-content-between position-relative" style="background:linear-gradient(165deg, rgba(10,12,20,0.96) 0%, rgba(6,7,14,0.98) 100%);border:1.5px solid rgba(0,201,255,0.35);border-radius:20px;box-shadow:0 15px 40px rgba(0,0,0,0.85);">
                    <div>
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2.5">
                                <div style="width:46px;height:46px;border-radius:14px;background:rgba(0,201,255,0.15);border:1.5px solid rgba(0,201,255,0.4);display:flex;align-items:center;justify-content:center;color:#00c9ff;font-size:1.4rem;box-shadow:0 0 15px rgba(0,201,255,0.25);">
                                    <i class="fa-brands fa-discord"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-white mb-0 text-truncate" style="max-width:180px;font-family:var(--font-cyber);letter-spacing:0.5px;"><?= htmlspecialchars($b['bot_name']) ?></h5>
                                    <small class="text-muted font-monospace" style="font-size:0.75rem;">NODE_ID: <?= htmlspecialchars($b['slot_key']) ?></small>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-1.5" style="background:rgba(0,0,0,0.6);border:1px solid <?= $status_color ?>;color:<?= $status_color ?>;font-size:0.75rem;font-weight:800;font-family:var(--font-cyber);letter-spacing:0.5px;">
                                ● <?= $status_text ?>
                            </span>
                        </div>

                        <!-- Info Pills -->
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge" style="background:#0e111d;color:#38bdf8;border:1px solid rgba(56,189,248,0.25);font-size:0.75rem;font-family:var(--font-cyber);">
                                <i class="fa-solid fa-code me-1"></i> <?= strtoupper($b['runtime']) ?>
                            </span>
                            <span class="badge" style="background:#0e111d;color:#facc15;border:1px solid rgba(250,204,21,0.25);font-size:0.75rem;font-family:var(--font-cyber);">
                                <i class="fa-solid fa-hard-drive me-1"></i> <?= number_format($b['disk_limit_mb']) ?> MB
                            </span>
                            <span class="badge" style="background:#0e111d;color:#cbd5e1;border:1px solid rgba(255,255,255,0.1);font-size:0.75rem;font-family:var(--font-cyber);">
                                <i class="fa-solid fa-clock me-1"></i> <?= date('d/m/Y', strtotime($b['expire_date'])) ?>
                            </span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="?page=discord_bot_console&id=<?= $b['id'] ?>" class="btn w-100 py-2.5 fw-bold btn-prod-buy-gold d-flex align-items-center justify-content-center gap-2" style="border-radius:12px;font-family:var(--font-cyber);font-size:0.92rem;letter-spacing:0.5px;">
                            <i class="fa-solid fa-terminal"></i> [ ACCESS CONSOLE // TERMINAL ➔ ]
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ══════════════════════════════════════════
         SECTION 2: PACKAGE PRICING & HERO CARD
    ══════════════════════════════════════════ -->
    <div class="row g-4 justify-content-center align-items-center mb-5" data-aos="fade-up">
        
        <!-- Left: Feature Highlights -->
        <div class="col-12 col-lg-7">
            <div class="p-4 p-md-5 rounded-4" style="background:linear-gradient(165deg, rgba(10,12,20,0.96) 0%, rgba(6,7,12,0.98) 100%);border:1.5px solid rgba(0,201,255,0.3);box-shadow:0 25px 70px rgba(0,0,0,0.9);position:relative;overflow:hidden;">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background:rgba(0,201,255,0.12);border:1.5px solid rgba(0,201,255,0.35);color:#00c9ff;font-size:0.8rem;font-weight:800;font-family:var(--font-cyber);letter-spacing:0.5px;">
                    <i class="fa-solid fa-bolt-lightning"></i> PRO BOT HOSTING CLUSTER // PM2 CLOUD
                </div>
                <h2 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);letter-spacing:1px;font-size:1.95rem;">
                    บอทออนไลน์ 24/7 เสถียร ไม่ดับ ไม่แล็ก
                </h2>
                <p class="text-muted mb-4" style="line-height:1.7;font-size:0.95rem;">
                    หมดปัญหาคอมดับหรือเน็ตหลุด! เช่าพื้นที่รันบอทบนระบบคลาวด์ความเร็วสูง พร้อมระบบกู้ชีพอัตโนมัติ (Auto-Restart on Crash), Realtime Web Terminal, และตัวแก้ไขโค้ดออนไลน์ในตัว
                </p>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#090b14;border:1px solid rgba(250,204,21,0.25);">
                            <div class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);font-size:0.9rem;"><i class="fa-solid fa-hard-drive text-warning me-1.5"></i> พื้นที่ 1 GB NVMe</div>
                            <small class="text-muted">ขยายพื้นที่เพิ่มได้ตลอดเวลา</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#090b14;border:1px solid rgba(0,201,255,0.25);">
                            <div class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);font-size:0.9rem;"><i class="fa-solid fa-code text-info me-1.5"></i> Node.js & Python</div>
                            <small class="text-muted">รองรับ Discord.js & Discord.py</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#090b14;border:1px solid rgba(74,222,128,0.25);">
                            <div class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);font-size:0.9rem;"><i class="fa-solid fa-terminal text-success me-1.5"></i> Web Live Terminal</div>
                            <small class="text-muted">ดู Log สดและส่งคำสั่งได้ทันที</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#090b14;border:1px solid rgba(248,113,113,0.25);">
                            <div class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);font-size:0.9rem;"><i class="fa-solid fa-file-zipper text-danger me-1.5"></i> อัปโหลดแตกไฟล์ ZIP</div>
                            <small class="text-muted">ลงโค้ดบอทได้ในคลิกเดียว</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Purchase Card -->
        <div class="col-12 col-md-8 col-lg-5">
            <div class="ranvyx-cat-card p-4 p-md-5 text-center position-relative" style="background:linear-gradient(165deg, rgba(8,10,18,0.98) 0%, rgba(4,5,10,0.99) 100%);border:2px solid #00c9ff;border-radius:26px;box-shadow:0 0 60px rgba(0,201,255,0.3), inset 0 0 25px rgba(0,201,255,0.08);">
                
                <!-- Popular Badge -->
                <div style="position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,#00c9ff,#0084ff);color:#000;font-weight:900;font-size:0.75rem;padding:5px 20px;border-radius:20px;letter-spacing:1.5px;font-family:var(--font-cyber);box-shadow:0 0 20px rgba(0,201,255,0.6);">
                    ★ BEST VALUE PLAN
                </div>

                <div class="mb-3 mt-2">
                    <div style="width:68px;height:68px;border-radius:18px;background:radial-gradient(circle, rgba(0,201,255,0.25) 0%, rgba(0,0,0,0.8) 100%);border:2px solid #00c9ff;display:flex;align-items:center;justify-content:center;margin:0 auto;color:#00c9ff;font-size:2rem;box-shadow:0 0 25px rgba(0,201,255,0.4);">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                </div>

                <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);letter-spacing:1px;">DISCORD BOT SLOT</h3>
                <p class="text-muted small mb-3">1 Bot Instance · ทำงานออนไลน์ 24/7 อัตโนมัติ</p>

                <div class="my-4 py-3 rounded-3" style="background:#05060a;border:1px dashed rgba(0,201,255,0.35);">
                    <div class="text-muted" style="font-size:0.8rem;font-family:var(--font-cyber);letter-spacing:1px;">RENTAL_FEE // ค่าบริการ</div>
                    <div class="fw-bold text-white" style="font-family:var(--font-cyber);font-size:3rem;line-height:1;">
                        <span style="font-size:1.5rem;color:#00c9ff;">฿</span><?= number_format($pkg['price'], 0) ?>
                        <span style="font-size:0.95rem;color:#94a3b8;font-weight:normal;">/ <?= intval($pkg['duration_days']) ?> DAYS</span>
                    </div>
                </div>

                <ul class="list-unstyled text-start mb-4 text-white" style="font-size:0.92rem;line-height:2.2;font-family:var(--font-main), 'Prompt', Kanit, sans-serif;">
                    <li><i class="fa-solid fa-circle-check text-info me-2"></i> พื้นที่จัดเก็บ <strong>1 GB (SSD NVMe)</strong></li>
                    <li><i class="fa-solid fa-circle-check text-info me-2"></i> สิทธิ์รันบอทออนไลน์ 24 ชั่วโมง</li>
                    <li><i class="fa-solid fa-circle-check text-info me-2"></i> หน้า Console ควบคุมและดู Log สด</li>
                    <li><i class="fa-solid fa-circle-check text-info me-2"></i> Web Code Editor แก้ไขโค้ดได้ในเว็บ</li>
                    <li><i class="fa-solid fa-circle-check text-info me-2"></i> ขยายพื้นที่ได้ (+1GB ละ ฿<?= number_format($pkg['addon_storage_gb_price'], 0) ?>)</li>
                </ul>

                <?php if ($user_id <= 0): ?>
                    <button type="button" onclick="openAuthModal('login')" class="btn w-100 py-3 fw-bold btn-prod-buy-gold" style="border-radius:14px;font-family:var(--font-cyber);letter-spacing:1px;font-size:1.02rem;">
                        <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> เข้าสู่ระบบเพื่อเช่าบอท ➔
                    </button>
                <?php else: ?>
                    <button type="button" onclick="openRentBotModal()" class="btn w-100 py-3 fw-bold btn-prod-buy-gold" style="border-radius:14px;font-family:var(--font-cyber);letter-spacing:1px;font-size:1.02rem;">
                        <i class="fa-solid fa-bolt-lightning me-1"></i> สั่งซื้อและเปิดใช้งานบอท ➔
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     MODAL: RENT NEW DISCORD BOT SLOT
══════════════════════════════════════════ -->
<div class="modal fade" id="rentBotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content text-white" style="background:#090a12;border:1px solid rgba(0,201,255,0.4);border-radius:20px;box-shadow:0 25px 70px rgba(0,0,0,0.9);">
            <div class="modal-header border-secondary border-opacity-25 pb-2">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-robot text-info"></i> สั่งซื้อแพ็กเกจเช่าบอท Discord
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body pt-3">
                <div class="p-3 rounded-3 mb-3 d-flex align-items-center justify-content-between" style="background:#06070a;border:1px solid rgba(255,255,255,0.06);">
                    <div>
                        <div class="text-muted small">ราคาแพ็กเกจ (30 วัน)</div>
                        <div class="fw-bold text-white fs-4" style="font-family:var(--font-cyber);">฿<?= number_format($pkg['price'], 2) ?></div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">ยอดเงินคงเหลือของคุณ</div>
                        <div class="fw-bold <?= ($user_point >= $pkg['price']) ? 'text-success' : 'text-danger' ?> fs-5">฿<?= number_format($user_point, 2) ?></div>
                    </div>
                </div>

                <!-- 1. Bot Name -->
                <div class="mb-3">
                    <label class="form-label text-white fw-bold small">ชื่อบอทของคุณ (Bot Name)</label>
                    <input type="text" id="rent_bot_name" class="form-control bg-dark border-secondary text-white" placeholder="เช่น My Music Bot, Welcome Bot" value="My Discord Bot" style="border-radius:10px;height:46px;">
                </div>

                <!-- 2. Runtime Selection -->
                <div class="mb-3">
                    <label class="form-label text-white fw-bold small">เลือกภาษาโปรแกรม (Runtime)</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="p-3 rounded-3 border d-block text-center cursor-pointer runtime-opt active" id="opt_node" onclick="selectRuntime('nodejs')" style="background:#0e111d;border-color:#00c9ff !important;cursor:pointer;">
                                <i class="fa-brands fa-node-js fs-3 text-success mb-1"></i>
                                <div class="fw-bold text-white small">Node.js (v20)</div>
                                <small class="text-muted" style="font-size:0.7rem;">Discord.js</small>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="p-3 rounded-3 border d-block text-center cursor-pointer runtime-opt" id="opt_python" onclick="selectRuntime('python')" style="background:#0e111d;border-color:rgba(255,255,255,0.1);cursor:pointer;">
                                <i class="fa-brands fa-python fs-3 text-warning mb-1"></i>
                                <div class="fw-bold text-white small">Python (v3.11)</div>
                                <small class="text-muted" style="font-size:0.7rem;">Discord.py</small>
                            </label>
                        </div>
                    </div>
                    <input type="hidden" id="rent_bot_runtime" value="nodejs">
                </div>

                <!-- 3. Template Selection -->
                <div class="mb-3">
                    <label class="form-label text-white fw-bold small">แม่แบบเริ่มต้น (Starter Template)</label>
                    <select id="rent_bot_template" class="form-select bg-dark border-secondary text-white" style="border-radius:10px;height:46px;">
                        <option value="profile_247">🟢 แม่แบบบอทออนสถานะโปรไฟล์ 24/7 (Profile 24/7 Online)</option>
                        <option value="voice_247">🎧 แม่แบบบอทออนห้องเสียง 24/7 (Voice 24/7 Online)</option>
                        <option value="music">🎵 แม่แบบบอทเปิดเพลง (Music Bot Template)</option>
                        <option value="welcome">👋 แม่แบบบอทต้อนรับ & แจกยศ (Welcome & Roles)</option>
                        <option value="fivem">🎮 แม่แบบบอทแสดงสถานะ FiveM Server</option>
                        <option value="blank">📄 เริ่มต้นโปรเจกต์เปล่า (Blank Starter)</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer border-secondary border-opacity-25 pt-2">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal" style="border-radius:8px;">ยกเลิก</button>
                <button type="button" id="btn_confirm_rent_bot" onclick="submitRentBot()" class="btn btn-prod-buy-gold px-4 fw-bold" style="border-radius:8px;font-family:var(--font-cyber);">
                    ยืนยันการเช่า (฿<?= number_format($pkg['price'], 0) ?>)
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedRuntime = 'nodejs';
let rentModalInstance = null;

$(document).ready(function() {
    if ($('#rentBotModal').parent().prop('tagName') !== 'BODY') {
        $('#rentBotModal').appendTo('body');
    }
});

function openRentBotModal() {
    <?php if ($user_id <= 0): ?>
        openAuthModal('login');
        return;
    <?php endif; ?>
    const el = document.getElementById('rentBotModal');
    if (!rentModalInstance) {
        rentModalInstance = new bootstrap.Modal(el, { backdrop: true, keyboard: true });
    }
    rentModalInstance.show();
}

function selectRuntime(rt) {
    selectedRuntime = rt;
    $('#rent_bot_runtime').val(rt);
    if (rt === 'nodejs') {
        $('#opt_node').css('border-color', '#00c9ff');
        $('#opt_python').css('border-color', 'rgba(255,255,255,0.1)');
    } else {
        $('#opt_python').css('border-color', '#00c9ff');
        $('#opt_node').css('border-color', 'rgba(255,255,255,0.1)');
    }
}

let isSubmittingRent = false;

function submitRentBot() {
    if (isSubmittingRent) return;

    const name = $('#rent_bot_name').val().trim();
    const runtime = $('#rent_bot_runtime').val();
    const template = $('#rent_bot_template').val();

    if (!name) {
        Swal.fire({ icon: 'warning', title: 'แจ้งเตือน', text: 'กรุณาระบุชื่อบอทของคุณ', background: '#0a0a0f', color: '#fff' });
        return;
    }

    isSubmittingRent = true;
    const btn = $('#btn_confirm_rent_bot');
    btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังดำเนินการ...');

    // 1. Instantly hide the rent modal to prevent backdrop overlap / double purchase
    if (rentModalInstance) {
        rentModalInstance.hide();
    }
    const modalEl = document.getElementById('rentBotModal');
    if (modalEl) {
        const bsModal = bootstrap.Modal.getInstance(modalEl);
        if (bsModal) bsModal.hide();
    }
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css({'overflow': '', 'padding-right': ''});

    // 2. Show loading modal
    Swal.fire({
        title: 'กำลังสั่งซื้อและสร้างพื้นที่บอท...',
        text: 'ระบบกำลังจัดสรรสล็อต NVMe และเตรียมโค้ดบอทของคุณ กรุณารอสักครู่',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        },
        background: '#0a0a0f',
        color: '#fff'
    });

    $.ajax({
        type: 'POST',
        url: 'system/discord_bot.php',
        data: {
            action: 'rent',
            bot_name: name,
            runtime: runtime,
            template: template
        },
        dataType: 'json'
    }).done(function(res) {
        isSubmittingRent = false;
        btn.prop('disabled', false).text('ยืนยันการเช่า (฿<?= number_format($pkg['price'], 0) ?>)');

        if (res.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: res.message,
                background: '#0a0a0f',
                color: '#fff',
                confirmButtonColor: '#38bdf8',
                confirmButtonText: 'เข้าสู่คอนโซลจัดการบอท ➔'
            }).then(() => {
                window.location.href = '?page=discord_bot_console&id=' + res.data.rental_id;
            });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
        }
    }).fail(function() {
        isSubmittingRent = false;
        btn.prop('disabled', false).text('ยืนยันการเช่า (฿<?= number_format($pkg['price'], 0) ?>)');
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง', background: '#0a0a0f', color: '#fff' });
    });
}
</script>
