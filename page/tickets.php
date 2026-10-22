<?php
/* ─────────────────────────────────────────────
   หน้าตั๋วของฉัน – ลูกค้า
   subpage=tickets        → รายการตั๋ว
   subpage=ticket_detail&tid=XX → แชทในตั๋ว
───────────────────────────────────────────── */
$uid   = $_SESSION['id'];
$uname = $user['username'];
$tid   = intval($_GET['tid'] ?? 0);

$cat_labels = [
    'code_not_working' => 'รหัสใช้ไม่ได้',
    'topup_issue'      => 'ปัญหาเติมเงิน',
    'wrong_item'       => 'ได้ของผิด',
    'vip_issue'        => 'ปัญหา VIP',
    'other'            => 'อื่นๆ',
];
$status_info = [
    'open'        => ['label'=>'รอดำเนินการ',    'color'=>'#f59e0b', 'dot'=>'🟡'],
    'in_progress' => ['label'=>'กำลังดำเนินการ', 'color'=>'#38bdf8', 'dot'=>'🔵'],
    'resolved'    => ['label'=>'แก้ไขแล้ว',      'color'=>'#10b981', 'dot'=>'🟢'],
    'closed'      => ['label'=>'ปิดแล้ว',         'color'=>'#6b7280', 'dot'=>'⚫'],
];

// ── Ticket Detail View ──
if ($tid > 0) {
    $ticket = dd_q("SELECT * FROM tickets WHERE id = ? AND uid = ? LIMIT 1", [$tid, $uid])->fetch(PDO::FETCH_ASSOC);
    if (!$ticket) { echo '<div class="p-4 text-center text-muted">ไม่พบตั๋วนี้</div>'; return; }
    $replies = dd_q("SELECT * FROM ticket_replies WHERE ticket_id = ? ORDER BY id ASC", [$tid])->fetchAll(PDO::FETCH_ASSOC);
    $st = $status_info[$ticket['status']] ?? $status_info['open'];
    $cat_name = $cat_labels[$ticket['category']] ?? $ticket['category'];
?>
<style>
.tk-chat-wrap { display:flex; flex-direction:column; gap:1rem; max-height:480px; overflow-y:auto; padding:1rem; scroll-behavior:smooth; }
.tk-bubble { display:flex; gap:0.75rem; max-width:85%; }
.tk-bubble.admin { align-self:flex-start; flex-direction:row; }
.tk-bubble.customer { align-self:flex-end; flex-direction:row-reverse; }
.tk-avatar {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:1rem;
}
.tk-bubble.admin .tk-avatar    { background:linear-gradient(135deg,var(--primary),var(--secondary)); }
.tk-bubble.customer .tk-avatar { background:linear-gradient(135deg,#a855f7,#7c3aed); }
.tk-msg-box {
    padding:0.7rem 1rem; border-radius:16px;
    font-size:0.875rem; line-height:1.5;
    color:var(--text-main);
    max-width:100%;
    word-break:break-word;
}
.tk-bubble.admin .tk-msg-box {
    background:var(--bg-card-alt);
    border:1px solid var(--border);
    border-top-left-radius:4px;
}
.tk-bubble.customer .tk-msg-box {
    background:linear-gradient(135deg,rgba(var(--primary-rgb),.25),rgba(var(--secondary-rgb),.18));
    border:1px solid rgba(var(--primary-rgb),.3);
    border-top-right-radius:4px;
}
.tk-time { font-size:0.68rem; color:var(--text-muted); margin-top:0.3rem; }
.tk-img-preview { max-width:200px; border-radius:10px; margin-top:0.5rem; border:1px solid var(--border); cursor:pointer; }
.tk-reply-box {
    background:var(--bg-card-alt); border:1px solid var(--border);
    border-radius:14px; padding:0.75rem 1rem;
}
.tk-reply-box textarea {
    background:transparent; border:none; outline:none; resize:none;
    color:var(--text-main); width:100%; font-size:0.875rem;
}
.tk-status-bar {
    display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;
    padding:0.75rem 1rem; border-radius:12px;
    background:var(--bg-card-alt); border:1px solid var(--border);
    margin-bottom:1rem;
}
</style>

<div class="p-1">
    <!-- Back button -->
    <a href="?page=profile&subpage=tickets" class="d-inline-flex align-items-center gap-2 mb-3 text-decoration-none" style="color:var(--text-muted);font-size:0.85rem;">
        <i class="fa-solid fa-arrow-left"></i> กลับไปรายการตั๋ว
    </a>

    <!-- Ticket info bar -->
    <div class="tk-status-bar mb-3">
        <span class="fw-bold" style="color:var(--text-heading);font-size:0.9rem;">#<?= $ticket['id'] ?> <?= htmlspecialchars($ticket['subject']) ?></span>
        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background:<?= $st['color'] ?>22;color:<?= $st['color'] ?>;border:1px solid <?= $st['color'] ?>55;font-size:0.72rem;">
            <?= $st['dot'] ?> <?= $st['label'] ?>
        </span>
        <span class="badge rounded-pill px-2 py-1" style="background:var(--border);color:var(--text-muted);font-size:0.7rem;"><?= $cat_name ?></span>
        <span class="ms-auto" style="color:var(--text-muted);font-size:0.72rem;"><?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></span>
    </div>

    <!-- Chat messages -->
    <div class="tk-chat-wrap" id="tk_chat_wrap" style="background:var(--bg-card-alt);border:1px solid var(--border);border-radius:16px;">
        <?php foreach ($replies as $r): ?>
        <div class="tk-bubble <?= $r['is_admin'] ? 'admin' : 'customer' ?>">
            <div class="tk-avatar">
                <?= $r['is_admin'] ? '⚙️' : '👤' ?>
            </div>
            <div>
                <div style="font-size:0.7rem;color:var(--text-muted);margin-bottom:0.25rem;<?= $r['is_admin'] ? '' : 'text-align:right;' ?>">
                    <?= $r['is_admin'] ? '<span style="color:var(--primary);font-weight:700;">แอดมิน</span>' : htmlspecialchars($r['uname']) ?>
                </div>
                <div class="tk-msg-box">
                    <?= nl2br(htmlspecialchars($r['message'])) ?>
                    <?php if ($r['img']): ?>
                        <br><img src="<?= htmlspecialchars($r['img']) ?>" class="tk-img-preview" onclick="window.open(this.src,'_blank')" alt="attachment">
                    <?php endif; ?>
                </div>
                <div class="tk-time <?= $r['is_admin'] ? '' : 'text-end' ?>"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Reply box -->
    <?php if ($ticket['status'] !== 'closed'): ?>
    <div class="tk-reply-box mt-3">
        <textarea id="tk_reply_msg" rows="3" placeholder="พิมพ์ข้อความตอบกลับ..."></textarea>
        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
            <label class="d-flex align-items-center gap-2 small" style="color:var(--text-muted);cursor:pointer;">
                <i class="fa-solid fa-paperclip"></i>
                <span id="img_name">แนบรูปภาพ</span>
                <input type="file" id="tk_reply_img" accept="image/*" style="display:none;" onchange="document.getElementById('img_name').textContent=this.files[0]?.name||'แนบรูปภาพ'">
            </label>
            <button class="btn btn-sm fw-bold px-4" onclick="sendReply(<?= $tid ?>)"
                style="background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;border:none;border-radius:10px;padding:0.5rem 1.25rem;">
                <i class="fa-solid fa-paper-plane me-1"></i>ส่ง
            </button>
        </div>
    </div>
    <?php else: ?>
    <div class="mt-3 p-3 text-center rounded-3" style="background:var(--bg-card-alt);border:1px solid var(--border);color:var(--text-muted);font-size:0.85rem;">
        ⚫ ตั๋วนี้ถูกปิดแล้ว ไม่สามารถส่งข้อความได้
    </div>
    <?php endif; ?>
</div>

<script>
// Scroll to bottom of chat
const wrap = document.getElementById('tk_chat_wrap');
if (wrap) wrap.scrollTop = wrap.scrollHeight;

function sendReply(tid) {
    const msg = document.getElementById('tk_reply_msg').value.trim();
    if (!msg) { Swal.fire({icon:'warning',title:'กรุณาพิมพ์ข้อความ',timer:1500,showConfirmButton:false}); return; }

    const fd = new FormData();
    fd.append('ticket_id', tid);
    fd.append('message', msg);
    const imgFile = document.getElementById('tk_reply_img').files[0];
    if (imgFile) fd.append('img', imgFile);

    fetch('system/ticket_reply.php', { method:'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            window.seamlessReload ? window.seamlessReload() : location.reload();
        } else {
            Swal.fire({icon:'error', title:'ผิดพลาด', text: res.message});
        }
    });
}
</script>

<?php
    return;
}

// ── Ticket List View ──
$tickets = dd_q("SELECT * FROM tickets WHERE uid = ? ORDER BY updated_at DESC", [$uid])->fetchAll(PDO::FETCH_ASSOC);
$open_count = count(array_filter($tickets, fn($t) => in_array($t['status'], ['open','in_progress'])));
?>

<style>
.tk-card {
    display:block; text-decoration:none;
    background:var(--bg-card-alt); border:1px solid var(--border);
    border-radius:14px; padding:1rem 1.25rem;
    margin-bottom:0.6rem; transition:all 0.2s ease;
    color:var(--text-main);
}
.tk-card:hover {
    border-color:rgba(var(--primary-rgb),.4);
    background:rgba(var(--primary-rgb),.05);
    transform:translateX(4px);
    color:var(--text-main);
    text-decoration:none;
}
.tk-status-dot {
    width:9px; height:9px; border-radius:50%; flex-shrink:0;
}
.tk-new-btn {
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff; border:none; border-radius:12px;
    padding:0.55rem 1.25rem; font-weight:700; font-size:0.85rem;
    cursor:pointer; transition:opacity .2s;
    display:inline-flex; align-items:center; gap:0.5rem;
}
.tk-new-btn:hover { opacity:0.85; }
</style>

<div class="p-1">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-0" style="color:var(--text-heading);">
                <i class="fa-solid fa-ticket me-2" style="color:var(--primary);"></i>ตั๋วของฉัน
            </h5>
            <p class="mb-0 mt-1" style="color:var(--text-muted);font-size:0.8rem;">ติดตามและจัดการปัญหาที่คุณแจ้งไว้</p>
        </div>
        <button class="tk-new-btn" data-bs-toggle="modal" data-bs-target="#new_ticket_modal">
            <i class="fa-solid fa-plus"></i> เปิดตั๋วใหม่
        </button>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="text-center p-3 rounded-3" style="background:rgba(var(--primary-rgb),.08);border:1px solid rgba(var(--primary-rgb),.2);">
                <div class="fw-bold" style="font-size:1.5rem;color:var(--primary);"><?= count($tickets) ?></div>
                <div style="color:var(--text-muted);font-size:0.75rem;">ตั๋วทั้งหมด</div>
            </div>
        </div>
        <div class="col-6">
            <div class="text-center p-3 rounded-3" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);">
                <div class="fw-bold" style="font-size:1.5rem;color:#f59e0b;"><?= $open_count ?></div>
                <div style="color:var(--text-muted);font-size:0.75rem;">รอดำเนินการ</div>
            </div>
        </div>
    </div>

    <!-- Ticket list -->
    <?php if (empty($tickets)): ?>
    <div class="text-center py-5" style="color:var(--text-muted);">
        <i class="fa-solid fa-ticket-simple mb-3" style="font-size:2.5rem;opacity:0.3;"></i>
        <p class="mb-0">ยังไม่มีตั๋วแจ้งปัญหา<br><span class="small">กดปุ่ม "เปิดตั๋วใหม่" เพื่อขอความช่วยเหลือ</span></p>
    </div>
    <?php else: ?>
        <?php foreach ($tickets as $t):
            $st = $status_info[$t['status']] ?? $status_info['open'];
            $cat_name = $cat_labels[$t['category']] ?? $t['category'];
            // Count unread admin replies (simple: check if latest reply is from admin)
            $last_reply = dd_q("SELECT is_admin FROM ticket_replies WHERE ticket_id = ? ORDER BY id DESC LIMIT 1", [$t['id']])->fetch(PDO::FETCH_ASSOC);
            $has_new = $last_reply && $last_reply['is_admin'] == 1;
        ?>
        <a href="?page=profile&subpage=ticket_detail&tid=<?= $t['id'] ?>" class="tk-card">
            <div class="d-flex align-items-center gap-3">
                <div class="tk-status-dot" style="background:<?= $st['color'] ?>;box-shadow:0 0 6px <?= $st['color'] ?>88;"></div>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="fw-bold" style="color:var(--text-heading);font-size:0.9rem;"><?= htmlspecialchars($t['subject']) ?></span>
                        <?php if ($has_new): ?>
                        <span class="badge rounded-pill" style="background:var(--primary);color:#000;font-size:0.65rem;">ใหม่</span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <span style="font-size:0.72rem;background:<?= $st['color'] ?>22;color:<?= $st['color'] ?>;border:1px solid <?= $st['color'] ?>55;border-radius:20px;padding:0.1rem 0.6rem;font-weight:600;"><?= $st['label'] ?></span>
                        <span style="font-size:0.72rem;color:var(--text-muted);"><?= $cat_name ?></span>
                        <span style="font-size:0.7rem;color:var(--text-muted);">· #<?= $t['id'] ?></span>
                    </div>
                </div>
                <div class="text-end flex-shrink-0" style="font-size:0.7rem;color:var(--text-muted);">
                    <?= date('d/m/Y', strtotime($t['updated_at'])) ?><br>
                    <i class="fa-solid fa-chevron-right mt-1" style="font-size:0.65rem;"></i>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- New Ticket Modal -->
<div class="modal fade" id="new_ticket_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0" style="background:var(--bg-card);border:1px solid var(--border)!important;">
            <div class="modal-header border-0" style="background:var(--bg-card-alt);border-bottom:1px solid var(--border)!important;border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold" style="color:var(--text-heading);">
                    <i class="fa-solid fa-plus-circle me-2" style="color:var(--primary);"></i>เปิดตั๋วใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="background:var(--bg-card);">
                <!-- Category Custom Dropdown -->
                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color:var(--text-heading);">ประเภทปัญหา</label>
                    <input type="hidden" id="nt_category" value="code_not_working">
                    <div id="nt_category_dropdown" style="position:relative;">
                        <div id="nt_category_trigger"
                            onclick="toggleNtDropdown()"
                            style="
                                display:flex; align-items:center; justify-content:space-between;
                                padding:0.6rem 1rem; border-radius:10px; cursor:pointer;
                                background:var(--bg-card-alt); border:1px solid var(--border);
                                color:var(--text-main); user-select:none;
                            ">
                            <span id="nt_category_label" style="font-weight:600;">รหัสใช้ไม่ได้</span>
                            <i class="fa-solid fa-chevron-down" id="nt_category_chevron" style="font-size:0.75rem;color:var(--text-muted);transition:transform 0.2s;"></i>
                        </div>
                        <div id="nt_category_list"
                            style="
                                display:none; position:absolute; top:calc(100% + 4px); left:0; right:0;
                                background:var(--bg-card-alt); border:1px solid var(--border);
                                border-radius:12px; z-index:9999;
                                box-shadow:0 8px 30px rgba(0,0,0,0.4);
                                overflow:hidden; max-height:240px; overflow-y:auto;
                            ">
                            <?php foreach ($cat_labels as $val => $label): ?>
                            <div class="nt-dd-option"
                                onclick="selectNtCategory('<?= $val ?>', '<?= htmlspecialchars($label, ENT_QUOTES) ?>')"
                                data-val="<?= $val ?>"
                                style="
                                    display:flex; align-items:center;
                                    padding:0.65rem 1rem; cursor:pointer;
                                    color:var(--text-main); transition:background 0.15s;
                                    border-bottom:1px solid var(--border);
                                    font-weight:600;
                                ">
                                <?= htmlspecialchars($label) ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <!-- Subject -->
                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color:var(--text-heading);">หัวเรื่อง</label>
                    <input type="text" id="nt_subject" class="form-control" placeholder="ระบุหัวเรื่องปัญหาของคุณ..."
                        style="background:var(--bg-card-alt);border:1px solid var(--border);color:var(--text-main);border-radius:10px;">
                </div>
                <!-- Message -->
                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color:var(--text-heading);">รายละเอียด</label>
                    <textarea id="nt_message" rows="4" class="form-control" placeholder="อธิบายปัญหาของคุณให้ชัดเจน..."
                        style="background:var(--bg-card-alt);border:1px solid var(--border);color:var(--text-main);border-radius:10px;resize:none;"></textarea>
                </div>
                <!-- Image -->
                <div class="mb-2">
                    <label class="form-label small fw-bold" style="color:var(--text-heading);">แนบรูปภาพ (ไม่บังคับ)</label>
                    <input type="file" id="nt_img" accept="image/*" class="form-control"
                        style="background:var(--bg-card-alt);border:1px solid var(--border);color:var(--text-main);border-radius:10px;">
                </div>
            </div>
            <div class="modal-footer border-0" style="background:var(--bg-card);border-top:1px solid var(--border)!important;border-radius:0 0 16px 16px;">
                <button class="btn px-4 rounded-3" data-bs-dismiss="modal" style="background:var(--bg-card-alt);border:1px solid var(--border);color:var(--text-muted);">ยกเลิก</button>
                <button class="btn fw-bold px-5 rounded-3" onclick="submitNewTicket()"
                    style="background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;border:none;box-shadow:0 4px 15px rgba(var(--primary-rgb),.3);">
                    <i class="fa-solid fa-paper-plane me-1"></i>ส่งตั๋ว
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ── Custom Dropdown Logic for New Ticket Modal ──
function toggleNtDropdown() {
    const list = document.getElementById('nt_category_list');
    const chevron = document.getElementById('nt_category_chevron');
    const isOpen = list.style.display === 'block';
    list.style.display = isOpen ? 'none' : 'block';
    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

function selectNtCategory(val, label) {
    document.getElementById('nt_category').value = val;
    document.getElementById('nt_category_label').textContent = label;
    
    // Highlight active option
    document.querySelectorAll('.nt-dd-option').forEach(el => {
        el.style.background = el.dataset.val === val ? 'rgba(var(--primary-rgb, 0,201,255), 0.12)' : '';
    });
    
    // Close dropdown
    document.getElementById('nt_category_list').style.display = 'none';
    document.getElementById('nt_category_chevron').style.transform = 'rotate(0deg)';
}

// Hover effects
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.nt-dd-option').forEach(el => {
        el.addEventListener('mouseenter', function() {
            if (this.dataset.val !== document.getElementById('nt_category').value) {
                this.style.background = 'rgba(255,255,255,0.05)';
            }
        });
        el.addEventListener('mouseleave', function() {
            if (this.dataset.val !== document.getElementById('nt_category').value) {
                this.style.background = '';
            }
        });
    });
});

// Close when clicking outside
document.addEventListener('click', function(e) {
    const dd = document.getElementById('nt_category_dropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('nt_category_list').style.display = 'none';
        document.getElementById('nt_category_chevron').style.transform = 'rotate(0deg)';
    }
});

function submitNewTicket() {
    const subject  = document.getElementById('nt_subject').value.trim();
    const message  = document.getElementById('nt_message').value.trim();
    const category = document.getElementById('nt_category').value;
    const imgFile  = document.getElementById('nt_img').files[0];

    if (!subject || !message) {
        Swal.fire({icon:'warning', title:'กรุณากรอกข้อมูลให้ครบ', timer:1800, showConfirmButton:false});
        return;
    }

    const fd = new FormData();
    fd.append('subject', subject);
    fd.append('message', message);
    fd.append('category', category);
    if (imgFile) fd.append('img', imgFile);

    fetch('system/ticket_create.php', { method:'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('new_ticket_modal')).hide();
            Swal.fire({icon:'success', title:'เปิดตั๋วสำเร็จ!', text:'ทีมงานจะตอบกลับเร็วๆ นี้ครับ', timer:2000, showConfirmButton:false})
            .then(() => window.navTo ? window.navTo('') : (location.href = '')+res.data.ticket_id);
        } else {
            Swal.fire({icon:'error', title:'ผิดพลาด', text: res.message});
        }
    });
}
</script>

