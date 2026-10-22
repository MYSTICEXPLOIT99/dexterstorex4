<?php
if (!isset($_SESSION['id'])) {
    echo "<script>window.navTo ? window.navTo('') : (location.href = '');</script>";
    exit;
}

$uid = $_SESSION['id'];

// Get cart items with product details and current stock
$cart_q = dd_q("
    SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.img, p.c_type
    FROM cart c 
    JOIN box_product p ON c.product_id = p.id 
    WHERE c.user_id = ? 
    ORDER BY c.id DESC
", [$uid]);

$cart_items = [];
$total_price = 0;
$total_qty = 0;

if ($cart_q && $cart_q->rowCount() > 0) {
    while ($row = $cart_q->fetch(PDO::FETCH_ASSOC)) {
        $stock_q = dd_q("SELECT COUNT(*) as cnt FROM box_stock WHERE p_id = ?", [$row['product_id']]);
        $row['stock'] = $stock_q ? $stock_q->fetch(PDO::FETCH_ASSOC)['cnt'] : 0;
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $total_price += $row['subtotal'];
        $total_qty += $row['quantity'];
        $cart_items[] = $row;
    }
}
?>

<style>
/* ── MICKEY STORE EXACT CART STYLES ── */
.mickey-cart-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 10px 0 40px;
}
.mickey-cart-box {
    background: #0d0d10;
    border: 1px solid #1f1f26;
    border-radius: 16px;
    padding: 22px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}
.mickey-cart-header-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}
.mickey-cart-icon-circle {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(var(--primary-rgb), 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1.1rem;
}
.mickey-cart-h-text {
    color: var(--primary);
    font-weight: 800;
    font-size: 1.05rem;
    line-height: 1.2;
}
.mickey-cart-h-sub {
    color: #777;
    font-size: 0.78rem;
}
.mickey-cart-top-bar {
    background: #121216;
    border: 1px solid #202028;
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}
.mickey-cart-cat-divider {
    text-align: center;
    position: relative;
    margin: 20px 0;
}
.mickey-cart-cat-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 1px;
    background: rgba(var(--primary-rgb), 0.25);
}
.mickey-cart-cat-divider span {
    position: relative;
    background: #0d0d10;
    padding: 0 14px;
    color: var(--primary);
    font-size: 0.8rem;
    font-weight: 700;
}
.mickey-cart-item-card {
    background: #121216;
    border: 1px solid #202028;
    border-radius: 14px;
    padding: 18px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.mickey-cart-item-left {
    display: flex;
    align-items: center;
    gap: 14px;
    flex: 1;
    min-width: 220px;
}
.mickey-cart-item-thumb {
    width: 70px;
    height: 70px;
    border-radius: 10px;
    object-fit: cover;
    background: #180808;
    border: 1px solid rgba(var(--primary-rgb), 0.3);
}
.mickey-cart-item-meta {
    flex: 1;
}
.mickey-cart-item-title {
    color: var(--primary);
    font-weight: 700;
    font-size: 0.95rem;
    margin-bottom: 4px;
}
.mickey-cart-item-subtext {
    font-size: 0.82rem;
    color: #888;
}
.mickey-cart-item-controls {
    display: flex;
    align-items: center;
    gap: 14px;
}
.mickey-cart-del-btn-sq {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: rgba(220,38,38,0.12);
    border: 1px solid rgba(220,38,38,0.3);
    color: #ff4d4d;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}
.mickey-cart-del-btn-sq:hover {
    background: #dc2626;
    color: #fff;
}
.mickey-cart-summary-line {
    display: flex;
    justify-content: space-between;
    font-size: 0.88rem;
    color: #9e9e9e;
    margin-bottom: 10px;
}
.mickey-cart-summary-line-val {
    color: #ffffff;
    font-weight: 700;
}
.mickey-cart-coupon-wrap {
    display: flex;
    gap: 8px;
    margin: 20px 0 14px;
}
.mickey-cart-coupon-input {
    background: #121216;
    border: 1px solid #22222a;
    border-radius: 10px;
    color: #fff;
    padding: 10px 14px;
    font-size: 0.88rem;
    flex: 1;
    outline: none;
}
.mickey-cart-coupon-btn {
    background: #160808;
    border: 1px solid rgba(var(--primary-rgb), 0.4);
    color: var(--primary);
    font-weight: 700;
    font-size: 0.85rem;
    padding: 0 16px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
}
.mickey-cart-coupon-btn:hover {
    background: var(--primary);
    color: #fff;
}
.mickey-cart-btn-pay {
    width: 100%;
    background: #1a0808;
    border: 1px solid rgba(var(--primary-rgb), 0.5);
    color: var(--primary);
    font-weight: 800;
    font-size: 1rem;
    padding: 12px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
}
.mickey-cart-btn-pay:hover {
    background: var(--primary);
    color: #ffffff;
    box-shadow: 0 0 20px rgba(var(--primary-rgb), 0.5);
}
</style>

<div class="mickey-cart-container" data-aos="fade-up">
    <?php if (empty($cart_items)): ?>
        <div class="mickey-cart-box text-center py-5">
            <i class="fa-solid fa-cart-arrow-down display-3 text-muted mb-3"></i>
            <h4 class="fw-bold text-white mb-2">ไม่มีสินค้าในตะกร้า</h4>
            <p class="text-muted small mb-4">คุณยังไม่ได้เพิ่มสินค้าใดๆ ลงในตะกร้าสินค้า</p>
            <a href="?page=shop" class="btn btn-danger rounded-pill px-4 py-2 fw-bold" style="background: var(--primary); border: none;">
                <i class="fa-solid fa-store me-2"></i> ไปที่หน้าร้านค้า
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Left: Cart Items (Mickey Store) -->
            <div class="col-12 col-lg-7">
                <div class="mickey-cart-box">
                    <div class="mickey-cart-header-title">
                        <div class="mickey-cart-icon-circle">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <div>
                            <div class="mickey-cart-h-text">ตะกร้าสินค้า</div>
                            <div class="mickey-cart-h-sub">รายการสินค้าในตะกร้าของท่าน</div>
                        </div>
                    </div>

                    <!-- Top Bar: Select All Checkbox & Clear -->
                    <div class="mickey-cart-top-bar">
                        <div class="d-flex align-items-center gap-2">
                            <input type="checkbox" id="cart_select_all" checked style="accent-color: var(--primary); width:18px; height:18px; cursor:pointer;">
                            <label for="cart_select_all" class="text-white small fw-bold mb-0" style="cursor:pointer;">(<?= count($cart_items) ?>)</label>
                        </div>
                        <button class="mickey-cart-del-btn-sq" onclick="clearAllCart()" title="ล้างตะกร้า">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>

                    <div class="mickey-cart-cat-divider">
                        <span>สินค้าทั่วไป</span>
                    </div>

                    <!-- Item List -->
                    <?php foreach ($cart_items as $item): ?>
                        <div class="mickey-cart-item-card" id="cart_row_<?= $item['cart_id'] ?>">
                            <div class="mickey-cart-item-left">
                                <input type="checkbox" class="cart-item-check" checked style="accent-color: var(--primary); width:18px; height:18px; cursor:pointer;">
                                <img src="<?= htmlspecialchars($item['img']) ?>" class="mickey-cart-item-thumb" onerror="this.src='https://placehold.co/100x100/150505/ff4d4d?text=Product'">
                                <div class="mickey-cart-item-meta">
                                    <div class="mickey-cart-item-title"><?= htmlspecialchars($item['name']) ?></div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="mickey-cart-item-subtext">ราคาต่อชิ้น</span>
                                        <span class="text-white small fw-bold"><?= number_format($item['price'], 2) ?> บาท</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="mickey-cart-item-subtext">ส่วนลดสินค้า</span>
                                        <span class="text-white small fw-bold">0.00 %</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="mickey-cart-item-subtext">ราคาสุทธิ</span>
                                        <span style="color:var(--primary); font-weight:800; font-size:0.95rem;"><?= number_format($item['subtotal'], 2) ?> บาท</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mickey-cart-item-controls">
                                <div class="mickey-cart-item-qty">
                                    <button type="button" class="mickey-cart-qty-btn" onclick="updateCartQty(<?= $item['cart_id'] ?>, -1)">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <span class="mickey-cart-qty-val"><?= $item['quantity'] ?></span>
                                    <button type="button" class="mickey-cart-qty-btn" onclick="updateCartQty(<?= $item['cart_id'] ?>, 1)">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                                <button type="button" class="mickey-cart-del-btn-sq" onclick="removeCartItem(<?= $item['cart_id'] ?>)" title="ลบรายการ">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Order Summary (Mickey Store) -->
            <div class="col-12 col-lg-5">
                <div class="mickey-cart-box">
                    <div class="mickey-cart-header-title">
                        <div class="mickey-cart-icon-circle">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <div class="mickey-cart-h-text">สรุปรายการ</div>
                            <div class="mickey-cart-h-sub">สรุป</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="fw-bold text-white small mb-3">สินค้าทั่วไป</div>
                        <div class="mickey-cart-summary-line">
                            <span>จำนวนชิ้นรวม</span>
                            <span class="mickey-cart-summary-line-val"><?= $total_qty ?></span>
                        </div>
                        <div class="mickey-cart-summary-line">
                            <span>ราคารวม</span>
                            <span class="mickey-cart-summary-line-val"><?= number_format($total_price, 2) ?> บาท</span>
                        </div>
                        <div class="mickey-cart-summary-line">
                            <span>ส่วนลด (บาท)</span>
                            <span class="mickey-cart-summary-line-val">0.00 บาท</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 mb-3" style="border-top:1px solid #202028;">
                            <span class="text-white fw-bold small">ราคาสุทธิ</span>
                            <span style="color:var(--primary); font-weight:800; font-size:1.1rem; font-family:'Kanit',sans-serif;"><?= number_format($total_price, 2) ?> บาท</span>
                        </div>
                    </div>

                    <div class="pt-3 mb-3" style="border-top:1px solid #202028;">
                        <div class="fw-bold text-white small mb-3">สรุป</div>
                        <div class="mickey-cart-summary-line">
                            <span>จำนวนชิ้นรวม</span>
                            <span class="mickey-cart-summary-line-val"><?= $total_qty ?></span>
                        </div>
                        <div class="mickey-cart-summary-line">
                            <span>สินค้าทั่วไปรวม</span>
                            <span style="color:var(--primary); font-weight:700;"><?= number_format($total_price, 2) ?> บาท</span>
                        </div>
                        <div class="mickey-cart-summary-line">
                            <span>ราคารวม</span>
                            <span style="color:var(--primary); font-weight:700;"><?= number_format($total_price, 2) ?> บาท</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 mb-3" style="border-top:1px solid #202028;">
                            <span class="text-white fw-bold small">ราคาสุทธิ</span>
                            <span style="color:var(--primary); font-weight:800; font-size:1.2rem; font-family:'Kanit',sans-serif;"><?= number_format($total_price, 2) ?> บาท</span>
                        </div>
                    </div>

                    <!-- Coupon Code Input -->
                    <div class="mickey-cart-coupon-wrap">
                        <input type="text" class="mickey-cart-coupon-input" id="cart_coupon_code" placeholder="กรอกรหัสคูปอง">
                        <button class="mickey-cart-coupon-btn" onclick="applyCoupon()">ใช้คูปอง</button>
                    </div>

                    <!-- Pay Button -->
                    <button class="mickey-cart-btn-pay" onclick="checkoutCart()">
                        ชำระเงิน
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function applyCoupon() {
    const code = document.getElementById('cart_coupon_code').value.trim();
    if (!code) {
        Swal.fire({ icon: 'warning', title: 'แจ้งเตือน', text: 'กรุณากรอกรหัสคูปอง' });
        return;
    }
    Swal.fire({ icon: 'info', title: 'คูปอง', text: 'ระบบจะคำนวณส่วนลดจากคูปองเมื่อทำการสั่งซื้อ' });
}
</script>

<script>
function updateCartQty(cartId, delta) {
    $.ajax({
        type: 'POST',
        url: 'system/cart_handler.php',
        data: { action: 'update_qty', cart_id: cartId, delta: delta },
        dataType: 'json'
    }).done(function(res) {
        if (res.status === 'success') {
            window.seamlessReload ? window.seamlessReload() : location.reload();
        } else {
            Swal.fire({ icon: 'warning', title: 'แจ้งเตือน', text: res.message });
        }
    });
}

function removeCartItem(cartId) {
    Swal.fire({
        title: 'ต้องการลบสินค้านี้ออกจากตะกร้า?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((res) => {
        if (res.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'system/cart_handler.php',
                data: { action: 'remove', cart_id: cartId },
                dataType: 'json'
            }).done(function(r) {
                if (r.status === 'success') {
                    window.seamlessReload ? window.seamlessReload() : location.reload();
                }
            });
        }
    });
}

function clearAllCart() {
    Swal.fire({
        title: 'ต้องการล้างสินค้าทั้งหมดในตะกร้า?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ล้างทั้งหมด',
        cancelButtonText: 'ยกเลิก'
    }).then((res) => {
        if (res.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'system/cart_handler.php',
                data: { action: 'clear' },
                dataType: 'json'
            }).done(function(r) {
                window.seamlessReload ? window.seamlessReload() : location.reload();
            });
        }
    });
}

function checkoutCart() {
    Swal.fire({
        title: 'ยืนยันการชำระเงินในตะกร้าสินค้า?',
        text: 'ระบบจะทำการตัดเงินและจัดส่งสินค้าในสต็อกให้อัตโนมัติ',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยันสั่งซื้อ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#d03e3e'
    }).then((res) => {
        if (res.isConfirmed) {
            Swal.fire({ title: 'กำลังประมวลผลคำสั่งซื้อ...', didOpen: () => { Swal.showLoading() } });
            $.ajax({
                type: 'POST',
                url: 'system/cart_handler.php',
                data: { action: 'checkout' },
                dataType: 'json'
            }).done(function(r) {
                if (r.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'สั่งซื้อสำเร็จ!',
                        html: r.message,
                        confirmButtonText: 'ดูประวัติสั่งซื้อ'
                    }).then(() => {
                        window.navTo ? window.navTo('') : (location.href = '');
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: r.message });
                }
            }).fail(function() {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ' });
            });
        }
    });
}
</script>
