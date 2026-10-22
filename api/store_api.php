<?php
/**
 * ⚡ CYGNUSXSTORE - Modern Headless REST API Engine for Next.js & Frontend Stores
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Disable HTML error output to preserve pure JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once dirname(__DIR__) . '/system/a_func.php';

function json_out($success, $message, $data = [], $extra = []) {
    $response = [
        'success' => (bool)$success,
        'status'  => $success ? 'success' : 'error',
        'message' => $message,
        'data'    => $data
    ];
    if (!empty($extra) && is_array($extra)) {
        $response = array_merge($response, $extra);
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

global $conn;
if (!isset($conn) || !$conn) {
    json_out(false, 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาตรวจสอบการตั้งค่าระบบ');
}

// Helper to get raw JSON payload or POST fields
$raw_input = file_get_contents('php://input');
$json_input = [];
if (!empty($raw_input)) {
    $decoded = json_decode($raw_input, true);
    if (is_array($decoded)) {
        $json_input = $decoded;
    }
}
$params = array_merge($_GET, $_POST, $json_input);
$action = trim($params['action'] ?? '');

// Generate stateless auth token for Next.js / External Frontends
function make_user_token($user_id, $password_hash) {
    return base64_encode($user_id . ':' . md5($user_id . ':' . $password_hash));
}

// Helper to get authenticated user ID from session or Bearer token
function get_auth_user_id() {
    global $conn, $params;
    if (isset($_SESSION['id']) && intval($_SESSION['id']) > 0) {
        return intval($_SESSION['id']);
    }
    if (isset($_SESSION['user']['id']) && intval($_SESSION['user']['id']) > 0) {
        return intval($_SESSION['user']['id']);
    }

    // Check Authorization header (Bearer token) or query/body token
    $token = '';
    $auth_hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
    if (!empty($auth_hdr) && preg_match('/Bearer\s+(.*)$/i', $auth_hdr, $matches)) {
        $token = trim($matches[1]);
    }
    if (empty($token) && !empty($params['token'])) {
        $token = trim($params['token']);
    }

    if (!empty($token)) {
        $decoded = @base64_decode($token);
        if ($decoded && strpos($decoded, ':') !== false) {
            list($uid, $hash) = explode(':', $decoded, 2);
            $uid = intval($uid);
            if ($uid > 0) {
                $q = $conn->prepare("SELECT id, password FROM users WHERE id = ? LIMIT 1");
                $q->execute([$uid]);
                $u = $q->fetch(PDO::FETCH_ASSOC);
                if ($u && md5($u['id'] . ':' . $u['password']) === $hash) {
                    return $uid;
                }
            }
        }
    }
    return 0;
}

switch ($action) {

    // ==========================================
    // 🌐 1. STORE INFORMATION & THEME SETTINGS
    // ==========================================
    case 'get_store_info':
        try {
            $set_stmt = $conn->query("SELECT * FROM setting LIMIT 1");
            $setting = $set_stmt ? $set_stmt->fetch(PDO::FETCH_ASSOC) : [];
            
            // Mask sensitive API keys
            if (isset($setting['slip_api_key'])) $setting['slip_api_key'] = !empty($setting['slip_api_key']) ? 'configured' : '';
            if (isset($setting['api_product_key'])) $setting['api_product_key'] = !empty($setting['api_product_key']) ? 'configured' : '';

            $bank_stmt = $conn->query("SELECT * FROM bank LIMIT 1");
            $bank = $bank_stmt ? $bank_stmt->fetch(PDO::FETCH_ASSOC) : [];

            $car_stmt = $conn->query("SELECT * FROM carousel ORDER BY id ASC");
            $carousels = $car_stmt ? $car_stmt->fetchAll(PDO::FETCH_ASSOC) : [];

            $stat_stmt = $conn->query("SELECT * FROM static LIMIT 1");
            $stats = $stat_stmt ? $stat_stmt->fetch(PDO::FETCH_ASSOC) : [
                's_count' => 0, 'b_count' => 0, 'm_count' => 0, 'c_count' => 0, 'sold_count' => 0
            ];

            // Real-time dynamic count
            try {
                $m_c = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
                $b_c = $conn->query("SELECT COUNT(*) FROM box_product")->fetchColumn();
                $s_c = $conn->query("SELECT COUNT(*) FROM box_stock")->fetchColumn();
                $sold_c = $conn->query("SELECT COUNT(*) FROM boxlog")->fetchColumn();
                $stats['m_count'] = intval($m_c);
                $stats['b_count'] = intval($b_c);
                $stats['s_count'] = intval($s_c);
                $stats['sold_count'] = intval($sold_c);
            } catch(Exception $e_cnt) {}

            $current_store_title = defined('STORE_SHOP_NAME') && !empty(STORE_SHOP_NAME) ? STORE_SHOP_NAME : ($setting['name'] ?? 'CYGNUSX STORE');

            json_out(true, 'ดึงข้อมูลร้านค้าสำเร็จ', [
                'setting'   => $setting,
                'bank'      => $bank,
                'carousels' => $carousels,
                'stats'     => $stats,
                'store_name'=> $current_store_title,
                'is_parent' => defined('IS_PARENT_STORE') ? IS_PARENT_STORE : true
            ]);
        } catch (Exception $e) {
            json_out(false, 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
        break;

    // ==========================================
    // 📂 2. CATEGORIES
    // ==========================================
    case 'get_categories':
        try {
            $stmt = $conn->query("SELECT c_id, c_name, des, img FROM category ORDER BY c_id ASC");
            $categories = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            json_out(true, 'ดึงข้อมูลหมวดหมู่สำเร็จ', $categories);
        } catch (Exception $e) {
            json_out(false, 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
        break;

    // ==========================================
    // 📦 3. PRODUCTS LIST & STOCK
    // ==========================================
    case 'get_products':
        try {
            $cat_id = intval($params['category_id'] ?? 0);
            $search = trim($params['search'] ?? '');

            $sql = "SELECT p.*, 
                    (SELECT COUNT(*) FROM box_stock s WHERE s.p_id = p.id) AS stock_count,
                    c.c_name AS category_name
                    FROM box_product p
                    LEFT JOIN category c ON p.c_type = c.c_name OR p.c_type = c.c_id
                    WHERE 1=1";
            $sql_params = [];

            if ($cat_id > 0) {
                $sql .= " AND (p.c_type = ? OR c.c_id = ?)";
                $sql_params[] = $cat_id;
                $sql_params[] = $cat_id;
            }

            if (!empty($search)) {
                $sql .= " AND (p.name LIKE ? OR p.des LIKE ?)";
                $sql_params[] = "%$search%";
                $sql_params[] = "%$search%";
            }

            $sql .= " ORDER BY p.id DESC";

            $stmt = $conn->prepare($sql);
            $stmt->execute($sql_params);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            json_out(true, 'ดึงข้อมูลสินค้าสำเร็จ', $products);
        } catch (Exception $e) {
            json_out(false, 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
        break;

    // ==========================================
    // 🔍 4. PRODUCT DETAIL
    // ==========================================
    case 'get_product_detail':
        try {
            $id = intval($params['id'] ?? 0);
            if ($id <= 0) {
                json_out(false, 'ไม่พบรหัสสินค้าที่ระบุ');
            }

            $stmt = $conn->prepare("SELECT p.*, 
                (SELECT COUNT(*) FROM box_stock s WHERE s.p_id = p.id) AS stock_count,
                c.c_name AS category_name
                FROM box_product p
                LEFT JOIN category c ON p.c_type = c.c_name OR p.c_type = c.c_id
                WHERE p.id = ? LIMIT 1");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                json_out(false, 'ไม่พบสินค้านี้ในระบบ');
            }

            json_out(true, 'ดึงข้อมูลสินค้ารายละเอียดสำเร็จ', $product);
        } catch (Exception $e) {
            json_out(false, 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
        break;

    // ==========================================
    // 🔑 5. AUTH: REGISTER
    // ==========================================
    case 'register':
        try {
            $username = trim($params['username'] ?? '');
            $password = trim($params['password'] ?? '');
            $email    = trim($params['email'] ?? '');

            if (empty($username) || empty($password)) {
                json_out(false, 'กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน');
            }
            if (strlen($username) < 3 || strlen($username) > 30) {
                json_out(false, 'ชื่อผู้ใช้ต้องมีความยาว 3 - 30 ตัวอักษร');
            }
            if (strlen($password) < 4) {
                json_out(false, 'รหัสผ่านต้องมีความยาวอย่างน้อย 4 ตัวอักษร');
            }

            // Check if username exists
            $chk = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
            $chk->execute([$username]);
            if ($chk->fetch()) {
                json_out(false, 'ชื่อผู้ใช้นี้มีผู้ใช้งานแล้ว กรุณาใช้ชื่ออื่น');
            }

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, email, point, total, total_topup, rank, role, date) 
                                    VALUES (?, ?, ?, 0.00, 0.00, 0.00, 'member', 'member', NOW())");
            $stmt->execute([$username, $hashed, $email]);
            $new_id = $conn->lastInsertId();

            $_SESSION['id'] = $new_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'member';

            $token = make_user_token($new_id, $hashed);

            json_out(true, 'สมัครสมาชิกสำเร็จ ยินดีต้อนรับ!', [
                'id' => $new_id,
                'username' => $username,
                'token' => $token,
                'point' => 0.00,
                'rank' => 'member',
                'role' => 'member'
            ]);
        } catch (Exception $e) {
            json_out(false, 'เกิดข้อผิดพลาดในการสมัครสมาชิก: ' . $e->getMessage());
        }
        break;

    // ==========================================
    // 🔓 6. AUTH: LOGIN
    // ==========================================
    case 'login':
        try {
            $username = trim($params['username'] ?? '');
            $password = trim($params['password'] ?? '');

            if (empty($username) || empty($password)) {
                json_out(false, 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน');
            }

            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$u) {
                json_out(false, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
            }

            $is_valid = false;
            if (password_verify($password, $u['password'])) {
                $is_valid = true;
            } elseif ($u['password'] === md5($password)) {
                $is_valid = true;
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $upd->execute([$new_hash, $u['id']]);
                $u['password'] = $new_hash;
            } elseif ($u['password'] === $password) {
                $is_valid = true;
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $upd->execute([$new_hash, $u['id']]);
                $u['password'] = $new_hash;
            }

            if (!$is_valid) {
                json_out(false, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
            }

            $_SESSION['id'] = $u['id'];
            $_SESSION['username'] = $u['username'];
            $_SESSION['role'] = $u['role'] ?? ($u['rank'] ?? 'member');

            $token = make_user_token($u['id'], $u['password']);

            json_out(true, 'เข้าสู่ระบบสำเร็จ', [
                'id' => $u['id'],
                'username' => $u['username'],
                'token' => $token,
                'point' => floatval($u['point'] ?? 0),
                'total_topup' => floatval($u['total_topup'] ?? 0),
                'rank' => $u['rank'] ?? 'member',
                'role' => $u['role'] ?? 'member'
            ]);
        } catch (Exception $e) {
            json_out(false, 'เกิดข้อผิดพลาดในการเข้าสู่ระบบ: ' . $e->getMessage());
        }
        break;

    // ==========================================
    // 🚪 7. AUTH: LOGOUT
    // ==========================================
    case 'logout':
        session_unset();
        @session_destroy();
        json_out(true, 'ออกจากระบบสำเร็จ');
        break;

    // ==========================================
    // 👤 8. USER PROFILE
    // ==========================================
    case 'get_user_profile':
        $uid = get_auth_user_id();
        if ($uid <= 0) {
            json_out(false, 'กรุณาเข้าสู่ระบบก่อน', null, ['is_logged_in' => false]);
        }

        try {
            $stmt = $conn->prepare("SELECT id, username, email, point, total, total_topup, rank, role, date FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$uid]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user_data) {
                json_out(false, 'ไม่พบข้อมูลผู้ใช้');
            }

            $user_data['point'] = floatval($user_data['point']);
            $user_data['total_topup'] = floatval($user_data['total_topup']);
            json_out(true, 'ดึงข้อมูลโปรไฟล์สำเร็จ', $user_data, ['is_logged_in' => true]);
        } catch (Exception $e) {
            json_out(false, 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
        break;

    // ==========================================
    // 🛒 9. ORDER: BUY PRODUCT (Auto-Delivery)
    // ==========================================
    case 'buy_product':
        $uid = get_auth_user_id();
        if ($uid <= 0) {
            json_out(false, 'กรุณาเข้าสู่ระบบก่อนทำการสั่งซื้อ');
        }

        $p_id = intval($params['product_id'] ?? 0);
        $quantity = max(1, intval($params['quantity'] ?? 1));

        if ($p_id <= 0) {
            json_out(false, 'กรุณาระบุสินค้าที่ต้องการซื้อ');
        }

        try {
            $conn->beginTransaction();

            // 1. Fetch User Points
            $u_stmt = $conn->prepare("SELECT id, username, point FROM users WHERE id = ? FOR UPDATE");
            $u_stmt->execute([$uid]);
            $user_row = $u_stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user_row) {
                $conn->rollBack();
                json_out(false, 'ไม่พบข้อมูลผู้ใช้งาน');
            }

            // 2. Fetch Product
            $p_stmt = $conn->prepare("SELECT * FROM box_product WHERE id = ? LIMIT 1");
            $p_stmt->execute([$p_id]);
            $prod = $p_stmt->fetch(PDO::FETCH_ASSOC);
            if (!$prod) {
                $conn->rollBack();
                json_out(false, 'ไม่พบสินค้านี้ในระบบ');
            }

            $unit_price = floatval($prod['price']);
            $total_cost = $unit_price * $quantity;
            $user_point = floatval($user_row['point']);

            if ($user_point < $total_cost) {
                $conn->rollBack();
                json_out(false, "ยอดเงินของคุณไม่เพียงพอ (ต้องการ ฿" . number_format($total_cost, 2) . " แต่มี ฿" . number_format($user_point, 2) . ")");
            }

            // 3. Check Stock
            $s_stmt = $conn->prepare("SELECT id, username, password FROM box_stock WHERE p_id = ? ORDER BY id ASC LIMIT ? FOR UPDATE");
            $s_stmt->bindValue(1, $p_id, PDO::PARAM_INT);
            $s_stmt->bindValue(2, $quantity, PDO::PARAM_INT);
            $s_stmt->execute();
            $stocks = $s_stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($stocks) < $quantity) {
                $conn->rollBack();
                json_out(false, "สินค้าในสต็อกมีไม่เพียงพอ (คงเหลือ " . count($stocks) . " ชิ้น)");
            }

            // 4. Deduct User Points
            $new_point = $user_point - $total_cost;
            $ded_stmt = $conn->prepare("UPDATE users SET point = point - ? WHERE id = ?");
            $ded_stmt->execute([$total_cost, $uid]);

            // 5. Deliver stock and remove from stock table
            $delivered_items = [];
            $stock_ids = [];
            foreach ($stocks as $item) {
                $stock_ids[] = $item['id'];
                $delivery_payload = !empty($item['password']) ? ($item['username'] . ' | ' . $item['password']) : $item['username'];
                $delivered_items[] = [
                    'username' => $item['username'],
                    'password' => $item['password'],
                    'delivered_data' => $delivery_payload
                ];

                // Log into boxlog
                $log_stmt = $conn->prepare("INSERT INTO boxlog (date, username, category, price, prize_name, uid) VALUES (NOW(), ?, ?, ?, ?, ?)");
                $log_stmt->execute([$user_row['username'], $prod['c_type'] ?? 'สินค้า', $unit_price, $prod['name'] . ' -> ' . $delivery_payload, $uid]);
            }

            // Delete stock items
            $in_clause = implode(',', array_fill(0, count($stock_ids), '?'));
            $del_stock = $conn->prepare("DELETE FROM box_stock WHERE id IN ($in_clause)");
            $del_stock->execute($stock_ids);

            // Update stats
            try {
                $conn->exec("UPDATE static SET sold_count = sold_count + $quantity, last_change = NOW()");
            } catch(Exception $e_st) {}

            $conn->commit();

            json_out(true, "สั่งซื้อ {$prod['name']} สำเร็จเรียบร้อยแล้ว!", [
                'product_name'    => $prod['name'],
                'quantity'        => $quantity,
                'total_cost'      => $total_cost,
                'remaining_point' => $new_point,
                'delivered_items' => $delivered_items
            ]);

        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            json_out(false, 'เกิดข้อผิดพลาดในการสั่งซื้อ: ' . $e->getMessage());
        }
        break;

    // ==========================================
    // 📜 10. ORDER HISTORY
    // ==========================================
    case 'get_order_history':
        $uid = get_auth_user_id();
        if ($uid <= 0) {
            json_out(false, 'กรุณาเข้าสู่ระบบก่อน');
        }

        try {
            $stmt = $conn->prepare("SELECT id, date, prize_name, price, category FROM boxlog WHERE uid = ? ORDER BY id DESC LIMIT 50");
            $stmt->execute([$uid]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_out(true, 'ดึงประวัติการสั่งซื้อสำเร็จ', $orders);
        } catch (Exception $e) {
            json_out(false, 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
        break;

    // ==========================================
    // 💳 11. SLIP TOPUP (SlipOK Integration)
    // ==========================================
    case 'topup_slip':
        $uid = get_auth_user_id();
        if ($uid <= 0) {
            json_out(false, 'กรุณาเข้าสู่ระบบก่อนเติมเงิน');
        }

        $qrcode_data = trim($params['qrcode_data'] ?? '');
        $slip_image  = $_FILES['slip_image'] ?? null;

        // Fetch slip configuration
        $set_stmt = $conn->query("SELECT slip_api_key, slip_api_branch, slip_api_type FROM setting LIMIT 1");
        $set = $set_stmt ? $set_stmt->fetch(PDO::FETCH_ASSOC) : [];
        $slip_key = trim($set['slip_api_key'] ?? '');
        $slip_branch = trim($set['slip_api_branch'] ?? '');

        if (empty($slip_key)) {
            json_out(false, 'ร้านค้านี้ยังไม่ได้ตั้งค่า SlipOK API กรุณาติดต่อแอดมิน');
        }

        $curl_target = "https://api.slipok.com/api/line/apikey/" . (!empty($slip_branch) ? $slip_branch : $slip_key);
        $ch = curl_init($curl_target);

        if (!empty($qrcode_data)) {
            $post_payload = json_encode(['data' => $qrcode_data]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'x-authorization: ' . $slip_key
            ]);
        } elseif ($slip_image && $slip_image['error'] === UPLOAD_ERR_OK) {
            $cfile = new CURLFile($slip_image['tmp_name'], $slip_image['type'], $slip_image['name']);
            $post_payload = ['files' => $cfile];
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'x-authorization: ' . $slip_key
            ]);
        } else {
            json_out(false, 'กรุณาแนบรูปสลิปหรือส่งข้อมูล QR Code');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $res_json = json_decode($result, true);

        if ($http_code === 200 && isset($res_json['success']) && $res_json['success']) {
            $amount = floatval($res_json['data']['amount'] ?? 0);
            $trans_ref = $res_json['data']['transRef'] ?? ('SLIP_' . time());

            if ($amount <= 0) {
                json_out(false, 'ยอดเงินในสลิปไม่ถูกต้อง');
            }

            // Check duplicate slip
            try {
                $chk_dup = $conn->prepare("SELECT id FROM topup_his WHERE link = ? LIMIT 1");
                $chk_dup->execute([$trans_ref]);
                if ($chk_dup->fetch()) {
                    json_out(false, 'สลิปนี้ถูกใช้งานไปแล้ว');
                }
            } catch(Exception $e_dup) {}

            // Credit points
            $upd = $conn->prepare("UPDATE users SET point = point + ?, total = total + ?, total_topup = total_topup + ? WHERE id = ?");
            $upd->execute([$amount, $amount, $amount, $uid]);

            // Save history
            $uname_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
            $uname_stmt->execute([$uid]);
            $uname = $uname_stmt->fetchColumn() ?: 'User';

            $ins = $conn->prepare("INSERT INTO topup_his (link, amount, date, uid, uname) VALUES (?, ?, NOW(), ?, ?)");
            $ins->execute([$trans_ref, $amount, $uid, $uname]);

            json_out(true, "เติมเงินสำเร็จเรียบร้อยแล้ว จำนวน ฿" . number_format($amount, 2), [
                'amount' => $amount,
                'trans_ref' => $trans_ref
            ]);
        } else {
            $msg = $res_json['message'] ?? 'ตรวจสอบสลิปไม่ผ่าน หรือสลิปไม่ถูกต้อง';
            json_out(false, $msg);
        }
        break;

    // ==========================================
    // 🎁 12. REDEEM CODE
    // ==========================================
    case 'redeem_code':
        $uid = get_auth_user_id();
        if ($uid <= 0) {
            json_out(false, 'กรุณาเข้าสู่ระบบก่อนใช้งานโค้ด');
        }

        $code = trim($params['code'] ?? '');
        if (empty($code)) {
            json_out(false, 'กรุณากรอกโค้ดของขวัญ');
        }

        try {
            $stmt = $conn->prepare("SELECT * FROM redeem WHERE code = ? LIMIT 1");
            $stmt->execute([$code]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$r) {
                json_out(false, 'ไม่พบโค้ดของขวัญนี้ หรือโค้ดหมดอายุแล้ว');
            }
            if ($r['count'] >= $r['max_count']) {
                json_out(false, 'โค้ดนี้ถูกใช้งานครบจำนวนสิทธิ์แล้ว');
            }

            // Check if user already used this code
            $chk_used = $conn->prepare("SELECT id FROM redeem_his WHERE uid = ? AND code = ? LIMIT 1");
            $chk_used->execute([$uid, $code]);
            if ($chk_used->fetch()) {
                json_out(false, 'คุณเคยใช้งานโค้ดนี้ไปแล้ว');
            }

            $prize = floatval($r['prize']);
            // Add point
            $conn->prepare("UPDATE users SET point = point + ? WHERE id = ?")->execute([$prize, $uid]);
            // Increment count
            $conn->prepare("UPDATE redeem SET count = count + 1 WHERE id = ?")->execute([$r['id']]);
            // Log history
            $conn->prepare("INSERT INTO redeem_his (uid, code, date) VALUES (?, ?, NOW())")->execute([$uid, $code]);

            json_out(true, "แลกโค้ดสำเร็จ! คุณได้รับเงินรางวัล ฿" . number_format($prize, 2), [
                'reward' => $prize
            ]);
        } catch (Exception $e) {
            json_out(false, 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
        break;

    // ==========================================
    // 🌐 13. RENTAL: GET PACKAGES
    // ==========================================
    case 'get_rental_packages':
        try {
            $stmt = $conn->query("SELECT * FROM web_rental_packages WHERE status = 'active' ORDER BY id ASC");
            $pkgs = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            foreach ($pkgs as &$p) {
                $p['price'] = floatval($p['price']);
                $p['duration_days'] = intval($p['duration_days']);
                $p['template_type'] = $p['template_type'] ?? 'php_classic';
                $features = array_filter(array_map('trim', explode("\n", $p['description'] ?? '')));
                $p['features'] = array_values(array_map(function($f) {
                    return ltrim($f, '•- *');
                }, $features));
            }
            json_out(true, 'ดึงรายการแพ็กเกจเช่าเว็บไซต์สำเร็จ', $pkgs);
        } catch (Exception $e) {
            json_out(false, 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
        break;

    // ==========================================
    // 🚀 14. RENTAL: RENT PACKAGE (Direct API)
    // ==========================================
    case 'rent_package':
        $uid = get_auth_user_id();
        if ($uid <= 0) {
            json_out(false, 'กรุณาเข้าสู่ระบบก่อนสั่งเช่าเว็บไซต์');
        }

        $package_id = intval($params['package_id'] ?? 0);
        $shop_name = strtolower(trim($params['shop_name'] ?? ''));
        $shop_display_name = trim($params['shop_display_name'] ?? $shop_name);

        if ($package_id <= 0) {
            json_out(false, 'กรุณาเลือกแพ็กเกจที่ต้องการเช่า');
        }
        if (empty($shop_name)) {
            json_out(false, 'กรุณากรอกชื่อร้านค้าภาษาอังกฤษ (Subdomain)');
        }
        if (!preg_match('/^[a-z0-9_-]{3,30}$/', $shop_name)) {
            json_out(false, 'ชื่อร้านค้าต้องเป็นตัวอักษรภาษาอังกฤษตัวพิมพ์เล็ก ตัวเลข 3-30 ตัวอักษร');
        }

        // Set session for rent_website.php engine
        $_SESSION['id'] = $uid;
        $_SESSION['username'] = $params['username'] ?? ($_SESSION['username'] ?? 'user');
        $_POST['package_id'] = $package_id;
        $_POST['shop_name'] = $shop_name;
        $_POST['shop_display_name'] = $shop_display_name;

        // Run provisioning engine
        ob_start();
        require dirname(__DIR__) . '/system/rent_website.php';
        $out = ob_get_clean();

        $resp = json_decode($out, true);
        if ($resp && isset($resp['status']) && $resp['status'] === 'success') {
            json_out(true, $resp['message'] ?? 'เช่าเว็บไซต์สำเร็จเรียบร้อยแล้ว!', $resp['data'] ?? $resp);
        } else {
            $msg = $resp['message'] ?? 'เกิดข้อผิดพลาดในการสั่งเช่าเว็บไซต์';
            json_out(false, $msg);
        }
        break;

    // ==========================================
    // 🏬 15. RENTAL: GET USER RENTALS
    // ==========================================
    case 'get_my_rentals':
        $uid = get_auth_user_id();
        if ($uid <= 0) {
            json_out(false, 'กรุณาเข้าสู่ระบบก่อนดูรายการร้านค้า');
        }

        try {
            $stmt = $conn->prepare("SELECT r.*, p.name AS pkg_name, p.template_type AS pkg_template_type 
                                    FROM rented_websites r 
                                    LEFT JOIN web_rental_packages p ON r.package_id = p.id 
                                    WHERE r.user_id = ? 
                                    ORDER BY r.id DESC");
            $stmt->execute([$uid]);
            $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $formatted = [];
            foreach ($rentals as $r) {
                $shop_slug = !empty($r['shop_name']) ? $r['shop_name'] : ($r['subdomain'] ?? 'store_'.$r['id']);
                $store_url = !empty($r['store_url']) ? $r['store_url'] : (SYSTEM_PROTOCOL . $shop_slug . "." . SYSTEM_DOMAIN . "/");
                $admin_pass = $r['admin_password'] ?? 'admin';
                $store_admin_url = $store_url . "?page=autologin&token=" . urlencode($admin_pass);

                $end_ts = !empty($r['end_date']) ? strtotime($r['end_date']) : strtotime('+30 days', strtotime($r['created_at']));
                $now = time();
                $diff_sec = max(0, $end_ts - $now);

                $formatted[] = [
                    'id'                => intval($r['id']),
                    'shop_name'         => $shop_slug,
                    'subdomain'         => $r['subdomain'] ?? $shop_slug,
                    'store_url'         => $store_url,
                    'store_admin_url'   => $store_admin_url,
                    'admin_username'    => $r['admin_username'] ?? 'admin',
                    'admin_password'    => $admin_pass,
                    'template_type'     => $r['template_type'] ?? ($r['pkg_template_type'] ?? 'php_classic'),
                    'package_name'      => $r['pkg_name'] ?? 'Custom Store',
                    'is_expired'        => ($end_ts < $now),
                    'seconds_remaining' => $diff_sec,
                    'days_remaining'    => floor($diff_sec / 86400),
                    'end_date'          => $r['end_date'],
                    'created_at'        => $r['created_at'],
                    'wheel_unlocked'    => !empty($r['wheel_unlocked']),
                    'backup_unlocked'   => !empty($r['backup_unlocked']),
                    'status'            => $r['status'] ?? 'active'
                ];
            }

            json_out(true, 'ดึงข้อมูลร้านค้าที่เช่าสำเร็จ', $formatted);
        } catch (Exception $e) {
            json_out(false, 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
        break;

    default:
        json_out(false, 'Action not recognized or missing action parameter.', [
            'available_actions' => [
                'get_store_info', 'get_categories', 'get_products', 'get_product_detail',
                'login', 'register', 'logout', 'get_user_profile',
                'buy_product', 'get_order_history', 'topup_slip', 'redeem_code',
                'get_rental_packages', 'rent_package', 'get_my_rentals'
            ]
        ]);
        break;
}
