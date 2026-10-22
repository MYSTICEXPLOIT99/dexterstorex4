<?php
if (!isset($_GET["id"])) {
    $all_products = dd_q("SELECT * FROM box_product ORDER BY id ASC");
    ?>
    <div class="p-4 p-lg-5">
        <div class="mb-4">
            <div class="section-eyebrow">// STOCK & KEYS</div>
            <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                <i class="fa-solid fa-boxes-stacked text-danger me-2"></i> จัดการสต็อก & คีย์สินค้า
            </h3>
            <p class="text-muted small mb-0">เลือกสินค้าที่ต้องการเพิ่มหรือจัดการคีย์/รหัสส่งมอบให้ลูกค้า</p>
        </div>
        <div class="row g-3">
            <?php 
            if ($all_products && $all_products->rowCount() > 0):
                while ($p = $all_products->fetch(PDO::FETCH_ASSOC)): 
                    $cnt_q = dd_q("SELECT COUNT(*) as c FROM box_stock WHERE p_id = ?", [$p['id']]);
                    $cnt = $cnt_q ? intval($cnt_q->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) : 0;
            ?>
            <div class="col-md-4 col-lg-3">
                <a href="?page=stock_manage&id=<?= $p['id'] ?>" class="text-decoration-none">
                    <div class="admin-inner-card text-center h-100 p-3" style="transition:all 0.2s;" onmouseover="this.style.borderColor='rgba(106,128,160,0.5)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'">
                        <img src="<?= htmlspecialchars($p['img']) ?>" class="rounded-3 mb-3 border border-secondary border-opacity-25"
                            style="width:70px;height:70px;object-fit:cover;" onerror="this.src='assets/icon/box.png'">
                        <div class="fw-bold text-white small mb-1" style="font-family:var(--font-cyber);"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="text-info fw-bold small mb-2">฿<?= number_format($p['price'], 2) ?></div>
                        <?php if ($cnt > 0): ?>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50"><?= $cnt ?> ชิ้นในสต็อก</span>
                        <?php else: ?>
                            <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50">สินค้าหมด</span>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
            <?php 
                endwhile;
            else:
            ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="fa-solid fa-box-open fs-1 mb-3"></i>
                    <h5>ยังไม่มีรายการสินค้า</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
} else {
    $prod_id = $_GET['id'];
    $find = dd_q("SELECT * FROM box_product WHERE id = ? ", [$prod_id]);
    $pd = ($find && $find->rowCount() > 0) ? $find->fetch(PDO::FETCH_ASSOC) : ['name' => 'สินค้า', 'id' => $prod_id];
    ?>
    <div class="p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-sm-center flex-column flex-sm-row mb-4 gap-3">
            <div>
                <a href="?page=stock_manage" class="btn-cyber-stock text-decoration-none mb-3 d-inline-flex" style="font-size:0.78rem;">
                    <i class="fa-solid fa-arrow-left"></i> กลับหน้ารวมสต็อก
                </a>
                <h3 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);">
                    <i class="fa-solid fa-boxes-stacked text-danger me-2"></i> สต็อกสินค้า: <span class="text-info"><?= htmlspecialchars($pd['name']) ?></span>
                </h3>
                <p class="text-muted small mb-0">จัดการรายการคีย์ / บัญชีส่งมอบ สำหรับสินค้านี้</p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <button class="btn-cyber-del" id="clear_all_stock" data-pid="<?= $prod_id ?>" style="white-space:nowrap;padding:8px 16px !important;font-size:0.84rem !important;">
                    <i class="fa-solid fa-trash-can"></i> ล้างสต็อกทั้งหมด
                </button>
                <button class="btn-cyber-primary" id="open_insert" style="white-space:nowrap;padding:8px 16px !important;font-size:0.84rem !important;">
                    <i class="fa-solid fa-plus"></i> เพิ่มคีย์สต็อก
                </button>
            </div>
        </div>

        <div class="admin-inner-card">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>ข้อมูลคีย์ / รหัสส่งมอบ</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center pe-3">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $get_items = dd_q("SELECT * FROM box_item WHERE b_id = ? ORDER BY id DESC", [$prod_id]);
                        if (!$get_items || $get_items->rowCount() == 0) {
                            $get_items = dd_q("SELECT * FROM box_stock WHERE p_id = ? ORDER BY id DESC", [$prod_id]);
                        }
                        if ($get_items && $get_items->rowCount() > 0) {
                            while ($row = $get_items->fetch(PDO::FETCH_ASSOC)) {
                                $key_val = $row['data'] ?? $row['username'] ?? $row['item'] ?? '';
                        ?>
                            <tr>
                                <td class="ps-3 fw-bold text-white">#<?= $row['id'] ?></td>
                                <td>
                                    <span class="text-white font-monospace small"><?= htmlspecialchars($key_val) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50">พร้อมส่งมอบ</span>
                                </td>
                                <td class="text-center pe-3">
                                    <button class="btn-cyber-del" onclick="del_stock('<?= $row['id'] ?>', '<?= htmlspecialchars(addslashes($key_val)) ?>')">
                                        <i class="fa-solid fa-trash"></i> ลบ
                                    </button>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">ยังไม่มีคีย์ในสต็อก กดปุ่ม "เพิ่มคีย์สต็อก" เพื่อเพิ่มคีย์</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal เพิ่มสต็อก (Bulk Add) -->
    <div class="modal fade" id="stock_insert" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content admin-inner-card border-secondary border-opacity-25 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold text-white mb-0" style="font-family:var(--font-cyber);">
                        <i class="fa-solid fa-plus text-danger me-2"></i> เพิ่มคีย์สต็อกสินค้า
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="p_id" value="<?= $prod_id ?>">
                    <div class="mb-3">
                        <label class="admin-label">ใส่คีย์/รหัสส่งมอบ (1 บรรทัด = 1 คีย์)</label>
                        <textarea id="p_data" rows="8" class="admin-input form-control font-monospace" placeholder="KEY-XXXX-1111&#10;KEY-YYYY-2222&#10;KEY-ZZZZ-3333"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn-cyber-primary w-100 py-2" id="insert_stock_btn">
                        <i class="fa-solid fa-floppy-disk me-1"></i> นำเข้าสต็อก
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $("#open_insert").click(function () {
        const myModal = new bootstrap.Modal($('#stock_insert'), { keyboard: false });
        myModal.show();
    });

    $("#insert_stock_btn").click(function () {
        var formData = new FormData();
        formData.append('p_id', $("#p_id").val());
        formData.append('data', $("#p_data").val());

        $.ajax({
            type: 'POST',
            url: 'system/backend/stock_insert.php',
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
                    confirmButtonColor: '#4b5d78'
                }).then(() => {
                    window.seamlessReload ? window.seamlessReload() : location.reload();
                });
            },
            error: function (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: err.responseJSON ? err.responseJSON.message : 'เกิดข้อผิดพลาด',
                    background: '#0a0a0f',
                    color: '#ffffff',
                    confirmButtonColor: '#4b5d78'
                });
            }
        });
    });

    function del_stock(id, name) {
        Swal.fire({
            title: 'ยืนยันการลบคีย์?',
            text: `ต้องการลบคีย์ ${name} หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบคีย์',
            cancelButtonText: 'ยกเลิก',
            background: '#0a0a0f',
            color: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('p_id', id);
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/stock_del.php',
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
                            confirmButtonColor: '#4b5d78'
                        }).then(() => {
                            window.seamlessReload ? window.seamlessReload() : location.reload();
                        });
                    }
                });
            }
        });
    }

    $("#clear_all_stock").click(function () {
        const pid = $(this).data('pid');
        Swal.fire({
            title: 'ยืนยันล้างสต็อกทั้งหมด?',
            text: 'คีย์ทั้งหมดของสินค้านี้จะถูกลบและไม่สามารถกู้คืนได้!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ล้างสต็อกทั้งหมด',
            cancelButtonText: 'ยกเลิก',
            background: '#0a0a0f',
            color: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('p_id', pid);
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/stock_clear.php',
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
                            confirmButtonColor: '#4b5d78'
                        }).then(() => {
                            window.seamlessReload ? window.seamlessReload() : location.reload();
                        });
                    }
                });
            }
        });
    });
    </script>
<?php } ?>
