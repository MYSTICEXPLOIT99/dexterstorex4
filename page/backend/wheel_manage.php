<?php
// Fetch categories for filter dropdown
$cats_q = dd_q("SELECT c_id, c_name FROM category ORDER BY c_id ASC");
$all_categories = $cats_q ? $cats_q->fetchAll(PDO::FETCH_ASSOC) : [];

// Fetch all wheels with category names
$wheels_q = dd_q("SELECT w.*, c.c_name as cat_name FROM wheels w LEFT JOIN category c ON w.category_id = c.c_id ORDER BY w.id DESC");
$all_wheels = $wheels_q ? $wheels_q->fetchAll(PDO::FETCH_ASSOC) : [];

// Selected wheel ID for item management
$selected_wheel_id = intval($_GET['wheel_id'] ?? ($all_wheels[0]['id'] ?? 1));

// Current selected wheel info
$cur_wheel = null;
foreach ($all_wheels as $w) {
    if ($w['id'] == $selected_wheel_id) {
        $cur_wheel = $w;
        break;
    }
}
if (!$cur_wheel && !empty($all_wheels)) {
    $cur_wheel = $all_wheels[0];
    $selected_wheel_id = $cur_wheel['id'];
}

// Fetch items for selected wheel
$items_q = dd_q("SELECT * FROM wheel_item WHERE wheel_id = ? ORDER BY sort_order ASC, id ASC", [$selected_wheel_id]);
$wheel_items = $items_q ? $items_q->fetchAll(PDO::FETCH_ASSOC) : [];

// Calculate total percentage
$total_percent = 0;
foreach ($wheel_items as $item) {
    $total_percent += intval($item['percent']);
}
?>

<style>
.wheel-stepper-box {
    display: inline-flex;
    align-items: center;
    background: rgba(0, 0, 0, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    overflow: hidden;
    height: 34px;
}
.wheel-stepper-btn {
    background: rgba(255, 255, 255, 0.06);
    border: none;
    color: #00c9ff;
    width: 30px;
    height: 100%;
    font-weight: bold;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    cursor: pointer;
}
.wheel-stepper-btn:hover {
    background: #00c9ff;
    color: #000;
}
.wheel-stepper-val {
    width: 44px;
    background: transparent;
    border: none;
    color: #00e5ff;
    font-weight: bold;
    text-align: center;
    font-size: 0.9rem;
    outline: none;
}
.wheel-stepper-unit {
    color: #888;
    font-size: 0.75rem;
    padding-right: 6px;
    font-weight: bold;
}
</style>

<div class="p-4 p-lg-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="section-eyebrow">// LUCKY DRAW & WHEEL MANAGEMENT</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-dharmachakra text-info me-2"></i> จัดการกล่องสุ่ม & วงล้อเสี่ยงโชค
            </h3>
            <p class="text-muted small mb-0">สร้างและปรับแต่งวงล้อ กำหนดราคาหมุน อัตราโอกาสออก (%) และจัดการของรางวัล</p>
        </div>
        <button class="btn-cyber-primary" data-bs-toggle="modal" data-bs-target="#create_wheel_modal">
            <i class="fa-solid fa-plus me-1"></i> สร้างวงล้อใหม่
        </button>
    </div>

    <!-- 1. ตารางวงล้อทั้งหมดในระบบ (Native Admin Table Theme) -->
    <div class="admin-inner-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-layer-group text-warning me-2"></i> วงล้อทั้งหมดในระบบ (<?= count($all_wheels) ?> วงล้อ)
            </h5>
            <span class="text-muted small">คลิกปุ่ม <strong>"จัดการช่องรางวัล"</strong> เพื่อเลือกปรับแต่ง</span>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="ps-3" style="width:70px;">ID</th>
                        <th>ชื่อวงล้อ</th>
                        <th>หมวดหมู่</th>
                        <th class="text-center">ราคาหมุน / ครั้ง</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center pe-3" style="width:230px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($all_wheels)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">ยังไม่มีวงล้อในระบบ กดปุ่ม "สร้างวงล้อใหม่" ด้านบนเพื่อเริ่มต้น</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($all_wheels as $w): ?>
                            <?php $is_cur = ($w['id'] == $selected_wheel_id); ?>
                            <tr style="<?= $is_cur ? 'background:rgba(0,201,255,0.06);' : '' ?>">
                                <td class="ps-3 fw-bold text-white">#<?= $w['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-white" style="font-family:var(--font-cyber);">
                                        <?= htmlspecialchars($w['title']) ?>
                                        <?php if ($is_cur): ?>
                                            <span class="badge bg-primary ms-1" style="font-size:0.65rem;">กำลังเลือก</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted text-truncate d-block" style="max-width:260px;"><?= htmlspecialchars($w['des'] ?: 'ไม่มีคำอธิบาย') ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-dark border border-secondary border-opacity-50 text-info" style="font-size:0.75rem;">
                                        <?= !empty($w['cat_name']) ? htmlspecialchars($w['cat_name']) : '🌐 ทั่วไป' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-info" style="font-family:var(--font-cyber);font-size:1rem;">฿<?= number_format($w['spin_price']) ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($w['status'] === 'on'): ?>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1" style="font-size:0.7rem;">🟢 เปิดใช้งาน</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 px-2 py-1" style="font-size:0.7rem;">🔴 ปิดใช้งาน</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="d-flex justify-content-center align-items-center gap-1.5">
                                        <a href="?page=wheel_manage&wheel_id=<?= $w['id'] ?>" class="btn-cyber-edit px-2 py-1" style="background:<?= $is_cur ? 'var(--primary, #00c9ff)' : '#1e293b' ?>;color:<?= $is_cur ? '#000' : '#00c9ff' ?>;border:1px solid #00c9ff;font-size:0.78rem;">
                                            <i class="fa-solid fa-gear me-1"></i> จัดการช่อง
                                        </a>
                                        <button class="btn-cyber-edit py-1 px-2 btn-edit-wheel" data-wheel='<?= json_encode($w, JSON_HEX_APOS|JSON_HEX_QUOT) ?>' title="แก้ไข">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn-cyber-del py-1 px-2 btn-del-wheel" data-id="<?= $w['id'] ?>" data-title="<?= htmlspecialchars($w['title']) ?>" title="ลบ">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($cur_wheel): ?>
    <!-- 2. ตารางจัดการช่องรางวัลของวงล้อที่เลือก -->
    <div class="admin-inner-card">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 pb-3 border-bottom border-secondary border-opacity-25">
            <div>
                <h5 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-list-check text-info me-2"></i> ช่องรางวัลของ: <span class="text-info"><?= htmlspecialchars($cur_wheel['title']) ?></span>
                </h5>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span id="totalPercentBadge" class="badge <?= ($total_percent == 100) ? 'bg-success bg-opacity-25 text-success border border-success' : 'bg-danger bg-opacity-25 text-danger border border-danger' ?> px-2.5 py-1" style="font-size:0.8rem;font-family:var(--font-cyber);">
                        <i class="fa-solid <?= ($total_percent == 100) ? 'fa-circle-check' : 'fa-triangle-exclamation' ?> me-1"></i>
                        โอกาสรวม: <span id="totalPercentText"><?= $total_percent ?>%</span> <?= ($total_percent == 100) ? '(ครบ 100% พอดี)' : '(ยังไม่ครบ 100%)' ?>
                    </span>
                    <span class="text-muted small">จำนวนช่องรางวัล: <?= count($wheel_items) ?> ช่อง</span>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-warning fw-bold px-3 py-1.5 rounded-3" id="btn_auto_balance" data-wheel-id="<?= $cur_wheel['id'] ?>" style="color:#000;font-size:0.85rem;">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> ปรับเกลี่ยให้ครบ 100% พอดี
                </button>
                <button class="btn-cyber-primary py-1.5 px-3" data-bs-toggle="modal" data-bs-target="#add_item_modal">
                    <i class="fa-solid fa-plus me-1"></i> เพิ่มรางวัลลงช่อง
                </button>
            </div>
        </div>

        <form id="form_batch_percents">
            <input type="hidden" name="action" value="save_batch_percentages">
            <input type="hidden" name="wheel_id" value="<?= $cur_wheel['id'] ?>">

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width:60px;">ลำดับ</th>
                            <th style="width:65px;">รูป</th>
                            <th>ชื่อรางวัล</th>
                            <th>ประเภท</th>
                            <th>มูลค่ารางวัล</th>
                            <th class="text-center" style="width:160px;">โอกาสออก (%)</th>
                            <th class="text-center" style="width:80px;">สีช่อง</th>
                            <th class="text-center pe-3" style="width:120px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($wheel_items)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">ยังไม่มีช่องรางวัลในวงล้อนี้ กดปุ่ม "เพิ่มรางวัลลงช่อง" ด้านบน</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($wheel_items as $idx => $it): ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-muted">#<?= $idx + 1 ?></td>
                                    <td>
                                        <img src="<?= htmlspecialchars($it['img'] ?: 'assets/icon/box.png') ?>" alt="item" style="width:36px;height:36px;object-fit:contain;border-radius:8px;background:rgba(255,255,255,0.05);padding:2px;border:1px solid rgba(255,255,255,0.1);" onerror="this.src='assets/icon/box.png'">
                                    </td>
                                    <td>
                                        <span class="text-white fw-bold" style="font-family:var(--font-cyber);"><?= htmlspecialchars($it['name']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($it['reward_type'] === 'point'): ?>
                                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1"><i class="fa-solid fa-coins me-1"></i> พอยท์</span>
                                        <?php elseif ($it['reward_type'] === 'product'): ?>
                                            <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25 px-2 py-1"><i class="fa-solid fa-box me-1"></i> สินค้า/คีย์</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25 px-2 py-1"><i class="fa-solid fa-ban me-1"></i> เกลือ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($it['reward_type'] === 'point'): ?>
                                            <span class="text-success fw-bold">+<?= number_format(floatval($it['reward_value'])) ?> ฿</span>
                                        <?php elseif ($it['reward_type'] === 'product'): ?>
                                            <span class="text-info fw-bold"><?= htmlspecialchars($it['reward_value']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="wheel-stepper-box">
                                            <button type="button" class="wheel-stepper-btn btn-step-pct" data-step="-1" data-target="pct_<?= $it['id'] ?>">-</button>
                                            <input type="number" name="percents[<?= $it['id'] ?>]" id="pct_<?= $it['id'] ?>" class="wheel-stepper-val pct-input" value="<?= $it['percent'] ?>" min="0" max="100">
                                            <span class="wheel-stepper-unit">%</span>
                                            <button type="button" class="wheel-stepper-btn btn-step-pct" data-step="1" data-target="pct_<?= $it['id'] ?>">+</button>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="d-inline-block rounded-circle border border-white border-opacity-25 shadow-sm" style="width:22px;height:22px;background:<?= htmlspecialchars($it['color']) ?>;vertical-align:middle;" title="<?= htmlspecialchars($it['color']) ?>"></span>
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-flex justify-content-center gap-1.5">
                                            <button type="button" class="btn-cyber-edit py-1 px-2 btn-edit-item" data-item='<?= json_encode($it, JSON_HEX_APOS|JSON_HEX_QUOT) ?>' title="แก้ไข">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button type="button" class="btn-cyber-del py-1 px-2 btn-del-item" data-id="<?= $it['id'] ?>" data-name="<?= htmlspecialchars($it['name']) ?>" title="ลบ">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($wheel_items)): ?>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-secondary border-opacity-25 flex-wrap gap-2">
                <small class="text-muted"><i class="fa-solid fa-circle-info text-info me-1"></i> กดปุ่ม + / - เพื่อปรับ % แล้วกดบันทึกผลรวมด้านขวา</small>
                <button type="submit" class="btn-cyber-primary py-2 px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกอัตรา % ทั้งหมด
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
    <?php endif; ?>
</div>

<!-- Modal 1: Create Wheel -->
<div class="modal fade" id="create_wheel_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-plus text-info me-2"></i> สร้างวงล้อเสี่ยงโชคใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form_create_wheel">
                <input type="hidden" name="action" value="create_wheel">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="admin-label">ชื่อวงล้อ (Wheel Title) <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="admin-input form-control" placeholder="เช่น วงล้อโชคลาภ FiveM / Valorant" required>
                    </div>
                    <div class="mb-3">
                        <label class="admin-label">หมวดหมู่สินค้า</label>
                        <select name="category_id" class="admin-input form-select">
                            <option value="0">🌐 ทั่วไป / ไม่ระบุหมวด</option>
                            <?php foreach ($all_categories as $c): ?>
                                <option value="<?= $c['c_id'] ?>"><?= htmlspecialchars($c['c_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="admin-label">ราคาหมุน / ครั้ง (พอยท์) <span class="text-danger">*</span></label>
                            <input type="number" name="spin_price" class="admin-input form-control" value="20" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="admin-label">สถานะ</label>
                            <select name="status" class="admin-input form-select">
                                <option value="on">🟢 เปิดใช้งาน</option>
                                <option value="off">🔴 ปิดใช้งาน</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="admin-label">คำอธิบายสั้นๆ</label>
                        <textarea name="des" class="admin-input form-control" rows="2" placeholder="รายละเอียดหรือเงื่อนไข..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn-cyber-primary w-100 py-2">
                        <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกสร้างวงล้อ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Edit Wheel -->
<div class="modal fade" id="edit_wheel_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-pen-to-square text-info me-2"></i> แก้ไขข้อมูลวงล้อ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form_edit_wheel">
                <input type="hidden" name="action" value="edit_wheel">
                <input type="hidden" name="id" id="edit_wheel_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="admin-label">ชื่อวงล้อ <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_wheel_title" class="admin-input form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="admin-label">หมวดหมู่</label>
                        <select name="category_id" id="edit_wheel_cat" class="admin-input form-select">
                            <option value="0">🌐 ทั่วไป / ไม่ระบุหมวด</option>
                            <?php foreach ($all_categories as $c): ?>
                                <option value="<?= $c['c_id'] ?>"><?= htmlspecialchars($c['c_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="admin-label">ราคาหมุน / ครั้ง</label>
                            <input type="number" name="spin_price" id="edit_wheel_price" class="admin-input form-control" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="admin-label">สถานะ</label>
                            <select name="status" id="edit_wheel_status" class="admin-input form-select">
                                <option value="on">🟢 เปิดใช้งาน</option>
                                <option value="off">🔴 ปิดใช้งาน</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="admin-label">คำอธิบาย</label>
                        <textarea name="des" id="edit_wheel_des" class="admin-input form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn-cyber-primary w-100 py-2">
                        <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Add Item to Wheel -->
<div class="modal fade" id="add_item_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-gift text-info me-2"></i> เพิ่มรางวัลลงช่องวงล้อ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form_add_item">
                <input type="hidden" name="action" value="add_item">
                <input type="hidden" name="wheel_id" value="<?= $selected_wheel_id ?>">
                <div class="modal-body p-4">
                    <!-- Quick Presets -->
                    <div class="mb-3 p-2.5 rounded-3" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);">
                        <small class="text-muted d-block mb-1.5 fw-bold"><i class="fa-solid fa-bolt text-warning me-1"></i> เทมเพลตด่วน:</small>
                        <div class="d-flex gap-1.5 flex-wrap">
                            <button type="button" class="btn btn-sm btn-dark border border-secondary border-opacity-50 py-1 px-2 text-success btn-preset" data-name="แต้ม 20 พอยท์" data-type="point" data-val="20" data-color="#22c55e">+20 พอยท์</button>
                            <button type="button" class="btn btn-sm btn-dark border border-secondary border-opacity-50 py-1 px-2 text-info btn-preset" data-name="แต้ม 50 พอยท์" data-type="point" data-val="50" data-color="#3b82f6">+50 พอยท์</button>
                            <button type="button" class="btn btn-sm btn-dark border border-secondary border-opacity-50 py-1 px-2 text-warning btn-preset" data-name="แต้ม 100 พอยท์" data-type="point" data-val="100" data-color="#eab308">+100 พอยท์</button>
                            <button type="button" class="btn btn-sm btn-dark border border-secondary border-opacity-50 py-1 px-2 text-danger btn-preset" data-name="แต้ม 500 พอยท์" data-type="point" data-val="500" data-color="#ef4444">+500 พอยท์</button>
                            <button type="button" class="btn btn-sm btn-dark border border-secondary border-opacity-50 py-1 px-2 text-secondary btn-preset" data-name="เสียใจด้วยนะ (เกลือ)" data-type="salt" data-val="0" data-color="#64748b">❌ เกลือ</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="admin-label">ชื่อรางวัล <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="add_item_name" class="admin-input form-control" placeholder="เช่น แต้ม 50 พอยท์ หรือ คีย์ VIP" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="admin-label">ประเภทรางวัล <span class="text-danger">*</span></label>
                            <select name="reward_type" id="add_reward_type" class="admin-input form-select" required>
                                <option value="point">🪙 พอยท์ (Point)</option>
                                <option value="product">📦 สินค้า / โค้ดคีย์</option>
                                <option value="salt">❌ เกลือ (ไม่ได้รางวัล)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="admin-label">มูลค่า / โค้ดที่ได้รับ</label>
                            <input type="text" name="reward_value" id="add_reward_value" class="admin-input form-control" placeholder="50" value="50">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="admin-label">โอกาสออก (%) <span class="text-danger">*</span></label>
                            <input type="number" name="percent" class="admin-input form-control" value="20" min="0" max="100" required>
                        </div>
                        <div class="col-6">
                            <label class="admin-label">สีพื้นหลังช่อง</label>
                            <input type="color" name="color" id="add_item_color" class="form-control form-control-color w-100" value="#00c9ff" style="background:#151824;border:1px solid rgba(255,255,255,0.1);height:42px;">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="admin-label">URL รูปภาพ (ไม่ใส่ใช้รูปกล่อง)</label>
                        <input type="text" name="img" class="admin-input form-control" placeholder="https://...">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn-cyber-primary w-100 py-2">
                        <i class="fa-solid fa-plus me-1"></i> เพิ่มลงวงล้อ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 4: Edit Item -->
<div class="modal fade" id="edit_item_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-pen-to-square text-info me-2"></i> แก้ไขช่องรางวัล
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form_edit_item">
                <input type="hidden" name="action" value="edit_item">
                <input type="hidden" name="id" id="edit_item_id">
                <input type="hidden" name="wheel_id" value="<?= $selected_wheel_id ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="admin-label">ชื่อรางวัล <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_item_name" class="admin-input form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="admin-label">ประเภทรางวัล</label>
                            <select name="reward_type" id="edit_item_reward_type" class="admin-input form-select" required>
                                <option value="point">🪙 พอยท์ (Point)</option>
                                <option value="product">📦 สินค้า / โค้ดคีย์</option>
                                <option value="salt">❌ เกลือ (ไม่ได้รางวัล)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="admin-label">มูลค่า / โค้ด</label>
                            <input type="text" name="reward_value" id="edit_item_reward_value" class="admin-input form-control">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="admin-label">โอกาสออก (%)</label>
                            <input type="number" name="percent" id="edit_item_percent" class="admin-input form-control" min="0" max="100" required>
                        </div>
                        <div class="col-6">
                            <label class="admin-label">สีพื้นหลังช่อง</label>
                            <input type="color" name="color" id="edit_item_color" class="form-control form-control-color w-100" style="background:#151824;border:1px solid rgba(255,255,255,0.1);height:42px;">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="admin-label">URL รูปภาพ</label>
                        <input type="text" name="img" id="edit_item_img" class="admin-input form-control">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn-cyber-primary w-100 py-2">
                        <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const API_URL = 'system/backend/wheel_admin.php';

    // Update total percent on live typing
    function updateLiveTotalPercent() {
        let total = 0;
        $('.pct-input').each(function() {
            total += parseInt($(this).val() || 0);
        });
        $('#totalPercentText').text(total + '%');
        if (total === 100) {
            $('#totalPercentBadge').removeClass('bg-danger border-danger text-danger').addClass('bg-success border-success text-success').html('<i class="fa-solid fa-circle-check me-1"></i> โอกาสรวม: ' + total + '% (ครบ 100% พอดี)');
        } else {
            $('#totalPercentBadge').removeClass('bg-success border-success text-success').addClass('bg-danger border-danger text-danger').html('<i class="fa-solid fa-triangle-exclamation me-1"></i> โอกาสรวม: ' + total + '% (ยังไม่ครบ 100%)');
        }
    }

    $('.pct-input').on('input change', updateLiveTotalPercent);

    // Stepper Button (+ / -)
    $('.btn-step-pct').on('click', function() {
        const step = parseInt($(this).data('step'));
        const targetId = $(this).data('target');
        const input = $('#' + targetId);
        let cur = parseInt(input.val() || 0) + step;
        if (cur < 0) cur = 0;
        if (cur > 100) cur = 100;
        input.val(cur);
        updateLiveTotalPercent();
    });

    // 1-Click Auto Balance to 100%
    $('#btn_auto_balance').on('click', function() {
        const wheelId = $(this).data('wheel-id');
        Swal.fire({
            title: 'เกลี่ยโอกาสออกให้ครบ 100%?',
            text: 'ระบบจะคำนวณแบ่งอัตราโอกาสออก (%) ของทุกช่องให้รวมกันได้ 100% พอดีเป๊ะโดยอัตโนมัติ',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#00c9ff',
            confirmButtonText: '⚡ ปรับให้ครบ 100% เลย',
            cancelButtonText: 'ยกเลิก',
            background: '#0a0a0f',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(API_URL, { action: 'auto_balance_percent', wheel_id: wheelId }, function(res) {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' })
                            .then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
                    }
                });
            }
        });
    });

    // Batch Save Percentages
    $('#form_batch_percents').on('submit', function(e) {
        e.preventDefault();
        $.post(API_URL, $(this).serialize(), function(res) {
            if (res.status === 'success') {
                Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
            }
        });
    });

    // Preset Button Click
    $('.btn-preset').on('click', function() {
        $('#add_item_name').val($(this).data('name'));
        $('#add_reward_type').val($(this).data('type'));
        $('#add_reward_value').val($(this).data('val'));
        $('#add_item_color').val($(this).data('color'));
    });

    // 1. Create Wheel
    $('#form_create_wheel').on('submit', function(e) {
        e.preventDefault();
        $.post(API_URL, $(this).serialize(), function(res) {
            if (res.status === 'success') {
                Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
            }
        });
    });

    // 2. Open Edit Wheel Modal
    $('.btn-edit-wheel').on('click', function() {
        const w = $(this).data('wheel');
        $('#edit_wheel_id').val(w.id);
        $('#edit_wheel_title').val(w.title);
        $('#edit_wheel_cat').val(w.category_id);
        $('#edit_wheel_price').val(w.spin_price);
        $('#edit_wheel_status').val(w.status);
        $('#edit_wheel_des').val(w.des);
        new bootstrap.Modal(document.getElementById('edit_wheel_modal')).show();
    });

    // 3. Submit Edit Wheel
    $('#form_edit_wheel').on('submit', function(e) {
        e.preventDefault();
        $.post(API_URL, $(this).serialize(), function(res) {
            if (res.status === 'success') {
                Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
            }
        });
    });

    // 4. Delete Wheel
    $('.btn-del-wheel').on('click', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        Swal.fire({
            title: 'ยืนยันการลบวงล้อ?',
            text: `ต้องการลบวงล้อ "${title}" และรายการรางวัลทั้งหมดใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'ใช่, ลบเลย',
            cancelButtonText: 'ยกเลิก',
            background: '#0a0a0f',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(API_URL, { action: 'delete_wheel', id: id }, function(res) {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'ลบสำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' })
                            .then(() => { window.location.href = '?page=wheel_manage'; });
                    } else {
                        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
                    }
                });
            }
        });
    });

    // 5. Add Item
    $('#form_add_item').on('submit', function(e) {
        e.preventDefault();
        $.post(API_URL, $(this).serialize(), function(res) {
            if (res.status === 'success') {
                Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
            }
        });
    });

    // 6. Open Edit Item Modal
    $('.btn-edit-item').on('click', function() {
        const it = $(this).data('item');
        $('#edit_item_id').val(it.id);
        $('#edit_item_name').val(it.name);
        $('#edit_item_reward_type').val(it.reward_type);
        $('#edit_item_reward_value').val(it.reward_value);
        $('#edit_item_percent').val(it.percent);
        $('#edit_item_color').val(it.color);
        $('#edit_item_img').val(it.img);
        new bootstrap.Modal(document.getElementById('edit_item_modal')).show();
    });

    // 7. Submit Edit Item
    $('#form_edit_item').on('submit', function(e) {
        e.preventDefault();
        $.post(API_URL, $(this).serialize(), function(res) {
            if (res.status === 'success') {
                Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
            }
        });
    });

    // 8. Delete Item
    $('.btn-del-item').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        Swal.fire({
            title: 'ยืนยันการลบช่องรางวัล?',
            text: `ต้องการลบรางวัล "${name}" ออกจากวงล้อใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'ใช่, ลบเลย',
            cancelButtonText: 'ยกเลิก',
            background: '#0a0a0f',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(API_URL, { action: 'delete_item', id: id }, function(res) {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'ลบสำเร็จ', text: res.message, background: '#0a0a0f', color: '#fff' })
                            .then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message, background: '#0a0a0f', color: '#fff' });
                    }
                });
            }
        });
    });
});
</script>
