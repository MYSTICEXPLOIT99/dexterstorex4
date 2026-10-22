<?php
$q_conf = dd_q("SELECT * FROM setting LIMIT 1");
$config = $q_conf ? $q_conf->fetch(PDO::FETCH_ASSOC) : [];
$discord_server_id = $config['discord_server_id'] ?? '';
$current_api_type = $config['slip_api_type'] ?? 'slipok';
$current_bank_name = $config['bank_name'] ?? 'กสิกรไทย';
?>
<div class="p-4 p-lg-5" data-aos="fade-up">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="section-eyebrow" style="letter-spacing:3px;">// STORE & PAYMENT GATEWAY SETTINGS</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-sliders text-primary me-2"></i> ตั้งค่าเว็บไซต์ & ระบบเชื่อมต่อ API ทุกค่าย
            </h3>
            <p class="text-muted small mb-0">ปรับแต่งชื่อร้านค้า โลโก้ ระบบตรวจสอบสลิปอัตโนมัติ (SlipOK / EasySlip / ThunderSlip) ข้อมูลบัญชี และ Discord</p>
        </div>
        <button class="btn-cyber-primary" id="btn_save_settings">
            <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการเปลี่ยนแปลง
        </button>
    </div>

    <div class="row g-4">
        <!-- ══════════════════════════════════════════
             1. PAYMENT & SLIP VERIFICATION API (MULTI-PROVIDER)
        ══════════════════════════════════════════ -->
        <div class="col-12">
            <div class="admin-inner-card" style="border-left: 3px solid #38bdf8;">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                            <i class="fa-solid fa-bolt text-info me-2"></i> ระบบ API ตรวจสอบสลิปอัตโนมัติ (รองรับทุกค่าย)
                        </h5>
                        <p class="text-muted small mb-0">เลือกค่าย API ที่คุณต้องการใช้งาน และกรอก API Key / Token เพื่อให้ระบบตรวจสอบยอดเงินและเติมพ้อยท์อัตโนมัติ</p>
                    </div>
                    <span class="badge" style="background:#0b1329; border:1px solid rgba(56,189,248,0.4); color:#38bdf8; font-family:var(--font-cyber); font-size:0.8rem; padding:6px 12px;">
                        <i class="fa-solid fa-shield-halved me-1"></i> MULTI-GATEWAY READY
                    </span>
                </div>

                <div class="row g-4 align-items-start">
                    <div class="col-lg-4">
                        <div class="d-flex flex-column h-100">
                            <label class="admin-label mb-2 d-flex align-items-center" style="min-height:24px;">
                                <span>ผู้ให้บริการ API (Provider)</span>
                            </label>
                            <select id="slip_api_type" class="admin-input form-select" style="height:48px;" onchange="updateApiGuide()">
                                <option value="slipok" <?= ($current_api_type == 'slipok') ? 'selected' : '' ?>>⚡ SlipOK (api.slipok.com) [แนะนำ]</option>
                                <option value="easyslip" <?= ($current_api_type == 'easyslip') ? 'selected' : '' ?>>🚀 EasySlip (easyslip.com)</option>
                                <option value="thunderslip" <?= ($current_api_type == 'thunderslip') ? 'selected' : '' ?>>⚡ ThunderSlip (thunder.in.th)</option>
                                <option value="openslip" <?= ($current_api_type == 'openslip') ? 'selected' : '' ?>>🌐 OpenSlip / RD-Slip (PromptPay QR)</option>
                                <option value="custom" <?= ($current_api_type == 'custom') ? 'selected' : '' ?>>🔧 Custom Webhook / Gateway</option>
                            </select>
                            <small class="text-muted d-block mt-2" style="font-size:0.75rem; min-height:18px;">เลือกระบบที่คุณสมัครสมาชิกไว้เพื่อใช้งาน</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="d-flex flex-column h-100">
                            <label class="admin-label mb-2 d-flex align-items-center" style="min-height:24px;">
                                <span id="label_api_key">API Key / Secret Token</span>
                            </label>
                            <input type="text" id="slip_api_key" class="admin-input form-control font-monospace" style="height:48px;" value="<?= htmlspecialchars($config['slip_api_key'] ?? '') ?>" placeholder="กรอก API Key หรือ Bearer Token">
                            <small class="text-muted d-block mt-2" id="hint_api_key" style="font-size:0.75rem; min-height:18px;">เช่น SLIPOKXXXXXXXX หรือ Token</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="d-flex flex-column h-100">
                            <label class="admin-label mb-2 d-flex align-items-center" style="min-height:24px;">
                                <span id="label_api_branch">Branch ID / Merchant ID</span>
                            </label>
                            <input type="text" id="slip_api_branch" class="admin-input form-control font-monospace" style="height:48px;" value="<?= htmlspecialchars($config['slip_api_branch'] ?? '') ?>" placeholder="เช่น 71124 หรือ Merchant ID">
                            <small class="text-muted d-block mt-2" id="hint_api_branch" style="font-size:0.75rem; min-height:18px;">รหัสสาขาสำหรับต่อท้าย URL ตรวจสอบสลิป</small>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Provider Notice Box -->
                <div class="mt-3 p-3 rounded-3" id="apiGuideBox" style="background:#050609; border:1px solid rgba(255,255,255,0.06);">
                    <div class="d-flex align-items-center gap-2 text-info fw-bold mb-1" style="font-size:0.85rem;">
                        <i class="fa-solid fa-circle-info"></i> <span id="guideTitle">คู่มือการตั้งค่า SlipOK</span>
                    </div>
                    <p class="text-muted small mb-0" id="guideDesc">
                        นำ API Key จากเว็บไซต์ SlipOK มากรอกในช่อง "API Key" และนำเลข Branch ID มาใส่ในช่อง "Branch ID" ระบบจะทำการส่งรูปสลิปไปตรวจสอบแบบ Server-Side ทันทีเมื่อผู้ใช้แนบสลิป
                    </p>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════
             2. BANKING & PAYMENT DETAILS (LEFT COLUMN)
        ══════════════════════════════════════════ -->
        <div class="col-lg-6">
            <div class="admin-inner-card mb-4">
                <h6 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-building-columns text-success me-2"></i> ข้อมูลบัญชีรับเงินหน้าร้าน
                </h6>

                <div class="mb-3">
                    <label class="admin-label">ธนาคารรับเงิน (Bank Name)</label>
                    <select id="bank_name" class="admin-input form-select">
                        <option value="กสิกรไทย" <?= ($current_bank_name == 'กสิกรไทย') ? 'selected' : '' ?>>ธนาคารกสิกรไทย (KBANK)</option>
                        <option value="ไทยพาณิชย์" <?= ($current_bank_name == 'ไทยพาณิชย์') ? 'selected' : '' ?>>ธนาคารไทยพาณิชย์ (SCB)</option>
                        <option value="กรุงไทย" <?= ($current_bank_name == 'กรุงไทย') ? 'selected' : '' ?>>ธนาคารกรุงไทย (KTB)</option>
                        <option value="กรุงเทพ" <?= ($current_bank_name == 'กรุงเทพ') ? 'selected' : '' ?>>ธนาคารกรุงเทพ (BBL)</option>
                        <option value="กรุงศรี" <?= ($current_bank_name == 'กรุงศรี') ? 'selected' : '' ?>>ธนาคารกรุงศรีอยุธยา (BAY)</option>
                        <option value="ทหารไทยธนชาต" <?= ($current_bank_name == 'ทหารไทยธนชาต') ? 'selected' : '' ?>>ธนาคารทหารไทยธนชาต (TTB)</option>
                        <option value="ออมสิน" <?= ($current_bank_name == 'ออมสิน') ? 'selected' : '' ?>>ธนาคารออมสิน (GSB)</option>
                        <option value="พร้อมเพย์" <?= ($current_bank_name == 'พร้อมเพย์') ? 'selected' : '' ?>>พร้อมเพย์ (PromptPay)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="admin-label">เลขบัญชีธนาคาร / เบอร์พร้อมเพย์ (Account Number)</label>
                    <input type="text" id="contact_phone" class="admin-input form-control font-monospace" value="<?= htmlspecialchars($config['contact_phone'] ?? '') ?>" placeholder="เช่น 144-8-33678-2">
                </div>

                <div class="mb-3">
                    <label class="admin-label">ชื่อเจ้าของบัญชี (Account Owner Name)</label>
                    <input type="text" id="contact_line" class="admin-input form-control" value="<?= htmlspecialchars($config['contact_line'] ?? '') ?>" placeholder="เช่น อดิศร จันทะมาน">
                </div>

                <div class="mb-3">
                    <label class="admin-label">เบอร์ TrueMoney Wallet (สำหรับรับเงิน / ซองอั่งเปา)</label>
                    <input type="text" id="site_phone" class="admin-input form-control font-monospace" value="<?= htmlspecialchars($config['wallet'] ?? '') ?>" placeholder="เช่น 0987654321">
                </div>

                <div class="mb-0">
                    <label class="admin-label">Discord Webhook Log URL (แจ้งเตือนยอดเติมเงิน/สั่งซื้อ)</label>
                    <input type="text" id="site_webhook" class="admin-input form-control font-monospace" value="<?= htmlspecialchars($config['webhook_dc'] ?? '') ?>" placeholder="https://discord.com/api/webhooks/...">
                </div>
            </div>

            <!-- Store Identity -->
            <div class="admin-inner-card">
                <h6 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-circle-info text-info me-2"></i> ข้อมูลพื้นฐานร้านค้า
                </h6>
                
                <div class="mb-3">
                    <label class="admin-label">ชื่อร้านค้า (Store Name)</label>
                    <input type="text" id="site_name" class="admin-input form-control" value="<?= htmlspecialchars($config['name'] ?? 'CYGNUSX STORE') ?>">
                </div>

                <div class="mb-3">
                    <label class="admin-label">โลโก้ร้านค้า (Logo Image URL)</label>
                    <input type="text" id="site_logo" class="admin-input form-control" value="<?= htmlspecialchars($config['logo'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="admin-label">คำอธิบายร้านค้า (Description / Tagline)</label>
                    <input type="text" id="site_des" class="admin-input form-control" value="<?= htmlspecialchars($config['des'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="admin-label">โลโก้แสดงในเมาส์ (Cursor Logo URL - เว้นว่างเพื่อใช้โลโก้ร้าน)</label>
                    <input type="text" id="cursor_logo" class="admin-input form-control" value="<?= htmlspecialchars($config['cursor_logo'] ?? '') ?>" placeholder="เว้นว่างเพื่อใช้โลโก้ร้านค้าเริ่มต้น">
                    <small class="text-muted" style="font-size:0.75rem;">หากเว้นว่าง ระบบจะนำโลโก้ร้านค้ามาแสดงในวงแหวนเมาส์โดยอัตโนมัติ</small>
                </div>

                <div class="mb-0">
                    <label class="admin-label">ข้อความประกาศหน้าร้าน (Announcement Text)</label>
                    <textarea id="site_ann" class="admin-input form-control" rows="3" placeholder="เช่น บริการรวดเร็ว ปลอดภัย 100% สามารถสั่งซื้อสินค้า ผ่านระบบ ออโต้ !!"><?= htmlspecialchars($config['ann'] ?? '') ?></textarea>
                    <small class="text-muted" style="font-size:0.75rem;">ข้อความนี้จะแสดงในกล่อง "ประกาศจากร้าน" ใต้การ์ดสถิติ</small>
                </div>
            </div>

            <!-- Display Widgets On/Off (Stats & Live Feed) -->
            <div class="admin-inner-card mt-4">
                <h6 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-chart-pie text-warning me-2"></i> เปิด/ปิด การแสดงผลสถิติ & ประวัติการซื้อหน้าร้าน
                </h6>

                <!-- Switch 1: Members Stat -->
                <div class="form-check form-switch mb-3 p-3 rounded-3 d-flex align-items-center justify-content-between ps-3" style="background:#090a10; border:1px solid rgba(255,255,255,0.08);">
                    <label class="form-check-label text-white small fw-bold mb-0" for="show_members_stat">
                        <i class="fa-solid fa-users text-warning me-2"></i> แสดงการ์ดสถิติ "ผู้ใช้งานเว็บ"
                    </label>
                    <input class="form-check-input fs-5 m-0" type="checkbox" id="show_members_stat" <?= (($config['show_members_stat'] ?? 'on') == 'on') ? 'checked' : '' ?>>
                </div>

                <!-- Switch 2: Sales Stat -->
                <div class="form-check form-switch mb-3 p-3 rounded-3 d-flex align-items-center justify-content-between ps-3" style="background:#090a10; border:1px solid rgba(255,255,255,0.08);">
                    <label class="form-check-label text-white small fw-bold mb-0" for="show_sales_stat">
                        <i class="fa-solid fa-cart-shopping text-warning me-2"></i> แสดงการ์ดสถิติ "ยอดการขาย"
                    </label>
                    <input class="form-check-input fs-5 m-0" type="checkbox" id="show_sales_stat" <?= (($config['show_sales_stat'] ?? 'on') == 'on') ? 'checked' : '' ?>>
                </div>

                <!-- Switch 3: Recent Purchases Feed -->
                <div class="form-check form-switch mb-0 p-3 rounded-3 d-flex align-items-center justify-content-between ps-3" style="background:#090a10; border:1px solid rgba(255,255,255,0.08);">
                    <label class="form-check-label text-white small fw-bold mb-0" for="show_recent_orders">
                        <i class="fa-solid fa-bullhorn text-info me-2"></i> แสดงกล่อง "ประกาศจากร้าน & ซื้อล่าสุด"
                    </label>
                    <input class="form-check-input fs-5 m-0" type="checkbox" id="show_recent_orders" <?= (($config['show_recent_orders'] ?? 'on') == 'on') ? 'checked' : '' ?>>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════
             3. MUSIC, SOCIAL & DISCORD WIDGET (RIGHT COLUMN)
        ══════════════════════════════════════════ -->
        <div class="col-lg-6">
            <!-- Social & Contact Channels -->
            <div class="admin-inner-card mb-4">
                <h6 class="fw-bold text-white mb-2" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-share-nodes text-primary me-2"></i> ช่องทางติดต่อ & โซเชียลมีเดีย (Floating Contact Popup)
                </h6>
                <p class="text-muted small mb-3">กรอกลิงก์ช่องทางติดต่อที่คุณมี ลิงก์ที่กรอกจะปรากฏในปุ่มพูดคุยมุมขวาล่างทันที</p>

                <!-- Discord -->
                <div class="mb-3">
                    <label class="admin-label d-flex align-items-center gap-2 mb-1">
                        <i class="fa-brands fa-discord text-primary"></i> <span>Discord Invite Link</span>
                    </label>
                    <input type="text" id="contact_discord" class="admin-input form-control font-monospace" value="<?= htmlspecialchars($config['contact_discord'] ?? '') ?>" placeholder="https://discord.gg/...">
                </div>

                <!-- YouTube -->
                <div class="mb-3">
                    <label class="admin-label d-flex align-items-center gap-2 mb-1">
                        <i class="fa-brands fa-youtube text-danger"></i> <span>YouTube Channel URL</span>
                    </label>
                    <input type="text" id="contact_youtube" class="admin-input form-control font-monospace" value="<?= htmlspecialchars($config['contact_youtube'] ?? '') ?>" placeholder="https://www.youtube.com/@...">
                </div>

                <!-- Facebook -->
                <div class="mb-3">
                    <label class="admin-label d-flex align-items-center gap-2 mb-1">
                        <i class="fa-brands fa-facebook text-info"></i> <span>Facebook Fanpage URL</span>
                    </label>
                    <input type="text" id="contact_facebook" class="admin-input form-control font-monospace" value="<?= htmlspecialchars($config['contact_facebook'] ?? '') ?>" placeholder="https://facebook.com/...">
                </div>

                <!-- TikTok -->
                <div class="mb-3">
                    <label class="admin-label d-flex align-items-center gap-2 mb-1">
                        <i class="fa-brands fa-tiktok text-white"></i> <span>TikTok Profile URL</span>
                    </label>
                    <input type="text" id="contact_tiktok" class="admin-input form-control font-monospace" value="<?= htmlspecialchars($config['contact_tiktok'] ?? '') ?>" placeholder="https://tiktok.com/@...">
                </div>

                <!-- LINE -->
                <div class="mb-0">
                    <label class="admin-label d-flex align-items-center gap-2 mb-1">
                        <i class="fa-brands fa-line text-success"></i> <span>LINE Official Account URL</span>
                    </label>
                    <input type="text" id="contact_line_url" class="admin-input form-control font-monospace" value="<?= htmlspecialchars($config['contact_line_url'] ?? '') ?>" placeholder="https://lin.ee/... หรือ https://line.me/ti/p/...">
                </div>
            </div>

            <!-- Background Music -->
            <div class="admin-inner-card mb-4">
                <h6 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-music text-danger me-2"></i> เพลงพื้นหลังเว็บไซต์ (Background Audio)
                </h6>

                <div class="mb-3">
                    <label class="admin-label">ลิงก์เพลงพื้นหลัง (รองรับ YouTube หรือ ลิงก์ตรง MP3)</label>
                    <input type="text" id="bg_music_url" class="admin-input form-control font-monospace" value="<?= htmlspecialchars($config['bg_music_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=... หรือ https://domain.com/music.mp3">
                    <small class="text-muted" style="font-size:0.75rem;">วางลิงก์ YouTube (เช่น https://youtu.be/... หรือ https://www.youtube.com/watch?v=...) หรือไฟล์ .mp3 ได้โดยตรง</small>
                </div>

                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="bg_music_auto" <?= (($config['bg_music_auto'] ?? 'on') == 'on') ? 'checked' : '' ?>>
                    <label class="form-check-label text-white small fw-bold" for="bg_music_auto">เปิดเล่นเพลงอัตโนมัติเมื่อเข้าเว็บ (Autoplay On)</label>
                </div>
            </div>

            <!-- Topup Channels On/Off & Details -->
            <div class="admin-inner-card">
                <h6 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-toggle-on text-success me-2"></i> เปิด/ปิด & จัดการช่องทางเติมเงิน (Payment Channels)
                </h6>

                <!-- Channel 1: Angpao -->
                <div class="p-3 rounded-3 mb-3" style="background:#090a10; border:1px solid rgba(255,255,255,0.08);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-gift text-danger fs-5"></i>
                            <span class="fw-bold text-white small">1. ซองอั่งเปา TrueMoney Wallet</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="topup_angpao_status" <?= (($config['topup_angpao_status'] ?? 'on') == 'on') ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <small class="text-muted" style="font-size:0.75rem;">รับซองของขวัญอัตโนมัติเข้าเบอร์ร้านค้าทันที</small>
                </div>

                <!-- Channel 2: Bank Transfer Slip -->
                <div class="p-3 rounded-3 mb-3" style="background:#090a10; border:1px solid rgba(255,255,255,0.08);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-building-columns text-primary fs-5"></i>
                            <span class="fw-bold text-white small">2. โอนเงินบัญชีธนาคาร (แนบสลิป)</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="topup_bank_status" <?= (($config['topup_bank_status'] ?? 'on') == 'on') ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <small class="text-muted" style="font-size:0.75rem;">แสดงเลขบัญชีและชื่อธนาคารจากด้านซ้ายมือ ให้ลูกค้าโอนแล้วแนบสลิป</small>
                </div>

                <!-- Channel 3: PromptPay QR Slip -->
                <div class="p-3 rounded-3 mb-3" style="background:#090a10; border:1px solid rgba(255,255,255,0.08);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-qrcode text-info fs-5"></i>
                            <span class="fw-bold text-white small">3. สแกนพร้อมเพย์ QR Code (แนบสลิป)</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="topup_promptpay_status" <?= (($config['topup_promptpay_status'] ?? 'on') == 'on') ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div class="row g-2 mt-1 align-items-end">
                        <div class="col-6">
                            <div class="d-flex align-items-end mb-1" style="min-height: 28px;">
                                <label class="admin-label mb-0" style="font-size:0.72rem; line-height: 1.2;">เบอร์พร้อมเพย์ (PromptPay)</label>
                            </div>
                            <input type="text" id="promptpay_number" class="admin-input form-control form-control-sm font-monospace" value="<?= htmlspecialchars($config['promptpay_number'] ?? ($config['wallet'] ?? '')) ?>" placeholder="เบอร์โทร / เลขบัตร">
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-end mb-1" style="min-height: 28px;">
                                <label class="admin-label mb-0" style="font-size:0.72rem; line-height: 1.2;">ชื่อบัญชี (Account Name)</label>
                            </div>
                            <input type="text" id="promptpay_name" class="admin-input form-control form-control-sm" value="<?= htmlspecialchars($config['promptpay_name'] ?? ($config['contact_line'] ?? '')) ?>" placeholder="ชื่อ-นามสกุล">
                        </div>
                    </div>
                </div>

                <!-- Channel 4: TrueMoney Transfer Slip -->
                <div class="p-3 rounded-3" style="background:#090a10; border:1px solid rgba(255,255,255,0.08);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-wallet text-warning fs-5"></i>
                            <span class="fw-bold text-white small">4. โอน TrueMoney Wallet (แนบสลิป)</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="topup_truemoney_slip_status" <?= (($config['topup_truemoney_slip_status'] ?? 'on') == 'on') ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div class="row g-2 mt-1 align-items-end">
                        <div class="col-6">
                            <div class="d-flex align-items-end mb-1" style="min-height: 28px;">
                                <label class="admin-label mb-0" style="font-size:0.72rem; line-height: 1.2;">เบอร์ TrueMoney (Wallet)</label>
                            </div>
                            <input type="text" id="truemoney_number" class="admin-input form-control form-control-sm font-monospace" value="<?= htmlspecialchars($config['truemoney_number'] ?? ($config['wallet'] ?? '')) ?>" placeholder="เบอร์ TrueMoney">
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-end mb-1" style="min-height: 28px;">
                                <label class="admin-label mb-0" style="font-size:0.72rem; line-height: 1.2;">ชื่อบัญชี (Account Name)</label>
                            </div>
                            <input type="text" id="truemoney_name" class="admin-input form-control form-control-sm" value="<?= htmlspecialchars($config['truemoney_name'] ?? ($config['contact_line'] ?? '')) ?>" placeholder="ชื่อ-นามสกุล">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateApiGuide() {
    const type = document.getElementById('slip_api_type').value;
    const title = document.getElementById('guideTitle');
    const desc = document.getElementById('guideDesc');
    const labelKey = document.getElementById('label_api_key');
    const hintKey = document.getElementById('hint_api_key');
    const labelBranch = document.getElementById('label_api_branch');
    const hintBranch = document.getElementById('hint_api_branch');

    if (type === 'slipok') {
        title.innerText = 'วิธีตั้งค่า SlipOK API (แนะนำ)';
        desc.innerText = 'สมัครสมาชิกที่ slipok.com > ไปที่เมนู Branch / สาขา > คัดลอก Branch ID มาใส่ในช่อง Branch ID และคัดลอก API Key มาใส่ในช่อง API Key';
        labelKey.innerText = 'API Key (SlipOK)';
        hintKey.innerText = 'นำมาจากหน้า API Keys ในคอนโซล SlipOK';
        labelBranch.innerText = 'Branch ID / รหัสสาขา (ถ้ามี)';
        hintBranch.innerText = 'หากใช้สาขาหลักสามารถใส่ 1 หรือว่างไว้ได้';
    } else if (type === 'easyslip') {
        desc.innerText = 'นำ Authorization Token (Bearer Token) จากคอนโซล EasySlip มากรอกในช่อง API Token ระบบจะเรียก Verify API ผ่าน EasySlip v1 ทันที';
        labelKey.innerText = 'EasySlip Bearer Token';
        hintKey.innerText = 'เช่น eyJhbGciOiJIUzI1NiIsInR5cCI6...';
        labelBranch.innerText = 'EasySlip Merchant ID';
        hintBranch.innerText = 'ระบุ Merchant ID หรือเว้นว่างไว้';
    } else if (type === 'thunderslip') {
        title.innerText = 'คู่มือการตั้งค่า ThunderSlip (thunder.in.th)';
        desc.innerText = 'กรอก Token สำหรับเรียกใช้งาน API ของ ThunderSlip ระบบจะตรวจสอบสลิปแบบรวดเร็วอัตโนมัติ';
        labelKey.innerText = 'ThunderSlip API Token';
        hintKey.innerText = 'Token ที่ได้รับจากแดชบอร์ด ThunderSlip';
        labelBranch.innerText = 'ThunderSlip Branch (ถ้ามี)';
        hintBranch.innerText = 'ระบุ Branch หรือเว้นว่างไว้';
    } else if (type === 'openslip') {
        title.innerText = 'คู่มือการตั้งค่า OpenSlip / PromptPay QR';
        desc.innerText = 'เชื่อมต่อระบบตรวจสอบสลิปแบบ Open API รองรับ PromptPay QR Code ทั่วประเทศ';
        labelKey.innerText = 'Client ID / Secret Token';
        hintKey.innerText = 'คีย์การเข้าถึง Open API';
        labelBranch.innerText = 'Account Ref (ถ้ามี)';
        hintBranch.innerText = 'เลขประจำตัวหรือ Ref';
    } else {
        title.innerText = 'กำหนดค่า Custom API Gateway';
        desc.innerText = 'กรอก API Key และ URL สำหรับระบบเชื่อมต่อแบบ Custom ที่คุณพัฒนาขึ้นเอง';
        labelKey.innerText = 'Custom Secret Key';
        hintKey.innerText = 'Secret Key หรือ Token';
        labelBranch.innerText = 'Custom Endpoint / Extra Param';
        hintBranch.innerText = 'พารามิเตอร์เพิ่มเติม';
    }
}

$("#btn_save_settings").click(function () {
    var formData = new FormData();
    formData.append('name', $("#site_name").val());
    formData.append('logo', $("#site_logo").val());
    formData.append('cursor_logo', $("#cursor_logo").val());
    formData.append('des', $("#site_des").val());
    formData.append('ann', $("#site_ann").val());
    formData.append('phone', $("#site_phone").val());
    formData.append('bank_name', $("#bank_name").val());
    formData.append('contact_phone', $("#contact_phone").val());
    formData.append('contact_line', $("#contact_line").val());
    formData.append('webhook_dc', $("#site_webhook").val());
    formData.append('slip_api_type', $("#slip_api_type").val());
    formData.append('slip_api_key', $("#slip_api_key").val());
    formData.append('slip_api_branch', $("#slip_api_branch").val());
    formData.append('bg_music_url', $("#bg_music_url").val());
    formData.append('bg_music_auto', $("#bg_music_auto").is(':checked') ? 'on' : 'off');
    formData.append('topup_angpao_status', $("#topup_angpao_status").is(':checked') ? 'on' : 'off');
    formData.append('topup_bank_status', $("#topup_bank_status").is(':checked') ? 'on' : 'off');
    formData.append('topup_promptpay_status', $("#topup_promptpay_status").is(':checked') ? 'on' : 'off');
    formData.append('topup_truemoney_slip_status', $("#topup_truemoney_slip_status").is(':checked') ? 'on' : 'off');
    formData.append('show_members_stat', $("#show_members_stat").is(':checked') ? 'on' : 'off');
    formData.append('show_sales_stat', $("#show_sales_stat").is(':checked') ? 'on' : 'off');
    formData.append('show_recent_orders', $("#show_recent_orders").is(':checked') ? 'on' : 'off');
    formData.append('promptpay_number', $("#promptpay_number").val());
    formData.append('promptpay_name', $("#promptpay_name").val());
    formData.append('truemoney_number', $("#truemoney_number").val());
    formData.append('truemoney_name', $("#truemoney_name").val());
    formData.append('discord_server_id', "<?= addslashes($config['discord_server_id'] ?? '') ?>");
    formData.append('contact_discord', $("#contact_discord").val());
    formData.append('contact_youtube', $("#contact_youtube").val());
    formData.append('contact_facebook', $("#contact_facebook").val());
    formData.append('contact_tiktok', $("#contact_tiktok").val());
    formData.append('contact_line_url', $("#contact_line_url").val());

    $.ajax({
        type: 'POST',
        url: 'system/backend/website.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ!',
                text: res.message,
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#4b5d78'
            }).then(() => {
                window.location.reload();
            });
        },
        error: function (err) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: err.responseJSON ? err.responseJSON.message : 'ไม่สามารถบันทึกข้อมูลได้',
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#4b5d78'
            });
        }
    });
});

$(document).ready(function() {
    updateApiGuide();
});
</script>

