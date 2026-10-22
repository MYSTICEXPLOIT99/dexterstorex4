<?php
// ดึงข้อมูลสินค้าทั้งหมดเพื่อแสดงเป็น checkbox
$products_q = dd_q("SELECT id, name, price, c_type FROM box_product ORDER BY id DESC");
$all_products = $products_q->fetchAll(PDO::FETCH_ASSOC);

// ดึงสินค้าที่เลือกไว้
$selected_prod_ids = array_filter(array_map('trim', explode(',', $config['pop_buy_products'] ?? '')));
?>
<div class="p-4 p-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="fa-solid fa-bell text-xez-primary me-2"></i> ระบบแจ้งเตือนซื้อของ (Social Proof Marquee / Popup)</h3>
            <p class="text-muted small mb-0">ปรับแต่งข้อความ ความเร็ว และเลือกสินค้าที่ต้องการให้เด้งโชว์หน้าเว็บ</p>
        </div>
        <button class="btn xez-btn-cyan px-4" id="save_social_proof_btn">
            <i class="fa-solid fa-floppy-disk me-2"></i> บันทึกข้อมูล
        </button>
    </div>

    <div class="row g-4">
        <!-- Settings Form -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-sliders text-xez-primary me-2"></i> ตั้งค่าการแสดงผล</h5>

                <!-- Status Switch -->
                <div class="form-check form-switch mb-4 p-3 bg-white rounded-3 border d-flex align-items-center justify-content-between ps-3">
                    <label class="form-check-label fw-bold text-dark mb-0" for="pop_buy_status">
                        <i class="fa-solid fa-power-off text-success me-2"></i>เปิดใช้งานแถบ/ข้อความแจ้งเตือนการสั่งซื้อ
                    </label>
                    <input class="form-check-input fs-4 m-0" type="checkbox" id="pop_buy_status" <?php if (($config['pop_buy_status'] ?? 'off') == 'on') echo 'checked'; ?>>
                </div>

                <!-- Interval -->
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold small">ความถี่ / หน่วงเวลาการเด้ง (วินาที)</label>
                    <input type="number" id="pop_buy_interval" class="form-control py-2" value="<?php echo htmlspecialchars($config['pop_buy_interval'] ?? '10'); ?>" min="1" max="120" style="border-radius: 12px;">
                    <div class="form-text small">เช่น 10 = แสดงหรือขยับข้อความทุกๆ 10 วินาที</div>
                </div>

                <!-- Names -->
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold small">รายชื่อลูกค้าสุ่ม (คั่นด้วยเครื่องหมายจุลภาค <code>,</code>)</label>
                    <textarea id="pop_buy_names" rows="3" class="form-control" style="border-radius: 12px;"><?php echo htmlspecialchars($config['pop_buy_names'] ?? 'คุณ A***, คุณ B***, คุณ N***, คุณ K***, คุณ P***'); ?></textarea>
                    <div class="form-text small">ระบบจะสุ่มชื่อเหล่านี้ขึ้นมาแสดงคู่กับการสั่งซื้อ</div>
                </div>

                <!-- Template -->
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold small">รูปแบบข้อความ (Template)</label>
                    <input type="text" id="pop_buy_template" class="form-control py-2" value="<?php echo htmlspecialchars($config['pop_buy_template'] ?? 'ได้ทำการสั่งซื้อ {product} สำเร็จ!'); ?>" style="border-radius: 12px;">
                    <div class="form-text small">ใช้ <code>{product}</code> เพื่อแทนที่ด้วยชื่อสินค้าสุ่ม</div>
                </div>
            </div>
        </div>

        <!-- Product Selector -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 bg-light rounded-4 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-boxes-stacked text-xez-primary me-2"></i> เลือกสินค้าที่ให้เด้ง</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary me-1" id="select_all_prods">เลือกทั้งหมด</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect_all_prods">ยกเลิก</button>
                    </div>
                </div>
                <p class="text-muted small mb-3">หากไม่เลือกสินค้าเลย ระบบจะสุ่มสลับจากสินค้าที่มีทั้งหมดในร้านค้า</p>

                <div class="border bg-white rounded-3 p-3 overflow-auto" style="max-height: 400px;">
                    <?php if (empty($all_products)): ?>
                        <p class="text-muted small text-center my-3">ยังไม่มีสินค้าในระบบ</p>
                    <?php else: ?>
                        <?php foreach ($all_products as $p): ?>
                            <?php $is_checked = empty($selected_prod_ids) || in_array($p['id'], $selected_prod_ids); ?>
                            <div class="form-check mb-2 pb-2 border-bottom">
                                <input class="form-check-input pop-buy-product-checkbox" type="checkbox" value="<?= $p['id'] ?>" id="p_check_<?= $p['id'] ?>" <?= $is_checked ? 'checked' : '' ?>>
                                <label class="form-check-label d-flex justify-content-between align-items-center w-100 pe-2" for="p_check_<?= $p['id'] ?>">
                                    <span class="text-truncate me-2 fw-medium small text-dark" style="max-width: 220px;"><?= htmlspecialchars($p['name']) ?></span>
                                    <span class="badge bg-secondary rounded-pill small">฿<?= number_format($p['price']) ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$("#select_all_prods").click(function() {
    $(".pop-buy-product-checkbox").prop('checked', true);
});
$("#deselect_all_prods").click(function() {
    $(".pop-buy-product-checkbox").prop('checked', false);
});

$("#save_social_proof_btn").click(function () {
    var formData = new FormData();
    formData.append('pop_buy_status', $("#pop_buy_status").is(':checked') ? 'on' : 'off');
    formData.append('pop_buy_interval', $("#pop_buy_interval").val());
    formData.append('pop_buy_names', $("#pop_buy_names").val());
    formData.append('pop_buy_template', $("#pop_buy_template").val());

    var selectedProducts = [];
    $(".pop-buy-product-checkbox:checked").each(function() {
        selectedProducts.push($(this).val());
    });
    formData.append('pop_buy_products', selectedProducts.join(','));

    Swal.fire({ title: 'กำลังบันทึก...', didOpen: () => { Swal.showLoading() } });

    $.ajax({
        type: 'POST',
        url: 'system/backend/save_social_proof_manage.php',
        data: formData,
        contentType: false,
        processData: false,
    }).done(function (res) {
        Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message }).then(() => window.seamlessReload ? window.seamlessReload() : window.seamlessReload ? window.seamlessReload() : location.reload());
    }).fail(function (jqXHR) {
        let res = jqXHR.responseJSON;
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res ? res.message : 'เกิดข้อผิดพลาดในการบันทึกข้อมูล' });
    });
});
</script>

