<?php
if (!admin($user)) {
    echo "<div class='p-5 text-center text-muted'><i class='fa-solid fa-lock fs-1 mb-3'></i><h3>คุณไม่มีสิทธิ์เข้าถึงหน้านี้</h3></div>";
    exit();
}

$cur_preset     = $config['theme_preset'] ?? 'gold';
$cur_primary    = $config['glow_primary'] ?? '#facc15';
$cur_secondary  = $config['glow_secondary'] ?? '#ca8a04';
$cur_speed      = $config['beam_speed'] ?? '4.5';
$cur_snow       = $config['snow_particle'] ?? 'on';
$cur_intensity  = $config['glow_intensity'] ?? 'high';

// Core Colors
$color_bg_main     = $config['color_bg_main'] ?? '#040508';
$color_navbar_bg   = $config['color_navbar_bg'] ?? '#06070c';
$color_card_bg     = $config['color_card_bg'] ?? '#08090e';
$color_lightning   = $config['color_lightning'] ?? '#facc15';
$color_border_beam = $config['color_border_beam'] ?? '#facc15';
$color_btn_primary = $config['color_btn_primary'] ?? '#facc15';
$color_btn_text    = $config['color_btn_text'] ?? '#08090d';

// Feature Toggles
$intro_splash_status     = $config['intro_splash_status'] ?? 'on';
$intro_title             = $config['intro_title'] ?? 'CYGNUSX STORE';
$intro_subtitle          = $config['intro_subtitle'] ?? 'PREMIUM AUTOMATED DIGITAL STORE';
$banner_lightning_status = $config['banner_lightning_status'] ?? 'on';
$cursor_orbit_status     = $config['cursor_orbit_status'] ?? 'on';
$ann_typewriter_speed    = $config['ann_typewriter_speed'] ?? 40;

// Zone-by-Zone Colors (17 Granular Points)
$z_marquee      = $config['zone_marquee'] ?? $cur_primary;
$z_navbar       = $config['zone_navbar'] ?? $cur_primary;
$z_banner       = $config['zone_banner'] ?? $cur_primary;
$z_product      = $config['zone_product'] ?? $cur_primary;
$z_category     = $config['zone_category'] ?? $cur_primary;
$z_wheel        = $config['zone_wheel'] ?? $cur_primary;
$z_profile      = $config['zone_profile'] ?? $cur_primary;
$z_warp         = $config['zone_warp'] ?? $cur_primary;
$z_history      = $config['zone_history'] ?? $cur_primary;
$z_orbit        = $config['zone_orbit'] ?? $cur_primary;
$z_community    = $config['zone_community'] ?? $cur_primary;
$z_music        = $config['zone_music'] ?? $cur_primary;
$z_floating_dc  = $config['zone_floating_dc'] ?? $cur_primary;
$z_cursor       = $config['zone_cursor'] ?? $cur_primary;
$z_topup        = $config['zone_topup'] ?? $cur_primary;
$z_modal        = $config['zone_modal'] ?? $cur_primary;
$z_footer       = $config['zone_footer'] ?? $cur_primary;
?>

<div class="p-4" data-aos="fade-up">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom border-secondary border-opacity-25">
        <div>
            <div class="section-eyebrow" style="color:#facc15;">// THEME & COMPLETE SYSTEM CUSTOMIZER</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-wand-magic-sparkles text-warning me-2"></i> ระบบปรับแต่งสีและฟังก์ชันหลังบ้าน 100%
            </h3>
            <p class="text-muted small mb-0">เลือกเปลี่ยนสีไฟพร้อมกันทั้งเว็บ หรือเลือกกำหนดสีไฟแยกเฉพาะจุด (สินค้า, วงล้อ, โปรไฟล์, วาปโหลดเข้าเพจ, แถบเมนู, เพลง, ดิส, ฯลฯ) ได้อย่างอิสระ</p>
        </div>
        <div>
            <button type="button" class="btn btn-warning fw-bold px-4 py-2" id="btnSaveTheme" style="font-family:var(--font-cyber); letter-spacing:0.5px; box-shadow:0 0 20px rgba(250,204,21,0.4);">
                <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการเปลี่ยนแปลง
            </button>
        </div>
    </div>

    <!-- 1. PRESET THEMES SELECTION (1-CLICK APPLY) -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-palette text-warning me-2"></i> 1. พรีเซ็ตสีไฟสำเร็จรูป (กด 1 คลิกเปลี่ยนสีทุกจุดทันที)
            </h5>
            <span class="text-muted small">คลิกเพื่อตั้งค่าสีไฟทุกจุดให้เป็นโทนเดียวกัน</span>
        </div>
        
        <div class="row g-3" id="presetThemeGrid">
            <!-- Preset: Cyber Gold -->
            <div class="col-6 col-md-4 col-xl-3">
                <div class="preset-theme-card <?= $cur_preset == 'gold' ? 'active' : '' ?>" onclick="applyGlobalPreset('gold', '#facc15', '#ca8a04')">
                    <div class="preset-color-preview" style="background: linear-gradient(135deg, #fef08a 0%, #facc15 50%, #ca8a04 100%); box-shadow: 0 0 15px #facc15;"></div>
                    <div class="preset-theme-title">👑 Cyber Gold</div>
                    <div class="preset-theme-desc">สีทองนีออนสุดหรูหรา</div>
                    <span class="preset-badge-active"><i class="fa-solid fa-check"></i> ใช้งานอยู่</span>
                </div>
            </div>

            <!-- Preset: Cyber Purple -->
            <div class="col-6 col-md-4 col-xl-3">
                <div class="preset-theme-card <?= $cur_preset == 'purple' ? 'active' : '' ?>" onclick="applyGlobalPreset('purple', '#c084fc', '#7e22ce')">
                    <div class="preset-color-preview" style="background: linear-gradient(135deg, #e9d5ff 0%, #c084fc 50%, #7e22ce 100%); box-shadow: 0 0 15px #c084fc;"></div>
                    <div class="preset-theme-title">💜 Neon Amethyst</div>
                    <div class="preset-theme-desc">สีม่วงนีออนเรืองแสง Cyber</div>
                    <span class="preset-badge-active"><i class="fa-solid fa-check"></i> ใช้งานอยู่</span>
                </div>
            </div>

            <!-- Preset: Ice Blue -->
            <div class="col-6 col-md-4 col-xl-3">
                <div class="preset-theme-card <?= $cur_preset == 'cyan' ? 'active' : '' ?>" onclick="applyGlobalPreset('cyan', '#38bdf8', '#0284c7')">
                    <div class="preset-color-preview" style="background: linear-gradient(135deg, #bae6fd 0%, #38bdf8 50%, #0369a1 100%); box-shadow: 0 0 15px #38bdf8;"></div>
                    <div class="preset-theme-title">💙 Ice Subzero</div>
                    <div class="preset-theme-desc">สีฟ้าไอซ์นีออนล้ำอนาคต</div>
                    <span class="preset-badge-active"><i class="fa-solid fa-check"></i> ใช้งานอยู่</span>
                </div>
            </div>

            <!-- Preset: Crimson Red -->
            <div class="col-6 col-md-4 col-xl-3">
                <div class="preset-theme-card <?= $cur_preset == 'red' ? 'active' : '' ?>" onclick="applyGlobalPreset('red', '#f87171', '#dc2626')">
                    <div class="preset-color-preview" style="background: linear-gradient(135deg, #fecaca 0%, #f87171 50%, #b91c1c 100%); box-shadow: 0 0 15px #f87171;"></div>
                    <div class="preset-theme-title">🔴 Crimson Ruby</div>
                    <div class="preset-theme-desc">สีแดงทับทิมดุดันทรงพลัง</div>
                    <span class="preset-badge-active"><i class="fa-solid fa-check"></i> ใช้งานอยู่</span>
                </div>
            </div>

            <!-- Preset: Toxic Matrix -->
            <div class="col-6 col-md-4 col-xl-3">
                <div class="preset-theme-card <?= $cur_preset == 'green' ? 'active' : '' ?>" onclick="applyGlobalPreset('green', '#4ade80', '#16a34a')">
                    <div class="preset-color-preview" style="background: linear-gradient(135deg, #bbf7d0 0%, #4ade80 50%, #15803d 100%); box-shadow: 0 0 15px #4ade80;"></div>
                    <div class="preset-theme-title">🟢 Toxic Matrix</div>
                    <div class="preset-theme-desc">สีเขียวเมทริกซ์นีออน</div>
                    <span class="preset-badge-active"><i class="fa-solid fa-check"></i> ใช้งานอยู่</span>
                </div>
            </div>

            <!-- Preset: Silver Diamond -->
            <div class="col-6 col-md-4 col-xl-3">
                <div class="preset-theme-card <?= $cur_preset == 'diamond' ? 'active' : '' ?>" onclick="applyGlobalPreset('diamond', '#ffffff', '#94a3b8')">
                    <div class="preset-color-preview" style="background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 50%, #64748b 100%); box-shadow: 0 0 15px #ffffff;"></div>
                    <div class="preset-theme-title">🤍 Platinum Diamond</div>
                    <div class="preset-theme-desc">สีขาวประกายเพชรพรีเมียม</div>
                    <span class="preset-badge-active"><i class="fa-solid fa-check"></i> ใช้งานอยู่</span>
                </div>
            </div>

            <!-- Preset: Rainbow Chroma -->
            <div class="col-6 col-md-4 col-xl-3">
                <div class="preset-theme-card <?= $cur_preset == 'rainbow' ? 'active' : '' ?>" onclick="applyGlobalPreset('rainbow', '#ff007f', '#00e5ff')">
                    <div class="preset-color-preview" style="background: linear-gradient(135deg, #ff007f, #7928ca, #0070f3, #00dfd8, #7928ca); box-shadow: 0 0 15px #00e5ff;"></div>
                    <div class="preset-theme-title">🌈 Rainbow Chroma</div>
                    <div class="preset-theme-desc">สีรุ้ง RGB Dynamic Flow</div>
                    <span class="preset-badge-active"><i class="fa-solid fa-check"></i> ใช้งานอยู่</span>
                </div>
            </div>

            <!-- Preset: Custom Zone Mode -->
            <div class="col-6 col-md-4 col-xl-3">
                <div class="preset-theme-card <?= $cur_preset == 'custom' ? 'active' : '' ?>" id="presetCardCustom" onclick="setCustomZoneMode()">
                    <div class="preset-color-preview" id="customColorPreview" style="background: conic-gradient(#facc15, #c084fc, #38bdf8, #f87171, #4ade80, #facc15); box-shadow: 0 0 15px #facc15;"></div>
                    <div class="preset-theme-title">🎨 Custom Zones</div>
                    <div class="preset-theme-desc">แยกปรับสีอิสระทีละจุด</div>
                    <span class="preset-badge-active"><i class="fa-solid fa-check"></i> ใช้งานอยู่</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. CORE & ZONE COLOR CONTROLS -->
    <form id="themeForm">
        <input type="hidden" id="theme_preset" name="theme_preset" value="<?= htmlspecialchars($cur_preset) ?>">
        <input type="hidden" id="glow_primary" name="glow_primary" value="<?= htmlspecialchars($cur_primary) ?>">
        <input type="hidden" id="glow_secondary" name="glow_secondary" value="<?= htmlspecialchars($cur_secondary) ?>">

        <!-- 2.1 CORE THEME COLORS -->
        <div class="mb-4">
            <h5 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-brush text-warning me-2"></i> 2. สีพื้นหลัง & โครงสร้างหลัก (Core Colors)
            </h5>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="zone-control-card">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="zone-dot" id="dot_color_bg_main" style="background:<?= htmlspecialchars($color_bg_main) ?>;"></span>
                            <span class="zone-name">สีพื้นหลังหลัก (Main Page BG)</span>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="color" id="pick_color_bg_main" class="form-control form-control-color bg-dark border-secondary p-1" value="<?= htmlspecialchars($color_bg_main) ?>" oninput="syncCoreColor('color_bg_main', this.value)">
                            <input type="text" class="admin-input form-control" id="color_bg_main" name="color_bg_main" value="<?= htmlspecialchars($color_bg_main) ?>" oninput="syncCoreHex('color_bg_main', this.value)">
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="zone-control-card">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="zone-dot" id="dot_color_navbar_bg" style="background:<?= htmlspecialchars($color_navbar_bg) ?>;"></span>
                            <span class="zone-name">สีพื้นหลังแถบเมนู (Navbar BG)</span>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="color" id="pick_color_navbar_bg" class="form-control form-control-color bg-dark border-secondary p-1" value="<?= htmlspecialchars($color_navbar_bg) ?>" oninput="syncCoreColor('color_navbar_bg', this.value)">
                            <input type="text" class="admin-input form-control" id="color_navbar_bg" name="color_navbar_bg" value="<?= htmlspecialchars($color_navbar_bg) ?>" oninput="syncCoreHex('color_navbar_bg', this.value)">
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="zone-control-card">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="zone-dot" id="dot_color_card_bg" style="background:<?= htmlspecialchars($color_card_bg) ?>;"></span>
                            <span class="zone-name">สีพื้นหลังการ์ดสินค้า & กล่อง (Card BG)</span>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="color" id="pick_color_card_bg" class="form-control form-control-color bg-dark border-secondary p-1" value="<?= htmlspecialchars($color_card_bg) ?>" oninput="syncCoreColor('color_card_bg', this.value)">
                            <input type="text" class="admin-input form-control" id="color_card_bg" name="color_card_bg" value="<?= htmlspecialchars($color_card_bg) ?>" oninput="syncCoreHex('color_card_bg', this.value)">
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="zone-control-card">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="zone-dot" id="dot_color_lightning" style="background:<?= htmlspecialchars($color_lightning) ?>;"></span>
                            <span class="zone-name">สีสายฟ้าวิ่งขอบแบนเนอร์ (Lightning)</span>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="color" id="pick_color_lightning" class="form-control form-control-color bg-dark border-secondary p-1" value="<?= htmlspecialchars($color_lightning) ?>" oninput="syncCoreColor('color_lightning', this.value)">
                            <input type="text" class="admin-input form-control" id="color_lightning" name="color_lightning" value="<?= htmlspecialchars($color_lightning) ?>" oninput="syncCoreHex('color_lightning', this.value)">
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="zone-control-card">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="zone-dot" id="dot_color_border_beam" style="background:<?= htmlspecialchars($color_border_beam) ?>;"></span>
                            <span class="zone-name">สีเส้นแสงหมุนรอบขอบ (Border Beam)</span>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="color" id="pick_color_border_beam" class="form-control form-control-color bg-dark border-secondary p-1" value="<?= htmlspecialchars($color_border_beam) ?>" oninput="syncCoreColor('color_border_beam', this.value)">
                            <input type="text" class="admin-input form-control" id="color_border_beam" name="color_border_beam" value="<?= htmlspecialchars($color_border_beam) ?>" oninput="syncCoreHex('color_border_beam', this.value)">
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="zone-control-card">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="zone-dot" id="dot_color_btn_primary" style="background:<?= htmlspecialchars($color_btn_primary) ?>;"></span>
                            <span class="zone-name">สีปุ่มหลัก & ไฮไลท์ (Primary Buttons)</span>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="color" id="pick_color_btn_primary" class="form-control form-control-color bg-dark border-secondary p-1" value="<?= htmlspecialchars($color_btn_primary) ?>" oninput="syncCoreColor('color_btn_primary', this.value)">
                            <input type="text" class="admin-input form-control" id="color_btn_primary" name="color_btn_primary" value="<?= htmlspecialchars($color_btn_primary) ?>" oninput="syncCoreHex('color_btn_primary', this.value)">
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="zone-control-card">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="zone-dot" id="dot_color_btn_text" style="background:<?= htmlspecialchars($color_btn_text) ?>;"></span>
                            <span class="zone-name">สีตัวอักษรบนปุ่มหลัก (Button Text)</span>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="color" id="pick_color_btn_text" class="form-control form-control-color bg-dark border-secondary p-1" value="<?= htmlspecialchars($color_btn_text) ?>" oninput="syncCoreColor('color_btn_text', this.value)">
                            <input type="text" class="admin-input form-control" id="color_btn_text" name="color_btn_text" value="<?= htmlspecialchars($color_btn_text) ?>" oninput="syncCoreHex('color_btn_text', this.value)">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2.2 FEATURE TOGGLES -->
        <div class="mb-4">
            <h5 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-toggle-on text-warning me-2"></i> 3. เปิด/ปิดฟังก์ชัน & เอฟเฟกต์พิเศษ (Feature Toggles)
            </h5>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="zone-control-card">
                        <label class="form-label text-white fw-bold small">หน้า Intro Portal (เปิดเว็บครั้งแรก)</label>
                        <select class="admin-input form-select mb-2" id="intro_splash_status" name="intro_splash_status">
                            <option value="on" <?= $intro_splash_status == 'on' ? 'selected' : '' ?>>🌟 เปิดใช้งาน (Meteor Splash On)</option>
                            <option value="off" <?= $intro_splash_status == 'off' ? 'selected' : '' ?>>❌ ปิดใช้งาน (เข้าหน้าเว็บตรง)</option>
                        </select>
                        <input type="text" class="admin-input form-control mb-1" name="intro_title" value="<?= htmlspecialchars($intro_title) ?>" placeholder="ชื่อหัวข้อ Intro">
                        <input type="text" class="admin-input form-control" name="intro_subtitle" value="<?= htmlspecialchars($intro_subtitle) ?>" placeholder="คำบรรยาย Intro">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="zone-control-card">
                        <label class="form-label text-white fw-bold small">เอฟเฟกต์สายฟ้าวิ่งมุมป้าย (Banner Lightning)</label>
                        <select class="admin-input form-select mb-2" id="banner_lightning_status" name="banner_lightning_status">
                            <option value="on" <?= $banner_lightning_status == 'on' ? 'selected' : '' ?>>⚡ เปิดใช้งาน (Laser Beam On)</option>
                            <option value="off" <?= $banner_lightning_status == 'off' ? 'selected' : '' ?>>❌ ปิดใช้งาน (Disabled)</option>
                        </select>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="zone-control-card">
                        <label class="form-label text-white fw-bold small">วงแหวนดวงดาวรอบเมาส์ (Celestial Orbit)</label>
                        <select class="admin-input form-select mb-2" id="cursor_orbit_status" name="cursor_orbit_status">
                            <option value="on" <?= $cursor_orbit_status == 'on' ? 'selected' : '' ?>>🪐 เปิดใช้งาน (Orbit Rings On)</option>
                            <option value="off" <?= $cursor_orbit_status == 'off' ? 'selected' : '' ?>>❌ ปิดใช้งาน (Disabled)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2.2 ZONE-BY-ZONE GRANULAR CONTROLS (17 ZONES) -->
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-sliders text-warning me-2"></i> 3. ปรับแต่งสีไฟแยกตามจุดแบบละเอียด (Zone-by-Zone Customizer - 17 โซน)
                </h5>
                <span class="badge bg-dark border border-secondary text-white px-3 py-1 small">
                    <i class="fa-solid fa-circle-info text-info me-1"></i> เลือกรหัสสีหรือคลิก Color Swatch เพื่อปรับแต่งเฉพาะจุด
                </span>
            </div>

            <div class="row g-3">
                <?php
                $allZones = [
                    'marquee'     => ['name' => '1. แถบตัววิ่งประกาศบนสุด (Marquee Bar)', 'val' => $z_marquee],
                    'navbar'      => ['name' => '2. แถบเมนูด้านบน & ปุ่ม Active (Navbar)', 'val' => $z_navbar],
                    'banner'      => ['name' => '3. กรอบแบนเนอร์ & แสงนีออนฮีโร่ (Banner Hero)', 'val' => $z_banner],
                    'product'     => ['name' => '4. กรอบแสงไฟสินค้า & ป้ายราคา (Product Cards)', 'val' => $z_product],
                    'category'    => ['name' => '5. แท็บเลือกหมวดหมู่สินค้า (Category Tabs)', 'val' => $z_category],
                    'wheel'       => ['name' => '6. วงล้อเสี่ยงโชค & กล่องสุ่ม (Lucky Wheel)', 'val' => $z_wheel],
                    'profile'     => ['name' => '7. โปรไฟล์ & ยอดเงินคงเหลือ (Account Modal)', 'val' => $z_profile],
                    'warp'        => ['name' => '8. วาปโหลดเข้าเพจสินค้า (Cyber Warp FX)', 'val' => $z_warp],
                    'history'     => ['name' => '9. ประวัติซื้อล่าสุด & Live Feed (Purchase Cards)', 'val' => $z_history],
                    'orbit'       => ['name' => '10. วงแหวนดวงดาว 3D (Celestial Orbit)', 'val' => $z_orbit],
                    'community'   => ['name' => '11. กล่องชุมชน Discord & ปุ่มเข้าร่วม', 'val' => $z_community],
                    'music'       => ['name' => '12. กล่องเครื่องเล่นเพลง (Music Dock)', 'val' => $z_music],
                    'floating_dc' => ['name' => '13. ปุ่มลอยติดต่อมุมจอ (Discord Float)', 'val' => $z_floating_dc],
                    'cursor'      => ['name' => '14. เคอร์เซอร์เมาส์ & ละอองดาว (Laser Cursor)', 'val' => $z_cursor],
                    'topup'       => ['name' => '15. การ์ดช่องทางเติมเงิน & ชำระเงิน (Topup Cards)', 'val' => $z_topup],
                    'modal'       => ['name' => '16. หน้าต่างป๊อปอัปแจ้งเตือน (Popup Modals)', 'val' => $z_modal],
                    'footer'      => ['name' => '17. ส่วนท้ายเว็บ & ข้อความลิขสิทธิ์ (Footer)', 'val' => $z_footer],
                ];

                foreach ($allZones as $zKey => $zInfo):
                ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="zone-control-card">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="zone-dot" id="dot_<?= $zKey ?>" style="background:<?= htmlspecialchars($zInfo['val']) ?>;"></span>
                            <span class="zone-name"><?= $zInfo['name'] ?></span>
                        </div>
                        <div class="input-group input-group-sm mb-2">
                            <input type="color" class="form-control form-control-color bg-dark border-secondary p-1" id="pick_<?= $zKey ?>" value="<?= htmlspecialchars($zInfo['val']) ?>" oninput="syncZoneColor('<?= $zKey ?>', this.value)">
                            <input type="text" class="admin-input form-control" id="zone_<?= $zKey ?>" name="zone_<?= $zKey ?>" value="<?= htmlspecialchars($zInfo['val']) ?>" oninput="syncZoneHex('<?= $zKey ?>', this.value)">
                        </div>
                        <div class="d-flex gap-1 justify-content-between zone-swatches">
                            <span class="swatch" style="background:#facc15" onclick="setZoneQuick('<?= $zKey ?>', '#facc15')"></span>
                            <span class="swatch" style="background:#c084fc" onclick="setZoneQuick('<?= $zKey ?>', '#c084fc')"></span>
                            <span class="swatch" style="background:#38bdf8" onclick="setZoneQuick('<?= $zKey ?>', '#38bdf8')"></span>
                            <span class="swatch" style="background:#f87171" onclick="setZoneQuick('<?= $zKey ?>', '#f87171')"></span>
                            <span class="swatch" style="background:#4ade80" onclick="setZoneQuick('<?= $zKey ?>', '#4ade80')"></span>
                            <span class="swatch" style="background:#ffffff" onclick="setZoneQuick('<?= $zKey ?>', '#ffffff')"></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 3. SPEED & FX CONTROLS -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-6">
                <div class="admin-sidebar p-4 h-100">
                    <h5 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
                        <i class="fa-solid fa-gauge-high text-warning me-2"></i> 3. ความเร็ว & เอฟเฟกต์พิเศษ
                    </h5>

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label text-white small fw-bold mb-0">ความเร็วลำแสงไฟวิ่งรอบกรอบ (Beam Rotation Speed)</label>
                            <span class="badge bg-warning text-dark fw-bold" id="speedBadge"><?= htmlspecialchars($cur_speed) ?>s</span>
                        </div>
                        <input type="range" class="form-range" id="beam_speed" name="beam_speed" min="1.5" max="9.0" step="0.5" value="<?= htmlspecialchars($cur_speed) ?>" oninput="onSpeedChange(this.value)">
                        <div class="d-flex justify-content-between text-muted" style="font-size:0.7rem;">
                            <span>เร็วมาก (1.5s)</span>
                            <span>ปานกลาง (4.5s)</span>
                            <span>นุ่มนวลช้า (9.0s)</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label text-white small fw-bold">ระดับความสว่าง (Glow Intensity)</label>
                            <select class="admin-input form-select" id="glow_intensity" name="glow_intensity" onchange="updateLivePreview()">
                                <option value="high" <?= $cur_intensity == 'high' ? 'selected' : '' ?>>🔥 High (สว่างเรืองแสงสูงสุด)</option>
                                <option value="normal" <?= $cur_intensity == 'normal' ? 'selected' : '' ?>>✨ Normal (ปานกลาง สบายตา)</option>
                                <option value="low" <?= $cur_intensity == 'low' ? 'selected' : '' ?>>🌙 Subtle (บางเบา เรียบหรู)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-white small fw-bold">ละอองดาวหิมะ (Snow Particle FX)</label>
                            <select class="admin-input form-select" id="snow_particle" name="snow_particle">
                                <option value="on" <?= $cur_snow == 'on' ? 'selected' : '' ?>>❄️ เปิดใช้งาน (Enabled)</option>
                                <option value="off" <?= $cur_snow == 'off' ? 'selected' : '' ?>>❌ ปิดใช้งาน (Disabled)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Interactive Preview Stage -->
            <div class="col-12 col-xl-6">
                <div class="admin-sidebar p-4 h-100 position-relative overflow-hidden">
                    <h5 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
                        <i class="fa-solid fa-eye text-info me-2"></i> 4. ตัวอย่างการแสดงผลจริง (Live Preview)
                    </h5>

                    <div class="preview-stage-container p-3 rounded-4" style="background:#050609; border:1px solid rgba(255,255,255,0.08);">
                        <!-- Mini Navbar Preview with Balance Pill & User Badge -->
                        <div class="preview-navbar mb-2" id="prevNavbar" style="border-color:<?= htmlspecialchars($z_navbar) ?>;">
                            <div class="fw-bold text-white small" style="font-family:var(--font-cyber);">CYGNUSX</div>
                            <div class="d-flex align-items-center gap-1">
                                <!-- Balance Pill -->
                                <span class="badge px-2 py-1" id="prevNavBalance" style="background:#0d0e14; border:1px solid <?= htmlspecialchars($z_profile) ?>; color:<?= htmlspecialchars($z_profile) ?>; font-size:0.6rem; border-radius:999px;">
                                    <i class="fa-solid fa-credit-card me-1"></i> ฿1,000,010
                                </span>
                                <!-- User Badge -->
                                <span class="badge" id="prevNavPill" style="background:<?= htmlspecialchars($z_navbar) ?>; color:#08090d; font-size:0.6rem;">ADMIN</span>
                            </div>
                        </div>

                        <!-- Mini Product Card, Wheel & Warp Preview -->
                        <div class="row g-2 align-items-center mb-2">
                            <!-- Product & Orbit -->
                            <div class="col-4">
                                <div class="preview-prod-card" id="prevCard" style="border-color:<?= htmlspecialchars($z_product) ?>;">
                                    <div class="preview-prod-img-box position-relative" style="height:60px; background:#10111a; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                                        <div class="preview-orbit-wrap" id="prevOrbit">
                                            <div class="preview-orbit-ring ring-1" style="border-color:<?= htmlspecialchars($z_orbit) ?>;"></div>
                                            <div class="preview-orbit-core" style="border-color:<?= htmlspecialchars($z_orbit) ?>;">
                                                <i class="fa-solid fa-crown" id="prevCrown" style="color:<?= htmlspecialchars($z_orbit) ?>; font-size:0.8rem;"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-1 text-center">
                                        <span class="fw-bold" id="prevPrice" style="color:<?= htmlspecialchars($z_product) ?>; font-size:0.65rem;">฿350</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Mini Cyber Warp Burst Preview -->
                            <div class="col-4">
                                <div class="p-2 rounded-3 text-center position-relative" id="prevWarpBox" style="background:#090a10; border:1.5px solid <?= htmlspecialchars($z_warp) ?>; height:90px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                                    <div class="position-relative d-flex align-items-center justify-content-center" style="width:36px; height:36px; border-radius:50%; background:#08090d; border:1.5px solid <?= htmlspecialchars($z_warp) ?>;" id="prevWarpEmblem">
                                        <i class="fa-solid fa-bolt" id="prevWarpBolt" style="color:<?= htmlspecialchars($z_warp) ?>; font-size:0.75rem;"></i>
                                    </div>
                                    <span class="badge mt-1 py-0 px-2 fw-bold" id="prevWarpBadge" style="font-size:0.55rem; background:<?= htmlspecialchars($z_warp) ?>; color:#08090d;">WARP FX</span>
                                </div>
                            </div>

                            <!-- Mini Profile Modal -->
                            <div class="col-4">
                                <div class="p-2 rounded-3 text-center position-relative" id="prevProfileModal" style="background:#090a10; border:1.5px solid <?= htmlspecialchars($z_profile) ?>; height:90px; display:flex; flex-direction:column; align-items:center; justify-content:space-between;">
                                    <div class="d-flex align-items-center gap-1">
                                        <div style="width:18px;height:18px;border-radius:50%;background:<?= htmlspecialchars($z_profile) ?>;border:1px solid rgba(255,255,255,0.4);color:#08090d;font-size:0.5rem;font-weight:900;display:flex;align-items:center;justify-content:center;" id="prevAvatarCore">AD</div>
                                        <span class="fw-bold" style="font-size:0.6rem; color:#fff;">Admin</span>
                                    </div>
                                    <div class="p-1 rounded-2 w-100" id="prevWalletStrip" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);">
                                        <span class="fw-bold" id="prevWalletAmt" style="font-size:0.65rem;color:<?= htmlspecialchars($z_profile) ?>;">฿1,000,010</span>
                                    </div>
                                    <span class="badge w-100 py-0" id="prevAdminPortalBtn" style="font-size:0.52rem;background:linear-gradient(135deg,#fff,<?= htmlspecialchars($z_profile) ?>);color:#08090d;">หลังบ้าน</span>
                                </div>
                            </div>
                        </div>

                        <!-- Mini Live Feed & Purchase History Card Preview -->
                        <div class="p-2 rounded-3 mb-2 d-flex align-items-center gap-2" id="prevFeedCard" style="background:#090a0f; border:1px solid <?= htmlspecialchars($z_history) ?>33; border-left:3.5px solid <?= htmlspecialchars($z_history) ?>;">
                            <div class="position-relative" style="width:38px; height:38px; background:#141520; border-radius:6px; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
                                <i class="fa-solid fa-box-open text-muted" style="font-size:0.85rem;"></i>
                                <span class="position-absolute badge p-0 px-1" id="prevFeedBadge" style="bottom:-3px; right:-3px; background:<?= htmlspecialchars($z_history) ?>; color:<?= htmlspecialchars(get_contrast_color($z_history)) ?>; font-size:0.5rem; font-weight:800; border-radius:4px;">10 ฿</span>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div class="d-flex align-items-center gap-1">
                                    <span style="width:5px; height:5px; border-radius:50%; background:<?= htmlspecialchars($z_history) ?>; display:inline-block;" id="prevFeedDot"></span>
                                    <span class="fw-bold" id="prevFeedTag" style="font-size:0.55rem; color:<?= htmlspecialchars($z_history) ?>; letter-spacing:0.5px;">NEW ORDER</span>
                                </div>
                                <div class="text-white fw-bold text-truncate" style="font-size:0.65rem;">AIMDES ล็อคหัว คอม!</div>
                                <div class="text-muted" style="font-size:0.55rem;">10:05 • จำนวน 1 ชิ้น</div>
                            </div>
                        </div>

                        <!-- Footer mini elements -->
                        <div class="d-flex align-items-center justify-content-between gap-1 mt-2">
                            <div class="p-1 rounded-2 text-center flex-grow-1" id="prevDcBox" style="background:#090a10; border:1px solid <?= htmlspecialchars($z_community) ?>; font-size:0.6rem;">
                                <span class="fw-bold" id="prevDcTitle" style="color:<?= htmlspecialchars($z_community) ?>;">DISCORD COMMUNITY</span>
                            </div>
                            <div class="p-1 rounded-2 text-center" id="prevMusicDock" style="background:#090a10; border:1px solid <?= htmlspecialchars($z_music) ?>; font-size:0.55rem; color:<?= htmlspecialchars($z_music) ?>; padding:4px 10px !important;">
                                <i class="fa-solid fa-music"></i>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" id="prevFloatingDc" style="width:22px; height:22px; background:<?= htmlspecialchars($z_floating_dc) ?>; color:#08090d; font-size:0.65rem; flex-shrink:0;">
                                <i class="fa-brands fa-discord"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.preset-theme-card {
    background: #090a10;
    border: 1.5px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 16px 14px;
    cursor: pointer;
    text-align: center;
    position: relative;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.preset-theme-card:hover {
    transform: translateY(-3px);
    border-color: rgba(250, 204, 21, 0.4);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6);
}

.preset-theme-card.active {
    border-color: #facc15;
    background: radial-gradient(circle at top center, rgba(250, 204, 21, 0.12) 0%, #090a10 70%);
    box-shadow: 0 0 25px rgba(250, 204, 21, 0.25);
}

.preset-color-preview {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    margin: 0 auto 10px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    transition: transform 0.25s ease;
}

.preset-theme-card:hover .preset-color-preview {
    transform: scale(1.1);
}

.preset-theme-title {
    font-weight: 800;
    font-size: 0.85rem;
    color: #ffffff;
    font-family: var(--font-cyber);
    margin-bottom: 2px;
}

.preset-theme-desc {
    font-size: 0.7rem;
    color: #94a3b8;
    line-height: 1.2;
}

.preset-badge-active {
    display: none;
    position: absolute;
    top: 6px;
    right: 6px;
    background: #facc15;
    color: #08090d;
    font-size: 0.58rem;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 999px;
}

.preset-theme-card.active .preset-badge-active {
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

/* Zone Control Card */
.zone-control-card {
    background: #090a10;
    border: 1px solid rgba(255, 255, 255, 0.09);
    border-radius: 14px;
    padding: 12px 14px;
    transition: all 0.2s ease;
}

.zone-control-card:hover {
    border-color: rgba(250, 204, 21, 0.35);
    background: #0c0d15;
}

.zone-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    box-shadow: 0 0 8px currentColor;
}

.zone-name {
    font-weight: 700;
    font-size: 0.78rem;
    color: #f1f5f9;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.zone-swatches .swatch {
    width: 16px;
    height: 16px;
    border-radius: 4px;
    cursor: pointer;
    border: 1px solid rgba(255,255,255,0.3);
    transition: transform 0.15s ease;
}

.zone-swatches .swatch:hover {
    transform: scale(1.3);
    border-color: #ffffff;
}

/* Live Preview Stage */
.preview-navbar {
    background: #090a10;
    border: 1.5px solid #facc15;
    border-radius: 999px;
    padding: 4px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.25s ease;
}

.preview-prod-card {
    background: #090a10;
    border: 1.5px solid #facc15;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.25s ease;
}

.preview-orbit-wrap {
    position: relative;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-orbit-ring {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}

.preview-orbit-ring.ring-1 {
    width: 42px;
    height: 42px;
    border: 1px dashed #facc15;
    animation: rotate360 4s linear infinite;
}

.preview-orbit-core {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #08090d;
    border: 1px solid #facc15;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script>
const zonesList = [
    'marquee', 'navbar', 'banner', 'product', 'category', 'wheel', 'profile', 
    'warp', 'history', 'orbit', 'community', 'music', 'floating_dc', 'cursor', 
    'topup', 'modal', 'footer'
];

function applyGlobalPreset(presetKey, primaryHex, secondaryHex) {
    $('#theme_preset').val(presetKey);
    $('#glow_primary').val(primaryHex);
    $('#glow_secondary').val(secondaryHex);

    // Apply primaryHex to all 17 zones
    zonesList.forEach(z => {
        $(`#zone_${z}`).val(primaryHex);
        $(`#pick_${z}`).val(primaryHex);
        $(`#dot_${z}`).css('background', primaryHex);
    });

    // Apply to Core Accent Colors
    const coreAccents = ['color_lightning', 'color_border_beam', 'color_btn_primary'];
    coreAccents.forEach(c => {
        $(`#${c}`).val(primaryHex);
        $(`#pick_${c}`).val(primaryHex);
        $(`#dot_${c}`).css('background', primaryHex);
    });

    $('.preset-theme-card').removeClass('active');
    $(`.preset-theme-card[onclick*="'${presetKey}'"]`).addClass('active');

    updateLivePreview();
}

function setCustomZoneMode() {
    $('#theme_preset').val('custom');
    $('.preset-theme-card').removeClass('active');
    $('#presetCardCustom').addClass('active');
    updateLivePreview();
}

function syncZoneColor(zone, val) {
    $(`#zone_${zone}`).val(val);
    $(`#dot_${zone}`).css('background', val);
    setCustomZoneMode();
    updateLivePreview();
}

function syncZoneHex(zone, val) {
    if (val && val.startsWith('#') && val.length === 7) {
        $(`#pick_${zone}`).val(val);
        $(`#dot_${zone}`).css('background', val);
        setCustomZoneMode();
        updateLivePreview();
    }
}

function syncCoreColor(id, val) {
    $(`#${id}`).val(val);
    $(`#dot_${id}`).css('background', val);
    setCustomZoneMode();
    updateLivePreview();
}

function syncCoreHex(id, val) {
    if (val && val.startsWith('#') && val.length === 7) {
        $(`#pick_${id}`).val(val);
        $(`#dot_${id}`).css('background', val);
        setCustomZoneMode();
        updateLivePreview();
    }
}

function setZoneQuick(zone, colorHex) {
    $(`#zone_${zone}`).val(colorHex);
    $(`#pick_${zone}`).val(colorHex);
    $(`#dot_${zone}`).css('background', colorHex);
    setCustomZoneMode();
    updateLivePreview();
}

function onSpeedChange(val) {
    $('#speedBadge').text(val + 's');
    updateLivePreview();
}

function updateLivePreview() {
    const z_marquee     = $('#zone_marquee').val() || '#facc15';
    const z_navbar      = $('#zone_navbar').val() || '#facc15';
    const z_product     = $('#zone_product').val() || '#facc15';
    const z_wheel       = $('#zone_wheel').val() || '#facc15';
    const z_profile     = $('#zone_profile').val() || '#facc15';
    const z_warp        = $('#zone_warp').val() || '#facc15';
    const z_history     = $('#zone_history').val() || '#facc15';
    const z_orbit       = $('#zone_orbit').val() || '#facc15';
    const z_community   = $('#zone_community').val() || '#facc15';
    const z_music       = $('#zone_music').val() || '#facc15';
    const z_floating_dc = $('#zone_floating_dc').val() || '#facc15';
    const speed         = $('#beam_speed').val() || '4.5';

    // Contrast Helper
    const getContrast = (hex) => {
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
        const r = parseInt(hex.substr(0,2),16)||0;
        const g = parseInt(hex.substr(2,2),16)||0;
        const b = parseInt(hex.substr(4,2),16)||0;
        const lum = (0.299*r + 0.587*g + 0.114*b)/255;
        return lum > 0.55 ? '#08090d' : '#ffffff';
    };

    // 1. Marquee
    $('#prevMarquee').css('background', z_marquee);

    // 2. Navbar
    $('#prevNavbar').css({ 'border-color': z_navbar, 'box-shadow': `0 0 12px ${z_navbar}55` });
    $('#prevNavPill').css('background', z_navbar);

    // 3. Product
    $('#prevCard').css({ 'border-color': z_product, 'box-shadow': `0 0 15px ${z_product}44` });
    $('#prevPrice').css('color', z_product);

    // 4. Lucky Wheel
    $('#prevWheelBox').css({ 'border-color': z_wheel, 'box-shadow': `0 0 15px ${z_wheel}44` });
    $('#prevWheelDisc').css({ 'border-color': z_wheel, 'box-shadow': `0 0 10px ${z_wheel}88` });
    $('#prevWheelSpinText').css('color', z_wheel);
    $('#prevWheelBtn').css('background', z_wheel);

    // 5. User Profile & Balance Modal
    const c_profile = getContrast(z_profile);
    $('#prevNavBalance').css({ 'border-color': z_profile, 'color': z_profile, 'box-shadow': `0 0 10px ${z_profile}55` });
    $('#prevProfileModal').css({ 'border-color': z_profile, 'box-shadow': `0 0 18px ${z_profile}44` });
    $('#prevAvatarCore').css({ 'background': z_profile, 'border-color': 'rgba(255,255,255,0.4)', 'color': c_profile, 'box-shadow': `0 0 8px ${z_profile}88` });
    $('#prevWalletAmt').css('color', z_profile);
    $('#prevAdminPortalBtn').css({ 'background': `linear-gradient(135deg, #ffffff 0%, ${z_profile} 100%)` });

    // 6. Cyber Warp FX
    const c_warp = getContrast(z_warp);
    $('#prevWarpBox').css({ 'border-color': z_warp, 'box-shadow': `0 0 15px ${z_warp}55` });
    $('#prevWarpEmblem').css({ 'border-color': z_warp, 'box-shadow': `0 0 12px ${z_warp}88` });
    $('#prevWarpBolt').css({ 'color': z_warp });
    $('#prevWarpBadge').css({ 'background': z_warp, 'color': c_warp });

    // 7. Recent Purchases & Live Feed Cards
    const c_history = getContrast(z_history);
    $('#prevFeedCard').css({ 'border-color': `${z_history}33`, 'border-left-color': z_history, 'box-shadow': `0 4px 15px ${z_history}22` });
    $('#prevFeedBadge').css({ 'background': z_history, 'color': c_history });
    $('#prevFeedDot').css({ 'background': z_history, 'box-shadow': `0 0 8px ${z_history}` });
    $('#prevFeedTag').css({ 'color': z_history });

    // 8. Orbit
    $('#prevOrbit .ring-1').css({ 'border-color': z_orbit, 'animation-duration': `${speed}s` });
    $('#prevOrbit .preview-orbit-core').css({ 'border-color': z_orbit, 'box-shadow': `0 0 10px ${z_orbit}88` });
    $('#prevCrown').css('color', z_orbit);

    // 9. Community Box
    $('#prevDcBox').css({ 'border-color': z_community, 'box-shadow': `0 0 12px ${z_community}44` });
    $('#prevDcTitle').css('color', z_community);

    // 10. Music
    $('#prevMusicDock').css({ 'border-color': z_music, 'color': z_music });

    // 11. Floating DC
    $('#prevFloatingDc').css({ 'background': z_floating_dc, 'box-shadow': `0 0 12px ${z_floating_dc}88` });
}

$(document).ready(function() {
    updateLivePreview();

    $('#btnSaveTheme').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังบันทึก...');

        $.ajax({
            type: 'POST',
            url: 'system/theme_save.php',
            data: $('#themeForm').serialize(),
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการเปลี่ยนแปลง');
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: res.message || 'บันทึกการตั้งค่าแสงไฟและสีธีมเรียบร้อยแล้ว',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: res.message || 'ไม่สามารถบันทึกข้อมูลได้'
                    });
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการเปลี่ยนแปลง');
                let errMsg = 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้';
                try {
                    if (xhr.responseText) {
                        const parsed = JSON.parse(xhr.responseText);
                        if (parsed.message) errMsg = parsed.message;
                    }
                } catch(e) {}
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด',
                    text: errMsg
                });
            }
        });
    });
});
</script>
