<div class="p-4 p-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="section-eyebrow">// PRODUCT & GACHA MANAGEMENT</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-box-open text-warning me-2"></i> จัดการสินค้าหน้าร้าน & สินค้าสุ่ม
            </h3>
            <p class="text-muted small mb-0">เพิ่ม แก้ไข กำหนดราคา จัดการสต็อก และตั้งค่าระบบสินค้าแบบสุ่ม (ปรับ % เกลือได้)</p>
        </div>
        <button class="btn-cyber-primary" id="open_insert" style="background: linear-gradient(135deg, #facc15, #ca8a04); color: #000; font-weight: 700; border: none; padding: 10px 20px; border-radius: 12px; box-shadow: 0 0 15px rgba(250, 204, 21, 0.3);">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มสินค้าใหม่
        </button>
    </div>

    <div class="admin-inner-card">
        <div class="table-responsive">
            <table class="admin-table align-middle">
                <thead>
                    <tr>
                        <th class="ps-3" style="width:70px;">ID</th>
                        <th style="width:70px;">ภาพสินค้า</th>
                        <th>ชื่อสินค้า / หมวดหมู่</th>
                        <th class="text-center">ประเภท</th>
                        <th class="text-center">ราคา</th>
                        <th class="text-center">คงเหลือในสต็อก</th>
                        <th class="text-center pe-3" style="width:240px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $get_prods = dd_q("SELECT * FROM box_product ORDER BY id DESC");
                    if ($get_prods && $get_prods->rowCount() > 0) {
                        while ($row = $get_prods->fetch(PDO::FETCH_ASSOC)) {
                            $stock_q = dd_q("SELECT COUNT(*) as count FROM box_stock WHERE p_id = ?", [$row['id']]);
                            $stock_count = $stock_q ? intval($stock_q->fetch(PDO::FETCH_ASSOC)['count'] ?? 0) : 0;
                            $is_random = (isset($row['type']) && intval($row['type']) === 0);
                            $win_pct = isset($row['percent']) ? intval($row['percent']) : 100;
                    ?>
                        <tr>
                            <td class="ps-3 fw-bold text-white">#<?php echo $row['id']; ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($row['img']); ?>" class="rounded-3 border border-secondary border-opacity-25"
                                    width="46px" height="46px" style="object-fit: cover;" onerror="this.src='assets/icon/box.png'">
                            </td>
                            <td>
                                <span class="text-white fw-bold d-block" style="font-family:var(--font-cyber); font-size: 0.95rem;"><?php echo htmlspecialchars($row['name']); ?></span>
                                <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25 mt-1" style="font-size: 0.72rem;">
                                    <?php echo htmlspecialchars($row['c_type'] ?? 'PROGRAM PRODUCT'); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($is_random): ?>
                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 px-2 py-1">
                                        <i class="fa-solid fa-dice me-1"></i> สินค้าสุ่ม (<?= $win_pct ?>%)
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-50 px-2 py-1">
                                        <i class="fa-solid fa-cart-shopping me-1"></i> ซื้อตรง
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-warning" style="font-family:var(--font-cyber);font-size:1.05rem;">฿<?php echo number_format($row['price'], 2); ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($stock_count > 0): ?>
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-2 py-1">
                                        <i class="fa-solid fa-box-open me-1"></i> <?= number_format($stock_count) ?> ชิ้น
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 px-2 py-1">
                                        <i class="fa-solid fa-ban me-1"></i> สินค้าหมด
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="?page=stock_manage&id=<?php echo $row["id"]; ?>"
                                        class="btn-cyber-stock" title="จัดการคีย์สต็อก">
                                        <i class="fa-solid fa-boxes-stacked"></i> สต็อก
                                    </a>
                                    <button class="btn-cyber-edit"
                                        onclick="get_detail(<?php echo $row['id']; ?>)" title="แก้ไขสินค้า">
                                        <i class="fa-solid fa-pen-to-square"></i> แก้ไข
                                    </button>
                                    <button class="btn-cyber-del"
                                        onclick="del('<?php echo $row['id']; ?>','<?php echo htmlspecialchars($row['name']); ?>')" title="ลบสินค้า">
                                        <i class="fa-solid fa-trash"></i> ลบ
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fa-solid fa-box-open fs-2 mb-2 d-block text-secondary"></i>
                                ยังไม่มีรายการสินค้า กดปุ่ม "เพิ่มสินค้าใหม่" ด้านบนเพื่อเริ่มลงขายสินค้า
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal เพิ่มสินค้าใหม่ -->
<div class="modal fade" id="product_insert" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg" style="background:#0c0d14; border: 1px solid rgba(250, 204, 21, 0.2) !important;">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-plus text-warning me-2"></i> เพิ่มสินค้าใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <!-- 1. ข้อมูลพื้นฐาน -->
                    <div class="col-md-7">
                        <label class="admin-label">ชื่อสินค้า <span class="text-danger">*</span></label>
                        <input type="text" id="p_name" class="admin-input form-control" placeholder="เช่น กล่องสุ่ม FiveM หรือ รหัส VIP">
                    </div>
                    <div class="col-md-5">
                        <label class="admin-label">ราคาสินค้า (บาท) <span class="text-danger">*</span></label>
                        <input type="number" id="p_price" class="admin-input form-control" placeholder="100" value="100" min="0" step="0.01">
                    </div>
                    <div class="col-md-6">
                        <label class="admin-label">หมวดหมู่สินค้า</label>
                        <select id="p_category" class="admin-input form-select">
                            <?php
                            $c_q = dd_q("SELECT * FROM category ORDER BY c_id DESC");
                            if ($c_q && $c_q->rowCount() > 0) {
                                while ($c = $c_q->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$c['c_name']}'>{$c['c_name']}</option>";
                                }
                            } else {
                                echo "<option value='PROGRAM PRODUCT'>PROGRAM PRODUCT</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-label">URL รูปภาพสินค้า</label>
                        <input type="text" id="p_img" class="admin-input form-control" placeholder="https://..." value="assets/icon/box.png">
                    </div>

                    <!-- 2. ประเภทสินค้า: ซื้อตรง vs สินค้าแบบสุ่ม -->
                    <div class="col-12">
                        <label class="admin-label text-warning"><i class="fa-solid fa-shapes me-1"></i> ประเภทสินค้า (วิธีกระบวนการขาย)</label>
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check form-check-inline p-2 px-3 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); cursor:pointer;">
                                <input class="form-check-input" type="radio" name="p_type" id="p_type_direct" value="1" checked onchange="toggleInsertGachaOptions()">
                                <label class="form-check-label text-white fw-bold cursor-pointer" for="p_type_direct">
                                    🛍️ สินค้าทั่วไป (ซื้อโดยตรง)
                                </label>
                            </div>
                            <div class="form-check form-check-inline p-2 px-3 rounded-3" style="background: rgba(250,204,21,0.08); border: 1px solid rgba(250,204,21,0.3); cursor:pointer;">
                                <input class="form-check-input" type="radio" name="p_type" id="p_type_random" value="0" onchange="toggleInsertGachaOptions()">
                                <label class="form-check-label text-warning fw-bold cursor-pointer" for="p_type_random">
                                    🎲 สินค้าแบบสุ่ม (Mystery Box / ปรับ % เกลือได้)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 3. แผงตั้งค่าสินค้าแบบสุ่ม (ปรากฏเมื่อเลือกสินค้าแบบสุ่ม) -->
                    <div class="col-12" id="insert_gacha_box" style="display:none;">
                        <div class="p-3 rounded-3" style="background: rgba(250, 204, 21, 0.05); border: 1px dashed rgba(250, 204, 21, 0.4);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-warning"><i class="fa-solid fa-sliders me-1"></i> ตั้งค่าอัตราโอกาสได้รางวัล (% Win Rate / % เกลือ)</span>
                                <span class="badge bg-warning text-dark fw-bold" id="p_pct_badge">โอกาสได้ 50% | เกลือ 50%</span>
                            </div>
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-8 col-sm-9">
                                    <input type="range" class="form-range" id="p_percent_range" min="0" max="100" value="50" oninput="syncInsertPercent(this.value)">
                                </div>
                                <div class="col-4 col-sm-3">
                                    <div class="input-group input-group-sm">
                                        <input type="number" id="p_percent" class="admin-input form-control text-center text-warning fw-bold" min="0" max="100" value="50" oninput="syncInsertPercent(this.value)">
                                        <span class="input-group-text bg-dark border-secondary text-white">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="admin-label text-muted small">ข้อความเมื่อเปิดได้เกลือ (รางวัลปลอบใจ)</label>
                                <input type="text" id="p_salt_prize" class="admin-input form-control form-control-sm" placeholder="เช่น เสียใจด้วยนะ ไม่ได้รับรางวัล หรือ ขอบคุณที่ร่วมสนุก" value="เสียใจด้วยนะ ไม่ได้รับรางวัล">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="admin-label">การรับประกันสินค้า</label>
                        <input type="text" id="p_warranty" class="admin-input form-control" placeholder="เช่น 1 ชั่วโมง, 1 วัน หรือ ไม่มีประกัน" value="1 ชั่วโมง (มีปัญหากดรายงานได้ทันที)">
                    </div>

                    <div class="col-md-6">
                        <label class="admin-label">ยอดขายจำลองเริ่มต้น (ชิ้น)</label>
                        <input type="number" id="p_sales_count" class="admin-input form-control" placeholder="0" value="24" min="0">
                    </div>

                    <!-- 4. รายละเอียด -->
                    <div class="col-12">
                        <label class="admin-label">รายละเอียดสินค้า</label>
                        <textarea id="p_des" rows="3" class="admin-input form-control" placeholder="รายละเอียดคุณสมบัติสินค้า / คำแนะนำการใช้งาน..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn-cyber-primary w-100 py-2" id="insert_btn" style="background: linear-gradient(135deg, #facc15, #ca8a04); color: #000; font-weight: 700; border: none; border-radius: 12px;">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกเพิ่มสินค้า (สต็อกคงเหลือ 2 ชิ้น)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal แก้ไขสินค้า -->
<div class="modal fade" id="product_detail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg" style="background:#0c0d14; border: 1px solid rgba(250, 204, 21, 0.2) !important;">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-pen-to-square text-warning me-2"></i> แก้ไขข้อมูลสินค้า
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="u_id">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="admin-label">ชื่อสินค้า <span class="text-danger">*</span></label>
                        <input type="text" id="u_name" class="admin-input form-control">
                    </div>
                    <div class="col-md-5">
                        <label class="admin-label">ราคาสินค้า (บาท) <span class="text-danger">*</span></label>
                        <input type="number" id="u_price" class="admin-input form-control" min="0" step="0.01">
                    </div>
                    <div class="col-md-6">
                        <label class="admin-label">หมวดหมู่สินค้า</label>
                        <select id="u_category" class="admin-input form-select">
                            <?php
                            $c_q2 = dd_q("SELECT * FROM category ORDER BY c_id DESC");
                            if ($c_q2 && $c_q2->rowCount() > 0) {
                                while ($c2 = $c_q2->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$c2['c_name']}'>{$c2['c_name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-label">URL รูปภาพสินค้า</label>
                        <input type="text" id="u_img" class="admin-input form-control">
                    </div>

                    <!-- ประเภทสินค้าในหน้าแก้ไข -->
                    <div class="col-12">
                        <label class="admin-label text-warning"><i class="fa-solid fa-shapes me-1"></i> ประเภทสินค้า (วิธีกระบวนการขาย)</label>
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check form-check-inline p-2 px-3 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); cursor:pointer;">
                                <input class="form-check-input" type="radio" name="u_type" id="u_type_direct" value="1" onchange="toggleEditGachaOptions()">
                                <label class="form-check-label text-white fw-bold cursor-pointer" for="u_type_direct">
                                    🛍️ สินค้าทั่วไป (ซื้อโดยตรง)
                                </label>
                            </div>
                            <div class="form-check form-check-inline p-2 px-3 rounded-3" style="background: rgba(250,204,21,0.08); border: 1px solid rgba(250,204,21,0.3); cursor:pointer;">
                                <input class="form-check-input" type="radio" name="u_type" id="u_type_random" value="0" onchange="toggleEditGachaOptions()">
                                <label class="form-check-label text-warning fw-bold cursor-pointer" for="u_type_random">
                                    🎲 สินค้าแบบสุ่ม (Mystery Box / ปรับ % เกลือได้)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- แผงตั้งค่าสินค้าแบบสุ่มในหน้าแก้ไข -->
                    <div class="col-12" id="edit_gacha_box" style="display:none;">
                        <div class="p-3 rounded-3" style="background: rgba(250, 204, 21, 0.05); border: 1px dashed rgba(250, 204, 21, 0.4);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-warning"><i class="fa-solid fa-sliders me-1"></i> ตั้งค่าอัตราโอกาสได้รางวัล (% Win Rate / % เกลือ)</span>
                                <span class="badge bg-warning text-dark fw-bold" id="u_pct_badge">โอกาสได้ 50% | เกลือ 50%</span>
                            </div>
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-8 col-sm-9">
                                    <input type="range" class="form-range" id="u_percent_range" min="0" max="100" value="50" oninput="syncEditPercent(this.value)">
                                </div>
                                <div class="col-4 col-sm-3">
                                    <div class="input-group input-group-sm">
                                        <input type="number" id="u_percent" class="admin-input form-control text-center text-warning fw-bold" min="0" max="100" value="50" oninput="syncEditPercent(this.value)">
                                        <span class="input-group-text bg-dark border-secondary text-white">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="admin-label text-muted small">ข้อความเมื่อเปิดได้เกลือ (รางวัลปลอบใจ)</label>
                                <input type="text" id="u_salt_prize" class="admin-input form-control form-control-sm" placeholder="เช่น เสียใจด้วยนะ ไม่ได้รับรางวัล">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="admin-label">การรับประกันสินค้า</label>
                        <input type="text" id="u_warranty" class="admin-input form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="admin-label">ยอดขายจำลอง (ชิ้น)</label>
                        <input type="number" id="u_sales_count" class="admin-input form-control" min="0">
                    </div>

                    <div class="col-12">
                        <label class="admin-label">รายละเอียดสินค้า</label>
                        <textarea id="u_des" rows="3" class="admin-input form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn-cyber-primary w-100 py-2" id="save_btn" style="background: linear-gradient(135deg, #facc15, #ca8a04); color: #000; font-weight: 700; border: none; border-radius: 12px;">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการแก้ไข
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleInsertGachaOptions() {
    const isRandom = $("#p_type_random").is(":checked");
    if (isRandom) {
        $("#insert_gacha_box").slideDown(200);
    } else {
        $("#insert_gacha_box").slideUp(200);
    }
}

function syncInsertPercent(val) {
    val = Math.max(0, Math.min(100, parseInt(val) || 0));
    $("#p_percent_range").val(val);
    $("#p_percent").val(val);
    const saltVal = 100 - val;
    $("#p_pct_badge").text(`โอกาสได้ ${val}% | เกลือ ${saltVal}%`);
}

function toggleEditGachaOptions() {
    const isRandom = $("#u_type_random").is(":checked");
    if (isRandom) {
        $("#edit_gacha_box").slideDown(200);
    } else {
        $("#edit_gacha_box").slideUp(200);
    }
}

function syncEditPercent(val) {
    val = Math.max(0, Math.min(100, parseInt(val) || 0));
    $("#u_percent_range").val(val);
    $("#u_percent").val(val);
    const saltVal = 100 - val;
    $("#u_pct_badge").text(`โอกาสได้ ${val}% | เกลือ ${saltVal}%`);
}

$("#open_insert").click(function () {
    const myModal = new bootstrap.Modal($('#product_insert'), { keyboard: false });
    myModal.show();
});

$("#insert_btn").click(function () {
    var name = $("#p_name").val().trim();
    var price = $("#p_price").val().trim();
    var category = $("#p_category").val();
    var isRandom = $("#p_type_random").is(":checked") ? 0 : 1;
    var percent = $("#p_percent").val();
    var saltPrize = $("#p_salt_prize").val().trim();
    var warranty = $("#p_warranty").val().trim();
    var salesCount = $("#p_sales_count").val().trim();

    if (!name || price === "") {
        Swal.fire({
            icon: 'warning',
            title: 'แจ้งเตือน',
            text: 'กรุณากรอกชื่อสินค้าและราคาสินค้าให้ครบถ้วน',
            background: '#0a0a0f',
            color: '#ffffff',
            confirmButtonColor: '#facc15'
        });
        return;
    }

    var formData = new FormData();
    formData.append('name', name);
    formData.append('price', price);
    formData.append('c_type', category || 'PROGRAM PRODUCT');
    formData.append('img', $("#p_img").val().trim() || 'assets/icon/box.png');
    formData.append('des', $("#p_des").val().trim());
    formData.append('type', isRandom);
    formData.append('percent', percent);
    formData.append('salt_prize', saltPrize || 'เสียใจด้วยนะ ไม่ได้รับรางวัล');
    formData.append('warranty', warranty || 'ไม่มีประกัน');
    formData.append('sales_count', salesCount || '0');

    $.ajax({
        type: 'POST',
        url: 'system/backend/product_insert.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: res.message,
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#facc15'
            }).then(() => {
                location.reload();
            });
        },
        error: function (err) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: err.responseJSON ? err.responseJSON.message : 'เกิดข้อผิดพลาดในการเพิ่มสินค้า',
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#ef4444'
            });
        }
    });
});

function get_detail(id) {
    var formData = new FormData();
    formData.append('p_id', id);
    formData.append('id', id);
    $.ajax({
        type: 'POST',
        url: 'system/backend/call/product_detail.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            $("#u_id").val(id);
            $("#u_name").val(res.name);
            $("#u_price").val(res.price);
            $("#u_category").val(res.c_type);
            $("#u_img").val(res.img);
            $("#u_des").val(res.des);
            $("#u_warranty").val(res.warranty || 'ไม่มีประกัน');
            $("#u_sales_count").val(res.sales_count || '0');

            const prodType = parseInt(res.type);
            if (prodType === 0) {
                $("#u_type_random").prop("checked", true);
            } else {
                $("#u_type_direct").prop("checked", true);
            }
            toggleEditGachaOptions();

            const winPct = res.percent !== undefined ? parseInt(res.percent) : 100;
            syncEditPercent(winPct);
            $("#u_salt_prize").val(res.salt_prize || 'เสียใจด้วยนะ ไม่ได้รับรางวัล');

            const myModal = new bootstrap.Modal($('#product_detail'), { keyboard: false });
            myModal.show();
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถดึงข้อมูลสินค้านี้ได้',
                background: '#0a0a0f',
                color: '#ffffff'
            });
        }
    });
}

$("#save_btn").click(function () {
    var name = $("#u_name").val().trim();
    var price = $("#u_price").val().trim();
    var isRandom = $("#u_type_random").is(":checked") ? 0 : 1;
    var percent = $("#u_percent").val();
    var saltPrize = $("#u_salt_prize").val().trim();
    var warranty = $("#u_warranty").val().trim();
    var salesCount = $("#u_sales_count").val().trim();

    if (!name || price === "") {
        Swal.fire({
            icon: 'warning',
            title: 'แจ้งเตือน',
            text: 'กรุณากรอกชื่อสินค้าและราคาสินค้า',
            background: '#0a0a0f',
            color: '#ffffff',
            confirmButtonColor: '#facc15'
        });
        return;
    }

    var formData = new FormData();
    formData.append('id', $("#u_id").val());
    formData.append('name', name);
    formData.append('price', price);
    formData.append('c_type', $("#u_category").val() || 'PROGRAM PRODUCT');
    formData.append('img', $("#u_img").val().trim() || 'assets/icon/box.png');
    formData.append('des', $("#u_des").val().trim());
    formData.append('type', isRandom);
    formData.append('percent', percent);
    formData.append('salt_prize', saltPrize || 'เสียใจด้วยนะ ไม่ได้รับรางวัล');
    formData.append('warranty', warranty || 'ไม่มีประกัน');
    formData.append('sales_count', salesCount || '0');

    $.ajax({
        type: 'POST',
        url: 'system/backend/product_update.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: res.message,
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#facc15'
            }).then(() => {
                location.reload();
            });
        },
        error: function (err) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: err.responseJSON ? err.responseJSON.message : 'เกิดข้อผิดพลาดในการแก้ไขสินค้า',
                background: '#0a0a0f',
                color: '#ffffff',
                confirmButtonColor: '#ef4444'
            });
        }
    });
});

function del(id, name) {
    Swal.fire({
        title: 'ยืนยันการลบสินค้า?',
        text: `คุณต้องการลบสินค้า "${name}" และสต็อกทั้งหมดใช่หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก',
        background: '#0a0a0f',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            var formData = new FormData();
            formData.append('id', id);
            formData.append('p_id', id);
            $.ajax({
                type: 'POST',
                url: 'system/backend/product_del.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบสำเร็จ',
                        text: res.message,
                        background: '#0a0a0f',
                        color: '#ffffff',
                        confirmButtonColor: '#facc15'
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
}
</script>
