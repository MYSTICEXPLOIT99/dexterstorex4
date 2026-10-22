<?php
// Topup Gateway & Dynamic Channel Forms (CYGNUSX STORE)

$user_point = isset($user['point']) ? number_format($user['point'], 2) : '0';

// Channel Statuses
$status_angpao   = ($config['topup_angpao_status'] ?? 'on') !== 'off';
$status_bank     = ($config['topup_bank_status'] ?? 'on') !== 'off';
$status_promptpay = ($config['topup_promptpay_status'] ?? 'on') !== 'off';
$status_truemoney = ($config['topup_truemoney_slip_status'] ?? 'on') !== 'off';

// Account Info
$bank_info = [
    'bank_name'      => !empty($config['bank_name']) ? $config['bank_name'] : 'ธนาคารกสิกรไทย',
    'account_name'   => !empty($config['contact_line']) ? $config['contact_line'] : 'ร้านค้า',
    'account_number' => !empty($config['contact_phone']) ? $config['contact_phone'] : '-'
];

$promptpay_num  = !empty($config['promptpay_number']) ? $config['promptpay_number'] : ($config['wallet'] ?? '');
$promptpay_name = !empty($config['promptpay_name']) ? $config['promptpay_name'] : ($config['contact_line'] ?? 'ร้านค้า');

$truemoney_num  = !empty($config['truemoney_number']) ? $config['truemoney_number'] : ($config['wallet'] ?? '');
$truemoney_name = !empty($config['truemoney_name']) ? $config['truemoney_name'] : ($config['contact_line'] ?? 'ร้านค้า');

$init_method = $_GET['method'] ?? '';
?>

<!-- Header -->
<div class="mb-4 text-center" data-aos="fade-down">
    <div class="section-eyebrow">// ระบบเติมเงิน & ชำระเงินอัตโนมัติ 24 ชม.</div>
    <h1 class="section-main-heading" style="font-size: 2.3rem;">เติมเงิน // <span style="color:#facc15;">เข้าสู่ระบบ</span></h1>
    <p class="section-subtext mb-3">เลือกช่องทางชำระเงินที่ต้องการ — ระบบประมวลผลและปรับยอดอัตโนมัติ 24/7</p>
    
    <!-- Balance HUD Badge -->
    <div class="wheel-pill-badge d-inline-flex align-items-center gap-3 mb-4" style="background:#090a12;border:1.5px solid rgba(250,204,21,0.3);padding:8px 24px;border-radius:999px;box-shadow:0 0 25px rgba(250,204,21,0.15);">
        <span style="font-family:var(--font-cyber);font-size:0.75rem;color:#94a3b8;letter-spacing:1px;"><i class="fa-solid fa-signal text-success me-1"></i> [ ระบบออนไลน์ ]</span>
        <span style="color:#ffffff;font-size:0.9rem;">ยอดคงเหลือ:</span>
        <span style="color:#facc15;font-family:var(--font-cyber);font-weight:900;font-size:1.15rem;letter-spacing:1px;text-shadow:0 0 10px rgba(250,204,21,0.5);">฿ <?= $user_point ?></span>
    </div>
</div>

<!-- ══════════════════════════════════════════
     VIEW 1: METHOD SELECTION
══════════════════════════════════════════ -->
<div id="viewMethodSelect" style="<?= !empty($init_method) ? 'display:none;' : 'display:block;' ?>">
    <div class="row g-3 justify-content-center" data-aos="fade-up" style="max-width: 1040px; margin: 0 auto;">
        
        <?php if ($status_bank): ?>
        <!-- Channel 1: ยืนยันสลิป ธนาคาร (Cyan Cyber Matrix) -->
        <div class="col-12 col-md-6">
            <div class="topup-box-card" onclick="switchTopupView('bank')">
                <div>
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="topup-box-icon-slip">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <span class="badge" style="background:rgba(56,189,248,0.1);border:1px solid rgba(56,189,248,0.3);color:#38bdf8;font-family:var(--font-cyber);font-size:0.7rem;letter-spacing:0.5px;">
                            <i class="fa-solid fa-bolt me-1"></i> ตรวจสอบทันที
                        </span>
                    </div>
                    <div class="topup-box-tag">[ ช่องทาง: โอนเงินผ่านธนาคาร ]</div>
                    <div class="topup-box-title">โอนเงินธนาคาร</div>
                    <div class="topup-box-sub">แนบสลิปตรวจสอบและเติมเครดิตอัตโนมัติ</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-secondary border-opacity-10">
                    <span class="topup-fee-badge-green"><i class="fa-solid fa-circle-check"></i> ค่าธรรมเนียม <span style="background:rgba(74,222,128,0.15);padding:2px 8px;border-radius:6px;border:1px solid rgba(74,222,128,0.3);">0.00%</span></span>
                    <span class="text-warning small fw-bold" style="font-family:var(--font-cyber);">เลือก ➔</span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($status_promptpay): ?>
        <!-- Channel 2: สแกนพร้อมเพย์ QR Code (Blue Cyber Matrix) -->
        <div class="col-12 col-md-6">
            <div class="topup-box-card" onclick="switchTopupView('promptpay')">
                <div>
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="topup-box-icon-slip" style="background:linear-gradient(135deg, rgba(14,165,233,0.2) 0%, rgba(3,105,161,0.4) 100%); color:#38bdf8; border-color:rgba(14,165,233,0.5);">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <span class="badge" style="background:rgba(14,165,233,0.1);border:1px solid rgba(14,165,233,0.3);color:#38bdf8;font-family:var(--font-cyber);font-size:0.7rem;letter-spacing:0.5px;">
                            <i class="fa-solid fa-qrcode me-1"></i> สแกน QR
                        </span>
                    </div>
                    <div class="topup-box-tag">[ ช่องทาง: สแกนพร้อมเพย์ ]</div>
                    <div class="topup-box-title">สแกนพร้อมเพย์</div>
                    <div class="topup-box-sub">QR Code PromptPay แนบสลิปตรวจสอบสด</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-secondary border-opacity-10">
                    <span class="topup-fee-badge-green"><i class="fa-solid fa-circle-check"></i> ค่าธรรมเนียม <span style="background:rgba(74,222,128,0.15);padding:2px 8px;border-radius:6px;border:1px solid rgba(74,222,128,0.3);">0.00%</span></span>
                    <span class="text-warning small fw-bold" style="font-family:var(--font-cyber);">เลือก ➔</span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($status_truemoney): ?>
        <!-- Channel 3: โอน TrueMoney Wallet แนบสลิป (Gold Cyber Matrix) -->
        <div class="col-12 col-md-6">
            <div class="topup-box-card" onclick="switchTopupView('truemoney_slip')">
                <div>
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="topup-box-icon-angpao" style="background:linear-gradient(135deg, rgba(245,158,11,0.2) 0%, rgba(217,119,6,0.4) 100%); color:#fbbf24; border-color:rgba(245,158,11,0.5); box-shadow:0 4px 18px rgba(245,158,11,0.3);">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <span class="badge" style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);color:#fbbf24;font-family:var(--font-cyber);font-size:0.7rem;letter-spacing:0.5px;">
                            <i class="fa-solid fa-wallet me-1"></i> วอลเล็ทสลิป
                        </span>
                    </div>
                    <div class="topup-box-tag">[ ช่องทาง: ทรูมันนี่วอลเล็ท ]</div>
                    <div class="topup-box-title">โอน TrueMoney Wallet</div>
                    <div class="topup-box-sub">โอนผ่านเบอร์วอลเล็ท แนบสลิปตรวจสอบทันที</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-secondary border-opacity-10">
                    <span class="topup-fee-badge-green"><i class="fa-solid fa-circle-check"></i> ค่าธรรมเนียม <span style="background:rgba(74,222,128,0.15);padding:2px 8px;border-radius:6px;border:1px solid rgba(74,222,128,0.3);">0.00%</span></span>
                    <span class="text-warning small fw-bold" style="font-family:var(--font-cyber);">เลือก ➔</span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($status_angpao): ?>
        <!-- Channel 4: ซองอั่งเปา TrueMoney (Ruby Red Cyber Matrix) -->
        <div class="col-12 col-md-6">
            <div class="topup-box-card" onclick="switchTopupView('angpao')">
                <div>
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="topup-box-icon-angpao">
                            <i class="fa-solid fa-gift"></i>
                        </div>
                        <span class="badge" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#f87171;font-family:var(--font-cyber);font-size:0.7rem;letter-spacing:0.5px;">
                            <i class="fa-solid fa-gift me-1"></i> ซองของขวัญ
                        </span>
                    </div>
                    <div class="topup-box-tag">[ ช่องทาง: ซองอั่งเปาทรูมันนี่ ]</div>
                    <div class="topup-box-title">ซองอั่งเปา TrueMoney</div>
                    <div class="topup-box-sub">วางลิงก์ซองของขวัญ รับเครดิตเข้าทันที 24 ชม.</div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-secondary border-opacity-10">
                    <span class="topup-fee-badge-red"><i class="fa-solid fa-triangle-exclamation"></i> ค่าธรรมเนียม <span style="background:rgba(239,68,68,0.15);padding:2px 8px;border-radius:6px;border:1px solid rgba(239,68,68,0.3);">2.9%</span> สูงสุด 20฿</span>
                    <span class="text-warning small fw-bold" style="font-family:var(--font-cyber);">เลือก ➔</span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!$status_angpao && !$status_bank && !$status_promptpay && !$status_truemoney): ?>
        <div class="col-12">
            <div class="p-5 text-center rounded-4" style="background:#080a12; border:1px dashed rgba(250,204,21,0.3);">
                <i class="fa-solid fa-lock fs-1 text-warning mb-3"></i>
                <h4 class="text-white fw-bold" style="font-family:var(--font-cyber);">[ แจ้งเตือน: ระบบเติมเงินปิดปรับปรุงชั่วคราว ]</h4>
                <p class="text-muted small mb-0">ระบบเติมเงินปิดปรับปรุงชั่วคราว แอดมินสามารถเปิดช่องทางได้ในระบบหลังบ้าน</p>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($status_angpao): ?>
        <!-- Quick How-To Angpao Banner -->
        <div class="col-12 mt-3">
            <div class="p-3 rounded-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background:#06070c;border:1px solid rgba(239,68,68,0.3);box-shadow:0 4px 20px rgba(0,0,0,0.6);" onclick="openFullHowToImage()" role="button">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:12px;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.4);display:flex;align-items:center;justify-content:center;color:#ef4444;font-size:1.3rem;">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <div>
                        <div class="text-white fw-bold" style="font-size:0.95rem;">ยังไม่เคยสร้างซองของขวัญ TrueMoney?</div>
                        <div class="text-muted small" style="font-size:0.8rem;">คลิกเพื่อดูวิธีสร้างซองและรับเครดิตทีละขั้นตอนได้ง่ายๆ</div>
                    </div>
                </div>
                <span class="btn btn-sm btn-outline-danger px-3 py-1.5 rounded-pill text-light" style="font-family:var(--font-cyber);font-size:0.75rem;">
                    <i class="fa-solid fa-book-open me-1"></i> ดูวิธีสร้างซอง ➔
                </span>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ══════════════════════════════════════════
     VIEW 2: ซองอั่งเปา TRUEMONEY (HACKER MECH HUD)
══════════════════════════════════════════ -->
<div id="viewAngpaoForm" style="<?= ($init_method == 'angpao') ? 'display:block;' : 'display:none;' ?>" data-aos="fade-up">
    <div class="cyber-topup-inner-hud p-4 p-md-5 mx-auto" style="max-width:920px;">
        <!-- Top Row -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <button type="button" class="cyber-back-btn" onclick="switchTopupView('select')">
                <i class="fa-solid fa-arrow-left me-1"></i> [ ย้อนกลับไปเลือกช่องทาง ]
            </button>
            <div class="text-end">
                <span class="badge" style="background:rgba(239,68,68,0.15);border:1.5px solid rgba(239,68,68,0.4);color:#fca5a5;font-family:var(--font-cyber);letter-spacing:1px;font-size:0.72rem;">
                    <i class="fa-solid fa-gift me-1"></i> ช่องทาง: ซองของขวัญทรูมันนี่
                </span>
            </div>
        </div>

        <div class="mb-3">
            <div class="auth-header-eyebrow" style="color:#ef4444;">// ระบบเติมเงินด้วยซองของขวัญอัตโนมัติ</div>
            <h3 class="auth-header-title mb-1" style="font-size:1.8rem;">ซองของขวัญ // <span style="color:#ef4444;">ทรูมันนี่วอลเล็ท</span></h3>
            <p class="auth-header-sub mb-3">วางลิงก์ซองของขวัญจาก TrueMoney Wallet เพื่อรับเครดิตอัตโนมัติ 24 ชม.</p>
        </div>

        <div class="p-3 p-md-4 rounded-4 mb-4" style="background:#06070c;border:1.5px solid rgba(239,68,68,0.3);">
            <div class="mb-3">
                <label class="cyber-field-label">
                    <span>// วางลิงก์ซองของขวัญ (TRUEMONEY URL)</span>
                    <span style="color:#fca5a5;">ระบบประมวลผลอัตโนมัติ</span>
                </label>
                <div class="auth-input-inner">
                    <i class="fa-solid fa-link auth-input-icon" style="color:#ef4444;"></i>
                    <input type="text" id="angpao_link" class="auth-custom-input py-3" placeholder="https://gift.truemoney.com/campaign/?v=xxxxxxxxxxxxxxxxxx" autocomplete="off">
                </div>
            </div>
            <p class="text-muted mb-0" style="font-size:0.76rem;line-height:1.6;font-family:var(--font-cyber);">
                <i class="fa-solid fa-circle-info text-warning me-1"></i> [ คำแนะนำ: คัดลอกลิงก์ซองของขวัญโดยตรงจากแอป TrueMoney Wallet — ห้ามแก้ไขลิงก์ ]
            </p>
        </div>

        <button type="button" id="btn_submit_angpao" onclick="submitAngpao()" class="auth-submit-btn" style="background:linear-gradient(135deg,#ffffff 0%,#fca5a5 50%,#ef4444 100%);color:#000;box-shadow:0 0 25px rgba(239,68,68,0.5);">
            <i class="fa-solid fa-bolt-lightning me-2"></i> [ ยืนยันการเติมเงิน ]
        </button>

        <!-- Tutorial Guide Box (How to create TrueMoney Angpao) -->
        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background:rgba(239,68,68,0.15);color:#fca5a5;border:1px solid rgba(239,68,68,0.4);font-family:var(--font-cyber);font-size:0.75rem;padding:4px 10px;border-radius:6px;">
                        <i class="fa-solid fa-book-open me-1"></i> คู่มือการเติมเงิน
                    </span>
                    <span class="text-white fw-bold" style="font-size: 0.95rem;">วิธีสร้างซองของขวัญ TrueMoney Wallet</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger text-light px-3 py-1 rounded-pill" onclick="openFullHowToImage()" style="font-size:0.75rem;font-family:var(--font-cyber);">
                    <i class="fa-solid fa-magnifying-glass-plus me-1"></i> ดูรูปภาพขนาดเต็ม
                </button>
            </div>
            
            <div class="tutorial-img-wrap text-center p-2 rounded-4" style="background:#040508;border:1.5px solid rgba(239,68,68,0.3);box-shadow:0 10px 30px rgba(0,0,0,0.8), 0 0 20px rgba(239,68,68,0.15);cursor:pointer;" onclick="openFullHowToImage()">
                <img src="assets/img/how_to_angpao.jpg" alt="วิธีการสร้างซองของขวัญ TrueMoney Wallet" class="img-fluid rounded-3" style="max-height: 540px; width: 100%; object-fit: contain;">
                <div class="text-muted small mt-2" style="font-size:0.75rem;font-family:var(--font-cyber);">
                    <i class="fa-solid fa-hand-pointer text-warning me-1"></i> [ คลิกที่รูปภาพเพื่อขยายดูขนาดเต็ม ]
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     VIEW 3: โอนเงินธนาคาร (HACKER MECH HUD)
══════════════════════════════════════════ -->
<div id="viewBankForm" style="<?= ($init_method == 'bank' || $init_method == 'slip') ? 'display:block;' : 'display:none;' ?>" data-aos="fade-up">
    <div class="cyber-topup-inner-hud p-4 p-md-5 mx-auto" style="max-width:920px;">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <button type="button" class="cyber-back-btn" onclick="switchTopupView('select')">
                <i class="fa-solid fa-arrow-left me-1"></i> [ ย้อนกลับไปเลือกช่องทาง ]
            </button>
            <div class="text-end">
                <span class="badge" style="background:rgba(56,189,248,0.12);border:1.5px solid rgba(56,189,248,0.35);color:#38bdf8;font-family:var(--font-cyber);letter-spacing:1px;font-size:0.72rem;">
                    <i class="fa-solid fa-shield-halved me-1"></i> ช่องทาง: โอนเงินผ่านธนาคาร
                </span>
            </div>
        </div>

        <div class="mb-3">
            <div class="auth-header-eyebrow">// ระบบตรวจสอบสลิปธนาคารอัตโนมัติ</div>
            <h3 class="auth-header-title mb-1" style="font-size:1.8rem;">โอนผ่านธนาคาร // <span style="color:#facc15;">แนบสลิป</span></h3>
            <p class="auth-header-sub mb-3">โอนเงินเข้าบัญชีธนาคารด้านล่าง แล้วแนบสลิปเพื่อยืนยันยอดอัตโนมัติด้วยระบบ AI OCR</p>
        </div>

        <!-- Bank Account Card Box (Cyber Mech Frame) -->
        <div class="cyber-topup-acc-box p-3 p-md-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg, rgba(16,185,129,0.25) 0%, rgba(5,150,105,0.45) 100%);border:1.5px solid #10b981;display:flex;align-items:center;justify-content:center;color:#34d399;font-size:1.6rem;box-shadow:0 0 20px rgba(16,185,129,0.35);">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div>
                        <span class="badge" style="background:rgba(250,204,21,0.1);color:#facc15;border:1px solid rgba(250,204,21,0.3);font-family:var(--font-cyber);font-size:0.68rem;padding:3px 8px;border-radius:4px;">
                            บัญชีร้านค้า: ตรวจสอบแล้ว
                        </span>
                        <div class="fw-bold text-white mt-1" style="font-family:var(--font-cyber);font-size:1.15rem;letter-spacing:0.5px;"><?= htmlspecialchars($bank_info['bank_name']) ?></div>
                    </div>
                </div>
                <span class="badge" style="background:#090a12;border:1px solid rgba(74,222,128,0.4);color:#4ade80;font-family:var(--font-cyber);font-size:0.72rem;letter-spacing:1px;">
                    <i class="fa-solid fa-circle-check me-1"></i> [ ระบบตรวจสอบพร้อมใช้งาน ]
                </span>
            </div>

            <!-- ชื่อบัญชี -->
            <div class="cyber-acc-field-row mb-2.5">
                <div class="cyber-field-label">
                    <span>// ชื่อบัญชีผู้รับโอน</span>
                    <span style="color:#facc15;"><i class="fa-solid fa-user-check me-1"></i> บัญชีร้านค้า</span>
                </div>
                <div class="fw-bold text-white" style="font-size:1.12rem;letter-spacing:0.5px;"><?= htmlspecialchars($bank_info['account_name']) ?></div>
            </div>

            <!-- เลขบัญชี -->
            <div class="cyber-acc-field-row d-flex align-items-center justify-content-between">
                <div>
                    <div class="cyber-field-label">
                        <span>// เลขบัญชีธนาคาร</span>
                        <span style="color:#4ade80;">พร้อมคัดลอก</span>
                    </div>
                    <div class="cyber-acc-hash" id="bankAccNum"><?= htmlspecialchars($bank_info['account_number']) ?></div>
                </div>
                <button type="button" class="cyber-copy-btn" onclick="copyText('bankAccNum')">
                    <i class="fa-regular fa-copy me-1"></i> คัดลอกเลขบัญชี
                </button>
            </div>
        </div>

        <!-- Slip Upload (Cyber Scanner Box) -->
        <div class="mb-4">
            <input type="file" id="bank_slip_file" accept="image/*" style="display:none;" onchange="handleSlipFile(this, 'bank')">
            <div class="cyber-slip-dropzone" onclick="document.getElementById('bank_slip_file').click()">
                <div style="width:54px;height:54px;border-radius:14px;background:rgba(250,204,21,0.12);border:1.5px solid rgba(250,204,21,0.4);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:#facc15;font-size:1.5rem;box-shadow:0 0 20px rgba(250,204,21,0.25);">
                    <i class="fa-solid fa-expand"></i>
                </div>
                <div class="fw-bold text-white mb-1" id="bankDropzoneText" style="font-family:var(--font-cyber);font-size:1.05rem;letter-spacing:1px;">
                    คลิกเพื่อเลือกรูปภาพสลิป หรือลากไฟล์มาวางที่นี่
                </div>
                <small class="text-muted" style="font-family:var(--font-cyber);font-size:0.75rem;">[ ระบบ AI สแกนสลิปอัตโนมัติ // เติมเครดิตทันที 24 ชม. ]</small>
                <div id="bankSlipPreviewWrap" class="mt-3" style="display:none;">
                    <img id="bankSlipPreviewImg" src="" style="max-height:170px;border-radius:10px;border:2px solid #facc15;box-shadow:0 0 25px rgba(250,204,21,0.4);">
                </div>
            </div>
        </div>

        <button type="button" id="btn_submit_bank_slip" onclick="submitSlip('bank_slip_file', '#btn_submit_bank_slip')" class="auth-submit-btn">
            <i class="fa-solid fa-bolt-lightning me-2"></i> [ ตรวจสอบสลิปและเติมเงิน ]
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════════
     VIEW 4: สแกนพร้อมเพย์ QR CODE (HACKER MECH HUD)
══════════════════════════════════════════ -->
<div id="viewPromptpayForm" style="<?= ($init_method == 'promptpay') ? 'display:block;' : 'display:none;' ?>" data-aos="fade-up">
    <div class="cyber-topup-inner-hud p-4 p-md-5 mx-auto" style="max-width:920px;">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <button type="button" class="cyber-back-btn" onclick="switchTopupView('select')">
                <i class="fa-solid fa-arrow-left me-1"></i> [ ย้อนกลับไปเลือกช่องทาง ]
            </button>
            <div class="text-end">
                <span class="badge" style="background:rgba(14,165,233,0.12);border:1.5px solid rgba(14,165,233,0.35);color:#38bdf8;font-family:var(--font-cyber);letter-spacing:1px;font-size:0.72rem;">
                    <i class="fa-solid fa-qrcode me-1"></i> ช่องทาง: สแกนพร้อมเพย์
                </span>
            </div>
        </div>

        <div class="mb-3">
            <div class="auth-header-eyebrow">// ระบบสร้าง QR Code ชำระเงินอัตโนมัติ</div>
            <h3 class="auth-header-title mb-1" style="font-size:1.8rem;">พร้อมเพย์ // <span style="color:#00c9ff;">สแกนจ่าย QR</span></h3>
            <p class="auth-header-sub mb-3">ระบุจำนวนเงินเพื่อสร้าง QR Code สแกนจ่ายแล้วแนบสลิปเพื่อเติมเครดิต</p>
        </div>

        <!-- 1. ใส่จำนวนเงิน -->
        <div class="cyber-topup-acc-box p-3 p-md-4 mb-4">
            <label class="cyber-field-label mb-2">
                <span><i class="fa-solid fa-coins text-warning me-1.5"></i> [01] // ระบุจำนวนเงินที่ต้องการเติม</span>
                <span class="badge bg-dark border border-secondary text-info">ขั้นต่ำ: ฿1.00</span>
            </label>
            <div class="input-group mb-3">
                <span class="input-group-text bg-dark border-secondary text-info fw-bold fs-4 px-3" style="background:#0c0d14 !important; border-color:rgba(0,201,255,0.4) !important;">฿</span>
                <input type="number" id="promptpayAmountInput" class="form-control bg-dark text-white fw-bold text-center fs-3" 
                       placeholder="0.00" min="1" step="any" oninput="updatePromptPayQR()" style="background:#0c0d14 !important; border-color:rgba(0,201,255,0.4) !important; font-family:'Space Mono',monospace; letter-spacing:1px; color:#00c9ff !important;">
                <button type="button" class="btn btn-info fw-bold px-4 text-dark" onclick="updatePromptPayQR()" style="font-family:var(--font-cyber);letter-spacing:1px;">
                    <i class="fa-solid fa-qrcode me-1"></i> สร้าง QR Code
                </button>
            </div>

            <!-- Quick Amount Presets -->
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-dark border border-secondary rounded-pill px-3 py-1 text-light fw-bold" onclick="setPromptPayPreset(50)">+50 ฿</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary rounded-pill px-3 py-1 text-light fw-bold" onclick="setPromptPayPreset(100)">+100 ฿</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary rounded-pill px-3 py-1 text-light fw-bold" onclick="setPromptPayPreset(300)">+300 ฿</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary rounded-pill px-3 py-1 text-light fw-bold" onclick="setPromptPayPreset(500)">+500 ฿</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary rounded-pill px-3 py-1 text-light fw-bold" onclick="setPromptPayPreset(1000)">+1,000 ฿</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary rounded-pill px-3 py-1 text-light fw-bold" onclick="setPromptPayPreset(2000)">+2,000 ฿</button>
            </div>
        </div>

        <!-- 2. PromptPay Dynamic QR Box (Shown when amount is specified) -->
        <div id="promptpayQrBox" class="p-4 rounded-4 mb-4 text-center" style="background:#06070c;border:1.5px solid rgba(0,201,255,0.4); display:none; box-shadow:0 0 35px rgba(0,201,255,0.2);">
            <div class="mb-2">
                <span class="badge px-3 py-1.5 rounded-pill fs-6 fw-bold" style="background:rgba(0,201,255,0.15); border:1.5px solid #00c9ff; color:#00c9ff; font-family:var(--font-cyber);">
                    <i class="fa-solid fa-receipt me-1"></i> ยอดที่ต้องชำระ: <span id="promptpayQrAmountDisplay" class="fw-bold fs-5 text-white ms-1">฿ 0.00</span>
                </span>
            </div>

            <!-- QR Container -->
            <div class="p-3 bg-white d-inline-block rounded-4 shadow-lg my-3 cyber-qr-scanner-box">
                <img id="promptpayQrImg" src="" alt="PromptPay QR Code" class="img-fluid" style="width:210px;height:210px;object-fit:contain;">
            </div>

            <div class="cyber-acc-field-row mb-2 mx-auto" style="max-width:520px;">
                <div class="cyber-field-label">
                    <span>// ชื่อบัญชีพร้อมเพย์</span>
                    <span style="color:#00c9ff;">ผู้รับเงิน</span>
                </div>
                <div class="fw-bold text-white" style="font-size:1.05rem;"><?= htmlspecialchars($promptpay_name) ?></div>
            </div>

            <div class="cyber-acc-field-row d-flex align-items-center justify-content-between mx-auto" style="max-width:520px;">
                <div>
                    <div class="cyber-field-label">
                        <span>// เบอร์ / รหัสพร้อมเพย์</span>
                        <span style="color:#4ade80;">พร้อมคัดลอก</span>
                    </div>
                    <div class="cyber-acc-hash" style="color:#00c9ff;text-shadow:0 0 12px rgba(0,201,255,0.6);" id="promptpayAccNum"><?= htmlspecialchars($promptpay_num) ?></div>
                </div>
                <button type="button" class="cyber-copy-btn" style="border-color:#00c9ff;color:#00c9ff;" onclick="copyText('promptpayAccNum')">
                    <i class="fa-regular fa-copy me-1"></i> คัดลอกรหัสพร้อมเพย์
                </button>
            </div>
        </div>

        <!-- 3. Slip Upload -->
        <div class="mb-4">
            <input type="file" id="promptpay_slip_file" accept="image/*" style="display:none;" onchange="handleSlipFile(this, 'promptpay')">
            <div class="cyber-slip-dropzone" onclick="document.getElementById('promptpay_slip_file').click()">
                <div style="width:54px;height:54px;border-radius:14px;background:rgba(0,201,255,0.12);border:1.5px solid rgba(0,201,255,0.4);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:#00c9ff;font-size:1.5rem;box-shadow:0 0 20px rgba(0,201,255,0.25);">
                    <i class="fa-solid fa-qrcode fs-4"></i>
                </div>
                <div class="fw-bold text-white mb-1" id="promptpayDropzoneText" style="font-family:var(--font-cyber);font-size:1.05rem;letter-spacing:1px;">
                    คลิกเพื่อเลือกรูปภาพสลิป หรือลากไฟล์มาวางที่นี่
                </div>
                <small class="text-muted" style="font-family:var(--font-cyber);font-size:0.75rem;">[ ระบบ AI สแกนสลิปอัตโนมัติ // เติมเครดิตทันที 24 ชม. ]</small>
                <div id="promptpaySlipPreviewWrap" class="mt-3" style="display:none;">
                    <img id="promptpaySlipPreviewImg" src="" style="max-height:170px;border-radius:10px;border:2px solid #00c9ff;box-shadow:0 0 25px rgba(0,201,255,0.4);">
                </div>
            </div>
        </div>

        <button type="button" id="btn_submit_promptpay_slip" onclick="submitSlip('promptpay_slip_file', '#btn_submit_promptpay_slip')" class="auth-submit-btn" style="background:linear-gradient(135deg,#ffffff 0%,#e0f2fe 50%,#00c9ff 100%);color:#000;box-shadow:0 0 25px rgba(0,201,255,0.5);">
            <i class="fa-solid fa-bolt-lightning me-2"></i> [ ตรวจสอบสลิปและเติมเงิน ]
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════════
     VIEW 5: โอน TRUEMONEY WALLET (HACKER MECH HUD)
══════════════════════════════════════════ -->
<div id="viewTruemoneySlipForm" style="<?= ($init_method == 'truemoney_slip') ? 'display:block;' : 'display:none;' ?>" data-aos="fade-up">
    <div class="cyber-topup-inner-hud p-4 p-md-5 mx-auto" style="max-width:920px;">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <button type="button" class="cyber-back-btn" onclick="switchTopupView('select')">
                <i class="fa-solid fa-arrow-left me-1"></i> [ ย้อนกลับไปเลือกช่องทาง ]
            </button>
            <div class="text-end">
                <span class="badge" style="background:rgba(245,158,11,0.12);border:1.5px solid rgba(245,158,11,0.35);color:#fbbf24;font-family:var(--font-cyber);letter-spacing:1px;font-size:0.72rem;">
                    <i class="fa-solid fa-wallet me-1"></i> ช่องทาง: ทรูมันนี่วอลเล็ท
                </span>
            </div>
        </div>

        <div class="mb-3">
            <div class="auth-header-eyebrow" style="color:#f59e0b;">// ระบบโอนวอลเล็ท & แนบสลิปอัตโนมัติ</div>
            <h3 class="auth-header-title mb-1" style="font-size:1.8rem;">ทรูมันนี่วอลเล็ท // <span style="color:#f59e0b;">แนบสลิป</span></h3>
            <p class="auth-header-sub mb-3">โอนผ่านแอป TrueMoney Wallet ตามเบอร์ด้านล่าง แล้วแนบสลิปเพื่อเติมเครดิต</p>
        </div>

        <!-- TrueMoney Account Info Box -->
        <div class="cyber-topup-acc-box p-3 p-md-4 mb-4" style="border-color:rgba(245,158,11,0.35) !important;">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg, rgba(245,158,11,0.25) 0%, rgba(217,119,6,0.45) 100%);border:1.5px solid #f59e0b;display:flex;align-items:center;justify-content:center;color:#fbbf24;font-size:1.6rem;box-shadow:0 0 20px rgba(245,158,11,0.35);">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div>
                        <span class="badge" style="background:rgba(245,158,11,0.1);color:#fbbf24;border:1px solid rgba(245,158,11,0.3);font-family:var(--font-cyber);font-size:0.68rem;padding:3px 8px;border-radius:4px;">
                            บัญชีวอลเล็ท: ตรวจสอบแล้ว
                        </span>
                        <div class="fw-bold text-white mt-1" style="font-family:var(--font-cyber);font-size:1.15rem;letter-spacing:0.5px;">TrueMoney Wallet</div>
                    </div>
                </div>
                <span class="badge" style="background:#090a12;border:1px solid rgba(245,158,11,0.4);color:#fbbf24;font-family:var(--font-cyber);font-size:0.72rem;letter-spacing:1px;">
                    <i class="fa-solid fa-circle-check me-1"></i> [ ระบบวอลเล็ทออนไลน์ ]
                </span>
            </div>

            <!-- ชื่อบัญชี -->
            <div class="cyber-acc-field-row mb-2.5">
                <div class="cyber-field-label">
                    <span>// ชื่อบัญชีผู้รับโอน</span>
                    <span style="color:#fbbf24;"><i class="fa-solid fa-user-check me-1"></i> เจ้าของวอลเล็ท</span>
                </div>
                <div class="fw-bold text-white" style="font-size:1.12rem;letter-spacing:0.5px;"><?= htmlspecialchars($truemoney_name) ?></div>
            </div>

            <!-- เบอร์โทร -->
            <div class="cyber-acc-field-row d-flex align-items-center justify-content-between">
                <div>
                    <div class="cyber-field-label">
                        <span>// เบอร์โทรศัพท์ TrueMoney Wallet</span>
                        <span style="color:#4ade80;">พร้อมคัดลอก</span>
                    </div>
                    <div class="cyber-acc-hash" style="color:#fbbf24;text-shadow:0 0 12px rgba(245,158,11,0.5);" id="truemoneyAccNum"><?= htmlspecialchars($truemoney_num) ?></div>
                </div>
                <button type="button" class="cyber-copy-btn" style="border-color:#f59e0b;color:#fbbf24;" onclick="copyText('truemoneyAccNum')">
                    <i class="fa-regular fa-copy me-1"></i> คัดลอกเบอร์วอลเล็ท
                </button>
            </div>
        </div>

        <!-- Slip Upload -->
        <div class="mb-4">
            <input type="file" id="truemoney_slip_file" accept="image/*" style="display:none;" onchange="handleSlipFile(this, 'truemoney')">
            <div class="cyber-slip-dropzone" style="border-color:rgba(245,158,11,0.4) !important;" onclick="document.getElementById('truemoney_slip_file').click()">
                <div style="width:54px;height:54px;border-radius:14px;background:rgba(245,158,11,0.12);border:1.5px solid rgba(245,158,11,0.4);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:#fbbf24;font-size:1.5rem;box-shadow:0 0 20px rgba(245,158,11,0.25);">
                    <i class="fa-solid fa-receipt fs-4"></i>
                </div>
                <div class="fw-bold text-white mb-1" id="truemoneyDropzoneText" style="font-family:var(--font-cyber);font-size:1.05rem;letter-spacing:1px;">
                    คลิกเพื่อเลือกรูปภาพสลิป หรือลากไฟล์มาวางที่นี่
                </div>
                <small class="text-muted" style="font-family:var(--font-cyber);font-size:0.75rem;">[ ระบบ AI สแกนสลิปอัตโนมัติ // เติมเครดิตทันที 24 ชม. ]</small>
                <div id="truemoneySlipPreviewWrap" class="mt-3" style="display:none;">
                    <img id="truemoneySlipPreviewImg" src="" style="max-height:170px;border-radius:10px;border:2px solid #f59e0b;box-shadow:0 0 25px rgba(245,158,11,0.4);">
                </div>
            </div>
        </div>

        <button type="button" id="btn_submit_truemoney_slip" onclick="submitSlip('truemoney_slip_file', '#btn_submit_truemoney_slip')" class="auth-submit-btn" style="background:linear-gradient(135deg,#ffffff 0%,#fef3c7 50%,#f59e0b 100%);color:#000;box-shadow:0 0 25px rgba(245,158,11,0.5);">
            <i class="fa-solid fa-bolt-lightning me-2"></i> [ ตรวจสอบสลิปและเติมเงิน ]
        </button>
    </div>
</div>

<script>
function switchTopupView(view) {
    $('#viewMethodSelect').hide();
    $('#viewAngpaoForm').hide();
    $('#viewBankForm').hide();
    $('#viewPromptpayForm').hide();
    $('#viewTruemoneySlipForm').hide();

    if (view === 'angpao') {
        $('#viewAngpaoForm').fadeIn(200);
    } else if (view === 'bank' || view === 'slip') {
        $('#viewBankForm').fadeIn(200);
    } else if (view === 'promptpay') {
        $('#viewPromptpayForm').fadeIn(200);
    } else if (view === 'truemoney_slip') {
        $('#viewTruemoneySlipForm').fadeIn(200);
    } else {
        $('#viewMethodSelect').fadeIn(200);
    }
}

function copyText(elementId) {
    var copyText = document.getElementById(elementId).innerText.trim();
    navigator.clipboard.writeText(copyText);
    Swal.fire({
        icon: 'success',
        title: 'คัดลอกแล้ว',
        text: copyText,
        timer: 1200,
        showConfirmButton: false,
        background: '#0a0a0f',
        color: '#fff'
    });
}

function handleSlipFile(input, prefix) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        $(`#${prefix}DropzoneText`).text(file.name);
        const reader = new FileReader();
        reader.onload = function(e) {
            $(`#${prefix}SlipPreviewImg`).attr('src', e.target.result);
            $(`#${prefix}SlipPreviewWrap`).show();
        }
        reader.readAsDataURL(file);
    }
}

function submitAngpao() {
    const link = $('#angpao_link').val().trim();
    if (!link) {
        Swal.fire({ icon: 'warning', title: 'แจ้งเตือน', text: 'กรุณากรอกลิงก์ซองอั่งเปา TrueMoney', background: '#0a0a0f', color: '#fff' });
        return;
    }

    const btn = $('#btn_submit_angpao');
    btn.attr('disabled', 'disabled').html('<i class="fa-solid fa-spinner fa-spin me-2"></i> กำลังตรวจสอบซอง...');

    const formData = new FormData();
    formData.append('link', link);

    $.ajax({
        type: 'POST',
        url: 'system/topup.php',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json'
    }).done(function(res) {
        btn.removeAttr('disabled').html('<i class="fa-solid fa-bolt-lightning me-2"></i> [ ยืนยันการเติมเงิน ]');
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message || 'ซองไม่ถูกต้องหรือหมดอายุ', background: '#0a0a0f', color: '#fff' });
        }
    }).fail(function(xhr) {
        btn.removeAttr('disabled').html('<i class="fa-solid fa-bolt-lightning me-2"></i> [ ยืนยันการเติมเงิน ]');
        let errMsg = 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errMsg = xhr.responseJSON.message;
        } else if (xhr.responseText) {
            try {
                const parsed = JSON.parse(xhr.responseText);
                if (parsed.message) errMsg = parsed.message;
            } catch(e) {}
        }
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: errMsg, background: '#0a0a0f', color: '#fff' });
    });
}

function submitSlip(fileInputId, btnSelector) {
    const fileInput = document.getElementById(fileInputId);
    if (!fileInput || !fileInput.files || !fileInput.files[0]) {
        Swal.fire({ icon: 'warning', title: 'แจ้งเตือน', text: 'กรุณาแนบรูปภาพสลิปการโอนเงิน', background: '#0a0a0f', color: '#fff' });
        return;
    }

    const btn = $(btnSelector);
    btn.attr('disabled', 'disabled').html('<i class="fa-solid fa-spinner fa-spin me-2"></i> กำลังตรวจสอบสลิป...');

    const formData = new FormData();
    formData.append('file', fileInput.files[0]);

    $.ajax({
        type: 'POST',
        url: 'system/slip.php',
        data: formData,
        contentType: false,
        processData: false
    }).done(function(res) {
        btn.removeAttr('disabled').html('<i class="fa-solid fa-bolt-lightning me-2"></i> [ ตรวจสอบสลิปและเติมเงิน ]');
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message || 'สลิปไม่ถูกต้องหรือถูกใช้งานแล้ว', background: '#0a0a0f', color: '#fff' });
        }
    }).fail(function() {
        btn.removeAttr('disabled').html('<i class="fa-solid fa-bolt-lightning me-2"></i> [ ตรวจสอบสลิปและเติมเงิน ]');
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', background: '#0a0a0f', color: '#fff' });
    });
}

function setPromptPayPreset(amt) {
    $('#promptpayAmountInput').val(amt);
    updatePromptPayQR();
}

function updatePromptPayQR() {
    let amt = parseFloat($('#promptpayAmountInput').val()) || 0;
    let pNum = '<?= htmlspecialchars($promptpay_num) ?>';
    if (!pNum) return;

    if (amt > 0) {
        let qrUrl = 'https://promptpay.io/' + encodeURIComponent(pNum) + '/' + amt.toFixed(2) + '.png';
        $('#promptpayQrImg').attr('src', qrUrl);
        $('#promptpayQrAmountDisplay').text('฿ ' + amt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#promptpayQrBox').slideDown(250);
    } else {
        $('#promptpayQrBox').slideUp(200);
    }
}

function openFullHowToImage() {
    Swal.fire({
        imageUrl: 'assets/img/how_to_angpao.jpg',
        imageAlt: 'วิธีสร้างซองของขวัญ TrueMoney Wallet',
        imageWidth: '95%',
        imageHeight: 'auto',
        showCloseButton: true,
        showConfirmButton: false,
        background: '#0a0a0f',
        backdrop: 'rgba(0, 0, 0, 0.85)',
        customClass: {
            image: 'rounded-4 shadow-lg border border-danger border-opacity-50'
        }
    });
}
</script>
