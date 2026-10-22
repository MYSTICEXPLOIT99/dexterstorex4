<?php
$sub = $_GET['page'] ?? 'backend';
function isActive($page, $current)
{
    return $page == $current ? 'active' : '';
}
?>
<div class="container-fluid mt-3 mb-5" data-aos="fade-up">
    <div class="container ps-0 pe-0 m-auto">
        <div class="row g-4">
            <!-- Sidebar Navigation (Desktop) -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="admin-sidebar p-3">
                    <div class="admin-sidebar-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-1.5">
                                <span class="dot-red" style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;box-shadow:0 0 6px #ef4444;"></span>
                                <span class="dot-yellow" style="width:8px;height:8px;border-radius:50%;background:#facc15;display:inline-block;box-shadow:0 0 6px #facc15;"></span>
                                <span class="dot-green" style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;box-shadow:0 0 6px #22c55e;"></span>
                            </div>
                            <span class="badge px-2 py-0.5" style="font-size:0.6rem;font-family:'Space Mono',monospace;background:rgba(56,189,248,0.15);border:1px solid rgba(56,189,248,0.4);color:#38bdf8;border-radius:999px;">
                                <i class="fa-solid fa-microchip me-1"></i>CORE_v2.5
                            </span>
                        </div>
                        <h5 class="fw-bold text-white mb-0 mt-2 d-flex align-items-center gap-2" style="font-family:var(--font-cyber);letter-spacing:1.5px;font-size:1.05rem;">
                            <i class="fa-solid fa-terminal text-warning"></i> ADMIN_CORE
                        </h5>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <p class="small mb-0" style="font-family:'Space Mono',monospace;font-size:0.68rem;color:#94a3b8;">root@<?= htmlspecialchars(strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $site_name ?: 'cygnusx'))) ?>:~#</p>
                            <span style="font-size:0.62rem; color:#22c55e; font-family:'Space Mono',monospace; font-weight:700;">● ONLINE</span>
                        </div>
                    </div>

                    <div class="list-group list-group-flush gap-1">
                        <!-- Module: Overview -->
                        <div class="admin-sidebar-heading">[ SYSTEM_DASHBOARD ]</div>
                        <a href="?page=backend" class="list-group-item <?= isActive('backend', $sub) ?>">
                            <i class="fa-solid fa-chart-pie text-warning"></i> แดชบอร์ด (Dashboard)
                        </a>

                        <!-- Module: Store Control -->
                        <div class="admin-sidebar-heading">[ STORE_MANAGEMENT ]</div>
                        <a href="?page=website" class="list-group-item <?= isActive('website', $sub) ?>">
                            <i class="fa-solid fa-sliders text-primary"></i> ตั้งค่าเว็บไซต์ & ชำระเงิน
                        </a>
                        <a href="?page=theme_manage" class="list-group-item <?= isActive('theme_manage', $sub) ?>">
                            <i class="fa-solid fa-wand-magic-sparkles text-warning"></i> ปรับแต่งสีไฟ 100% (Theme FX)
                        </a>
                        <a href="?page=banner_manage" class="list-group-item <?= isActive('banner_manage', $sub) ?>">
                            <i class="fa-solid fa-images text-info"></i> จัดการป้ายหน้าร้าน (Banners)
                        </a>
                        <a href="?page=category_manage" class="list-group-item <?= isActive('category_manage', $sub) ?>">
                            <i class="fa-solid fa-layer-group text-warning"></i> จัดการหมวดหมู่
                        </a>
                        <a href="?page=product_manage" class="list-group-item <?= isActive('product_manage', $sub) ?>">
                            <i class="fa-solid fa-box-open text-success"></i> จัดการสินค้า
                        </a>
                        <a href="?page=stock_manage" class="list-group-item <?= isActive('stock_manage', $sub) ?>">
                            <i class="fa-solid fa-boxes-stacked text-danger"></i> สต็อกสินค้า & คีย์
                        </a>
                        <a href="?page=wheel_manage" class="list-group-item <?= isActive('wheel_manage', $sub) ?>">
                            <i class="fa-solid fa-dharmachakra text-info"></i> จัดการกล่องสุ่ม & วงล้อ
                        </a>

                        <?php if (defined('IS_PARENT_STORE') && IS_PARENT_STORE === true): ?>
                            <!-- Module: Cloud SaaS -->
                            <div class="admin-sidebar-heading">[ CLOUD_SAAS_ENGINE ]</div>
                            <a href="?page=discord_bot_manage" class="list-group-item <?= isActive('discord_bot_manage', $sub) ?>">
                                <i class="fa-solid fa-robot text-primary"></i> จัดการระบบเช่าบอท (Discord Bot)
                            </a>
                            <a href="?page=rental_packages" class="list-group-item <?= isActive('rental_packages', $sub) ?>">
                                <i class="fa-solid fa-server text-success"></i> จัดการแพ็กเกจเช่าเว็บ
                            </a>
                            <a href="?page=api_store_manage" class="list-group-item <?= isActive('api_store_manage', $sub) ?>">
                                <i class="fa-solid fa-store text-info"></i> จัดการร้านค้าที่เช่า
                            </a>
                        <?php endif; ?>

                        <!-- Module: Members & Finance -->
                        <div class="admin-sidebar-heading">[ ACCOUNTS & FINANCE ]</div>
                        <a href="?page=user_edit" class="list-group-item <?= isActive('user_edit', $sub) ?>">
                            <i class="fa-solid fa-users text-info"></i> จัดการสมาชิก (Users)
                        </a>
                        <a href="?page=backend_topup_history" class="list-group-item <?= isActive('backend_topup_history', $sub) ?>">
                            <i class="fa-solid fa-wallet text-success"></i> ประวัติการเติมเงิน
                        </a>
                        <a href="?page=backend_buy_history" class="list-group-item <?= isActive('backend_buy_history', $sub) ?>">
                            <i class="fa-solid fa-receipt text-warning"></i> ประวัติการสั่งซื้อ
                        </a>

                        <!-- Module: Promotions & Contact -->
                        <div class="admin-sidebar-heading">[ MARKETING & REWARDS ]</div>
                        <a href="?page=code_manage" class="list-group-item <?= isActive('code_manage', $sub) ?>">
                            <i class="fa-solid fa-ticket text-info"></i> โค้ดแลกพ้อยท์
                        </a>
                        <a href="?page=discount_manage" class="list-group-item <?= isActive('discount_manage', $sub) ?>">
                            <i class="fa-solid fa-percent text-danger"></i> โค้ดส่วนลดสินค้า
                        </a>
                        <a href="?page=contact_manage" class="list-group-item <?= isActive('contact_manage', $sub) ?>">
                            <i class="fa-solid fa-address-book text-primary"></i> ช่องทางติดต่อ & โซเชียล
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Select -->
            <div class="col-12 d-lg-none">
                <div class="admin-sidebar p-3">
                    <select class="admin-input form-select w-100" onchange="if(window.loadSeamlessPage){ window.loadSeamlessPage(this.value, true); } else { window.location.href=this.value; }">
                        <optgroup label="ภาพรวมร้านค้า">
                            <option value="?page=backend" <?= isActive('backend',$sub)?'selected':'' ?>>📊 แดชบอร์ด (Dashboard)</option>
                        </optgroup>
                        <optgroup label="จัดการร้านค้า">
                            <option value="?page=website" <?= isActive('website',$sub)?'selected':'' ?>>⚙️ ตั้งค่าเว็บไซต์ & ชำระเงิน</option>
                            <?php if (defined('IS_PARENT_STORE') && IS_PARENT_STORE === true): ?>
                                <option value="?page=discord_bot_manage" <?= isActive('discord_bot_manage',$sub)?'selected':'' ?>>🤖 จัดการระบบเช่าบอท</option>
                                <option value="?page=rental_packages" <?= isActive('rental_packages',$sub)?'selected':'' ?>>🌐 จัดการแพ็กเกจเช่าเว็บ</option>
                                <option value="?page=api_store_manage" <?= isActive('api_store_manage',$sub)?'selected':'' ?>>🏬 จัดการร้านค้าที่เช่า</option>
                            <?php endif; ?>
                            <option value="?page=theme_manage" <?= isActive('theme_manage',$sub)?'selected':'' ?>>✨ ปรับแต่งสีไฟ & ธีมเว็บ</option>
                            <option value="?page=banner_manage" <?= isActive('banner_manage',$sub)?'selected':'' ?>>🖼️ จัดการป้ายหน้าร้าน (Banners)</option>
                            <option value="?page=category_manage" <?= isActive('category_manage',$sub)?'selected':'' ?>>📂 จัดการหมวดหมู่</option>
                            <option value="?page=product_manage" <?= isActive('product_manage',$sub)?'selected':'' ?>>📦 จัดการสินค้า</option>
                            <option value="?page=stock_manage" <?= isActive('stock_manage',$sub)?'selected':'' ?>>🗃️ สต็อกสินค้า & คีย์</option>
                            <option value="?page=wheel_manage" <?= isActive('wheel_manage',$sub)?'selected':'' ?>>🎡 จัดการกล่องสุ่ม & วงล้อ</option>
                        </optgroup>
                        <optgroup label="สมาชิก & การเงิน">
                            <option value="?page=user_edit" <?= isActive('user_edit',$sub)?'selected':'' ?>>👥 จัดการสมาชิก</option>
                            <option value="?page=backend_topup_history" <?= isActive('backend_topup_history',$sub)?'selected':'' ?>>💳 ประวัติการเติมเงิน</option>
                            <option value="?page=backend_buy_history" <?= isActive('backend_buy_history',$sub)?'selected':'' ?>>🛍️ ประวัติการสั่งซื้อ</option>
                        </optgroup>
                        <optgroup label="โปรโมชั่น & ติดต่อ">
                            <option value="?page=code_manage" <?= isActive('code_manage',$sub)?'selected':'' ?>>🎫 โค้ดแลกพ้อยท์</option>
                            <option value="?page=discount_manage" <?= isActive('discount_manage',$sub)?'selected':'' ?>>🏷️ โค้ดส่วนลดสินค้า</option>
                            <option value="?page=contact_manage" <?= isActive('contact_manage',$sub)?'selected':'' ?>>📞 ช่องทางติดต่อ & โซเชียล (Discord)</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-12 col-lg-9">
                <div class="admin-content-card p-0" style="min-height: 75vh;">
                    <?php
                    $page = $_GET['page'] ?? 'backend';
                    $backend_pages = [
                        'backend'               => 'page/backend/dashboard.php',
                        'theme_manage'          => 'page/backend/theme_manage.php',
                        'user_edit'             => 'page/backend/user_edit.php',
                        'category_manage'       => 'page/backend/category.php',
                        'product_manage'        => 'page/backend/product.php',
                        'stock_manage'          => 'page/backend/stock.php',
                        'wheel_manage'          => 'page/backend/wheel_manage.php',
                        'banner_manage'         => 'page/backend/banner_manage.php',
                        'code_manage'           => 'page/backend/code_manage.php',
                        'discount_manage'       => 'page/backend/discount_manage.php',
                        'backend_buy_history'   => 'page/backend/buy_history.php',
                        'backend_topup_history' => 'page/backend/topup_history.php',
                        'website'               => 'page/backend/website.php',
                        'contact_manage'        => 'page/backend/contact_manage.php',
                    ];
                    if (defined('IS_PARENT_STORE') && IS_PARENT_STORE === true) {
                        $backend_pages['discord_bot_manage'] = 'page/backend/discord_bot_manage.php';
                        $backend_pages['rental_packages'] = 'page/backend/rental_packages.php';
                        $backend_pages['api_store_manage'] = 'page/backend/api_store_manage.php';
                    }

                    if (admin($user) && array_key_exists($page, $backend_pages)) {
                        require_once($backend_pages[$page]);
                    } else {
                        echo "<div class='p-5 text-center text-muted'><i class='fa-solid fa-lock fs-1 mb-3'></i><h3>ไม่พบหน้านี้หรือคุณไม่มีสิทธิ์เข้าถึง</h3></div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Ensure all modals are appended to body so they are perfectly centered and interactive
    $('.modal').appendTo('body');
});
</script>
