<?php
$q_conf = dd_q("SELECT * FROM setting LIMIT 1");
$config = $q_conf ? $q_conf->fetch(PDO::FETCH_ASSOC) : [];
$discord_server_id = $config['discord_server_id'] ?? '';
?>
<div class="p-4 p-lg-5" data-aos="fade-up">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 pb-3 border-bottom border-secondary border-opacity-25">
        <div>
            <div class="section-eyebrow" style="color:#38bdf8;">// CONTACT & SOCIAL MEDIA MANAGEMENT</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-address-book text-info me-2"></i> จัดการช่องทางติดต่อ & โซเชียลมีเดีย (Contact & Discord Widget)
            </h3>
            <p class="text-muted small mb-0">ตั้งค่าลิงก์โซเชียลมีเดีย (Discord, TikTok, YouTube), ข้อมูลติดต่อ และวิดเจ็ตเซิร์ฟเวอร์ Discord</p>
        </div>
        <button class="btn btn-primary fw-bold px-4 py-2" id="save_contact_btn" style="font-family:var(--font-cyber); box-shadow:0 0 20px rgba(56,189,248,0.4);">
            <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกข้อมูล
        </button>
    </div>

    <div class="row g-4">
        <!-- Column 1: Social Links & Contact Channels -->
        <div class="col-12 col-lg-7">
            <!-- 1. Social Channels -->
            <div class="admin-inner-card mb-4">
                <h6 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-share-nodes text-warning me-2"></i> 1. ช่องทางโซเชียลมีเดียหลัก (Social Links)
                </h6>

                <!-- Discord Invite URL -->
                <div class="mb-3 p-3 rounded-3" style="background:rgba(88,101,242,0.08);border:1px solid rgba(88,101,242,0.25);">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:44px;height:44px;background:rgba(88,101,242,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#5865f2;font-size:1.3rem;flex-shrink:0;">
                            <i class="fa-brands fa-discord"></i>
                        </div>
                        <div class="flex-grow-1">
                            <label class="fw-bold mb-1 d-block" style="color:#5865f2;font-size:0.85rem;font-family:var(--font-cyber);">Discord Invite URL</label>
                            <input type="text" id="contact_discord" class="admin-input form-control" placeholder="https://discord.gg/xxxxxx" value="<?php echo htmlspecialchars($config['contact_discord'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- TikTok URL -->
                <div class="mb-3 p-3 rounded-3" style="background:rgba(255,0,80,0.08);border:1px solid rgba(255,0,80,0.25);">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:44px;height:44px;background:rgba(255,0,80,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#ff0050;font-size:1.3rem;flex-shrink:0;">
                            <i class="fa-brands fa-tiktok"></i>
                        </div>
                        <div class="flex-grow-1">
                            <label class="fw-bold mb-1 d-block" style="color:#ff0050;font-size:0.85rem;font-family:var(--font-cyber);">TikTok URL</label>
                            <input type="text" id="contact_tiktok" class="admin-input form-control" placeholder="https://www.tiktok.com/@youraccount" value="<?php echo htmlspecialchars($config['contact_tiktok'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- YouTube / Facebook URL -->
                <div class="mb-0 p-3 rounded-3" style="background:rgba(255,0,0,0.08);border:1px solid rgba(255,0,0,0.25);">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:44px;height:44px;background:rgba(255,0,0,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#ff4444;font-size:1.3rem;flex-shrink:0;">
                            <i class="fa-brands fa-youtube"></i>
                        </div>
                        <div class="flex-grow-1">
                            <label class="fw-bold mb-1 d-block" style="color:#ff4444;font-size:0.85rem;font-family:var(--font-cyber);">YouTube / Facebook URL</label>
                            <input type="text" id="contact_facebook" class="admin-input form-control" placeholder="https://www.youtube.com/@channel หรือ Facebook URL" value="<?php echo htmlspecialchars($config['contact_facebook'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Other Contact Channels -->
            <div class="admin-inner-card">
                <h6 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-phone text-success me-2"></i> 2. เบอร์โทร & ช่องทางติดต่ออื่น (Direct Contacts)
                </h6>

                <!-- Phone -->
                <div class="mb-3 p-3 rounded-3" style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.25);">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:44px;height:44px;background:rgba(34,197,94,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#22c55e;font-size:1.3rem;flex-shrink:0;">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="flex-grow-1">
                            <label class="fw-bold mb-1 d-block" style="color:#22c55e;font-size:0.85rem;font-family:var(--font-cyber);">Phone / เบอร์โทรศัพท์ / พร้อมเพย์</label>
                            <input type="text" id="contact_phone" class="admin-input form-control" placeholder="0812345678" value="<?php echo htmlspecialchars($config['contact_phone'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Line -->
                <div class="mb-3 p-3 rounded-3" style="background:rgba(6,199,85,0.08);border:1px solid rgba(6,199,85,0.25);">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:44px;height:44px;background:rgba(6,199,85,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#06c755;font-size:1.3rem;flex-shrink:0;">
                            <i class="fa-brands fa-line"></i>
                        </div>
                        <div class="flex-grow-1">
                            <label class="fw-bold mb-1 d-block" style="color:#06c755;font-size:0.85rem;font-family:var(--font-cyber);">Line ID / บัญชีไลน์</label>
                            <input type="text" id="contact_line" class="admin-input form-control" placeholder="@lineid หรือชื่อบัญชี" value="<?php echo htmlspecialchars($config['contact_line'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Custom Channel -->
                <div class="p-3 rounded-3" style="background:rgba(106,128,160,0.08);border:1px solid rgba(106,128,160,0.25);">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:44px;height:44px;background:rgba(106,128,160,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#93c5fd;font-size:1.3rem;flex-shrink:0;">
                            <i class="fa-solid fa-link"></i>
                        </div>
                        <div class="flex-grow-1">
                            <label class="fw-bold mb-1 d-block text-info" style="font-size:0.85rem;font-family:var(--font-cyber);">Custom Channel (ช่องทางเพิ่มเติม)</label>
                            <div class="row g-2">
                                <div class="col-5">
                                    <input type="text" id="contact_other_label" class="admin-input form-control" placeholder="ชื่อช่องทาง (เช่น Instagram)" value="<?php echo htmlspecialchars($config['contact_other_label'] ?? ''); ?>">
                                </div>
                                <div class="col-7">
                                    <input type="text" id="contact_other_url" class="admin-input form-control" placeholder="URL หรือข้อความ" value="<?php echo htmlspecialchars($config['contact_other_url'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 2: Discord Server Widget & Live Preview -->
        <div class="col-12 col-lg-5">
            <div class="admin-inner-card h-100">
                <h6 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
                    <i class="fa-brands fa-discord text-primary me-2"></i> 3. วิดเจ็ตเซิร์ฟเวอร์ Discord (Server Widget)
                </h6>

                <div class="mb-3">
                    <label class="admin-label">ID เซิร์ฟเวอร์ Discord (Server ID หรือ Widget API URL)</label>
                    <input type="text" id="discord_server_id" class="admin-input form-control font-monospace" value="<?= htmlspecialchars($discord_server_id) ?>" placeholder="ตัวอย่าง 1493946116230615197 (ปล่อยว่างหากไม่ต้องการแสดงวิดเจ็ต)" oninput="refreshAdminDiscordWidget()">
                    <small class="text-muted d-block mt-1" style="font-size:0.75rem;">
                        นำมาจาก: <strong>Discord > Server Settings > Widget > Enable Widget > คัดลอก Server ID</strong> (หากไม่ต้องการแสดงวิดเจ็ตให้ลบช่องนี้ให้ว่างแล้วกดบันทึก)
                    </small>
                </div>

                <div class="p-3 rounded-3 mb-3" style="background:#050609; border:1px solid rgba(255,255,255,0.06);">
                    <div class="admin-label mb-2 text-start">ตัวอย่างวิดเจ็ตหน้าร้าน (Live Preview)</div>
                    
                    <div class="discord-widget-card" style="background:#2b2d31;border-radius:12px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.8);border:1px solid rgba(255,255,255,0.08);max-width:380px;margin:0 auto;">
                        <div class="discord-widget-header" style="background:#5865F2;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;color:#ffffff;font-size:0.85rem;font-weight:700;">
                            <span><i class="fa-brands fa-discord me-2"></i> <span id="admin_dc_online_count">...</span> Members Online</span>
                            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#22c55e;box-shadow:0 0 6px #22c55e;"></span>
                        </div>
                        <div class="px-3 pt-2 pb-1" style="font-size:0.7rem;font-weight:700;color:#949ba4;letter-spacing:0.05em;" id="admin_dc_sub_count">
                            ออนไลน์ — ...
                        </div>
                        <div class="discord-widget-users" id="admin_dc_user_list" style="max-height:160px;overflow-y:auto;padding:0 12px 6px;">
                            <div class="text-center py-3 text-muted small"><i class="fa-solid fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</div>
                        </div>
                        <div class="px-3 py-1 text-center" id="admin_dc_remaining" style="background:#232428;font-size:0.65rem;color:#80848e;border-top:1px solid rgba(255,255,255,0.04);">
                            (Discord แสดงสูงสุด 100 คนใน Widget)
                        </div>
                        <div class="discord-widget-footer" style="background:#1e1f22;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;font-size:0.75rem;color:#949ba4;">
                            <span>Hangout with people who get it</span>
                            <a href="javascript:void(0)" id="admin_dc_join_btn" target="_blank" class="btn-discord-join-sm" style="background:#5865F2;color:#ffffff;font-weight:700;font-size:0.72rem;padding:4px 10px;border-radius:4px;text-decoration:none;">Join Discord</a>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-3" style="background:rgba(56,189,248,0.05);border:1px solid rgba(56,189,248,0.15);">
                    <h6 class="fw-bold text-info small mb-1"><i class="fa-solid fa-circle-info me-1"></i> คำแนะนำ</h6>
                    <p class="text-muted small mb-0" style="line-height:1.5;">ช่องทางใดที่ปล่อยว่างไว้ ระบบจะไม่นำไปแสดงในหน้า ติดต่อเรา และวิดเจ็ตหน้าร้านครับ</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$("#save_contact_btn").click(function () {
    const btn = $(this);
    btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังบันทึก...');

    var formData = new FormData();
    formData.append('name', "<?php echo htmlspecialchars($config['name'] ?? 'CYGNUSX STORE'); ?>");
    formData.append('logo', "<?php echo htmlspecialchars($config['logo'] ?? ''); ?>");
    formData.append('des', "<?php echo htmlspecialchars($config['des'] ?? ''); ?>");
    formData.append('ann', "<?php echo htmlspecialchars($config['ann'] ?? ''); ?>");
    formData.append('phone', "<?php echo htmlspecialchars($config['wallet'] ?? ''); ?>");
    formData.append('bank_name', "<?php echo htmlspecialchars($config['bank_name'] ?? 'กสิกรไทย'); ?>");
    formData.append('webhook_dc', "<?php echo htmlspecialchars($config['webhook_dc'] ?? ''); ?>");
    formData.append('slip_api_type', "<?php echo htmlspecialchars($config['slip_api_type'] ?? 'slipok'); ?>");
    formData.append('slip_api_key', "<?php echo htmlspecialchars($config['slip_api_key'] ?? ''); ?>");
    formData.append('slip_api_branch', "<?php echo htmlspecialchars($config['slip_api_branch'] ?? ''); ?>");
    formData.append('bg_music_url', "<?php echo htmlspecialchars($config['bg_music_url'] ?? ''); ?>");
    formData.append('bg_music_auto', "<?php echo htmlspecialchars($config['bg_music_auto'] ?? 'on'); ?>");
    
    // Contacts & Discord Widget
    formData.append('contact_discord', $("#contact_discord").val());
    formData.append('contact_facebook', $("#contact_facebook").val());
    formData.append('contact_tiktok', $("#contact_tiktok").val());
    formData.append('contact_phone', $("#contact_phone").val());
    formData.append('contact_line', $("#contact_line").val());
    formData.append('contact_other_label', $("#contact_other_label").val());
    formData.append('contact_other_url', $("#contact_other_url").val());
    formData.append('discord_server_id', $("#discord_server_id").val());

    $.ajax({
        type: 'POST',
        url: 'system/backend/website.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i> บันทึกข้อมูล');
            Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ!',
                text: res.message || 'บันทึกข้อมูลช่องทางติดต่อเรียบร้อยแล้ว',
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#4b5d78'
            }).then(() => {
                window.location.reload();
            });
        },
        error: function (err) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i> บันทึกข้อมูล');
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

// Run discord widget preview on load
let _dcAdminDebounce = null;
function refreshAdminDiscordWidget() {
    clearTimeout(_dcAdminDebounce);
    _dcAdminDebounce = setTimeout(() => {
        const rawId = $("#discord_server_id").val().trim();
        if (!rawId) {
            $("#admin_dc_online_count").text('0');
            $("#admin_dc_sub_count").text('ออนไลน์ — 0');
            $("#admin_dc_user_list").html('<div class="text-center py-3 text-muted small">กรอก Server ID เพื่อแสดงตัวอย่าง</div>');
            return;
        }

        $.ajax({
            type: 'GET',
            url: 'system/discord_widget_proxy.php',
            data: { server_id: rawId },
            dataType: 'json',
            success: function(data) {
                if (!data) return;
                const presence = data.presence_count || (data.members ? data.members.length : 0) || 0;
                $("#admin_dc_online_count").text(presence);
                $("#admin_dc_sub_count").text(`ออนไลน์ — ${presence}`);

                if (data.instant_invite) {
                    $("#admin_dc_join_btn").attr('href', data.instant_invite);
                }

                const members = data.members || [];
                let html = '';
                members.slice(0, 8).forEach(m => {
                    const avatar = m.avatar_url || 'https://cdn.discordapp.com/embed/avatars/0.png';
                    const name = m.username || 'Discord User';
                    const game = m.game ? `<div style="font-size:0.62rem;color:#949ba4;">${m.game.name}</div>` : '';
                    html += `
                        <div class="discord-widget-user-item" style="display:flex;align-items:center;gap:8px;padding:4px 0;border-bottom:1px solid rgba(255,255,255,0.03);">
                            <img src="${avatar}" style="width:26px;height:26px;border-radius:50%;object-fit:cover;">
                            <div>
                                <div style="font-size:0.75rem;color:#dbdee1;font-weight:600;">${name}</div>
                                ${game}
                            </div>
                        </div>
                    `;
                });
                $("#admin_dc_user_list").html(html || '<div class="text-center py-2 text-muted small">ไม่มีผู้ใช้ออนไลน์ในขณะนี้</div>');
                
                const extra = Math.max(0, presence - members.length);
                if (extra > 0) {
                    $("#admin_dc_remaining").text(`และอีก ${extra} คน (Discord แสดงสูงสุด 100 คนใน Widget)`);
                } else {
                    $("#admin_dc_remaining").text(`(Discord แสดงสูงสุด 100 คนใน Widget)`);
                }
            }
        });
    }, 300);
}

$(document).ready(function() {
    refreshAdminDiscordWidget();
});
</script>
