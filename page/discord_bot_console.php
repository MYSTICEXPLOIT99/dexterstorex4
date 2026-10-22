<?php
// 🤖 Discord Bot Interactive Control Console (CYGNUSX STORE)
global $conn, $user;
if (!isset($conn) || !$conn) {
    require_once dirname(__DIR__) . '/system/a_func.php';
}

$user_id = intval($_SESSION['id'] ?? ($_SESSION['user']['id'] ?? ($user['id'] ?? 0)));
$bot_id = intval($_GET['id'] ?? 0);

if ($user_id <= 0) {
    echo "<script>location.href='?page=login';</script>";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM discord_bot_rentals WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$bot_id, $user_id]);
$bot = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bot) {
    echo "<div class='p-5 text-center text-white'><h4>ไม่พบข้อมูลบอท หรือคุณไม่มีสิทธิ์เข้าถึงคอนโซลนี้</h4><a href='?page=discord_bot' class='btn btn-primary mt-3'>← กลับสู่หน้ารายการบอท</a></div>";
    return;
}

$is_expired = (strtotime($bot['expire_date']) < time());
$status_color = $is_expired ? '#ef4444' : (($bot['status'] == 'running') ? '#22c55e' : '#94a3b8');
$status_text  = $is_expired ? 'หมดอายุการใช้งาน' : (($bot['status'] == 'running') ? 'ONLINE (ออนไลน์ 24/7)' : 'STOPPED (ปิดอยู่)');
?>

<div class="mb-4" data-aos="fade-down">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="?page=discord_bot" class="btn btn-sm btn-dark px-3 py-1.5" style="border-radius:8px;background:#141520;border:1px solid rgba(255,255,255,0.1);color:#cbd5e1;">
                    ← รายการบอท
                </a>
                <span class="badge" style="background:#0c1020;border:1px solid rgba(56,189,248,0.3);color:#38bdf8;font-family:var(--font-cyber);">
                    SLOT: <?= htmlspecialchars($bot['slot_key']) ?>
                </span>
            </div>
            <h2 class="fw-bold text-white mb-0 d-flex align-items-center gap-2" style="font-family:var(--font-cyber);">
                <i class="fa-brands fa-discord text-primary"></i> <?= htmlspecialchars($bot['bot_name']) ?>
            </h2>
        </div>

        <!-- Power Controls & Lock Start -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn fw-bold px-3 py-2 <?= !empty($bot['lock_online']) ? 'btn-outline-success' : 'btn-outline-secondary' ?>" id="btn_toggle_lock" onclick="toggleLockOnline()" style="border-radius:10px;font-size:0.85rem;" title="เปิด/ปิดการล็อคไม่ให้บอทถูกปิด">
                <i class="fa-solid <?= !empty($bot['lock_online']) ? 'fa-lock text-success' : 'fa-lock-open text-muted' ?> me-1" id="lockIcon"></i>
                <span id="lockText"><?= !empty($bot['lock_online']) ? 'ล็อคสตาร์ท 24/7 (ACTIVE)' : 'ปลดล็อคแล้ว' ?></span>
            </button>
            <button type="button" class="btn btn-success fw-bold px-3 py-2" id="btn_power_start" onclick="sendPowerAction('start')" <?= ($bot['status'] == 'running' || $is_expired) ? 'disabled' : '' ?> style="border-radius:10px;">
                <i class="fa-solid fa-play me-1"></i> Start
            </button>
            <button type="button" class="btn btn-warning fw-bold px-3 py-2 text-dark" id="btn_power_restart" onclick="sendPowerAction('restart')" <?= ($bot['status'] != 'running' || $is_expired) ? 'disabled' : '' ?> style="border-radius:10px;">
                <i class="fa-solid fa-rotate-right me-1"></i> Restart
            </button>
            <button type="button" class="btn btn-danger fw-bold px-3 py-2" id="btn_power_stop" onclick="sendPowerAction('stop')" <?= ($bot['status'] != 'running' || !empty($bot['lock_online'])) ? 'disabled' : '' ?> style="border-radius:10px;" title="<?= !empty($bot['lock_online']) ? 'โหมดล็อคการทำงานเปิดอยู่ ไม่สามารถกดปิดได้' : 'หยุดการทำงาน' ?>">
                <i class="fa-solid fa-stop me-1"></i> Stop
            </button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     TOP METRICS & STATUS CARDS
══════════════════════════════════════════ -->
<div class="row g-3 mb-4" data-aos="fade-up">
    <!-- 1. Live Status -->
    <div class="col-12 col-md-4">
        <div class="p-3 rounded-4 h-100" style="background:#080a12;border:1px solid rgba(255,255,255,0.08);">
            <div class="text-muted small mb-1">สถานะการทำงาน (Power State)</div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge rounded-pill px-3 py-1.5" style="background:rgba(0,0,0,0.5);border:1px solid <?= $status_color ?>;color:<?= $status_color ?>;font-weight:bold;font-size:0.88rem;" id="botStateBadge">
                    ● <?= $status_text ?>
                </span>
            </div>
            <small class="text-muted">Runtime: <strong><?= strtoupper($bot['runtime']) ?></strong> (<?= htmlspecialchars($bot['main_file']) ?>)</small>
        </div>
    </div>

    <!-- 2. Storage Quota + Upgrade Button -->
    <div class="col-12 col-md-4">
        <div class="p-3 rounded-4 h-100" style="background:#080a12;border:1px solid rgba(255,255,255,0.08);">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small">พื้นที่จัดเก็บ (NVMe Disk)</span>
                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2.5 py-0.5 fw-bold" style="font-size:0.72rem;" onclick="openUpgradeStorageModal()">
                    <i class="fa-solid fa-plus"></i> ขยายพื้นที่ (+1GB)
                </button>
            </div>
            <div class="fw-bold text-white fs-5 mb-1" id="diskUsageText">
                <span id="diskUsedMb">0</span> MB / <span id="diskLimitMb"><?= number_format($bot['disk_limit_mb']) ?></span> MB
            </div>
            <div class="progress" style="height:6px;background:rgba(255,255,255,0.08);">
                <div class="progress-bar bg-info" id="diskProgressBar" role="progressbar" style="width: 5%;"></div>
            </div>
        </div>
    </div>

    <!-- 3. Expiration & Renewal -->
    <div class="col-12 col-md-4">
        <div class="p-3 rounded-4 h-100" style="background:#080a12;border:1px solid rgba(255,255,255,0.08);">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small">ระยะเวลาเช่าบอท</span>
                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-0.5 fw-bold text-warning" style="font-size:0.72rem;" onclick="openRenewModal()">
                    <i class="fa-solid fa-clock-rotate-left"></i> ต่ออายุ
                </button>
            </div>
            <div class="fw-bold text-white fs-5 mb-1">
                <?= date('d/m/Y H:i', strtotime($bot['expire_date'])) ?>
            </div>
            <small class="<?= $is_expired ? 'text-danger' : 'text-success' ?>">
                <?= $is_expired ? 'หมดอายุแล้ว กรุณาต่ออายุ' : 'เหลือเวลา: ' . max(0, round((strtotime($bot['expire_date']) - time()) / 86400)) . ' วัน' ?>
            </small>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     CONSOLE TABS & WORKSPACE
══════════════════════════════════════════ -->
<div class="ranvyx-cat-card p-4 rounded-4" style="background:#080910;border:1px solid rgba(106,128,160,0.3);box-shadow:0 25px 70px rgba(0,0,0,0.85);" data-aos="fade-up">
    
    <!-- Tab Headers -->
    <ul class="nav nav-pills gap-2 mb-4 border-bottom border-secondary border-opacity-25 pb-3 flex-nowrap overflow-auto" id="consoleTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active px-3 py-2 rounded-3 text-white fw-bold d-flex align-items-center gap-2" id="tab-terminal-btn" data-bs-toggle="pill" data-bs-target="#tabTerminal" type="button" style="font-size:0.9rem;">
                <i class="fa-solid fa-terminal text-info"></i> <span>Live Terminal</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-3 py-2 rounded-3 text-white fw-bold d-flex align-items-center gap-2" id="tab-files-btn" data-bs-toggle="pill" data-bs-target="#tabFiles" type="button" onclick="loadFilesList()" style="font-size:0.9rem;">
                <i class="fa-solid fa-folder-open text-warning"></i> <span>File Manager</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-3 py-2 rounded-3 text-white fw-bold d-flex align-items-center gap-2" id="tab-editor-btn" data-bs-toggle="pill" data-bs-target="#tabEditor" type="button" style="font-size:0.9rem;">
                <i class="fa-solid fa-code text-success"></i> <span>Code Editor</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-3 py-2 rounded-3 text-white fw-bold d-flex align-items-center gap-2" id="tab-env-btn" data-bs-toggle="pill" data-bs-target="#tabEnv" type="button" onclick="loadEnvFile()" style="font-size:0.9rem;">
                <i class="fa-solid fa-key text-primary"></i> <span>Environment (.env)</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-3 py-2 rounded-3 text-white fw-bold d-flex align-items-center gap-2" id="tab-templates-btn" data-bs-toggle="pill" data-bs-target="#tabTemplates" type="button" style="font-size:0.9rem;">
                <i class="fa-solid fa-cubes text-danger"></i> <span>บอทสำเร็จรูป (Templates)</span>
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="consoleTabsContent">
        
        <!-- ══════════════════════════════════════════
             TAB 1: LIVE TERMINAL
        ══════════════════════════════════════════ -->
        <div class="tab-pane fade show active" id="tabTerminal" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted"><i class="fa-solid fa-circle text-success" style="font-size:0.55rem;"></i> Realtime output log stream</small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-dark px-2.5 py-1" onclick="fetchBotLogs(true)" title="รีเฟรช Log">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-dark px-2.5 py-1" onclick="$('#terminalOutput').empty();" title="เคลียร์หน้าจอ">
                        <i class="fa-solid fa-eraser"></i>
                    </button>
                </div>
            </div>

            <!-- Matrix Console Terminal Box -->
            <div class="p-3 rounded-4" style="background:#030407;border:1px solid rgba(255,255,255,0.1);font-family:'Space Mono',Consolas,monospace;box-shadow:inset 0 0 30px rgba(0,0,0,0.9);">
                <pre id="terminalOutput" style="height: 380px; overflow-y: auto; color: #38bdf8; font-size: 0.85rem; line-height: 1.6; margin: 0; white-space: pre-wrap; word-break: break-all;">[SYSTEM] Connecting to Bot Terminal stream...</pre>
            </div>
        </div>

        <!-- ══════════════════════════════════════════
             TAB 2: FILE MANAGER
        ══════════════════════════════════════════ -->
        <div class="tab-pane fade" id="tabFiles" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-white mb-0">จัดการไฟล์โค้ด (Workspace Files)</h5>
                    <small class="text-muted">โฟลเดอร์บอท: <code>/bots/<?= htmlspecialchars($bot['slot_key']) ?>/</code></small>
                </div>
                <div class="d-flex gap-2">
                    <input type="file" id="zip_upload_input" accept=".zip" style="display:none;" onchange="handleZipUpload(this)">
                    <button type="button" class="btn btn-sm btn-outline-info px-3" onclick="document.getElementById('zip_upload_input').click()">
                        <i class="fa-solid fa-file-zipper me-1"></i> อัปโหลดและแตกไฟล์ ZIP
                    </button>
                    <button type="button" class="btn btn-sm btn-dark px-3" onclick="loadFilesList()">
                        <i class="fa-solid fa-arrows-rotate me-1"></i> รีเฟรช
                    </button>
                </div>
            </div>

            <div class="table-responsive rounded-3" style="border:1px solid rgba(255,255,255,0.08);">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-muted small" style="background:#040508;">
                            <th>ชื่อไฟล์ / โฟลเดอร์</th>
                            <th>ขนาด</th>
                            <th>แก้ไขล่าสุด</th>
                            <th class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="filesTableBody">
                        <tr><td colspan="4" class="text-center text-muted py-4">กำลังโหลดรายการไฟล์...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══════════════════════════════════════════
             TAB 3: CODE EDITOR
        ══════════════════════════════════════════ -->
        <div class="tab-pane fade" id="tabEditor" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">กำลังแก้ไขไฟล์:</span>
                    <input type="text" id="editing_filename" class="form-control form-control-sm bg-dark border-secondary text-white font-monospace" value="<?= htmlspecialchars($bot['main_file']) ?>" style="width:200px;">
                </div>
                <button type="button" class="btn btn-success fw-bold px-4" onclick="saveEditorFile()">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกไฟล์
                </button>
            </div>

            <div class="rounded-4 overflow-hidden" style="border:1px solid rgba(255,255,255,0.12);">
                <textarea id="codeEditorArea" class="form-control font-monospace p-3 text-white" style="height:440px;background:#030408;color:#00ffcc !important;font-size:0.9rem;line-height:1.6;border:none;resize:vertical;" spellcheck="false"></textarea>
            </div>
        </div>

        <!-- ══════════════════════════════════════════
             TAB 4: ENVIRONMENT VARIABLES (.ENV)
        ══════════════════════════════════════════ -->
        <div class="tab-pane fade" id="tabEnv" role="tabpanel">
            <div class="p-4 rounded-4" style="background:#05060b;border:1px solid rgba(255,255,255,0.08);">
                <h5 class="fw-bold text-white mb-2"><i class="fa-solid fa-shield-halved text-info me-2"></i> จัดการ Token และค่า Configuration (.env)</h5>
                <p class="text-muted small mb-4">ระบบจะอ่านค่า Token และตัวแปรสภาพแวดล้อมจากไฟล์ <code>.env</code> เพื่อความปลอดภัยของข้อมูล</p>

                <div class="mb-3">
                    <label class="form-label text-white fw-bold small">DISCORD BOT TOKEN</label>
                    <input type="password" id="env_discord_token" class="form-control bg-dark border-secondary text-white font-monospace" placeholder="วาง Discord Bot Token ที่ได้จาก Discord Developer Portal ที่นี่" style="height:46px;">
                </div>

                <div class="mb-4">
                    <label class="form-label text-white fw-bold small">เนื้อหา .env ทั้งหมด (Full .env Content)</label>
                    <textarea id="env_raw_content" class="form-control bg-dark border-secondary text-white font-monospace p-3" rows="6" placeholder="KEY=VALUE"></textarea>
                </div>

                <button type="button" class="btn btn-prod-buy-gold px-4 py-2.5 fw-bold" onclick="saveEnvFile()">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกค่า .env
                </button>
            </div>
        </div>

        <!-- ══════════════════════════════════════════
             TAB 5: ONE-CLICK BOT TEMPLATES
        ══════════════════════════════════════════ -->
        <div class="tab-pane fade" id="tabTemplates" role="tabpanel">
            <h5 class="fw-bold text-white mb-3">แม่แบบบอทสำเร็จรูป (One-Click Bot Deployer)</h5>
            <p class="text-muted small mb-4">เลือกแม่แบบที่ต้องการเพื่อแทนที่โค้ดเริ่มต้นในบอทของคุณได้ทันที</p>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between" style="background:#05060b;border:1px solid rgba(56,189,248,0.2);">
                        <div>
                            <div class="fs-2 text-info mb-2"><i class="fa-solid fa-music"></i></div>
                            <h5 class="fw-bold text-white mb-1">บอทเปิดเพลง Discord</h5>
                            <small class="text-muted">เล่นเพลงในห้อง Voice Channel สั่งงานผ่านคำสั่ง !play</small>
                        </div>
                        <button type="button" class="btn btn-outline-info w-100 mt-3" onclick="applyTemplate('music')">
                            ติดตั้งแม่แบบนี้ ➔
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between" style="background:#05060b;border:1px solid rgba(34,197,94,0.2);">
                        <div>
                            <div class="fs-2 text-success mb-2"><i class="fa-solid fa-hand-wave"></i></div>
                            <h5 class="fw-bold text-white mb-1">บอทต้อนรับ & แจกยศ</h5>
                            <small class="text-muted">ส่งการ์ดต้อนรับคนเข้าเซิร์ฟเวอร์ และแจกยศสมาชิกเริ่มต้นอัตโนมัติ</small>
                        </div>
                        <button type="button" class="btn btn-outline-success w-100 mt-3" onclick="applyTemplate('welcome')">
                            ติดตั้งแม่แบบนี้ ➔
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between" style="background:#05060b;border:1px solid rgba(245,158,11,0.2);">
                        <div>
                            <div class="fs-2 text-warning mb-2"><i class="fa-solid fa-gamepad"></i></div>
                            <h5 class="fw-bold text-white mb-1">บอทสถานะ FiveM Server</h5>
                            <small class="text-muted">แสดงจำนวนผู้เล่นออนไลน์ และสถานะเซิร์ฟเวอร์ FiveM แบบ Realtime</small>
                        </div>
                        <button type="button" class="btn btn-outline-warning w-100 mt-3" onclick="applyTemplate('fivem')">
                            ติดตั้งแม่แบบนี้ ➔
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     MODAL: UPGRADE STORAGE (+1 GB = ฿20)
══════════════════════════════════════════ -->
<div class="modal fade" id="upgradeStorageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content text-white" style="background:#090a12;border:1px solid #00c9ff;border-radius:20px;">
            <div class="modal-header border-secondary border-opacity-25 pb-2">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i class="fa-solid fa-hard-drive text-info"></i> ขยายพื้นที่บอท (Storage Upgrade)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 text-center">
                <p class="text-muted small">เพิ่มพื้นที่จัดเก็บข้อมูลบอทสำหรับเก็บไฟล์ขนาดใหญ่ เช่น Database, Assets, เพลง</p>
                <div class="p-3 rounded-3 mb-2" style="background:#06070a;border:1px dashed rgba(255,255,255,0.15);">
                    <div class="text-muted small">อัตราค่าบริการ</div>
                    <div class="fw-bold text-info fs-3">+1 GB (1,024 MB) = ฿20.00</div>
                </div>
                <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill mb-3 text-warning" style="background:rgba(250,204,21,0.1);border:1px solid rgba(250,204,21,0.25);font-size:0.75rem;font-weight:700;">
                    <i class="fa-solid fa-shield-halved"></i> ล็อคพื้นที่สูงสุดไม่เกิน 10 GB ต่อบัญชี
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label text-white small fw-bold">เลือกขนาดพื้นที่ที่ต้องการเพิ่ม:</label>
                    <select id="upgrade_gb_select" class="form-select bg-dark border-secondary text-white" style="height:46px;">
                        <option value="1">+1 GB (1,024 MB) — ฿20.00</option>
                        <option value="2">+2 GB (2,048 MB) — ฿40.00</option>
                        <option value="5">+5 GB (5,120 MB) — ฿100.00</option>
                        <option value="9">+9 GB (9,216 MB) — ฿180.00 (Max Cap)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-secondary border-opacity-25 pt-2">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-info fw-bold text-dark px-4" onclick="submitUpgradeStorage()">
                    ยืนยันการเพิ่มพื้นที่
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     MODAL: RENEW DURATION
══════════════════════════════════════════ -->
<div class="modal fade" id="renewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content text-white" style="background:#090a12;border:1px solid #facc15;border-radius:20px;">
            <div class="modal-header border-secondary border-opacity-25 pb-2">
                <h5 class="modal-title fw-bold text-warning d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left"></i> ต่ออายุการใช้งานบอท
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="mb-3">
                    <label class="form-label text-white small fw-bold">เลือกระยะเวลาต่ออายุ:</label>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-warning text-start py-2.5 px-3" onclick="submitRenew(7)">
                            <div class="fw-bold">+7 วัน (฿15.00)</div>
                            <small class="text-muted">ต่ออายุระยะสั้น</small>
                        </button>
                        <button type="button" class="btn btn-outline-warning text-start py-2.5 px-3" onclick="submitRenew(15)">
                            <div class="fw-bold">+15 วัน (฿30.00)</div>
                            <small class="text-muted">ต่ออายุครึ่งเดือน</small>
                        </button>
                        <button type="button" class="btn btn-warning text-dark text-start py-2.5 px-3 fw-bold" onclick="submitRenew(30)">
                            <div class="fw-bold">+30 วัน (฿50.00) ★ แนะนำ</div>
                            <small class="text-dark">ต่ออายุเต็มเดือน</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const BOT_ID = <?= $bot_id ?>;
let logPollTimer = null;

$(document).ready(function() {
    $('#upgradeStorageModal, #renewModal').appendTo('body');
    fetchBotStatus();
    fetchBotLogs();
    loadEditorFile('<?= htmlspecialchars($bot['main_file']) ?>');

    // Realtime log polling every 3.5 seconds
    logPollTimer = setInterval(fetchBotLogs, 3500);
});

function fetchBotStatus() {
    $.getJSON('system/discord_bot.php', { action: 'get_status', bot_id: BOT_ID }, function(res) {
        if (res.status === 'success') {
            const b = res.data;
            $('#diskUsedMb').text(b.disk_used_mb);
            $('#diskLimitMb').text(b.disk_limit_mb);
            $('#diskProgressBar').css('width', b.disk_percent + '%');

            if (b.is_running) {
                $('#botStateBadge').css({'border-color': '#22c55e', 'color': '#22c55e'}).text('● ONLINE (ออนไลน์ 24/7)');
                $('#btn_power_start').attr('disabled', true);
                $('#btn_power_restart, #btn_power_stop').removeAttr('disabled');
            } else {
                $('#botStateBadge').css({'border-color': '#94a3b8', 'color': '#94a3b8'}).text('○ STOPPED (ปิดอยู่)');
                $('#btn_power_start').removeAttr('disabled');
                $('#btn_power_restart, #btn_power_stop').attr('disabled', true);
            }
        }
    });
}

function fetchBotLogs(manual = false) {
    $.getJSON('system/discord_bot.php', { action: 'get_logs', bot_id: BOT_ID }, function(res) {
        if (res.status === 'success') {
            const out = $('#terminalOutput');
            out.text(res.data.logs || '[EMPTY LOGS]');
            out.scrollTop(out[0].scrollHeight);
        }
    });
}

function toggleLockOnline() {
    $.post('system/discord_bot.php', { action: 'toggle_lock_online', bot_id: BOT_ID }, function(res) {
        if (res.status === 'success') {
            Swal.fire({
                icon: res.data.lock_online ? 'success' : 'info',
                title: res.data.lock_online ? '🔒 ล็อคสตาร์ท 24/7 เรียบร้อย!' : '🔓 ปลดล็อคเรียบร้อย',
                text: res.message,
                background: '#0a0a0f',
                color: '#fff',
                timer: 2000,
                showConfirmButton: false
            });
            if (res.data.lock_online) {
                $('#btn_toggle_lock').removeClass('btn-outline-secondary').addClass('btn-outline-success');
                $('#lockIcon').removeClass('fa-lock-open text-muted').addClass('fa-lock text-success');
                $('#lockText').text('ล็อคสตาร์ท 24/7 (ACTIVE)');
                $('#btn_power_stop').attr('disabled', true).attr('title', 'โหมดล็อคการทำงานเปิดอยู่ ไม่สามารถกดปิดได้');
            } else {
                $('#btn_toggle_lock').removeClass('btn-outline-success').addClass('btn-outline-secondary');
                $('#lockIcon').removeClass('fa-lock text-success').addClass('fa-lock-open text-muted');
                $('#lockText').text('ปลดล็อคแล้ว');
                $('#btn_power_stop').removeAttr('disabled').attr('title', 'หยุดการทำงาน');
            }
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
        }
    }, 'json');
}

function sendPowerAction(action) {
    if (action === 'stop' && $('#lockIcon').hasClass('fa-lock')) {
        Swal.fire({
            icon: 'warning',
            title: '🔒 บอทถูกล็อคการทำงานไว้',
            text: 'โหมดล็อคบอทออนไลน์ 24/7 กำลังทำงานอยู่ หากต้องการปิดบอท กรุณากดปุ่ม "ล็อคสตาร์ท 24/7" เพื่อปลดล็อคก่อน',
            background: '#0a0a0f',
            color: '#fff'
        });
        return;
    }

    const btn = $(`#btn_power_${action}`);
    btn.attr('disabled', true);

    $.post('system/discord_bot.php', { action: 'power', bot_id: BOT_ID, power: action }, function(res) {
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, timer: 1500, showConfirmButton: false, background: '#0a0a0f', color: '#fff' });
            fetchBotStatus();
            fetchBotLogs();
        } else {
            btn.removeAttr('disabled');
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
        }
    }, 'json');
}

function loadFilesList() {
    $('#filesTableBody').html('<tr><td colspan="4" class="text-center text-muted py-4">กำลังโหลด...</td></tr>');
    $.getJSON('system/discord_bot.php', { action: 'list_files', bot_id: BOT_ID }, function(res) {
        if (res.status === 'success') {
            let html = '';
            if (res.data.files.length === 0) {
                html = '<tr><td colspan="4" class="text-center text-muted py-4">ไม่มีไฟล์ในโฟลเดอร์</td></tr>';
            } else {
                res.data.files.forEach(f => {
                    const icon = f.is_dir ? '<i class="fa-solid fa-folder text-warning me-2"></i>' : '<i class="fa-solid fa-file-code text-info me-2"></i>';
                    html += `<tr>
                        <td class="fw-bold">${icon} <span role="button" class="text-white" onclick="openFileInEditor('${f.name}')">${f.name}</span></td>
                        <td class="text-muted small">${f.size}</td>
                        <td class="text-muted small">${f.updated}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-dark me-1" onclick="openFileInEditor('${f.name}')" title="เปิดแก้ไข"><i class="fa-solid fa-pen-to-square text-info"></i></button>
                            <button class="btn btn-sm btn-dark" onclick="deleteFile('${f.name}')" title="ลบไฟล์"><i class="fa-solid fa-trash text-danger"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#filesTableBody').html(html);
        }
    });
}

function openFileInEditor(filename) {
    loadEditorFile(filename);
    const triggerEl = document.querySelector('#tab-editor-btn');
    bootstrap.Tab.getInstance(triggerEl) ? bootstrap.Tab.getInstance(triggerEl).show() : new bootstrap.Tab(triggerEl).show();
}

function loadEditorFile(filename) {
    $('#editing_filename').val(filename);
    $.post('system/discord_bot.php', { action: 'read_file', bot_id: BOT_ID, filename: filename }, function(res) {
        if (res.status === 'success') {
            $('#codeEditorArea').val(res.data.content);
        }
    }, 'json');
}

function saveEditorFile() {
    const filename = $('#editing_filename').val().trim();
    const content  = $('#codeEditorArea').val();
    $.post('system/discord_bot.php', { action: 'save_file', bot_id: BOT_ID, filename: filename, content: content }, function(res) {
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ', text: res.message, timer: 1200, showConfirmButton: false, background: '#0a0a0f', color: '#fff' });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
        }
    }, 'json');
}

function deleteFile(filename) {
    Swal.fire({
        title: 'ยืนยันการลบไฟล์?',
        text: `คุณต้องการลบไฟล์ "${filename}" ใช่หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก',
        background: '#0a0a0f',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('system/discord_bot.php', { action: 'delete_file', bot_id: BOT_ID, filename: filename }, function(res) {
                if (res.status === 'success') {
                    loadFilesList();
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
                }
            }, 'json');
        }
    });
}

function handleZipUpload(input) {
    if (!input.files || !input.files[0]) return;
    const formData = new FormData();
    formData.append('action', 'upload_zip');
    formData.append('bot_id', BOT_ID);
    formData.append('zip_file', input.files[0]);

    Swal.fire({ title: 'กำลังอัปโหลดและแตกไฟล์ ZIP...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }, background: '#0a0a0f', color: '#fff' });

    $.ajax({
        type: 'POST',
        url: 'system/discord_bot.php',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json'
    }).done(function(res) {
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' });
            loadFilesList();
            fetchBotStatus();
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
        }
    });
}

function loadEnvFile() {
    $.post('system/discord_bot.php', { action: 'read_file', bot_id: BOT_ID, filename: '.env' }, function(res) {
        if (res.status === 'success') {
            $('#env_raw_content').val(res.data.content);
            const m = res.data.content.match(/DISCORD_TOKEN=(.*)/);
            if (m && m[1]) $('#env_discord_token').val(m[1].trim());
        }
    }, 'json');
}

function saveEnvFile() {
    let token = $('#env_discord_token').val().trim();
    let raw = $('#env_raw_content').val();
    if (token) {
        if (raw.includes('DISCORD_TOKEN=')) {
            raw = raw.replace(/DISCORD_TOKEN=.*/, 'DISCORD_TOKEN=' + token);
        } else {
            raw = "DISCORD_TOKEN=" + token + "\n" + raw;
        }
    }
    $.post('system/discord_bot.php', { action: 'save_file', bot_id: BOT_ID, filename: '.env', content: raw }, function(res) {
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: 'บันทึกค่า .env สำเร็จ!', text: 'หากบอทกำลังทำงานอยู่ กรุณากด Restart เพื่อนำค่าใหม่ไปใช้', background: '#0a0a0f', color: '#fff' });
        }
    }, 'json');
}

function applyTemplate(tpl) {
    Swal.fire({
        title: 'ยืนยันติดตั้งแม่แบบ?',
        text: 'การติดตั้งแม่แบบจะเขียนทับไฟล์โค้ดเริ่มต้น',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยันติดตั้ง',
        cancelButtonText: 'ยกเลิก',
        background: '#0a0a0f',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('system/discord_bot.php', { action: 'apply_template', bot_id: BOT_ID, template: tpl }, function(res) {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' });
                    loadFilesList();
                }
            }, 'json');
        }
    });
}

function openUpgradeStorageModal() {
    new bootstrap.Modal(document.getElementById('upgradeStorageModal')).show();
}

function submitUpgradeStorage() {
    const gb = $('#upgrade_gb_select').val();
    $.post('system/discord_bot.php', { action: 'upgrade_storage', bot_id: BOT_ID, add_gb: gb }, function(res) {
        if (res.status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('upgradeStorageModal')).hide();
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' });
            fetchBotStatus();
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
        }
    }, 'json');
}

function openRenewModal() {
    new bootstrap.Modal(document.getElementById('renewModal')).show();
}

function submitRenew(days) {
    $.post('system/discord_bot.php', { action: 'renew', bot_id: BOT_ID, days: days }, function(res) {
        if (res.status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('renewModal')).hide();
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
        }
    }, 'json');
}
</script>
