<?php
// Contact Page (Dynamic Multi-Channel Integration)
$contact_discord = !empty($config['contact_discord']) ? $config['contact_discord'] : '';
$contact_tiktok = !empty($config['contact_tiktok']) ? $config['contact_tiktok'] : '';
$contact_facebook = !empty($config['contact_facebook']) ? $config['contact_facebook'] : '';
$contact_line = !empty($config['contact_line']) ? $config['contact_line'] : '';
$contact_other_label = !empty($config['contact_other_label']) ? $config['contact_other_label'] : '';
$contact_other_url = !empty($config['contact_other_url']) ? $config['contact_other_url'] : '';
?>

<div class="store-contact-page py-4" data-aos="fade-up" style="max-width: 900px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="text-center mb-4">
        <div class="section-eyebrow" style="letter-spacing: 3px;">// ติดต่อเรา</div>
        <h1 class="section-main-heading fw-bold mb-2" style="font-family: var(--font-cyber); font-size: 2.8rem; letter-spacing: 2px;">
            CONNECT
        </h1>
        <div class="d-inline-block px-3 py-1 mb-2" style="font-family: var(--font-cyber); font-size: 0.78rem; letter-spacing: 2px; color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 6px; background: rgba(255, 255, 255, 0.02);">
            <?= htmlspecialchars($site_name) ?>
        </div>
        <p class="section-subtext" style="color: #94a3b8; font-size: 0.9rem;">
            ติดตามและทักเราได้ทุกช่องทาง — ทีมงานพร้อมช่วยเหลือคุณ 24/7
        </p>
    </div>

    <!-- Contact Channels 2-Column Grid -->
    <div class="contact-page-grid">
        <!-- 1. หน้าแรกเว็บไซต์ -->
        <a href="?page=home" class="contact-channel-card contact-website">
            <div class="contact-channel-glow"></div>
            <div class="contact-channel-icon" style="background: rgba(168, 85, 247, 0.12); border-color: rgba(168, 85, 247, 0.3); color: #c084fc;">
                <i class="fa-solid fa-globe fs-5"></i>
            </div>
            <div class="contact-channel-body">
                <span class="contact-channel-label"><?= htmlspecialchars($site_name) ?></span>
                <span class="contact-channel-hint">หน้าร้านหลัก</span>
            </div>
            <div class="contact-channel-arrow">↗</div>
        </a>

        <!-- 2. Discord -->
        <?php if (!empty($contact_discord)): ?>
        <a href="<?= htmlspecialchars($contact_discord) ?>" target="_blank" class="contact-channel-card contact-discord">
            <div class="contact-channel-glow"></div>
            <div class="contact-channel-icon" style="background: rgba(88, 101, 242, 0.15); border-color: rgba(88, 101, 242, 0.35); color: #5865F2;">
                <i class="fa-brands fa-discord fs-5"></i>
            </div>
            <div class="contact-channel-body">
                <span class="contact-channel-label">Discord</span>
                <span class="contact-channel-hint">แชทกับทีมงาน / ซัพพอร์ต</span>
            </div>
            <div class="contact-channel-arrow">↗</div>
        </a>
        <?php endif; ?>

        <!-- 3. Facebook -->
        <?php if (!empty($contact_facebook)): ?>
        <a href="<?= htmlspecialchars($contact_facebook) ?>" target="_blank" class="contact-channel-card contact-facebook">
            <div class="contact-channel-glow"></div>
            <div class="contact-channel-icon" style="background: rgba(24, 119, 242, 0.15); border-color: rgba(24, 119, 242, 0.35); color: #1877f2;">
                <i class="fa-brands fa-facebook fs-5"></i>
            </div>
            <div class="contact-channel-body">
                <span class="contact-channel-label">Facebook</span>
                <span class="contact-channel-hint">ติดตามข่าวสาร & โปรโมชั่น</span>
            </div>
            <div class="contact-channel-arrow">↗</div>
        </a>
        <?php endif; ?>

        <!-- 4. TikTok -->
        <?php if (!empty($contact_tiktok)): ?>
        <a href="<?= htmlspecialchars($contact_tiktok) ?>" target="_blank" class="contact-channel-card contact-tiktok">
            <div class="contact-channel-glow"></div>
            <div class="contact-channel-icon" style="background: rgba(34, 211, 238, 0.12); border-color: rgba(34, 211, 238, 0.3); color: #22d3ee;">
                <i class="fa-brands fa-tiktok fs-5"></i>
            </div>
            <div class="contact-channel-body">
                <span class="contact-channel-label">TikTok</span>
                <span class="contact-channel-hint">คลิปสั้น & ไฮไลท์</span>
            </div>
            <div class="contact-channel-arrow">↗</div>
        </a>
        <?php endif; ?>

        <!-- 5. ช่องทางอื่นๆ -->
        <?php if (!empty($contact_other_url)): ?>
        <a href="<?= htmlspecialchars($contact_other_url) ?>" target="_blank" class="contact-channel-card contact-youtube">
            <div class="contact-channel-glow"></div>
            <div class="contact-channel-icon" style="background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: #ef4444;">
                <i class="fa-solid fa-link fs-5"></i>
            </div>
            <div class="contact-channel-body">
                <span class="contact-channel-label"><?= htmlspecialchars($contact_other_label ?: 'ช่องทางติดต่อเพิ่มเติม') ?></span>
                <span class="contact-channel-hint">คลิกเพื่อเปิดลิงก์</span>
            </div>
            <div class="contact-channel-arrow">↗</div>
        </a>
        <?php endif; ?>
    </div>
</div>
