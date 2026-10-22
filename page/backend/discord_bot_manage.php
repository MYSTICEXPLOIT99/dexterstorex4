<?php
// 🤖 Admin Discord Bot Hosting & Slot Management (CYGNUSX STORE)
if (($user['rank'] ?? '0') != '1') {
    echo "<div class='text-center text-white p-5'><h4>คุณไม่มีสิทธิ์เข้าถึงหน้านี้</h4></div>";
    return;
}

// Handle Package Price Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_bot_package'])) {
    $base_price  = floatval($_POST['base_price'] ?? 50.00);
    $disk_mb     = intval($_POST['disk_mb'] ?? 1024);
    $addon_gb    = floatval($_POST['addon_storage_gb_price'] ?? 20.00);
    $renewal_7d  = floatval($_POST['renewal_7d'] ?? 15.00);
    $renewal_15d = floatval($_POST['renewal_15d'] ?? 30.00);
    $renewal_30d = floatval($_POST['renewal_30d'] ?? 50.00);

    $upd = $conn->prepare("UPDATE discord_bot_packages SET price = ?, disk_mb = ?, addon_storage_gb_price = ?, renewal_7d = ?, renewal_15d = ?, renewal_30d = ? WHERE id = 1");
    $upd->execute([$base_price, $disk_mb, $addon_gb, $renewal_7d, $renewal_15d, $renewal_30d]);
    echo "<script>Swal.fire({icon:'success',title:'สำเร็จ',text:'บันทึกการตั้งค่าแพ็กเกจเช่าบอทเรียบร้อยแล้ว',background:'#0a0a0f',color:'#fff'}).then(()=>{location.href='?page=backend&menu=discord_bot_manage';});</script>";
}

// Handle Admin Delete Bot
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_delete_bot'])) {
    $del_id = intval($_POST['del_id'] ?? 0);
    $del_stmt = $conn->prepare("SELECT * FROM discord_bot_rentals WHERE id = ? LIMIT 1");
    $del_stmt->execute([$del_id]);
    $del_bot = $del_stmt->fetch(PDO::FETCH_ASSOC);

    if ($del_bot) {
        // Stop process
        if (!empty($del_bot['pid'])) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                @exec("taskkill /F /T /PID {$del_bot['pid']} 2>&1");
            } else {
                @exec("kill -9 {$del_bot['pid']} 2>&1");
            }
        }
        // Delete folder
        $slot_dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'bots' . DIRECTORY_SEPARATOR . $del_bot['slot_key'];
        if (file_exists($slot_dir)) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($slot_dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                @$todo($fileinfo->getRealPath());
            }
            @rmdir($slot_dir);
        }
        $conn->prepare("DELETE FROM discord_bot_rentals WHERE id = ?")->execute([$del_id]);
        echo "<script>Swal.fire({icon:'success',title:'สำเร็จ',text:'ลบสล็อตบอทเรียบร้อยแล้ว',background:'#0a0a0f',color:'#fff'}).then(()=>{location.href='?page=backend&menu=discord_bot_manage';});</script>";
    }
}

// Fetch Package Settings
$pkg_stmt = $conn->query("SELECT * FROM discord_bot_packages LIMIT 1");
$pkg = $pkg_stmt ? $pkg_stmt->fetch(PDO::FETCH_ASSOC) : [
    'price' => 50.00,
    'disk_mb' => 1024,
    'addon_storage_gb_price' => 20.00,
    'renewal_7d' => 15.00,
    'renewal_15d' => 30.00,
    'renewal_30d' => 50.00
];

// Fetch All Bots
$all_bots_q = $conn->query("SELECT r.*, u.username FROM discord_bot_rentals r LEFT JOIN users u ON r.user_id = u.id ORDER BY r.id DESC");
$all_bots = $all_bots_q ? $all_bots_q->fetchAll(PDO::FETCH_ASSOC) : [];

$total_bots = count($all_bots);
$active_bots = 0;
foreach ($all_bots as $b) {
    if (strtotime($b['expire_date']) >= time()) $active_bots++;
}
?>

<div class="p-4 p-lg-5" data-aos="fade-up">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="section-eyebrow" style="letter-spacing:3px;">// DISCORD BOT MANAGEMENT</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-robot text-primary me-2"></i> จัดการระบบเช่าบอทดิสคอร์ส (Discord Bot Hosting)
            </h3>
            <p class="text-muted small mb-0">ตั้งราคาแพ็กเกจ, ตรวจสอบสถานะการรันบอทของลูกค้าทั้งหมด, และจัดการสล็อตบอทในระบบ</p>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         1. OVERVIEW STATISTICS
    ══════════════════════════════════════════ -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="admin-inner-card p-3 d-flex align-items-center gap-3" style="background:#090a12;border-left:4px solid #38bdf8;">
                <div style="width:48px;height:48px;border-radius:12px;background:rgba(56,189,248,0.15);display:flex;align-items:center;justify-content:center;color:#38bdf8;font-size:1.5rem;">
                    <i class="fa-solid fa-server"></i>
                </div>
                <div>
                    <div class="text-muted small">สล็อตบอททั้งหมดในระบบ</div>
                    <div class="fw-bold text-white fs-4" style="font-family:var(--font-cyber);"><?= number_format($total_bots) ?> ตัว</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="admin-inner-card p-3 d-flex align-items-center gap-3" style="background:#090a12;border-left:4px solid #22c55e;">
                <div style="width:48px;height:48px;border-radius:12px;background:rgba(34,197,94,0.15);display:flex;align-items:center;justify-content:center;color:#22c55e;font-size:1.5rem;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <div class="text-muted small">บอทที่ยังไม่หมดอายุ (Active)</div>
                    <div class="fw-bold text-success fs-4" style="font-family:var(--font-cyber);"><?= number_format($active_bots) ?> ตัว</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="admin-inner-card p-3 d-flex align-items-center gap-3" style="background:#090a12;border-left:4px solid #facc15;">
                <div style="width:48px;height:48px;border-radius:12px;background:rgba(250,204,21,0.15);display:flex;align-items:center;justify-content:center;color:#facc15;font-size:1.5rem;">
                    <i class="fa-solid fa-hard-drive"></i>
                </div>
                <div>
                    <div class="text-muted small">ราคาเช่าเริ่มต้น / 30 วัน</div>
                    <div class="fw-bold text-warning fs-4" style="font-family:var(--font-cyber);">฿<?= number_format($pkg['price'], 2) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         2. PACKAGE PRICING CONFIGURATION
    ══════════════════════════════════════════ -->
    <div class="admin-inner-card p-4 mb-4" style="background:#080912;border:1px solid rgba(255,255,255,0.08);border-radius:18px;">
        <h5 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
            <i class="fa-solid fa-sliders text-info me-2"></i> ตั้งค่าราคาแพ็กเกจและ Addon พื้นที่
        </h5>

        <form method="POST" class="row g-3">
            <input type="hidden" name="update_bot_package" value="1">
            <div class="col-md-4">
                <label class="form-label text-white small fw-bold">ราคาเช่าเริ่มต้น (30 วัน) [บาท]</label>
                <input type="number" step="any" name="base_price" class="form-control bg-dark border-secondary text-white fw-bold" value="<?= htmlspecialchars($pkg['price']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label text-white small fw-bold">พื้นที่เริ่มต้น (MB) [1024 = 1GB]</label>
                <input type="number" name="disk_mb" class="form-control bg-dark border-secondary text-white fw-bold" value="<?= htmlspecialchars($pkg['disk_mb']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label text-white small fw-bold">ราคาขยายพื้นที่ต่อ +1 GB (บาท)</label>
                <input type="number" step="any" name="addon_storage_gb_price" class="form-control bg-dark border-secondary text-white fw-bold" value="<?= htmlspecialchars($pkg['addon_storage_gb_price']) ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label text-white small fw-bold">ราคาต่ออายุ 7 วัน (บาท)</label>
                <input type="number" step="any" name="renewal_7d" class="form-control bg-dark border-secondary text-white" value="<?= htmlspecialchars($pkg['renewal_7d']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label text-white small fw-bold">ราคาต่ออายุ 15 วัน (บาท)</label>
                <input type="number" step="any" name="renewal_15d" class="form-control bg-dark border-secondary text-white" value="<?= htmlspecialchars($pkg['renewal_15d']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label text-white small fw-bold">ราคาต่ออายุ 30 วัน (บาท)</label>
                <input type="number" step="any" name="renewal_30d" class="form-control bg-dark border-secondary text-white" value="<?= htmlspecialchars($pkg['renewal_30d']) ?>" required>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-cyber-primary fw-bold px-4 py-2.5">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการตั้งค่าราคา
                </button>
            </div>
        </form>
    </div>

    <!-- ══════════════════════════════════════════
         3. ALL BOT INSTANCES TABLE
    ══════════════════════════════════════════ -->
    <div class="admin-inner-card p-4" style="background:#080912;border:1px solid rgba(255,255,255,0.08);border-radius:18px;">
        <h5 class="fw-bold text-white mb-3" style="font-family:var(--font-cyber);">
            <i class="fa-solid fa-list-check text-warning me-2"></i> รายการบอทของลูกค้าทั้งหมด
        </h5>

        <div class="table-responsive rounded-3" style="border:1px solid rgba(255,255,255,0.08);">
            <table class="table table-dark table-hover mb-0 align-middle" style="white-space:nowrap;">
                <thead>
                    <tr class="text-muted small" style="background:#040508;">
                        <th>ID</th>
                        <th>ผู้ใช้งาน (User)</th>
                        <th>ชื่อบอท (Bot Name)</th>
                        <th>สล็อต (Slot)</th>
                        <th>ภาษา (Runtime)</th>
                        <th>พื้นที่ (Limit)</th>
                        <th>สถานะ</th>
                        <th>วันหมดอายุ</th>
                        <th class="text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($all_bots)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">ยังไม่มีลูกค้าสั่งเช่าบอทในระบบ</td></tr>
                    <?php else: ?>
                        <?php foreach ($all_bots as $b): 
                            $is_exp = (strtotime($b['expire_date']) < time());
                            $st_badge = $is_exp ? '<span class="badge bg-danger">หมดอายุ</span>' : (($b['status'] == 'running') ? '<span class="badge bg-success">ONLINE</span>' : '<span class="badge bg-secondary">STOPPED</span>');
                        ?>
                        <tr>
                            <td>#<?= htmlspecialchars($b['id']) ?></td>
                            <td class="fw-bold text-info"><i class="fa-solid fa-user me-1"></i> <?= htmlspecialchars($b['username'] ?? 'User #' . $b['user_id']) ?></td>
                            <td class="fw-bold text-white"><?= htmlspecialchars($b['bot_name']) ?></td>
                            <td><code><?= htmlspecialchars($b['slot_key']) ?></code></td>
                            <td><span class="badge bg-dark border border-secondary"><?= strtoupper($b['runtime']) ?></span></td>
                            <td><?= number_format($b['disk_limit_mb']) ?> MB</td>
                            <td><?= $st_badge ?></td>
                            <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($b['expire_date'])) ?></td>
                            <td class="text-end">
                                <form id="delete_form_<?= $b['id'] ?>" method="POST" class="d-inline">
                                    <input type="hidden" name="admin_delete_bot" value="1">
                                    <input type="hidden" name="del_id" value="<?= $b['id'] ?>">
                                    <button type="button" onclick="confirmDeleteBot(<?= $b['id'] ?>, '<?= htmlspecialchars(addslashes($b['bot_name'])) ?>')" class="btn btn-sm btn-danger" style="border-radius:8px;" title="ลบสล็อตบอท">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDeleteBot(botId, botName) {
    Swal.fire({
        title: 'ยืนยันการลบสล็อตบอท?',
        html: `คุณต้องการลบสล็อตบอท <strong>"${botName}"</strong> (ID: #${botId}) ใช่หรือไม่?<br><small class="text-danger mt-2 d-block">⚠️ ข้อมูลโค้ด, ไฟล์ และโปรเซสของบอททั้งหมดจะถูกลบถาวร</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#4b5563',
        confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> ยืนยันการลบ',
        cancelButtonText: 'ยกเลิก',
        background: '#0a0a0f',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'กำลังลบสล็อตบอท...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); },
                background: '#0a0a0f',
                color: '#ffffff'
            });
            document.getElementById(`delete_form_${botId}`).submit();
        }
    });
}
</script>
