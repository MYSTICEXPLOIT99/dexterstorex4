<?php
// Ensure this page is only loaded within index.php / shop.php context
if (!isset($c_row)) {
    die("Direct access not allowed");
}

$c_id = $c_row['c_id'];
$maps_q = dd_q("SELECT * FROM roblox_maps WHERE c_id = ? ORDER BY id ASC", [$c_id]);
$maps = $maps_q->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="roblox-shop-container py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-white"><i class="fa-solid fa-gamepad text-xez-primary me-2"></i>
                <?= htmlspecialchars($c_row['c_name']) ?></h4>
            <p class="text-muted small mb-0"><?= htmlspecialchars($c_row['des']) ?></p>
        </div>
        <a href="?page=shop" class="btn xez-btn-outline btn-sm px-4 text-white">
            <i class="fa-solid fa-arrow-left me-1"></i> กลับ
        </a>
    </div>

    <?php if (count($maps) == 0): ?>
        <div class="text-center py-5">
            <p class="text-muted fs-5">ขออภัย ขณะนี้ยังไม่มีแมพให้บริการในหมวดหมู่นี้</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Left Side: Selection and Inputs -->
            <div class="col-lg-8">
                <!-- 1. Select Map -->
                <div class="roblox-section-card mb-4">
                    <h5 class="section-title text-white mb-3"><span class="accent-bar me-2"></span>เลือกแมพ</h5>
                    <div class="row g-3">
                        <?php foreach ($maps as $index => $map): ?>
                            <div class="col-md-4 col-6">
                                <div class="map-select-card <?php echo $index === 0 ? 'active' : ''; ?>" 
                                     data-id="<?php echo $map['id']; ?>"
                                     data-name="<?php echo htmlspecialchars($map['name']); ?>"
                                     data-price="<?php echo $map['rate_price']; ?>"
                                     data-unit="<?php echo htmlspecialchars($map['rate_unit']); ?>"
                                     data-min="<?php echo $map['min_qty']; ?>"
                                     data-max="<?php echo $map['max_qty']; ?>"
                                     data-presets="<?php echo htmlspecialchars($map['presets']); ?>"
                                     onclick="selectMap(this)">
                                    <div class="map-image-container">
                                        <img src="<?php echo htmlspecialchars($map['img']); ?>" alt="<?php echo htmlspecialchars($map['name']); ?>">
                                    </div>
                                    <div class="map-info text-center mt-2">
                                        <div class="map-name fw-bold text-white"><?php echo htmlspecialchars($map['name']); ?></div>
                                        <div class="map-rate text-xez-primary fw-bold small">฿<?php echo number_format($map['rate_price'], 0); ?> / <?php echo htmlspecialchars($map['rate_unit']); ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 2. Account Information -->
                <div class="roblox-section-card mb-4">
                    <h5 class="section-title text-white mb-3"><span class="accent-bar me-2"></span>ข้อมูลบัญชี</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">USERNAME</label>
                            <input type="text" id="roblox_username" class="form-control roblox-input" placeholder="ชื่อผู้เล่น Roblox">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">PASSWORD</label>
                            <input type="password" id="roblox_password" class="form-control roblox-input" placeholder="รหัสผ่านเกม">
                        </div>
                    </div>
                    <div class="alert alert-warning-soft mt-3 mb-0 d-flex align-items-center">
                        <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                        <span class="small">รหัสผ่านจะถูกเข้ารหัสและปลอดภัย ทีมงานจะไม่นำไปใช้ในทางอื่น</span>
                    </div>
                </div>

                <!-- 3. Quantity Wanted -->
                <div class="roblox-section-card mb-4">
                    <h5 class="section-title text-white mb-3"><span class="accent-bar me-2"></span>จำนวนที่ต้องการ</h5>
                    <div class="qty-section-wrapper p-3">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="text-white fw-bold" id="qty_title">ต้องการกี่ M?</span>
                            <div class="qty-control d-flex align-items-center">
                                <button class="qty-btn" onclick="adjustQty(-1)"><i class="fa-solid fa-minus"></i></button>
                                <div class="qty-display-box mx-2">
                                    <span class="qty-number-display" id="qty_display">1</span>
                                    <input type="hidden" id="qty_number" value="1">
                                </div>
                                <button class="qty-btn" onclick="adjustQty(1)"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>

                        <!-- Slider -->
                        <div class="mb-4">
                            <input type="range" class="form-range roblox-range" id="qty_range" min="1" max="500" value="1" oninput="syncQty(this.value)">
                            <div class="d-flex justify-content-between text-muted small mt-1">
                                <span id="lbl_min">ต่ำสุด 1M</span>
                                <span id="lbl_max">สูงสุด 500M</span>
                            </div>
                        </div>

                        <!-- Preset Buttons -->
                        <div class="preset-buttons-wrapper d-flex flex-wrap gap-2" id="preset_container">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Sticky Checkout Card -->
            <div class="col-lg-4">
                <div class="roblox-section-card sticky-top" style="top: 90px; z-index: 10;">
                    <h5 class="section-title text-white mb-3"><span class="accent-bar me-2"></span>สรุปคำสั่งซื้อ</h5>
                    
                    <div class="checkout-summary p-3 mb-4 rounded bg-dark-soft">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">แมพที่เลือก:</span>
                            <span class="text-white fw-bold small text-truncate" style="max-width: 180px;" id="summary_map_name">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">เรทราคา:</span>
                            <span class="text-white small" id="summary_rate">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">จำนวน:</span>
                            <span class="text-white fw-bold small" id="summary_qty">-</span>
                        </div>
                        <hr class="my-2 border-secondary">
                        <div class="d-flex justify-content-between">
                            <span class="text-white fw-bold">ราคารวมทั้งสิ้น:</span>
                            <span class="text-xez-primary fw-bold fs-4" id="summary_total_price">฿0</span>
                        </div>
                    </div>

                    <button class="btn roblox-buy-btn w-100 py-3 fw-bold" onclick="submitOrder()">
                        <i class="fa-solid fa-shopping-basket me-2"></i>ยืนยันสั่งซื้อ
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
/* Custom styled Roblox shop components */
.roblox-section-card {
    background: rgba(15, 23, 42, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}
.accent-bar {
    display: inline-block;
    width: 4px;
    height: 16px;
    background: var(--xez-primary);
    border-radius: 2px;
    vertical-align: middle;
}
.map-select-card {
    background: rgba(30, 41, 59, 0.5);
    border: 2px solid transparent;
    border-radius: 12px;
    padding: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.map-select-card:hover {
    background: rgba(30, 41, 59, 0.8);
    transform: translateY(-2px);
}
.map-select-card.active {
    background: rgba(16, 185, 129, 0.1);
    border-color: #10b981;
    box-shadow: 0 0 15px rgba(16, 185, 129, 0.2);
}
.map-image-container {
    aspect-ratio: 1/1;
    border-radius: 8px;
    overflow: hidden;
}
.map-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.map-select-card:hover .map-image-container img {
    transform: scale(1.05);
}
.roblox-input {
    background: rgba(30, 41, 59, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    border-radius: 8px;
    padding: 10px 14px;
}
.roblox-input:focus {
    background: rgba(30, 41, 59, 1);
    border-color: var(--xez-primary);
    color: #fff;
    box-shadow: 0 0 8px rgba(var(--xez-primary-rgb), 0.3);
}
.alert-warning-soft {
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.2);
    color: #f59e0b;
    border-radius: 10px;
    padding: 12px;
}
.bg-dark-soft {
    background: rgba(30, 41, 59, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.qty-control {
    background: rgba(30, 41, 59, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 4px;
}
.qty-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    background: rgba(255,255,255,0.05);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}
.qty-btn:hover {
    background: var(--xez-primary);
    color: #fff;
}
.qty-display-box {
    display: flex;
    align-items: baseline;
    gap: 4px;
    min-width: 80px;
    justify-content: center;
}
.qty-number-display {
    color: #fff;
    font-weight: 800;
    font-size: 1.8rem;
    line-height: 1;
    min-width: 40px;
    text-align: right;
}
/* Legacy hidden - keep for compatibility */
.qty-input-wrapper { display: none; }
.qty-number-input { display: none; }
.qty-unit-label { display: none; }
.roblox-range::-webkit-slider-runnable-track {
    background: rgba(255,255,255,0.1);
    height: 8px;
    border-radius: 4px;
}
.roblox-range::-webkit-slider-thumb {
    background: var(--xez-primary);
    border: 2px solid #fff;
    width: 20px;
    height: 20px;
    margin-top: -6px;
    border-radius: 50%;
}
.preset-btn {
    background: rgba(30, 41, 59, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    padding: 8px 18px;
    border-radius: 20px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.preset-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--xez-primary);
}
.preset-btn.active {
    background: var(--xez-primary);
    color: #000;
    font-weight: bold;
    border-color: var(--xez-primary);
    box-shadow: 0 0 10px rgba(var(--xez-primary-rgb), 0.3);
}
.roblox-buy-btn {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
}
.roblox-buy-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
    color: #fff;
}
</style>

<script>
let selectedMapId = 0;
let ratePrice = 0;
let rateUnit = 'M';
let minQty = 1;
let maxQty = 500;

// Default presets (used if DB not set)
const DEFAULT_PRESETS = [1, 3, 5, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100];

function selectMap(element) {
    $('.map-select-card').removeClass('active');
    $(element).addClass('active');

    selectedMapId = parseInt($(element).data('id'));
    ratePrice     = parseFloat($(element).data('price'));
    rateUnit      = $(element).data('unit') || 'M';
    minQty        = parseInt($(element).data('min')) || 1;
    maxQty        = parseInt($(element).data('max')) || 500;
    let presetsStr = $(element).data('presets');

    // Sync slider bounds
    $('#qty_range').attr('min', minQty);
    $('#qty_range').attr('max', maxQty);

    $('#lbl_min').text('ต่ำสุด ' + minQty + rateUnit);
    $('#lbl_max').text('สูงสุด ' + maxQty + rateUnit);
    $('#qty_title').text('ต้องการกี่ ' + rateUnit + '?');

    // Parse presets from DB or fall back to defaults
    let presets = DEFAULT_PRESETS;
    if (presetsStr && presetsStr.toString().trim() !== '') {
        let parsed = presetsStr.toString().split(',').map(Number).filter(n => !isNaN(n) && n >= minQty && n <= maxQty);
        if (parsed.length > 0) presets = parsed;
    }

    // Render preset buttons: show only the number, M is separate label
    let presetHtml = '';
    presets.forEach(val => {
        presetHtml += `<button class="preset-btn" data-val="${val}" onclick="syncQty(${val})">${val}M</button>`;
    });
    $('#preset_container').html(presetHtml);

    // Sync summary header
    $('#summary_map_name').text($(element).data('name'));
    $('#summary_rate').text(`1${rateUnit} = ฿${ratePrice.toFixed(2)}`);

    // Set value to min
    let currentVal = parseInt($('#qty_number').val());
    if (isNaN(currentVal) || currentVal < minQty || currentVal > maxQty) {
        syncQty(minQty);
    } else {
        syncQty(currentVal);
    }
}

function syncQty(value) {
    let val = parseInt(value);
    if (isNaN(val)) val = minQty;
    if (val < minQty) val = minQty;
    if (val > maxQty) val = maxQty;

    // Update hidden value + visible display
    $('#qty_number').val(val);
    $('#qty_display').text(val);
    $('#qty_range').val(val);

    // Highlight active preset button
    $('.preset-btn').removeClass('active');
    $(`.preset-btn[data-val="${val}"]`).addClass('active');

    updateSummary();
}

function adjustQty(amount) {
    let current = parseInt($('#qty_number').val()) || minQty;
    syncQty(current + amount);
}

function updateSummary() {
    let qty        = parseInt($('#qty_number').val()) || minQty;
    let totalPrice = qty * ratePrice;

    $('#summary_qty').text(`${qty} ${rateUnit}`);
    $('#summary_total_price').text(`฿${totalPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
    $('.roblox-buy-btn').html(`<i class="fa-solid fa-shopping-basket me-2"></i>ยืนยันสั่งซื้อ (หักเงิน ฿${totalPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})})`);
}

function submitOrder() {
    let username = $('#roblox_username').val().trim();
    let password = $('#roblox_password').val().trim();
    let qty      = parseInt($('#qty_number').val());

    if (!selectedMapId) {
        Swal.fire({ icon: 'warning', title: 'แจ้งเตือน', text: 'กรุณาเลือกแมพก่อนทำรายการ' });
        return;
    }
    if (username === '' || password === '') {
        Swal.fire({ icon: 'warning', title: 'กรุณากรอกข้อมูล', text: 'กรุณากรอก Roblox Username และ Password ให้ครบถ้วน' });
        return;
    }

    <?php if (!isset($_SESSION['id'])): ?>
        Swal.fire({
            icon: 'warning', title: 'กรุณาเข้าสู่ระบบ',
            text: 'คุณต้องเข้าสู่ระบบก่อนจึงจะสามารถทำรายการได้',
            confirmButtonText: 'เข้าสู่ระบบ', showCancelButton: true, cancelButtonText: 'ยกเลิก'
        }).then((result) => { if (result.isConfirmed) window.navTo ? window.navTo('') : (location.href = ''); });
        return;
    <?php endif; ?>

    let totalPrice = qty * ratePrice;

    Swal.fire({
        title: 'ยืนยันสั่งซื้อ?',
        html: `บัญชี: <strong>${username}</strong><br>จำนวน: <strong>${qty}${rateUnit}</strong><br>เรทราคา: <strong>฿${ratePrice.toFixed(2)} / ${rateUnit}</strong><br>ราคารวม: <strong>฿${totalPrice.toFixed(2)}</strong>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยันซื้อ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#10b981'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'system/buy_roblox_map.php',
                data: { map_id: selectedMapId, roblox_username: username, roblox_password: password, qty: qty },
                dataType: 'json'
            }).done(function (res) {
                Swal.fire({ icon: 'success', title: 'สั่งซื้อสำเร็จ!', text: res.message, confirmButtonText: 'ตกลง' })
                    .then(() => { window.navTo ? window.navTo('') : (location.href = ''); });
            }).fail(function (xhr) {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: xhr.responseJSON ? xhr.responseJSON.message : 'เกิดข้อผิดพลาดในการเชื่อมต่อ' });
            });
        }
    });
}

// Auto-select first map on page load
$(document).ready(function() {
    let firstMap = $('.map-select-card').first();
    if (firstMap.length) selectMap(firstMap[0]);
});
</script>

