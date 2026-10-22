<?php
/* ─────────────────────────────────────────
   หน้าจัดการตั๋ว – หลังบ้านแอดมิน
───────────────────────────────────────── */
$tid = intval($_GET['tid'] ?? 0);

$cat_labels = [
    'code_not_working' => 'รหัสใช้ไม่ได้ / ปัญหาสินค้า',
    'warranty_claim'   => 'เคลมประกันสินค้า',
    'topup_issue'      => 'ปัญหาเติมเงิน',
    'wrong_item'       => 'ได้ของผิด / ไม่ครบ',
    'refund'           => 'ขอคืนเงิน',
    'product_inquiry'  => 'สอบถามข้อมูลสินค้า',
    'vip_issue'        => 'ปัญหา VIP',
    'other'            => 'อื่นๆ',
];
$status_info = [
    'open'        => ['label'=>'รอดำเนินการ',    'color'=>'#f59e0b'],
    'in_progress' => ['label'=>'กำลังดำเนินการ', 'color'=>'#38bdf8'],
    'resolved'    => ['label'=>'แก้ไขแล้ว',      'color'=>'#10b981'],
    'closed'      => ['label'=>'ปิดแล้ว',         'color'=>'#6b7280'],
];

// ── Ticket Detail (admin) ──
if ($tid > 0) {
    $ticket  = dd_q("SELECT t.*, u.vip_level FROM tickets t LEFT JOIN users u ON t.uid=u.id WHERE t.id=? LIMIT 1", [$tid])->fetch(PDO::FETCH_ASSOC);
    if (!$ticket) { echo '<div class="p-5 text-center text-muted">ไม่พบตั๋วนี้</div>'; return; }
    $replies = dd_q("SELECT * FROM ticket_replies WHERE ticket_id=? ORDER BY id ASC", [$tid])->fetchAll(PDO::FETCH_ASSOC);
    $st = $status_info[$ticket['status']] ?? $status_info['open'];
    $images_arr = !empty($ticket['images']) ? json_decode($ticket['images'], true) : [];
?>
<style>
.tk-chat-wrap { display:flex; flex-direction:column; gap:1rem; max-height:500px; overflow-y:auto; padding:1.25rem; scroll-behavior:smooth; }
.tk-bubble { display:flex; gap:0.75rem; }
.tk-bubble.admin    { align-self:flex-end; flex-direction:row-reverse; }
.tk-bubble.customer { align-self:flex-start; }
.tk-avatar { width:36px; height:36px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:1rem; }
.tk-bubble.admin .tk-avatar    { background:linear-gradient(135deg,var(--primary),var(--secondary)); }
.tk-bubble.customer .tk-avatar { background:rgba(255,255,255,0.08); border:1px solid var(--border); }
.tk-msg-box { padding:0.7rem 1rem; border-radius:16px; font-size:0.875rem; line-height:1.5; color:var(--text-main); word-break:break-word; }
.tk-bubble.customer .tk-msg-box { background:var(--bg-card-alt); border:1px solid var(--border); border-top-left-radius:4px; }
.tk-bubble.admin .tk-msg-box   { background:linear-gradient(135deg,rgba(var(--primary-rgb),.25),rgba(var(--secondary-rgb),.18)); border:1px solid rgba(var(--primary-rgb),.3); border-top-right-radius:4px; }
.tk-time { font-size:0.68rem; color:var(--text-muted); margin-top:0.25rem; }
.tk-img-prev { max-width:200px; border-radius:10px; margin-top:0.5rem; border:1px solid var(--border); cursor:pointer; }
.status-btn { border:1px solid var(--border); background:var(--bg-card-alt); color:var(--text-muted); border-radius:8px; padding:0.35rem 0.8rem; font-size:0.78rem; font-weight:600; cursor:pointer; transition:all .2s; }
.status-btn:hover, .status-btn.active { background:rgba(var(--primary-rgb),.15); color:var(--primary); border-color:rgba(var(--primary-rgb),.4); }
</style>

<div class="p-4">
    <a href="?page=ticket_manage" class="d-inline-flex align-items-center gap-2 mb-4 text-decoration-none" style="color:var(--text-muted);font-size:0.85rem;">
        <i class="fa-solid fa-arrow-left"></i> กลับรายการตั๋ว
    </a>

    <!-- Info bar -->
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4 p-3 rounded-3" style="background:#090a10;border:1px solid rgba(255,255,255,0.08);">
        <div>
            <h5 class="fw-bold mb-1" style="color:#ffffff;">
                #<?= $ticket['id'] ?> <?= htmlspecialchars($ticket['subject']) ?>
            </h5>
            <div class="d-flex gap-2 flex-wrap align-items-center mb-2">
                <span class="badge rounded-pill px-3 fw-bold" style="background:<?= $st['color'] ?>22;color:<?= $st['color'] ?>;border:1px solid <?= $st['color'] ?>55;">
                    <?= $st['label'] ?>
                </span>
                <span class="badge rounded-pill bg-dark border text-white"><?= $cat_labels[$ticket['category']] ?? $ticket['category'] ?></span>
                <span style="color:#94a3b8;font-size:0.78rem;">· ผู้แจ้ง: <strong class="text-warning"><?= htmlspecialchars($ticket['uname']) ?></strong></span>
                <span style="color:#94a3b8;font-size:0.75rem;">· <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></span>
            </div>

            <!-- Extra metadata row -->
            <div class="d-flex gap-3 flex-wrap align-items-center small" style="color:#cbd5e1;">
                <?php if (!empty($ticket['product_name'])): ?>
                    <div><i class="fa-solid fa-box text-warning me-1"></i> สินค้า: <strong><?= htmlspecialchars($ticket['product_name']) ?></strong></div>
                <?php endif; ?>
                <?php if (!empty($ticket['order_id'])): ?>
                    <div><i class="fa-solid fa-receipt text-info me-1"></i> ออเดอร์: #<?= intval($ticket['order_id']) ?></div>
                <?php endif; ?>
                <?php if (!empty($ticket['contact'])): ?>
                    <div><i class="fa-solid fa-address-book text-success me-1"></i> ติดต่อ: <strong><?= htmlspecialchars($ticket['contact']) ?></strong></div>
                <?php endif; ?>
            </div>

            <?php if (!empty($images_arr)): ?>
                <div class="d-flex gap-2 mt-2 flex-wrap">
                    <?php foreach ($images_arr as $im): ?>
                        <a href="<?= htmlspecialchars($im) ?>" target="_blank">
                            <img src="<?= htmlspecialchars($im) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:1px solid rgba(250,204,21,0.4);" alt="Attachment">
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- Status controls -->
        <div class="d-flex gap-2 flex-wrap">
            <?php foreach ($status_info as $sv => $si): ?>
            <button class="status-btn <?= $ticket['status']===$sv?'active':'' ?>" onclick="changeStatus(<?= $tid ?>, '<?= $sv ?>')">
                <?= $si['label'] ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Chat -->
    <div class="tk-chat-wrap mb-3" id="tk_chat_wrap" style="background:var(--bg-card-alt);border:1px solid var(--border);border-radius:16px;">
        <?php foreach ($replies as $r): ?>
        <div class="tk-bubble <?= $r['is_admin'] ? 'admin' : 'customer' ?>">
            <div class="tk-avatar"><?= $r['is_admin'] ? '⚙️' : '👤' ?></div>
            <div style="max-width:75%;">
                <div style="font-size:0.7rem;color:var(--text-muted);margin-bottom:0.2rem;<?= $r['is_admin']?'text-align:right':'' ?>">
                    <?= $r['is_admin'] ? '<span style="color:var(--primary);font-weight:700;">แอดมิน</span>' : htmlspecialchars($r['uname']) ?>
                </div>
                <div class="tk-msg-box">
                    <?= nl2br(htmlspecialchars($r['message'])) ?>
                    <?php if ($r['img']): ?>
                    <br><img src="<?= htmlspecialchars($r['img']) ?>" class="tk-img-prev" onclick="window.open(this.src,'_blank')">
                    <?php endif; ?>
                </div>
                <div class="tk-time <?= $r['is_admin']?'text-end':'' ?>"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Admin Reply -->
    <?php if ($ticket['status'] !== 'closed'): ?>
    <div style="background:var(--bg-card-alt);border:1px solid var(--border);border-radius:14px;padding:0.85rem 1rem;">
        <textarea id="admin_reply" rows="3" placeholder="พิมพ์คำตอบ..." style="background:transparent;border:none;outline:none;resize:none;color:var(--text-main);width:100%;font-size:0.875rem;"></textarea>
        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <label style="cursor:pointer;color:var(--text-muted);font-size:0.82rem;">
                    <i class="fa-solid fa-paperclip me-1"></i><span id="admin_img_name">แนบรูป</span>
                    <input type="file" id="admin_reply_img" accept="image/*" style="display:none;" onchange="document.getElementById('admin_img_name').textContent=this.files[0]?.name||'แนบรูป'">
                </label>
                <select id="reply_status" style="background:var(--bg-card);border:1px solid var(--border);color:var(--text-main);border-radius:8px;padding:0.3rem 0.6rem;font-size:0.78rem;">
                    <option value="">— ไม่เปลี่ยนสถานะ —</option>
                    <?php foreach ($status_info as $sv => $si): ?>
                    <option value="<?= $sv ?>" <?= $ticket['status']===$sv?'selected':'' ?>><?= $si['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button onclick="adminReply(<?= $tid ?>)" class="btn fw-bold"
                style="background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;border:none;border-radius:10px;padding:0.5rem 1.5rem;">
                <i class="fa-solid fa-paper-plane me-1"></i>ตอบกลับ
            </button>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
const wrap = document.getElementById('tk_chat_wrap');
if (wrap) wrap.scrollTop = wrap.scrollHeight;

function changeStatus(tid, status) {
    fetch('system/ticket_reply.php', {
        method:'POST',
        body: (() => { const f=new FormData(); f.append('ticket_id',tid); f.append('message','[เปลี่ยนสถานะ]'); f.append('status',status); return f; })()
    }).then(() => window.seamlessReload ? window.seamlessReload() : location.reload());
}

function adminReply(tid) {
    const msg = document.getElementById('admin_reply').value.trim();
    if (!msg) { Swal.fire({icon:'warning',title:'กรุณาพิมพ์ข้อความ',timer:1500,showConfirmButton:false}); return; }
    const fd = new FormData();
    fd.append('ticket_id', tid);
    fd.append('message', msg);
    fd.append('status', document.getElementById('reply_status').value);
    const img = document.getElementById('admin_reply_img').files[0];
    if (img) fd.append('img', img);

    fetch('system/ticket_reply.php', { method:'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.status==='success') window.seamlessReload ? window.seamlessReload() : location.reload();
        else Swal.fire({icon:'error', title:'ผิดพลาด', text:res.message});
    });
}
</script>
<?php
    return;
}

// ── Ticket List (admin) ──
$filter = $_GET['status'] ?? 'all';
$cat_filter = $_GET['cat'] ?? 'all';
$params = [];
$where  = "WHERE 1=1";
if ($filter !== 'all') { $where .= " AND status=?"; $params[] = $filter; }
if ($cat_filter !== 'all') { $where .= " AND category=?"; $params[] = $cat_filter; }

$tickets = dd_q("SELECT t.*, (SELECT COUNT(*) FROM ticket_replies WHERE ticket_id=t.id) AS reply_count FROM tickets t $where ORDER BY FIELD(status,'open','in_progress','resolved','closed'), updated_at DESC", $params)->fetchAll(PDO::FETCH_ASSOC);

$counts = dd_q("SELECT status, COUNT(*) as c FROM tickets GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$cat_counts = dd_q("SELECT category, COUNT(*) as c FROM tickets GROUP BY category")->fetchAll(PDO::FETCH_KEY_PAIR);
$total  = array_sum($counts);
?>
<style>
.filter-tab { border:1px solid var(--border); background:var(--bg-card-alt); color:var(--text-muted); border-radius:10px; padding:0.35rem 0.85rem; font-size:0.8rem; font-weight:600; text-decoration:none; transition:all .2s; }
.filter-tab:hover, .filter-tab.active { background:rgba(var(--primary-rgb),.15); color:var(--primary); border-color:rgba(var(--primary-rgb),.4); text-decoration:none; }
.tk-admin-row { display:flex; align-items:center; padding:0.9rem 1.1rem; border-bottom:1px solid var(--border); transition:background .15s; text-decoration:none; color:var(--text-main); }
.tk-admin-row:hover { background:rgba(var(--primary-rgb),.05); text-decoration:none; color:var(--text-main); }
.tk-admin-row:last-child { border-bottom:none; }
</style>

<div class="p-4 p-lg-5">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--text-heading);">
                <i class="fa-solid fa-headset me-2" style="color:var(--primary);"></i>จัดการตั๋ว & รายงานปัญหา
                <?php if (($counts['open'] ?? 0) > 0): ?>
                <span class="badge ms-2" style="background:#f59e0b;color:#000;font-size:0.7rem;border-radius:20px;padding:0.3rem 0.7rem;">
                    <?= $counts['open'] ?> รอดำเนินการ
                </span>
                <?php endif; ?>
            </h4>
            <p class="mb-0 mt-1" style="color:var(--text-muted);font-size:0.82rem;">รับเรื่องรายงานปัญหาสินค้า เคลมประกัน และตอบคำถามจากลูกค้าในระบบ</p>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="row g-3 mb-4">
        <?php
        $stat_cards = [
            ['label'=>'ตั๋วทั้งหมด',     'val'=>$total,                    'color'=>'var(--primary)', 'icon'=>'fa-ticket'],
            ['label'=>'รอดำเนินการ',    'val'=>$counts['open']??0,        'color'=>'#f59e0b',        'icon'=>'fa-clock'],
            ['label'=>'กำลังดำเนินการ','val'=>$counts['in_progress']??0,  'color'=>'#38bdf8',        'icon'=>'fa-spinner'],
            ['label'=>'แก้ไขแล้ว',      'val'=>$counts['resolved']??0,    'color'=>'#10b981',        'icon'=>'fa-circle-check'],
        ];
        foreach ($stat_cards as $sc): ?>
        <div class="col-6 col-md-3">
            <div class="text-center p-3 rounded-3" style="background:var(--bg-card-alt);border:1px solid var(--border);">
                <i class="fa-solid <?= $sc['icon'] ?> mb-2" style="color:<?= $sc['color'] ?>;font-size:1.3rem;"></i>
                <div class="fw-bold" style="font-size:1.4rem;color:<?= $sc['color'] ?>;"><?= $sc['val'] ?></div>
                <div style="color:var(--text-muted);font-size:0.72rem;"><?= $sc['label'] ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Filter tabs (Status & Category) -->
    <div class="d-flex gap-2 flex-wrap mb-2">
        <a href="?page=ticket_manage" class="filter-tab <?= ($filter==='all'&&$cat_filter==='all')?'active':'' ?>">ทั้งหมด (<?= $total ?>)</a>
        <a href="?page=ticket_manage&status=open" class="filter-tab <?= $filter==='open'?'active':'' ?>">รอดำเนินการ (<?= $counts['open']??0 ?>)</a>
        <a href="?page=ticket_manage&status=in_progress" class="filter-tab <?= $filter==='in_progress'?'active':'' ?>">กำลังดำเนินการ (<?= $counts['in_progress']??0 ?>)</a>
        <a href="?page=ticket_manage&status=resolved" class="filter-tab <?= $filter==='resolved'?'active':'' ?>">แก้ไขแล้ว (<?= $counts['resolved']??0 ?>)</a>
        <a href="?page=ticket_manage&status=closed" class="filter-tab <?= $filter==='closed'?'active':'' ?>">ปิดแล้ว (<?= $counts['closed']??0 ?>)</a>
    </div>

    <div class="d-flex gap-2 flex-wrap mb-3 pt-2 border-top" style="border-color:rgba(255,255,255,0.06)!important;">
        <span class="text-muted small align-self-center me-1"><i class="fa-solid fa-filter me-1"></i>หมวดหมู่:</span>
        <a href="?page=ticket_manage&cat=code_not_working" class="filter-tab <?= $cat_filter==='code_not_working'?'active':'' ?>">รหัสใช้ไม่ได้ (<?= $cat_counts['code_not_working']??0 ?>)</a>
        <a href="?page=ticket_manage&cat=warranty_claim" class="filter-tab <?= $cat_filter==='warranty_claim'?'active':'' ?>">เคลมประกัน (<?= $cat_counts['warranty_claim']??0 ?>)</a>
        <a href="?page=ticket_manage&cat=topup_issue" class="filter-tab <?= $cat_filter==='topup_issue'?'active':'' ?>">ปัญหาเติมเงิน (<?= $cat_counts['topup_issue']??0 ?>)</a>
        <a href="?page=ticket_manage&cat=wrong_item" class="filter-tab <?= $cat_filter==='wrong_item'?'active':'' ?>">ได้ของผิด (<?= $cat_counts['wrong_item']??0 ?>)</a>
    </div>

    <!-- Ticket table -->
    <div style="background:var(--bg-card-alt);border:1px solid var(--border);border-radius:16px;overflow:hidden;">
        <!-- Table header -->
        <div style="display:grid;grid-template-columns:60px 1fr 130px 110px 90px 90px;padding:0.7rem 1.1rem;border-bottom:1px solid var(--border);background:rgba(255,255,255,0.02);">
            <span style="font-size:0.75rem;color:var(--text-muted);font-weight:700;">#</span>
            <span style="font-size:0.75rem;color:var(--text-muted);font-weight:700;">หัวเรื่อง / ผู้ใช้</span>
            <span style="font-size:0.75rem;color:var(--text-muted);font-weight:700;text-align:center;">ประเภท</span>
            <span style="font-size:0.75rem;color:var(--text-muted);font-weight:700;text-align:center;">สถานะ</span>
            <span style="font-size:0.75rem;color:var(--text-muted);font-weight:700;text-align:center;">ข้อความ</span>
            <span style="font-size:0.75rem;color:var(--text-muted);font-weight:700;text-align:center;">อัปเดต</span>
        </div>

        <?php if (empty($tickets)): ?>
        <div class="text-center py-5" style="color:var(--text-muted);">ไม่มีตั๋วในขณะนี้</div>
        <?php else: foreach ($tickets as $t):
            $st = $status_info[$t['status']] ?? $status_info['open'];
            $cat_name = $cat_labels[$t['category']] ?? $t['category'];
        ?>
        <a href="?page=ticket_manage&tid=<?= $t['id'] ?>" class="tk-admin-row"
            style="display:grid;grid-template-columns:60px 1fr 130px 110px 90px 90px;align-items:center;">
            <span style="font-weight:700;color:var(--text-muted);font-size:0.82rem;">#<?= $t['id'] ?></span>
            <div>
                <div class="fw-bold" style="color:var(--text-heading);font-size:0.87rem;"><?= htmlspecialchars(mb_substr($t['subject'], 0, 40)) ?><?= mb_strlen($t['subject'])>40?'…':'' ?></div>
                <div style="font-size:0.72rem;color:var(--primary);"><?= htmlspecialchars($t['uname']) ?></div>
            </div>
            <div class="text-center">
                <span style="font-size:0.7rem;background:var(--border);color:var(--text-muted);border-radius:20px;padding:0.15rem 0.6rem;"><?= $cat_name ?></span>
            </div>
            <div class="text-center">
                <span class="badge rounded-pill fw-bold" style="background:<?= $st['color'] ?>22;color:<?= $st['color'] ?>;border:1px solid <?= $st['color'] ?>55;font-size:0.7rem;padding:0.3rem 0.7rem;">
                    <?= $st['label'] ?>
                </span>
            </div>
            <div class="text-center" style="color:var(--text-muted);font-size:0.78rem;">
                <i class="fa-solid fa-comment me-1"></i><?= $t['reply_count'] ?>
            </div>
            <div class="text-center" style="color:var(--text-muted);font-size:0.72rem;">
                <?= date('d/m<br>H:i', strtotime($t['updated_at'])) ?>
            </div>
        </a>
        <?php endforeach; endif; ?>
    </div>
</div>

