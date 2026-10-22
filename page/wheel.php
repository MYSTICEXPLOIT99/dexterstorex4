<?php
// Wheel Spin Page (Dynamic Database Integration with Upright Physics & Blank Wheel Support)

$selected_wheel_id = intval($_GET['id'] ?? 0);
if ($selected_wheel_id > 0) {
    $wheel_q = dd_q("SELECT * FROM wheels WHERE id = ? LIMIT 1", [$selected_wheel_id]);
} else {
    $wheel_q = dd_q("SELECT * FROM wheels WHERE status = 'on' ORDER BY id ASC LIMIT 1");
    if (!$wheel_q || $wheel_q->rowCount() == 0) {
        $wheel_q = dd_q("SELECT * FROM wheels ORDER BY id ASC LIMIT 1");
    }
}
$cur_wheel = ($wheel_q && $wheel_q->rowCount() > 0) ? $wheel_q->fetch(PDO::FETCH_ASSOC) : null;

$spin_price = $cur_wheel ? floatval($cur_wheel['spin_price']) : 20.00;
$wheel_title = $cur_wheel ? $cur_wheel['title'] : 'กล่องสุ่ม & วงล้อเสี่ยงโชค';
$wheel_des = $cur_wheel ? ($cur_wheel['des'] ?: 'หมุนกงล้อลุ้นของรางวัลสุดคุ้ม') : 'หมุนกงล้อลุ้นของรางวัล';

$user_points = 0;
if (isset($_SESSION['id'])) {
    $u_res = dd_q("SELECT point FROM users WHERE id = ?", [$_SESSION['id']]);
    if ($u_res && $u_res->rowCount() > 0) {
        $user_points = floatval($u_res->fetch(PDO::FETCH_ASSOC)['point'] ?? 0);
    }
}

$wheel_items = [];
if ($cur_wheel) {
    $items_q = dd_q("SELECT * FROM wheel_item WHERE wheel_id = ? ORDER BY sort_order ASC, id ASC", [$cur_wheel['id']]);
    if ($items_q && $items_q->rowCount() > 0) {
        while ($r = $items_q->fetch(PDO::FETCH_ASSOC)) {
            $wheel_items[] = [
                'id' => intval($r['id']),
                'label' => $r['name'],
                'type' => $r['reward_type'],
                'reward_value' => $r['reward_value'],
                'percent' => intval($r['percent']),
                'color' => !empty($r['color']) ? $r['color'] : '#00c9ff',
                'img' => !empty($r['img']) ? $r['img'] : 'assets/icon/box.png'
            ];
        }
    }
}

// Support Blank Wheel Mode
$is_blank_mode = empty($wheel_items);
if ($is_blank_mode) {
    for ($i = 1; $i <= 8; $i++) {
        $wheel_items[] = [
            'id' => 0,
            'label' => '???',
            'type' => 'salt',
            'reward_value' => '0',
            'percent' => 0,
            'color' => ($i % 2 === 0) ? '#38bdf8' : '#818cf8',
            'img' => 'assets/icon/box.png'
        ];
    }
}
?>

<div class="wheel-page-container text-center py-4" data-aos="fade-up">
    <!-- Header -->
    <h1 class="wheel-main-title"><?= htmlspecialchars($wheel_title) ?></h1>
    <p class="wheel-subtitle"><?= htmlspecialchars($wheel_des) ?></p>

    <!-- Top Action Pills -->
    <div class="wheel-action-pills d-flex justify-content-center align-items-center gap-2 mb-4 flex-wrap">
        <?php if ($is_blank_mode): ?>
            <div class="wheel-pill-badge" style="color:#f59e0b;"><i class="fa-solid fa-hourglass-half me-1"></i> วงล้อเปล่า (เร็วๆ นี้)</div>
        <?php else: ?>
            <div class="wheel-pill-badge">ราคาหมุน ฿<?= number_format($spin_price, 0) ?></div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['id'])): ?>
            <a href="javascript:void(0)" onclick="openAuthModal('login')" class="wheel-pill-badge text-decoration-none" role="button">เข้าสู่ระบบเพื่อหมุน</a>
        <?php else: ?>
            <div class="wheel-pill-badge" style="color:#60a5fa;"><i class="fa-solid fa-coins me-1"></i> ยอดเงินคงเหลือ: <?= number_format($user_points, 2) ?> ฿</div>
        <?php endif; ?>
    </div>

    <!-- Interactive Wheel Stage with Celestial FX -->
    <div class="wheel-stage-wrap">
        <!-- Celestial Background Glow Rings -->
        <div class="wheel-celestial-orbit-wrap">
            <div class="wheel-orbit-ring ring-1"></div>
            <div class="wheel-orbit-ring ring-2"></div>
            <div class="wheel-orbit-ring ring-3"></div>
            <span class="wheel-orbit-node node-1">✦</span>
            <span class="wheel-orbit-node node-2">★</span>
            <span class="wheel-orbit-node node-3">✦</span>
            <span class="wheel-orbit-node node-4">★</span>
        </div>

        <!-- Top Pointer Triangle -->
        <div class="wheel-pointer-arrow">
            <div class="wheel-pointer-core"></div>
        </div>

        <!-- Canvas Disc Container with Rotating Conic Light Beam -->
        <div class="wheel-disc-box">
            <canvas id="wheelCanvas" width="520" height="520" class="wheel-canvas-disc"></canvas>
            
            <!-- Center SPIN Hub -->
            <div class="wheel-center-spin-hub" id="spinHubBtn" role="button" onclick="startSpin()">
                <div class="spin-hub-glow"></div>
                <span><?= $is_blank_mode ? 'SOON' : 'SPIN' ?></span>
            </div>
        </div>
    </div>

    <!-- Spin Button Below -->
    <div class="mt-4 mb-5">
        <button type="button" class="btn-wheel-action-spin" id="spinBottomBtn" onclick="startSpin()" style="<?= $is_blank_mode ? 'opacity:0.85;' : '' ?>">
            <?= $is_blank_mode ? 'เร็วๆ นี้' : 'หมุนวงล้อ' ?>
        </button>
    </div>

    <!-- Bottom Prizes Stock Horizontal Row or Empty State Note -->
    <?php if ($is_blank_mode): ?>
        <div class="p-4 my-2 rounded-4 text-center mx-auto" style="background:rgba(255,255,255,0.02);border:1px dashed rgba(255,255,255,0.12);max-width:580px;">
            <i class="fa-solid fa-dharmachakra fs-3 text-warning mb-2 d-block opacity-75"></i>
            <div class="text-white fw-bold mb-1" style="font-family:var(--font-cyber);">วงล้อกำลังจัดเตรียมของรางวัล</div>
            <div class="text-muted small">แอดมินสามารถเข้าไปเพิ่มรายการของรางวัลและกำหนดโอกาสออกได้ที่ระบบหลังบ้าน</div>
        </div>
    <?php else: ?>
        <div class="wheel-prizes-horizontal-strip">
            <?php foreach ($wheel_items as $item): ?>
                <div class="wheel-strip-card">
                    <img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['label']) ?>" class="wheel-strip-thumb" onerror="this.src='assets/icon/box.png'">
                    <div class="wheel-strip-meta">
                        <div class="wheel-strip-title"><?= htmlspecialchars($item['label']) ?></div>
                        <div class="wheel-strip-stock">
                            <?php if ($item['type'] === 'point'): ?>
                                <span class="text-success">+<?= number_format(floatval($item['reward_value'])) ?> พอยท์</span>
                            <?php elseif ($item['type'] === 'product'): ?>
                                <span class="text-info"><?= htmlspecialchars($item['reward_value']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">โอกาส <?= $item['percent'] ?>%</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
(function() {
    const canvas = document.getElementById('wheelCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const items = <?= json_encode($wheel_items) ?>;
    const wheelId = <?= intval($cur_wheel['id'] ?? 1) ?>;
    const isBlankMode = <?= $is_blank_mode ? 'true' : 'false' ?>;
    const numSegments = items.length;
    const anglePerSegment = (2 * Math.PI) / numSegments;
    
    let currentAngle = 0;
    let isSpinning = false;
    let animFrameId = null;

    // Helper: Draw Rounded Rectangle on Canvas
    function roundRect(ctx, x, y, width, height, radius) {
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
    }

    // Preload item images
    const loadedImages = {};
    items.forEach((item, index) => {
        const img = new Image();
        img.crossOrigin = "anonymous";
        img.src = item.img;
        img.onload = () => {
            loadedImages[index] = img;
            if (!isSpinning) drawWheel(currentAngle, 0);
        };
        img.onerror = () => {
            if (!isSpinning) drawWheel(currentAngle, 0);
        };
    });

    function drawWheel(angle, sway = 0) {
        const W = canvas.width;
        const H = canvas.height;
        const centerX = W / 2;
        const centerY = H / 2;
        const radius = W / 2 - 6;
        ctx.clearRect(0, 0, W, H);

        // 1. Draw Outer Dark Metallic Base Disc
        ctx.save();
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
        ctx.fillStyle = '#0a0b12';
        ctx.fill();
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.16)';
        ctx.lineWidth = 3.5;
        ctx.stroke();
        ctx.restore();

        // 2. Draw Rotating Sectors
        ctx.save();
        ctx.translate(centerX, centerY);
        ctx.rotate(angle);

        const baseOffset = -Math.PI / 2 - (anglePerSegment / 2);
        for (let i = 0; i < numSegments; i++) {
            const startAngle = baseOffset + i * anglePerSegment;
            const endAngle = startAngle + anglePerSegment;

            ctx.save();
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.arc(0, 0, radius - 2, startAngle, endAngle);
            ctx.closePath();

            // Custom Segment Color or Alternating Background
            ctx.fillStyle = (i % 2 === 0) ? '#13151f' : '#181b28';
            ctx.fill();

            // Spoke Divider Line
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.12)';
            ctx.lineWidth = 1.8;
            ctx.stroke();
            ctx.restore();
        }
        ctx.restore();

        // 3. Draw Hanging Upright Cards
        const cardDist = numSegments > 6 ? 170 : 160;
        const cardW = numSegments > 8 ? 74 : (numSegments > 6 ? 82 : 90);
        const cardH = numSegments > 8 ? 74 : (numSegments > 6 ? 82 : 90);

        for (let i = 0; i < numSegments; i++) {
            const orbitAngle = angle + (-Math.PI / 2 + i * anglePerSegment);
            const cardCenterX = centerX + Math.cos(orbitAngle) * cardDist;
            const cardCenterY = centerY + Math.sin(orbitAngle) * cardDist;

            ctx.save();
            ctx.translate(cardCenterX, cardCenterY);

            if (sway !== 0) {
                ctx.rotate(sway);
            }

            // Card Shadow
            ctx.shadowColor = 'rgba(0, 0, 0, 0.9)';
            ctx.shadowBlur = 18;
            ctx.shadowOffsetY = 6;

            // Card Dark Background & Colored Border
            ctx.fillStyle = '#06070c';
            roundRect(ctx, -cardW / 2, -cardH / 2, cardW, cardH, 12);
            ctx.fill();

            ctx.shadowColor = 'transparent';
            ctx.strokeStyle = items[i].color || 'rgba(255, 255, 255, 0.2)';
            ctx.lineWidth = 1.8;
            ctx.stroke();

            // Card Product Image
            if (loadedImages[i]) {
                try {
                    const imgW = cardW - 20;
                    const imgH = cardH - 24;
                    const imgX = -imgW / 2;
                    const imgY = -cardH / 2 + 5;

                    ctx.save();
                    roundRect(ctx, imgX, imgY, imgW, imgH, 8);
                    ctx.clip();
                    ctx.drawImage(loadedImages[i], imgX, imgY, imgW, imgH);
                    ctx.restore();
                } catch(e) {}
            }

            // Card Bottom Label Tag Pill
            const labelText = items[i].label;
            const pillW = cardW - 12;
            const pillH = 15;
            const pillX = -pillW / 2;
            const pillY = cardH / 2 - 18;

            ctx.fillStyle = 'rgba(0, 0, 0, 0.92)';
            roundRect(ctx, pillX, pillY, pillW, pillH, 4);
            ctx.fill();

            ctx.strokeStyle = 'rgba(255, 255, 255, 0.12)';
            ctx.lineWidth = 0.8;
            ctx.stroke();

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 9px Prompt, Kanit, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(labelText, 0, pillY + pillH / 2);

            ctx.restore();
        }
    }

    drawWheel(0, 0);

    function easeOutQuart(x) {
        return 1 - Math.pow(1 - x, 4);
    }

    window.startSpin = function() {
        if (isSpinning) return;

        if (isBlankMode) {
            Swal.fire({
                icon: 'info',
                title: 'เร็วๆ นี้!',
                text: 'วงล้อยังไม่เปิดให้บริการ / กำลังจัดเตรียมของรางวัล',
                background: '#0a0a0f',
                color: '#fff',
                confirmButtonColor: '#4b5d78'
            });
            return;
        }

        <?php if (!isset($_SESSION['id'])): ?>
            if (typeof openAuthModal === 'function') {
                openAuthModal('login');
            } else {
                window.navTo ? window.navTo('login') : (location.href = '?page=login');
            }
            return;
        <?php endif; ?>

        const spinHub = document.getElementById('spinHubBtn');
        const spinBtn = document.getElementById('spinBottomBtn');
        if (spinHub) spinHub.style.pointerEvents = 'none';
        if (spinBtn) {
            spinBtn.setAttribute('disabled', 'disabled');
            spinBtn.innerText = 'กำลังหมุน...';
        }

        // Call live Backend Spin API
        $.ajax({
            type: 'POST',
            url: 'system/wheel_spin.php',
            data: { wheel_id: wheelId },
            dataType: 'json'
        }).done(function(res) {
            if (res.status !== 'success') {
                if (spinHub) spinHub.style.pointerEvents = 'auto';
                if (spinBtn) {
                    spinBtn.removeAttribute('disabled');
                    spinBtn.innerText = 'หมุนวงล้อ';
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'แจ้งเตือน',
                    text: res.message || 'เกิดข้อผิดพลาด',
                    background: '#0a0a0f',
                    color: '#fff',
                    confirmButtonColor: '#4b5d78'
                });
                return;
            }

            isSpinning = true;
            const prizeIndex = res.winning_index;
            const wonItem = res.item;
            const extraRounds = 5 + Math.floor(Math.random() * 3);

            const targetSectorAngle = -(prizeIndex * anglePerSegment);
            const currentMod = currentAngle % (2 * Math.PI);
            let diff = targetSectorAngle - currentMod;
            while (diff < 0) diff += (2 * Math.PI);
            
            const startAngle = currentAngle;
            const totalRotationDelta = (extraRounds * 2 * Math.PI) + diff;
            const spinDuration = 5200;
            const startTime = performance.now();

            let lastAngle = startAngle;

            function animateSpin(now) {
                const elapsed = now - startTime;
                const progress = Math.min(1, elapsed / spinDuration);
                const eased = easeOutQuart(progress);

                currentAngle = startAngle + totalRotationDelta * eased;
                
                const velocity = (currentAngle - lastAngle);
                lastAngle = currentAngle;

                const swayFrequency = 24;
                const swayIntensity = (1 - progress) * 0.14 * Math.min(1, velocity * 4);
                const sway = Math.sin(progress * swayFrequency) * swayIntensity;

                drawWheel(currentAngle, sway);

                if (progress < 1) {
                    animFrameId = requestAnimationFrame(animateSpin);
                } else {
                    drawWheel(currentAngle, 0);
                    isSpinning = false;

                    if (spinHub) spinHub.style.pointerEvents = 'auto';
                    if (spinBtn) {
                        spinBtn.removeAttribute('disabled');
                        spinBtn.innerText = 'หมุนวงล้อ';
                    }

                    if (wonItem.type === 'salt') {
                        Swal.fire({
                            icon: 'info',
                            title: 'เสียใจด้วยนะ!',
                            text: 'ไม่ได้รับรางวัลในรอบนี้ ไว้ลองใหม่นะ!',
                            background: '#0a0a0f',
                            color: '#fff',
                            confirmButtonColor: '#4b5d78'
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: '🎉 ยินดีด้วย! คุณได้รับของรางวัล',
                            html: `
                                <div class="text-center py-2">
                                    <img src="${wonItem.img}" style="width:84px;height:84px;object-fit:contain;border-radius:12px;margin-bottom:12px;filter:drop-shadow(0 0 18px rgba(96,165,250,0.5));">
                                    <h4 class="text-white fw-bold mb-2" style="font-family:var(--font-cyber);">${wonItem.label}</h4>
                                    <div class="p-2 rounded mb-2" style="background:#050609; border:1px solid rgba(106,128,160,0.4); font-family:monospace; color:#38bdf8; font-size:0.85rem; word-break:break-all;">
                                        ${wonItem.key}
                                    </div>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'ตกลง',
                            confirmButtonColor: '#38bdf8',
                            background: '#0a0a0f',
                            color: '#fff',
                            timer: 4500,
                            timerProgressBar: true
                        });
                    }
                }
            }

            animFrameId = requestAnimationFrame(animateSpin);

        }).fail(function() {
            if (spinHub) spinHub.style.pointerEvents = 'auto';
            if (spinBtn) {
                spinBtn.removeAttribute('disabled');
                spinBtn.innerText = 'หมุนวงล้อ';
            }
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้',
                background: '#0a0a0f',
                color: '#fff',
                confirmButtonColor: '#4b5d78'
            });
        });
    };
})();
</script>
