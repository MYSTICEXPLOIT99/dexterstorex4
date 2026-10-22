<?php
// Fetch all users with VIP info
$get_user = dd_q("SELECT id, username, point, total, total_topup, vip_level FROM users ORDER BY total_topup DESC");
$vip_ranks = ['Iron','Bronze','Silver','Gold','Platinum','Diamond','Ascendant','Immortal','Radiant'];
$vip_colors = [
    'Iron'      => '#6b7280',
    'Bronze'    => '#d97706',
    'Silver'    => '#94a3b8',
    'Gold'      => '#f59e0b',
    'Platinum'  => '#38bdf8',
    'Diamond'   => '#00c9ff',
    'Ascendant' => '#10b981',
    'Immortal'  => '#ec4899',
    'Radiant'   => '#ffd700',
];
$vip_icons = [
    'Iron'      => 'fa-shield',
    'Bronze'    => 'fa-shield-heart',
    'Silver'    => 'fa-medal',
    'Gold'      => 'fa-award',
    'Platinum'  => 'fa-shield-halved',
    'Diamond'   => 'fa-gem',
    'Ascendant' => 'fa-gem',
    'Immortal'  => 'fa-chess-king',
    'Radiant'   => 'fa-crown',
];
?>

<div class="p-4 p-lg-5">
    <div class="mb-5">
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-crown me-2" style="color:#f59e0b;"></i> จัดการระดับ VIP สมาชิก
        </h3>
        <p class="text-muted small">ปรับยศ VIP ยอดเงิน และยอดเติมสะสมของสมาชิกแต่ละคน</p>
    </div>

    <div class="table-responsive">
        <table id="vip_table" class="table table-hover border-0 w-100">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 py-3 ps-4 text-muted small fw-bold">ID</th>
                    <th class="border-0 py-3 text-muted small fw-bold">ชื่อผู้ใช้งาน</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-center">ระดับ VIP</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-center">ยอดเติมสะสม</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-center">เงินคงเหลือ</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-center">เครื่องมือ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $get_user->fetch(PDO::FETCH_ASSOC)):
                    $rank = $row['vip_level'] ?: 'Iron';
                    $rcolor = $vip_colors[$rank] ?? '#6b7280';
                    $ricon  = $vip_icons[$rank] ?? 'fa-shield';
                ?>
                <tr class="align-middle border-bottom">
                    <td class="py-3 ps-4 text-dark small fw-bold">#<?= $row['id'] ?></td>
                    <td class="py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-light-soft rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width:32px;height:32px;">
                                <i class="fa-solid fa-user text-xdvz-primary" style="font-size:0.8rem;"></i>
                            </div>
                            <div>
                                <span class="text-dark fw-medium small"><?= htmlspecialchars($row['username']) ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 text-center">
                        <span class="badge rounded-pill px-3 py-2 fw-bold"
                            style="background:<?= $rcolor ?>;color:#000;font-size:0.75rem;">
                            <i class="fa-solid <?= $ricon ?> me-1"></i><?= $rank ?>
                        </span>
                    </td>
                    <td class="py-3 text-center">
                        <span class="fw-bold" style="color:#f59e0b;">฿<?= number_format($row['total_topup'] ?? 0, 2) ?></span>
                    </td>
                    <td class="py-3 text-center">
                        <span class="text-main fw-bold">฿<?= number_format($row['point'], 2) ?></span>
                    </td>
                    <td class="py-3 text-center">
                        <button class="btn btn-warning btn-sm rounded-pill px-3 fw-bold"
                            onclick="openVipEdit(<?= $row['id'] ?>, '<?= htmlspecialchars($row['username'], ENT_QUOTES) ?>', '<?= $rank ?>', <?= floatval($row['total_topup'] ?? 0) ?>, <?= floatval($row['point']) ?>)">
                            <i class="fa-solid fa-edit me-1"></i> แก้ไข
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal แก้ไข VIP -->
<div class="modal fade" id="vip_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow" style="background:var(--bg-card);border:1px solid var(--border) !important;">
            <div class="modal-header border-0" style="background:var(--bg-card-alt);border-bottom:1px solid var(--border) !important;">
                <h5 class="fw-bold mb-0" style="color:var(--text-heading);">
                    <i class="fa-solid fa-crown me-2" style="color:#f59e0b;"></i>
                    แก้ไข VIP — <span id="modal_username" style="color:var(--text-muted);"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="background:var(--bg-card);">

                <!-- VIP Rank Custom Dropdown -->
                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color:var(--text-heading);">ระดับยศ VIP</label>

                    <!-- Hidden input to store actual value -->
                    <input type="hidden" id="modal_vip_rank" value="Iron">

                    <!-- Custom dropdown trigger -->
                    <div id="vip_dropdown" style="position:relative;">
                        <div id="vip_dropdown_trigger"
                            onclick="toggleVipDropdown()"
                            style="
                                display:flex; align-items:center; justify-content:space-between;
                                padding:0.6rem 1rem; border-radius:10px; cursor:pointer;
                                background:var(--bg-card-alt); border:1px solid var(--border);
                                color:var(--text-main); user-select:none;
                            ">
                            <div style="display:flex;align-items:center;gap:0.6rem;">
                                <div id="vip_dd_dot" style="width:12px;height:12px;border-radius:50%;background:#6b7280;flex-shrink:0;"></div>
                                <span id="vip_dd_label" style="font-weight:600;">Iron</span>
                            </div>
                            <i class="fa-solid fa-chevron-down" id="vip_dd_chevron" style="font-size:0.75rem;color:var(--text-muted);transition:transform 0.2s;"></i>
                        </div>

                        <!-- Dropdown list -->
                        <div id="vip_dropdown_list"
                            style="
                                display:none; position:absolute; top:calc(100% + 4px); left:0; right:0;
                                background:var(--bg-card-alt); border:1px solid var(--border);
                                border-radius:12px; z-index:9999;
                                box-shadow:0 8px 30px rgba(0,0,0,0.4);
                                overflow:hidden; max-height:260px; overflow-y:auto;
                            ">
                            <?php foreach ($vip_ranks as $r):
                                $rc = $vip_colors[$r];
                                $ri = $vip_icons[$r];
                            ?>
                            <div class="vip-dd-option"
                                onclick="selectVipRank('<?= $r ?>', '<?= $rc ?>')"
                                data-rank="<?= $r ?>"
                                style="
                                    display:flex; align-items:center; gap:0.75rem;
                                    padding:0.65rem 1rem; cursor:pointer;
                                    color:var(--text-main); transition:background 0.15s;
                                    border-bottom:1px solid var(--border);
                                ">
                                <div style="width:12px;height:12px;border-radius:50%;background:<?= $rc ?>;flex-shrink:0;"></div>
                                <i class="fa-solid <?= $ri ?>" style="color:<?= $rc ?>;width:14px;text-align:center;font-size:0.8rem;"></i>
                                <span style="font-weight:600;"><?= $r ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Preview hint -->
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <span id="rank_preview_text" class="small" style="color:var(--text-muted);">Iron — ยังไม่มีสิทธิ์พิเศษ</span>
                    </div>
                </div>

                <!-- Divider -->
                <hr class="my-3" style="border-color:var(--border);">

                <!-- Total Topup -->
                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color:var(--text-heading);">
                        <i class="fa-solid fa-coins me-1 text-warning"></i>
                        ยอดเติมเงินสะสมรวม (฿)
                    </label>
                    <input type="number" id="modal_total_topup" class="form-control py-2"
                        placeholder="ปล่อยว่างถ้าไม่ต้องการเปลี่ยน" step="0.01" min="0"
                        style="background:var(--bg-card-alt);border:1px solid var(--border);color:var(--text-main);">
                    <div class="form-text" style="color:var(--text-muted);">ระบบจะคำนวณยศใหม่อัตโนมัติจากยอดนี้ (เว้นว่างถ้าไม่แก้)</div>
                </div>

                <!-- Point Balance -->
                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color:var(--text-heading);">
                        <i class="fa-solid fa-wallet me-1 text-success"></i>
                        เงินคงเหลือในระบบ (฿)
                    </label>
                    <input type="number" id="modal_point" class="form-control py-2"
                        placeholder="ปล่อยว่างถ้าไม่ต้องการเปลี่ยน" step="0.01" min="0"
                        style="background:var(--bg-card-alt);border:1px solid var(--border);color:var(--text-main);">
                    <div class="form-text" id="modal_point_hint"></div>
                </div>

            </div>
            <div class="modal-footer border-0" style="background:var(--bg-card);border-top:1px solid var(--border) !important;">
                <button type="button" class="btn rounded-3 px-4" data-bs-dismiss="modal"
                    style="background:var(--bg-card-alt);border:1px solid var(--border);color:var(--text-muted);">ยกเลิก</button>
                <button type="button" class="btn w-50 py-2 rounded-3 fw-bold" id="vip_save_btn"
                    style="background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;border:none;box-shadow:0 4px 15px rgba(var(--primary-rgb),0.3);">
                    <i class="fa-solid fa-check me-1"></i>บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#vip_table').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json" },
        "order": [[3, "desc"]]
    });
});

// VIP rank descriptions and colors
const rankDesc = {
    'Iron':      'Iron — ยังไม่มีสิทธิ์พิเศษ',
    'Bronze':    'Bronze — Cashback 1.5%',
    'Silver':    'Silver — Cashback 3%',
    'Gold':      'Gold — Cashback 4%',
    'Platinum':  'Platinum — Cashback 6%',
    'Diamond':   'Diamond — Cashback 8%',
    'Ascendant': 'Ascendant — Cashback 10%',
    'Immortal':  'Immortal — Cashback 12%',
    'Radiant':   'Radiant — Cashback 15% (สูงสุด) ✨',
};
const rankColors = {
    'Iron':'#6b7280','Bronze':'#d97706','Silver':'#94a3b8','Gold':'#f59e0b',
    'Platinum':'#38bdf8','Diamond':'#00c9ff','Ascendant':'#10b981',
    'Immortal':'#ec4899','Radiant':'#ffd700'
};

// ── Custom Dropdown Logic ──
function toggleVipDropdown() {
    const list = document.getElementById('vip_dropdown_list');
    const chevron = document.getElementById('vip_dd_chevron');
    const isOpen = list.style.display === 'block';
    list.style.display = isOpen ? 'none' : 'block';
    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

function selectVipRank(rank, color) {
    // Set hidden input value
    document.getElementById('modal_vip_rank').value = rank;

    // Update trigger display
    document.getElementById('vip_dd_dot').style.background = color;
    document.getElementById('vip_dd_label').textContent = rank;

    // Highlight selected option
    document.querySelectorAll('.vip-dd-option').forEach(el => {
        el.style.background = el.dataset.rank === rank
            ? 'rgba(var(--primary-rgb, 0,201,255), 0.12)'
            : '';
    });

    // Update preview text
    document.getElementById('rank_preview_text').textContent = rankDesc[rank] || rank;

    // Close dropdown
    document.getElementById('vip_dropdown_list').style.display = 'none';
    document.getElementById('vip_dd_chevron').style.transform = 'rotate(0deg)';
}

// Hover effect on options
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.vip-dd-option').forEach(el => {
        el.addEventListener('mouseenter', function() {
            if (this.dataset.rank !== document.getElementById('modal_vip_rank').value) {
                this.style.background = 'rgba(255,255,255,0.05)';
            }
        });
        el.addEventListener('mouseleave', function() {
            if (this.dataset.rank !== document.getElementById('modal_vip_rank').value) {
                this.style.background = '';
            }
        });
    });
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dd = document.getElementById('vip_dropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('vip_dropdown_list').style.display = 'none';
        document.getElementById('vip_dd_chevron').style.transform = 'rotate(0deg)';
    }
});

// ── Open modal ──
function openVipEdit(uid, username, currentRank, totalTopup, point) {
    $('#vip_save_btn').attr('data-id', uid);
    $('#modal_username').text(username);

    // Set custom dropdown to current rank
    const color = rankColors[currentRank] || '#6b7280';
    selectVipRank(currentRank, color);

    $('#modal_total_topup').val('');
    $('#modal_point').val('');
    $('#modal_point_hint').text('ยอดปัจจุบัน: ฿' + parseFloat(point).toLocaleString('th-TH', {minimumFractionDigits:2}));
    new bootstrap.Modal('#vip_modal').show();
}

// ── Save ──
$('#vip_save_btn').click(function () {
    const uid = $(this).attr('data-id');
    if (!uid) return;

    var formData = new FormData();
    formData.append('id',       uid);
    formData.append('vip_rank', document.getElementById('modal_vip_rank').value);
    if ($('#modal_total_topup').val() !== '') formData.append('total_topup', $('#modal_total_topup').val());
    if ($('#modal_point').val() !== '')       formData.append('point', $('#modal_point').val());

    const btn = $(this);
    btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>กำลังบันทึก...');

    $.ajax({
        type: 'POST', url: 'system/backend/vip_setting.php',
        data: formData, contentType: false, processData: false,
    }).done(function (res) {
        btn.prop('disabled', false).html('<i class="fa-solid fa-check me-1"></i>บันทึกการเปลี่ยนแปลง');
        Swal.fire({ icon:'success', title:'สำเร็จ!', text: res.message, timer:1500, showConfirmButton:false })
            .then(() => window.seamlessReload ? window.seamlessReload() : window.seamlessReload ? window.seamlessReload() : location.reload());
    }).fail(function (xhr) {
        btn.prop('disabled', false).html('<i class="fa-solid fa-check me-1"></i>บันทึกการเปลี่ยนแปลง');
        Swal.fire({ icon:'error', title:'ผิดพลาด!', text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาด' });
    });
});
</script>

