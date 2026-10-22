<?php
// Shop & Category Page (CYGNUSX STORE - Exact Match Screenshot 27)

$selected_cat = trim($_GET['category'] ?? '');
$search_query = trim($_GET['search'] ?? '');

// Fetch all categories from DB
$db_cats_q = dd_q("SELECT * FROM category ORDER BY c_id ASC");
$all_categories = [];
if ($db_cats_q && $db_cats_q->rowCount() > 0) {
    while ($c = $db_cats_q->fetch(PDO::FETCH_ASSOC)) {
        $c_name = $c['c_name'];
        $p_count_q = dd_q("SELECT COUNT(*) as cnt FROM box_product WHERE c_type = ?", [$c_name]);
        $p_count = $p_count_q ? intval($p_count_q->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0) : 0;

        $all_categories[] = [
            'id' => $c['c_id'],
            'title' => $c['c_name'],
            'banner_img' => !empty($c['img']) ? $c['img'] : 'https://CYGNUSXstore.com/uploads/products/bf80695c7eaa.webp',
            'desc' => $c['des'] ?? '',
            'count' => $p_count
        ];
    }
}

// Check if a category is selected or searching
$is_viewing_category = !empty($selected_cat) || !empty($search_query);

// Resolve current category title
$current_cat_title = $selected_cat;
foreach ($all_categories as $ac) {
    if ($ac['id'] == $selected_cat || strcasecmp($ac['title'], $selected_cat) == 0) {
        $current_cat_title = $ac['title'];
        break;
    }
}

// Fetch products for selected category or search
$products = [];
if ($is_viewing_category) {
    $product_sql = "SELECT * FROM box_product WHERE 1=1";
    $params = [];

    if (!empty($selected_cat)) {
        $product_sql .= " AND (c_type LIKE ? OR name LIKE ?)";
        $params[] = "%$selected_cat%";
        $params[] = "%$selected_cat%";
    }
    if (!empty($search_query)) {
        $product_sql .= " AND (name LIKE ? OR des LIKE ?)";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
    }
    $product_sql .= " ORDER BY id ASC";
    $products_q = dd_q($product_sql, $params);
    if ($products_q) {
        while ($p_row = $products_q->fetch(PDO::FETCH_ASSOC)) {
            $stk_cnt_q = dd_q("SELECT COUNT(*) as c FROM box_stock WHERE p_id = ?", [$p_row['id']]);
            $stk_cnt = $stk_cnt_q ? intval($stk_cnt_q->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) : 0;
            $stock_enabled = isset($p_row['stock_enabled']) ? intval($p_row['stock_enabled']) : 1;
            $is_unlimited = (isset($p_row['is_unlimited']) && intval($p_row['is_unlimited']) === 1) || ($stock_enabled === 0);
            $is_random = (isset($p_row['type']) && intval($p_row['type']) === 0);
            $products[] = [
                'id' => $p_row['id'],
                'name' => $p_row['name'],
                'price' => (float)$p_row['price'],
                'stock' => $stk_cnt,
                'stock_enabled' => $stock_enabled,
                'is_unlimited' => $is_unlimited,
                'is_random' => $is_random,
                'percent' => intval($p_row['percent'] ?? 100),
                'sales_count' => intval($p_row['sales_count'] ?? 0),
                'img' => !empty($p_row['img']) ? $p_row['img'] : 'assets/icon/box.png',
                'c_type' => $p_row['c_type'] ?? '',
                'des' => $p_row['des'] ?? ''
            ];
        }
    }
}
?>

<div class="store-shell" data-aos="fade-up">

    <?php if ($is_viewing_category): ?>
        <!-- ══════════════════════════════════════════
             DEDICATED CATEGORY PRODUCT VIEW (SCREENSHOT 27)
        ══════════════════════════════════════════ -->
        <!-- Top Back Button -->
        <div class="mb-4">
            <a href="?page=shop" class="btn btn-outline-secondary rounded-pill px-4 py-2" style="font-size:0.85rem;font-weight:700;">
                <i class="fa-solid fa-arrow-left me-2"></i> กลับหมวดหมู่
            </a>
        </div>

        <!-- Category Header Title -->
        <div class="mb-4">
            <div class="section-eyebrow">// PRODUCTS</div>
            <h1 class="fw-bold text-white mb-1" style="font-family:var(--font-cyber);font-size:2.2rem;letter-spacing:1.5px;">
                <?= htmlspecialchars(!empty($current_cat_title) ? $current_cat_title : 'ค้นหาสินค้า: ' . $search_query) ?>
            </h1>
            <p class="text-muted small mb-0">
                มีรายการสินค้าในหมวดหมู่นี้ • <?= count($products) ?> สินค้าในหมวดนี้
            </p>
        </div>

        <!-- 4-Column Product Grid -->
        <?php if (count($products) > 0): ?>
            <div class="products-grid-home mb-5">
                <?php foreach ($products as $p): 
                    $is_unlimited = !empty($p['is_unlimited']);
                ?>
                    <div class="ranvyx-prod-card position-relative" style="cursor:pointer;" onclick="window.navTo('?page=product&id=<?= $p['id'] ?>')">
                        <?php if (!empty($p['is_random'])): ?>
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
                                <div class="ranvyx-prod-tag-gold"><?= htmlspecialchars(!empty($p['c_type']) ? $p['c_type'] : 'PRODUCT') ?>...</div>
                                <div class="d-flex align-items-center justify-content-between mt-2 mb-3">
                                    <div class="ranvyx-prod-price-v2">฿<?= number_format($p['price']) ?></div>
                                    <div class="ranvyx-stock-pill-gold">
                                        <i class="fa-solid fa-box-open"></i> คงเหลือ <?= max(2, intval($p['stock'])) ?>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="?page=product&id=<?= $p['id'] ?>" class="btn-prod-buy-gold" onclick="event.stopPropagation()">
                                    <?php if (!empty($p['is_random'])): ?>
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
        <?php else: ?>
            <div class="text-center py-5 mb-5" style="background:#090a0f;border:1px solid rgba(255,255,255,0.08);border-radius:18px;">
                <i class="fa-solid fa-box-open fs-1 text-muted mb-3"></i>
                <h5 class="text-white fw-bold">ยังไม่มีสินค้าในหมวดหมู่นี้</h5>
                <p class="text-muted small mb-0">โปรดติดตามการอัปเดตสต็อกเร็วๆ นี้</p>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- ══════════════════════════════════════════
             CATEGORIES MAIN LIST VIEW (DESTINY STYLE)
        ══════════════════════════════════════════ -->
        <div class="mb-4">
            <div class="section-eyebrow" style="color:#eab308;">// ALL CATEGORIES</div>
            <h1 class="section-main-heading" style="font-size: 2.2rem; color: #facc15;">All Categories</h1>
            <p class="section-subtext">Browse our collections — pick what fits your setup.</p>
        </div>

        <!-- 2x2 Category Cards Grid -->
        <?php if (empty($all_categories)): ?>
            <div class="text-center py-5 mb-5" style="background:#080912;border:1px solid rgba(255,255,255,0.08);border-radius:18px;">
                <i class="fa-solid fa-layer-group text-muted fs-1 mb-3"></i>
                <h4 class="text-white fw-bold">ยังไม่มีหมวดหมู่สินค้า</h4>
                <p class="text-muted small">ร้านค้านี้ยังไม่ได้เพิ่มหมวดหมู่สินค้าในระบบ</p>
            </div>
        <?php else: ?>
            <div class="cyber-cat-grid mb-5">
                <?php foreach ($all_categories as $cat): 
                    $cat_name_upper = strtoupper(trim($cat['title']));
                    $cat_des_clean = !empty($cat['des']) ? $cat['des'] : ('หมวดหมู่ ' . $cat['title']);
                ?>
                    <div class="cyber-cat-card" onclick="window.navTo('?page=shop&category=<?= urlencode($cat['title']) ?>')">
                        <!-- Background Image on the right -->
                        <div class="cyber-cat-bg" style="background-image: url('<?= htmlspecialchars($cat['banner_img']) ?>');"></div>

                        

                        <!-- 1. Normal View (Title + Subtitle aligned to left) -->
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
        <?php endif; ?>
    <?php endif; ?>
</div>

