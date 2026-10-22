<div class="p-4 p-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="fa-solid fa-gamepad text-xdvz-primary me-2"></i>
                จัดการแมพและเรทราคา Roblox</h3>
            <p class="text-muted small mb-0">สร้าง แก้ไข หรือลบแมพและตั้งเรทราคาสำหรับหมวดหมู่บริการประเภทแมพ</p>
        </div>
        <button class="btn xdvz-btn-cyan shadow-sm px-4" id="open_insert">
            <i class="fa-solid fa-plus me-2"></i> เพิ่มแมพใหม่
        </button>
    </div>

    <div class="table-responsive">
        <table id="maps_table" class="table table-hover border-0 w-100">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 py-3 ps-4 text-muted small fw-bold">ID</th>
                    <th class="border-0 py-3 text-muted small fw-bold">ภาพ</th>
                    <th class="border-0 py-3 text-muted small fw-bold">ชื่อแมพ</th>
                    <th class="border-0 py-3 text-muted small fw-bold">หมวดหมู่</th>
                    <th class="border-0 py-3 text-muted small fw-bold">เรทราคา (บาท)</th>
                    <th class="border-0 py-3 text-muted small fw-bold">หน่วย</th>
                    <th class="border-0 py-3 text-muted small fw-bold">ขั้นต่ำ / สูงสุด</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-center">เครื่องมือ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $get_maps = dd_q("SELECT r.*, c.c_name FROM roblox_maps r LEFT JOIN category c ON r.c_id = c.c_id ORDER BY r.id DESC");
                while ($row = $get_maps->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <tr class="align-middle border-bottom">
                        <td class="py-3 ps-4 text-dark small fw-bold">#<?php echo $row['id']; ?></td>
                        <td class="py-3">
                            <img src="<?php echo htmlspecialchars($row['img']); ?>" class="rounded-3 shadow-sm border"
                                width="60px" height="60px" style="object-fit: cover;">
                        </td>
                        <td class="py-3">
                            <span class="text-dark fw-bold small"><?php echo htmlspecialchars($row['name']); ?></span>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-info small"><?php echo htmlspecialchars($row['c_name'] ?? 'ไม่มี'); ?></span>
                        </td>
                        <td class="py-3">
                            <span class="text-dark fw-bold small">฿<?php echo number_format($row['rate_price'], 2); ?></span>
                        </td>
                        <td class="py-3">
                            <span class="text-muted small"><?php echo htmlspecialchars($row['rate_unit']); ?></span>
                        </td>
                        <td class="py-3">
                            <span class="text-muted small"><?php echo $row['min_qty']; ?> - <?php echo $row['max_qty']; ?></span>
                        </td>
                        <td class="py-3 text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-warning btn-sm rounded-pill px-3 fw-bold"
                                    onclick="get_detail(<?php echo $row['id']; ?>)">
                                    <i class="fa-solid fa-edit me-1"></i> แก้ไข
                                </button>
                                <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold"
                                    onclick="del('<?php echo $row['id']; ?>','<?php echo htmlspecialchars($row['name']); ?>')">
                                    <i class="fa-solid fa-trash me-1"></i> ลบ
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal เพิ่มแมพ -->
<div class="modal fade" id="map_insert" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="fw-bold text-dark mb-0">เพิ่มแมพใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">หมวดหมู่</label>
                    <select id="p_c_id" class="form-select py-2">
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        <?php
                        $cats = dd_q("SELECT * FROM category WHERE c_type = 1 ORDER BY c_id DESC");
                        while ($cat = $cats->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="'.$cat['c_id'].'">'.htmlspecialchars($cat['c_name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">ชื่อแมพ</label>
                    <input type="text" id="p_name" class="form-control py-2" placeholder="เช่น Driving Empire">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">ลิงค์รูปภาพ (URL)</label>
                    <input type="text" id="p_img" class="form-control py-2">
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">เรทราคา (บาท)</label>
                        <input type="number" step="0.01" id="p_rate_price" class="form-control py-2" value="5.00">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">หน่วยเรท</label>
                        <input type="text" id="p_rate_unit" class="form-control py-2" value="1M" placeholder="เช่น 1M หรือ 100K">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">จำนวนขั้นต่ำ</label>
                        <input type="number" id="p_min_qty" class="form-control py-2" value="1">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">จำนวนสูงสุด</label>
                        <input type="number" id="p_max_qty" class="form-control py-2" value="500">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold">ค่า Presets สำหรับปุ่มกด (เว้นวรรคด้วยจุลภาค `,` )</label>
                    <input type="text" id="p_presets" class="form-control py-2" value="1,26,51,101,151,201,251,300,375,450,500" placeholder="เช่น 1,5,10,20,50">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-dark w-100 py-2 rounded-3" id="insert_btn">บันทึกแมพ</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal แก้ไขแมพ -->
<div class="modal fade" id="map_detail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="fw-bold text-dark mb-0">แก้ไขข้อมูลแมพ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">หมวดหมู่</label>
                    <select id="c_id" class="form-select py-2">
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        <?php
                        $cats = dd_q("SELECT * FROM category WHERE c_type = 1 ORDER BY c_id DESC");
                        while ($cat = $cats->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="'.$cat['c_id'].'">'.htmlspecialchars($cat['c_name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">ชื่อแมพ</label>
                    <input type="text" id="name" class="form-control py-2">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">ลิงค์รูปภาพ (URL)</label>
                    <input type="text" id="img" class="form-control py-2">
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">เรทราคา (บาท)</label>
                        <input type="number" step="0.01" id="rate_price" class="form-control py-2">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">หน่วยเรท</label>
                        <input type="text" id="rate_unit" class="form-control py-2">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">จำนวนขั้นต่ำ</label>
                        <input type="number" id="min_qty" class="form-control py-2">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">จำนวนสูงสุด</label>
                        <input type="number" id="max_qty" class="form-control py-2">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold">ค่า Presets สำหรับปุ่มกด (เว้นวรรคด้วยจุลภาค `,` )</label>
                    <input type="text" id="presets" class="form-control py-2">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-dark w-100 py-2 rounded-3" id="save_btn">อัปเดตข้อมูล</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#maps_table').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json" }
        });
    });

    $("#open_insert").click(() => { new bootstrap.Modal('#map_insert').show(); });

    $("#insert_btn").click(function () {
        var formData = new FormData();
        formData.append('action', 'insert');
        formData.append('c_id', $("#p_c_id").val());
        formData.append('name', $("#p_name").val());
        formData.append('img', $("#p_img").val());
        formData.append('rate_price', $("#p_rate_price").val());
        formData.append('rate_unit', $("#p_rate_unit").val());
        formData.append('min_qty', $("#p_min_qty").val());
        formData.append('max_qty', $("#p_max_qty").val());
        formData.append('presets', $("#p_presets").val());

        $.ajax({
            type: 'POST',
            url: 'system/backend/roblox_maps_api.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function (res) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message }).then(() => window.seamlessReload ? window.seamlessReload() : window.seamlessReload ? window.seamlessReload() : location.reload());
        }).fail(function(xhr) {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: xhr.responseJSON ? xhr.responseJSON.message : 'เกิดข้อผิดพลาด' });
        });
    });

    $("#save_btn").click(function () {
        var formData = new FormData();
        formData.append('action', 'update');
        formData.append('id', $("#save_btn").attr("data-id"));
        formData.append('c_id', $("#c_id").val());
        formData.append('name', $("#name").val());
        formData.append('img', $("#img").val());
        formData.append('rate_price', $("#rate_price").val());
        formData.append('rate_unit', $("#rate_unit").val());
        formData.append('min_qty', $("#min_qty").val());
        formData.append('max_qty', $("#max_qty").val());
        formData.append('presets', $("#presets").val());

        $.ajax({
            type: 'POST',
            url: 'system/backend/roblox_maps_api.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function (res) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message }).then(() => window.seamlessReload ? window.seamlessReload() : window.seamlessReload ? window.seamlessReload() : location.reload());
        }).fail(function(xhr) {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: xhr.responseJSON ? xhr.responseJSON.message : 'เกิดข้อผิดพลาด' });
        });
    });

    function get_detail(id) {
        $.ajax({
            type: 'POST',
            url: 'system/backend/roblox_maps_api.php',
            data: { action: 'get_detail', id: id }
        }).done(function (res) {
            $("#c_id").val(res.c_id);
            $("#name").val(res.name);
            $("#img").val(res.img);
            $("#rate_price").val(res.rate_price);
            $("#rate_unit").val(res.rate_unit);
            $("#min_qty").val(res.min_qty);
            $("#max_qty").val(res.max_qty);
            $("#presets").val(res.presets);
            $("#save_btn").attr("data-id", id);
            new bootstrap.Modal('#map_detail').show();
        }).fail(function(xhr) {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: xhr.responseJSON ? xhr.responseJSON.message : 'เกิดข้อผิดพลาด' });
        });
    }

    function del(id, name) {
        Swal.fire({
            title: 'ยืนยันที่จะลบ?',
            text: "คุณแน่ใจที่จะลบแมพ " + name + " หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ใช่, ลบเลย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'system/backend/roblox_maps_api.php',
                    data: { action: 'delete', id: id }
                }).done(function (res) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message }).then(() => window.seamlessReload ? window.seamlessReload() : window.seamlessReload ? window.seamlessReload() : location.reload());
                }).fail(function(xhr) {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: xhr.responseJSON ? xhr.responseJSON.message : 'เกิดข้อผิดพลาด' });
                });
            }
        });
    }
</script>

