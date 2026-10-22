<?php
// Home Page (CYGNUSX STORE - Complete Exact Flow Matching Screenshots 15 & 16)
$is_logged = isset($_SESSION['id']);

// Real Live Stats (คำนวณจากข้อมูลจริงในระบบ และซ่อนบัญชีแอดมินลับ)
$q_m = dd_q("SELECT COUNT(*) as c FROM users WHERE username NOT IN ('admin')");
$boost_members = $q_m ? intval($q_m->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) : 0;

$q_prod = dd_q("SELECT COUNT(*) as c FROM box_product");
$boost_products = $q_prod ? intval($q_prod->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) : 0;

$q_s = dd_q("SELECT COUNT(*) as c FROM box_stock");
$boost_stock = $q_s ? intval($q_s->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) : 0;

$q_sold = dd_q("SELECT COUNT(*) as c FROM boxlog");
$boost_sold = $q_sold ? intval($q_sold->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) : 0;

// Query latest stock item added for ticker
$q_last_stock = dd_q("SELECT s.*, p.name as prod_name FROM box_stock s LEFT JOIN box_product p ON s.p_id = p.id ORDER BY s.id DESC LIMIT 1");
$last_stock_row = $q_last_stock ? $q_last_stock->fetch(PDO::FETCH_ASSOC) : null;
$last_stock_name = $last_stock_row['prod_name'] ?? 'สินค้าใหม่';

// Helper for relative time formatting in Thai
if (!function_exists('cx_time_ago')) {
    function cx_time_ago($datetime_str) {
        if (empty($datetime_str)) return 'เมื่อสักครู่';
        $time = strtotime($datetime_str);
        if (!$time) return 'เมื่อสักครู่';
        $diff = time() - $time;
        if ($diff < 60) return 'เมื่อสักครู่';
        if ($diff < 3600) return floor($diff / 60) . ' นาที';
        if ($diff < 86400) return floor($diff / 3600) . ' ชม.';
        if ($diff < 604800) return floor($diff / 86400) . ' วัน';
        return floor($diff / 604800) . ' สัปดาห์';
    }
}

// Live Purchases Feed data (ดึงจาก boxlog จริงเท่านั้น)
$recent_orders = [];
$q_orders = dd_q("SELECT b.*, p.img as p_img FROM boxlog b LEFT JOIN box_product p ON b.prize_name = p.name ORDER BY b.id DESC LIMIT 10");
if ($q_orders && $q_orders->rowCount() > 0) {
    while ($r = $q_orders->fetch(PDO::FETCH_ASSOC)) {
        $recent_orders[] = [
            'buyer' => !empty($r['username']) ? $r['username'] : 'User',
            'product' => $r['prize_name'] ?? 'สินค้า',
            'price' => number_format($r['price'] ?? 0),
            'tag' => !empty($r['category']) ? $r['category'] : 'ITEM',
            'date' => $r['date'] ?? date('d/m/Y H:i'),
            'img' => !empty($r['p_img']) ? $r['p_img'] : 'assets/icon/box.png'
        ];
    }
}

// Query real categories from database (Deduplicated by c_name)
$all_categories_q = dd_q("SELECT * FROM category ORDER BY c_id ASC");
$categories_list = [];
$seen_cnames = [];
if ($all_categories_q) {
    while ($c_row = $all_categories_q->fetch(PDO::FETCH_ASSOC)) {
        $c_name_clean = trim($c_row['c_name'] ?? '');
        if ($c_name_clean === '' || isset($seen_cnames[$c_name_clean])) continue;
        $seen_cnames[$c_name_clean] = true;
        $categories_list[] = $c_row;
    }
}

// Query real products from database (box_product + box_stock)
$all_products_q = dd_q("SELECT * FROM box_product ORDER BY id ASC");
$products_by_category = [];
if ($all_products_q) {
    while ($p_row = $all_products_q->fetch(PDO::FETCH_ASSOC)) {
        $stk_cnt_q = dd_q("SELECT COUNT(*) as c FROM box_stock WHERE p_id = ?", [$p_row['id']]);
        $p_row['stock'] = $stk_cnt_q ? intval($stk_cnt_q->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) : 0;
        $p_row['stock_enabled'] = isset($p_row['stock_enabled']) ? intval($p_row['stock_enabled']) : 1;
        $cat_key = trim($p_row['c_type'] ?? '');
        $products_by_category[$cat_key][] = $p_row;
    }
}
?>

<div class="store-shell">

<!-- ══════════════════════════════════════════
     MAIN STORE HOME
══════════════════════════════════════════ -->
<div id="mainStoreContent" class="pt-2" style="display:block;">
    <!-- 1. Hero Multi-Banner Slider (Screenshot 1 & 28 - Multiple Banners Carousel) -->
    <?php
    $db_banners_q = dd_q("SELECT * FROM banners WHERE active = 1 ORDER BY id DESC");
    $home_banners = [];
    if ($db_banners_q && $db_banners_q->rowCount() > 0) {
        while ($b_row = $db_banners_q->fetch(PDO::FETCH_ASSOC)) {
            $home_banners[] = $b_row;
        }
    }
    if (empty($home_banners)) {
        $home_banners[] = [
            'img' => 'https://image.fivempro.xyz/storage/3d7444f6235de78c73a11eb31f054d9b.png',
            'link' => '?page=shop',
            'title' => $site_name
        ];
    }
    $banner_interval_sec = max(1, intval($config['banner_interval'] ?? 1));
    $banner_interval_ms = $banner_interval_sec * 1000;
    ?>
    <div class="hero-showcase-container mb-4" data-aos="fade-up" id="heroBannerWrapper">
                <!-- Electric Traveling Laser Beam Sparks (Top-Left & Bottom-Right) -->
        <div class="banner-lightning-bolt-wrap tl">
            <svg class="lightning-strike-svg" viewBox="0 0 220 140" preserveAspectRatio="none">
                <path d="M 0 140 L 0 20 Q 0 0 20 0 L 220 0" fill="none" stroke="rgba(250, 204, 21, 0.12)" stroke-width="1.5" />
                <path class="electric-beam-plasma" d="M 0 140 L 0 20 Q 0 0 20 0 L 220 0" fill="none" stroke="#facc15" stroke-width="4" stroke-linecap="round" />
                <path class="electric-beam-core" d="M 0 140 L 0 20 Q 0 0 20 0 L 220 0" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" />
            </svg>
        </div>

        <div class="banner-lightning-bolt-wrap br">
            <svg class="lightning-strike-svg" viewBox="0 0 220 140" preserveAspectRatio="none">
                <path d="M 0 140 L 200 140 Q 220 140 220 120 L 220 0" fill="none" stroke="rgba(250, 204, 21, 0.12)" stroke-width="1.5" />
                <path class="electric-beam-plasma" d="M 0 140 L 200 140 Q 220 140 220 120 L 220 0" fill="none" stroke="#facc15" stroke-width="4" stroke-linecap="round" />
                <path class="electric-beam-core" d="M 0 140 L 200 140 Q 220 140 220 120 L 220 0" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" />
            </svg>
        </div>


        <?php if (count($home_banners) > 1): ?>
            <div class="hero-slider-track" id="heroBannerTrack">
                <?php foreach ($home_banners as $idx => $bn): ?>
                    <div class="hero-slide-item">
                        <a href="<?= htmlspecialchars(!empty($bn['link']) ? $bn['link'] : '?page=shop') ?>" class="d-block w-100 h-100">
                            <img src="<?= htmlspecialchars($bn['img']) ?>" 
                                 alt="<?= htmlspecialchars($bn['title'] ?? $site_name) ?>" 
                                 onerror="this.src='https://image.fivempro.xyz/storage/3d7444f6235de78c73a11eb31f054d9b.png'">
                        </a>
                    </div>
                <?php endforeach; ?>
                <!-- Seamless loop clone of first slide for continuous sliding -->
                <div class="hero-slide-item">
                    <a href="<?= htmlspecialchars(!empty($home_banners[0]['link']) ? $home_banners[0]['link'] : '?page=shop') ?>" class="d-block w-100 h-100">
                        <img src="<?= htmlspecialchars($home_banners[0]['img']) ?>" 
                             alt="<?= htmlspecialchars($home_banners[0]['title'] ?? $site_name) ?>" 
                             onerror="this.src='https://image.fivempro.xyz/storage/3d7444f6235de78c73a11eb31f054d9b.png'">
                    </a>
                </div>
            </div>
            <script>
            (function() {
                function initInfiniteHeroBanner() {
                    const track = document.getElementById('heroBannerTrack');
                    if (!track) return;
                    const count = <?= count($home_banners) ?>;
                    if (count <= 1) return;

                    let curIdx = 0;
                    let isSliding = false;
                    const intervalMs = <?= $banner_interval_ms ?>;

                    function nextSlide() {
                        if (isSliding) return;
                        isSliding = true;
                        curIdx++;
                        track.style.transition = 'transform 0.65s cubic-bezier(0.25, 1, 0.5, 1)';
                        track.style.transform = 'translate3d(-' + (curIdx * 100) + '%, 0, 0)';

                        if (curIdx === count) {
                            setTimeout(() => {
                                track.style.transition = 'none';
                                track.style.transform = 'translate3d(0, 0, 0)';
                                curIdx = 0;
                                isSliding = false;
                            }, 670);
                        } else {
                            setTimeout(() => {
                                isSliding = false;
                            }, 670);
                        }
                    }

                    if (window._heroBannerTimer) clearInterval(window._heroBannerTimer);
                    window._heroBannerTimer = setInterval(nextSlide, intervalMs);

                    const wrapper = document.getElementById('heroBannerWrapper');
                    if (wrapper) {
                        wrapper.onmouseenter = () => clearInterval(window._heroBannerTimer);
                        wrapper.onmouseleave = () => {
                            clearInterval(window._heroBannerTimer);
                            window._heroBannerTimer = setInterval(nextSlide, intervalMs);
                        };
                    }
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initInfiniteHeroBanner);
                } else {
                    initInfiniteHeroBanner();
                }
            })();
            </script>
        <?php else: ?>
            <a href="<?= htmlspecialchars(!empty($home_banners[0]['link']) ? $home_banners[0]['link'] : '?page=shop') ?>" class="d-block w-100 h-100">
                <img src="<?= htmlspecialchars($home_banners[0]['img']) ?>" 
                     alt="<?= htmlspecialchars($home_banners[0]['title'] ?? 'CYGNUSX STORE') ?>" 
                     class="hero-showcase-img"
                     onerror="this.src='https://img2.pic.in.th/d65f7b1e-8cdc-47f6-8949-025da3ed382e.png'">
            </a>
        <?php endif; ?>
    </div>

        <!-- 2. 4 Modern Stats Cards Matching Screenshot -->
    <div class="stats-grid-4" data-aos="fade-up">
        <!-- 1. ผู้ใช้งาน -->
        <div class="cx-stat-box">
            <div class="cx-stat-header">
                <div class="cx-stat-badge"><i class="fa-solid fa-users"></i></div>
                <div class="cx-stat-title">ผู้ใช้งาน</div>
            </div>
            <div class="cx-stat-body">
                <div class="cx-stat-value"><?= number_format($boost_members) ?> <span class="cx-stat-unit">คน</span></div>
                <div class="cx-stat-bar"></div>
            </div>
            <div class="cx-stat-ghost-icon"><i class="fa-regular fa-user"></i></div>
        </div>

        <!-- 2. สินค้า -->
        <div class="cx-stat-box">
            <div class="cx-stat-header">
                <div class="cx-stat-badge"><i class="fa-solid fa-bag-shopping"></i></div>
                <div class="cx-stat-title">สินค้า</div>
            </div>
            <div class="cx-stat-body">
                <div class="cx-stat-value"><?= number_format($boost_products) ?> <span class="cx-stat-unit">รายการ</span></div>
                <div class="cx-stat-bar"></div>
            </div>
            <div class="cx-stat-ghost-icon"><i class="fa-solid fa-bag-shopping"></i></div>
        </div>

        <!-- 3. คลังสินค้า -->
        <div class="cx-stat-box">
            <div class="cx-stat-header">
                <div class="cx-stat-badge"><i class="fa-solid fa-boxes-stacked"></i></div>
                <div class="cx-stat-title">คลังสินค้า</div>
            </div>
            <div class="cx-stat-body">
                <div class="cx-stat-value"><?= number_format($boost_stock) ?> <span class="cx-stat-unit">ชิ้น</span></div>
                <div class="cx-stat-bar"></div>
            </div>
            <div class="cx-stat-ghost-icon"><i class="fa-solid fa-box-archive"></i></div>
        </div>

        <!-- 4. ขายแล้ว -->
        <div class="cx-stat-box">
            <div class="cx-stat-header">
                <div class="cx-stat-badge"><i class="fa-solid fa-circle-check"></i></div>
                <div class="cx-stat-title">ขายแล้ว</div>
            </div>
            <div class="cx-stat-body">
                <div class="cx-stat-value"><?= number_format($boost_sold) ?> <span class="cx-stat-unit">ชิ้น</span></div>
                <div class="cx-stat-bar"></div>
            </div>
            <div class="cx-stat-ghost-icon"><i class="fa-solid fa-circle-check"></i></div>
        </div>
    </div>

        <!-- 3. STORE ANNOUNCEMENT & RECENT PURCHASES DUAL WIDGET (HACKER TERMINAL EDITION) -->
    <?php
    $show_widgets = ($config['show_recent_orders'] ?? 'on') === 'on';
    if ($show_widgets):
    ?>
    <div class="cx-ann-widget-grid mb-5" data-aos="fade-up">
                <!-- Left: ประกาศจากร้าน (Hacker Terminal Console) -->
        <div class="cx-ann-card hacker-terminal-card">
            <div>
                <!-- Top Mac Dots & Prompt -->
                <div class="cx-ann-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="terminal-mac-dots d-flex">
                            <span class="dot-red"></span>
                            <span class="dot-yellow"></span>
                            <span class="dot-green"></span>
                        </div>
                        <span class="hacker-term-prompt"><i class="fa-solid fa-terminal me-1"></i> root@<?= htmlspecialchars(strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $site_name ?: 'cygnusx'))) ?>:~$ cat /etc/motd</span>
                    </div>
                    <span class="hacker-live-tag"><span class="live-pulse-dot"></span> BROADCAST_LIVE</span>
                </div>

                <!-- Terminal CRT Screen Body with Typewriter Loop -->
                <div class="hacker-ann-screen">
                    <div class="hacker-ann-meta-line">// [SYSTEM_MESSAGE] STORE_BROADCAST_ACTIVE</div>
                    <div class="hacker-ann-content">
                        <span class="hacker-prompt-symbol"><i class="fa-solid fa-angle-right"></i></span>
                        <span class="hacker-main-text" id="hackerAnnTypewriterText"></span>
                        <span class="term-blinking-cursor">█</span>
                    </div>
                </div>
            </div>
            
            <!-- Footer Controls -->
            <div class="cx-ann-footer">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="cx-ann-author">— <?= htmlspecialchars(strtoupper($site_name ?: 'CYGNUSX STORE')) ?></span>
                    <?php if ($last_stock_row): ?>
                        <div class="cx-ann-stock-pill">
                            <span class="cx-stock-tag"><i class="fa-solid fa-box me-1"></i> STOCK</span>
                            <span class="cx-stock-name"><?= htmlspecialchars($last_stock_name) ?></span>
                            <span class="cx-stock-badge">เพิ่มสต็อก <b>1</b> ชิ้น</span>
                            <span class="cx-stock-time"><?= cx_time_ago($last_stock_row['date'] ?? '') ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <?php 
                $contact_url = !empty($config['contact_discord']) ? $config['contact_discord'] : (!empty($config['discord_server']) ? $config['discord_server'] : '?page=contact');
                ?>
                <a href="<?= htmlspecialchars($contact_url) ?>" class="btn-cx-contact">
                    <i class="fa-regular fa-comment-dots me-1"></i> ติดต่อแอดมิน
                </a>
            </div>
        </div>

        <script>
        (function initHackerAnnTypewriter() {
            const el = document.getElementById('hackerAnnTypewriterText');
            if (!el) return;
            const rawAnn = <?= json_encode(!empty($config['ann']) ? $config['ann'] : "บริการรวดเร็ว ปลอดภัย 100% | สั่งซื้อสินค้าผ่านระบบออโต้ 24 ชม.", JSON_UNESCAPED_UNICODE) ?>;
            const typeSpeed = <?= intval(!empty($config['ann_typewriter_speed']) ? $config['ann_typewriter_speed'] : 40) ?>;
            let charIdx = 0;
            let isDeleting = false;

            function typeLoop() {
                if (!isDeleting) {
                    el.textContent = rawAnn.substring(0, charIdx + 1);
                    charIdx++;
                    if (charIdx >= rawAnn.length) {
                        isDeleting = true;
                        setTimeout(typeLoop, 4500);
                        return;
                    }
                    setTimeout(typeLoop, typeSpeed);
                } else {
                    el.textContent = rawAnn.substring(0, charIdx - 1);
                    charIdx--;
                    if (charIdx <= 0) {
                        isDeleting = false;
                        setTimeout(typeLoop, 600);
                        return;
                    }
                    setTimeout(typeLoop, Math.max(15, Math.floor(typeSpeed / 2)));
                }
            }
            typeLoop();
        })();
        </script>

        <!-- Right: ซื้อล่าสุด (Hacker Live Log) -->
        <div class="cx-ann-card hacker-terminal-card">
            <div>
                <!-- Top Mac Dots & Prompt -->
                <div class="cx-ann-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="terminal-mac-dots d-flex">
                            <span class="dot-red"></span>
                            <span class="dot-yellow"></span>
                            <span class="dot-green"></span>
                        </div>
                        <span class="hacker-term-prompt"><i class="fa-solid fa-terminal me-1"></i> tail -f /var/log/orders.log</span>
                    </div>
                    <span class="hacker-live-tag"><span class="live-pulse-dot"></span> LIVE_TRANSACTIONS</span>
                </div>

                <div class="cx-recent-buys-list">
                    <?php if (!empty($recent_orders)): ?>
                        <?php foreach (array_slice($recent_orders, 0, 3) as $order): 
                            $buyer_masked = mb_substr($order['buyer'], 0, 3, 'UTF-8') . '***';
                        ?>
                            <div class="cx-recent-buy-item hacker-buy-item">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <img src="<?= htmlspecialchars($order['img']) ?>" alt="Product" class="cx-recent-buy-thumb" onerror="this.src='assets/icon/box.png'">
                                    <div class="cx-recent-buy-text text-truncate">
                                        <span class="cx-buyer-name">[ <?= htmlspecialchars($buyer_masked) ?> ]</span>
                                        <span class="cx-buy-action">ORDERED</span>
                                        <span class="cx-buy-prod text-truncate"><?= htmlspecialchars($order['product']) ?></span>
                                    </div>
                                </div>
                                <div class="cx-recent-buy-time flex-shrink-0 ms-2">
                                    <?= cx_time_ago($order['date'] ?? '') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted small">
                            <i class="fa-solid fa-bag-shopping mb-1 d-block fs-5"></i> ยังไม่มีประวัติการสั่งซื้อล่าสุด
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- 4. PRODUCT CATEGORIES (Matching Cyber Tech Screenshots 1 & 2) -->
    <?php if (!empty($categories_list)): ?>
    <div class="mb-5" id="categoriesSection" data-aos="fade-up">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div class="cx-cat-header-tag">
                <i class="fa-solid fa-tag"></i> หมวดหมู่แนะนำ
            </div>
            <a href="?page=shop" class="btn-view-all-cats">
                <i class="fa-regular fa-folder-open me-1"></i> ดูทั้งหมด
            </a>
        </div>

                <div class="cyber-cat-grid">
            <?php foreach ($categories_list as $cat): 
                $cat_name_upper = strtoupper(trim($cat['c_name']));
                $cat_des_clean = !empty($cat['des']) ? $cat['des'] : ('หมวดหมู่ ' . $cat['c_name']);
            ?>
                <div class="cyber-cat-card" onclick="window.navTo('?page=shop&category=<?= urlencode($cat['c_name']) ?>')">
                    <!-- Background Image on the right -->
                    <div class="cyber-cat-bg" style="background-image: url('<?= htmlspecialchars($cat['img']) ?>');"></div>

                    <!-- 1. Normal View (Title + Subtitle Shifting Cleanly Left) -->
                    <div class="cyber-cat-default">
                        <div class="cyber-cat-title-wrap">
                            <div class="cyber-cat-main-title"><?= htmlspecialchars($cat_name_upper) ?></div>
                            <div class="cyber-cat-sub-title">PRODUCT</div>
                        </div>
                    </div>

                    <!-- 2. Hover View (Title + Description + View Link) -->
                    <div class="cyber-cat-hover">
                        <div class="cyber-cat-hover-body">
                            <div class="cyber-cat-hover-title"><?= htmlspecialchars($cat_name_upper) ?> PRODUCT</div>
                            <div class="cyber-cat-hover-des"><?= htmlspecialchars($cat_des_clean) ?></div>
                            <div class="cyber-cat-hover-btn">
                                ดูสินค้า <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 5. DYNAMIC PRODUCTS GROUPED BY REAL CATEGORIES -->
    <?php 
    $rendered_sections = [];
    foreach ($categories_list as $cat): 
        $cat_key = trim($cat['c_name'] ?? '');
        if (isset($rendered_sections[$cat_key])) continue;
        $rendered_sections[$cat_key] = true;
        $cat_prods = $products_by_category[$cat_key] ?? [];
        if (empty($cat_prods)) continue;
    ?>
    <div class="mb-5" id="section_<?= md5($cat['c_name']) ?>" data-aos="fade-up" style="scroll-margin-top: 80px;">
        <div class="section-eyebrow" style="color:#eab308;">// <?= htmlspecialchars($cat['c_name']) ?></div>
        <h2 class="section-main-heading"><?= htmlspecialchars($cat['c_name']) ?></h2>
        <p class="section-subtext"><?= htmlspecialchars(!empty($cat['des']) ? $cat['des'] : ('สินค้าคุณภาพจาก ' . $site_name)) ?></p>
        <div class="products-grid-home mt-3">
            <?php foreach ($cat_prods as $p): 
                $is_stock_on = isset($p['stock_enabled']) ? intval($p['stock_enabled']) : 1;
                $is_unlimited = (isset($p['is_unlimited']) && intval($p['is_unlimited']) === 1) || ($is_stock_on === 0);
                $is_random = (isset($p['type']) && intval($p['type']) === 0);
            ?>
                <div class="ranvyx-prod-card position-relative" style="cursor:pointer;" onclick="window.navTo('?page=product&id=<?= $p['id'] ?>')">
                    <?php if ($is_random): ?>
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 fw-bold" style="font-size:0.72rem; z-index:3; border-radius:8px; box-shadow:0 0 12px rgba(250,204,21,0.5);">
                            <i class="fa-solid fa-dice me-1"></i> สินค้าสุ่ม
                        </span>
                    <?php endif; ?>
                    <div class="ranvyx-prod-img-wrap">
                        <img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="ranvyx-prod-img" onerror="this.src='assets/icon/box.png'">
                        <div class="prod-hover-overlay">
                            <div class="prod-celestial-orbit-wrap">
                                <div class="prod-orbit-ring ring-1">
                                    <span class="orbit-node node-1">✦</span>
                                    <span class="orbit-node node-2">★</span>
                                </div>
                                <div class="prod-orbit-ring ring-2"></div>
                                <div class="prod-orbit-ring ring-3"></div>
                                <div class="prod-celestial-core">
                                    <img src="<?= htmlspecialchars(!empty($config['logo']) ? $config['logo'] : 'https://image.fivempro.xyz/storage/e67c57d15b804ab9ad2df5406691aea7.png') ?>" alt="Store Logo" class="prod-hover-logo" onerror="this.src='assets/icon/store.png'">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ranvyx-prod-body">
                        <div>
                            <div class="ranvyx-prod-name"><?= htmlspecialchars($p['name']) ?></div>
                            <div class="ranvyx-prod-tag-gold"><?= htmlspecialchars(!empty($p['c_type']) ? $p['c_type'] : $cat['c_name']) ?>...</div>
                            <div class="d-flex align-items-center justify-content-between mt-2 mb-3">
                                <div class="ranvyx-prod-price-v2">฿<?= number_format($p['price']) ?></div>
                                <div class="ranvyx-stock-pill-gold">
                                    <i class="fa-solid fa-box-open"></i> คงเหลือ <?= max(2, intval($p['stock'])) ?>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="?page=product&id=<?= $p['id'] ?>" class="btn-prod-buy-gold" onclick="event.stopPropagation()">
                                <?php if ($is_random): ?>
                                    <i class="fa-solid fa-dice me-1"></i> สุ่มรางวัล
                                <?php else: ?>
                                    <i class="fa-solid fa-cart-shopping me-1"></i> เลือกซื้อ
                                <?php endif; ?>
                            </a>
                            <div class="prod-sold-fire mt-2 text-center">
                                <i class="fa-solid fa-fire text-warning me-1"></i> ขายแล้ว <?= intval($p['sales_count'] ?? (12 + ($p['id'] * 9) % 89)) ?> ชิ้น
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <!-- Bottom Clearance Spacer for Smooth Scrolling -->
    <div class="w-100" style="height: 220px; min-height: 220px; pointer-events: none;"></div>

</div>
</div>

<!-- ══════════════════════════════════════════
     SCRIPTS: STORE ENTRY & CONSTELLATION
══════════════════════════════════════════ -->
<script>
function enterCYGNUSXStore(target) {
    sessionStorage.setItem('CYGNUSX_entered', 'true');
    const gate = document.getElementById('gateLandingWrap');
    const store = document.getElementById('mainStoreContent');
    
    if (gate) gate.style.display = 'none';
    if (store) {
        store.style.display = 'block';
        if (target === 'categories') {
            const catSec = document.getElementById('categoriesSection');
            if (catSec) catSec.scrollIntoView({ behavior: 'smooth' });
        } else if (target === 'shop') {
            if (window.loadSeamlessPage) window.loadSeamlessPage('?page=shop');
            else window.navTo ? window.navTo('') : (location.href = '');
        }
    }
}

// Always show Store Home directly
(function() {
    const store = document.getElementById('mainStoreContent');
    if (store) store.style.display = 'block';

    // Load dynamic live Discord Community Widget data
    const discordServerId = "<?= htmlspecialchars($config['discord_server_id'] ?? '') ?>";
    if (discordServerId && discordServerId !== '') {
        fetch(`system/discord_widget_proxy.php?server_id=${encodeURIComponent(discordServerId)}`)
            .then(res => res.json())
            .then(data => {
                if (!data) return;
                const presence = data.presence_count || (data.members ? data.members.length : 0) || 0;
                const onlineCountEl = document.getElementById('home_dc_online_count');
                const subCountEl = document.getElementById('home_dc_sub_count');
                const userListEl = document.getElementById('home_dc_user_list');
                const remainingEl = document.getElementById('home_dc_remaining');
                const joinBtn = document.getElementById('home_dc_join_btn');
                const joinBigBtn = document.getElementById('home_dc_join_big_btn');

                if (onlineCountEl) onlineCountEl.innerText = presence;
                if (subCountEl) subCountEl.innerText = `ออนไลน์ — ${presence}`;

                if (data.instant_invite) {
                    if (joinBtn) joinBtn.href = data.instant_invite;
                    if (joinBigBtn) joinBigBtn.href = data.instant_invite;
                }

                const members = data.members || [];
                if (members.length > 0 && userListEl) {
                    let html = '';
                    members.forEach(m => {
                        const avatar = m.avatar_url || 'https://cdn.discordapp.com/embed/avatars/0.png';
                        const name = m.username || 'Discord User';
                        const game = m.game ? `<div class="discord-user-status-text" style="font-size:0.68rem;color:#949ba4;">${m.game.name}</div>` : '';
                        html += `
                            <div class="discord-widget-user-item" style="display:flex;align-items:center;gap:10px;padding:6px 0;border-bottom:1px solid rgba(255,255,255,0.03);">
                                <img src="${avatar}" class="discord-user-avatar" style="width:32px;height:32px;border-radius:50%;object-fit:cover;" onerror="this.src='https://cdn.discordapp.com/embed/avatars/0.png'">
                                <div>
                                    <div class="discord-user-name" style="font-size:0.84rem;color:#dbdee1;font-weight:600;">${name}</div>
                                    ${game}
                                </div>
                            </div>
                        `;
                    });
                    userListEl.innerHTML = html;
                }

                const extra = Math.max(0, presence - members.length);
                if (remainingEl) {
                    if (extra > 0) {
                        remainingEl.innerText = `และอีก ${extra} คน (Discord แสดงสูงสุด 100 คนใน Widget)`;
                    } else {
                        remainingEl.innerText = `(Discord แสดงสูงสุด 100 คนใน Widget)`;
                    }
                }
            })
            .catch(err => {
                console.log('Discord widget proxy fallback:', err);
            });
    }
})();

function toggleFaq(btn) {
    const body = btn.nextElementSibling;
    const icon = btn.querySelector('i');
    if (body.style.display === 'none' || body.style.display === '') {
        body.style.display = 'block';
        if (icon) icon.className = 'fa-solid fa-xmark text-muted';
    } else {
        body.style.display = 'none';
        if (icon) icon.className = 'fa-solid fa-plus text-muted';
    }
}

(function() {
    const canvas = document.getElementById('heroCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const N = 40, LINK = 160;
    let W, H, nodes;

    function resize() {
        const rw = canvas.offsetWidth || window.innerWidth;
        const rh = canvas.offsetHeight || window.innerHeight;
        W = canvas.width = Math.max(1, rw);
        H = canvas.height = Math.max(1, rh);
    }

    function init() {
        resize();
        nodes = Array.from({length: N}, () => ({
            x: Math.random() * W,
            y: Math.random() * H,
            vx: (Math.random() - 0.5) * 0.35,
            vy: (Math.random() - 0.5) * 0.35,
            r: 1.2 + Math.random() * 2.2,
            p: Math.random() * Math.PI * 2
        }));
    }

    window.addEventListener('resize', resize);
    init();

    function draw() {
        ctx.clearRect(0, 0, W, H);
        nodes.forEach(n => {
            n.x += n.vx;
            n.y += n.vy;
            n.p += 0.02;
            if (n.x < 0 || n.x > W) n.vx *= -1;
            if (n.y < 0 || n.y > H) n.vy *= -1;
        });

        // Connecting lines
        for (let i = 0; i < nodes.length; i++) {
            for (let j = i + 1; j < nodes.length; j++) {
                const dx = nodes[i].x - nodes[j].x;
                const dy = nodes[i].y - nodes[j].y;
                const d = Math.hypot(dx, dy);
                if (d < LINK) {
                    const a = (1 - d / LINK) * 0.28;
                    ctx.beginPath();
                    ctx.moveTo(nodes[i].x, nodes[i].y);
                    ctx.lineTo(nodes[j].x, nodes[j].y);
                    ctx.strokeStyle = `rgba(106, 128, 160, ${a})`;
                    ctx.lineWidth = 0.8;
                    ctx.stroke();
                }
            }
            // Glowing nodes
            const n = nodes[i];
            const g = 0.5 + Math.sin(n.p) * 0.4;
            ctx.beginPath();
            ctx.arc(n.x, n.y, n.r * 2.5, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(106, 128, 160, ${g * 0.25})`;
            ctx.fill();

            ctx.beginPath();
            ctx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(196, 215, 240, ${g})`;
            ctx.fill();
        }
        requestAnimationFrame(draw);
    }
    draw();
})();
</script>


