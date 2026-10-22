<?php
$setting = dd_q("SELECT * FROM setting LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$truemoney_phone = $setting['wallet'] ?? '';
?>

<div class="container-sm mt-4">
    <div class="xez-card p-4">
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h4 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-wallet text-warning me-2"></i> TrueMoney Wallet Transfer</h4>
                <span class="badge bg-warning-soft text-warning border border-warning rounded-pill px-3"><i class="fa-solid fa-shield-check me-1"></i> SlipOK Verified</span>
            </div>
            <p class="text-warning small fw-bold mb-0"># ระบบตรวจสอบสลิป TrueMoney อัตโนมัติผ่าน SlipOK API</p>
            <p class="text-muted x-small">โอนเงินเข้า Wallet ร้านค้าแล้วแนบสลิปด้านล่าง ระบบจะเติมเงินให้ทันที</p>
        </div>

        <hr class="border-secondary opacity-10 mb-4">

        <div class="col-lg-12">
            <!-- TrueMoney Info Card -->
            <div class="card border-0 bg-light rounded-4 p-4 mb-4 text-center">
                <div class="mb-3">
                    <img src="https://img2.pic.in.th/download7a68ea330c321b38.png"
                        class="img-fluid rounded-4 shadow-sm p-2 bg-white" style="width: 70px; height: 70px; object-fit: contain;">
                </div>
                <h5 class="fw-bold text-dark mb-1">โอนเงินเข้าบัญชี TrueMoney Wallet</h5>
                <h4 class="fw-bold text-warning mb-3" id="walletNumber">
                    <?= htmlspecialchars($truemoney_phone) ?>
                </h4>
                
                <button class="btn btn-warning text-white fw-bold btn-sm rounded-pill px-4 py-2" onclick="copyWalletNumber()">
                    <i class="fa-solid fa-copy me-2"></i> คัดลอกเบอร์วอลเล็ต
                </button>
            </div>

            <!-- Upload Zone -->
            <label for="slipInput" class="upload-zone border-dashed rounded-4 p-5 text-center mb-4 d-block w-100" id="dropZone" style="cursor:pointer;">
                <div id="uploadPlaceholder">
                    <i class="fa-solid fa-cloud-arrow-up text-warning display-4 mb-3"></i>
                    <h5 class="fw-bold text-dark">แนบไฟล์สลิปการโอน TrueMoney ที่นี่</h5>
                    <p class="text-muted small">รองรับไฟล์ภาพ JPG, PNG (สลิป TrueMoney Wallet แท้เท่านั้น)</p>
                </div>
                <div id="previewZone" class="d-none">
                    <img id="slipPreview" class="img-fluid rounded-3 shadow-sm mb-3" style="max-height: 300px;">
                    <p class="text-muted small">คลิกเพื่อเปลี่ยนรูปสลิป</p>
                </div>
                <input type="file" id="slipInput" name="slipInput" accept="image/*" style="display:none;">
            </label>

            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <button class="btn btn-warning text-white w-100 py-3 fw-bold rounded-3 fs-5 shadow-sm d-none" id="submitSlipBtn">
                        <i class="fa-solid fa-paper-plane me-2"></i> ตรวจสอบสลิปและเติมเงิน
                    </button>
                </div>
                <div class="col-12 col-md-6 ms-auto">
                    <a href="?page=topup" class="btn btn-secondary w-100 py-3 fw-bold rounded-3 fs-5 shadow-sm" style="border-radius: 10px;">
                        <i class="fa-solid fa-xmark-circle me-2"></i> ย้อนกลับ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedFile = null;

    function copyWalletNumber() {
        var num = $("#walletNumber").text().trim();
        navigator.clipboard.writeText(num);
        Swal.fire({ icon: 'success', title: 'คัดลอกเบอร์วอลเล็ตแล้ว', timer: 1000, showConfirmButton: false });
    }

    $("#slipInput").change(function (e) {
        if (!e.target.files.length) return;
        selectedFile = e.target.files[0];
        const imageUrl = URL.createObjectURL(selectedFile);

        $("#slipPreview").attr("src", imageUrl);
        $("#uploadPlaceholder").addClass("d-none");
        $("#previewZone").removeClass("d-none");
        $("#submitSlipBtn").removeClass("d-none");
    });

    // Drag and Drop
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('slipInput');

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('bg-light-soft');
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('bg-light-soft');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('bg-light-soft');

        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            $("#slipInput").trigger("change");
        }
    });

    $("#submitSlipBtn").click(function () {
        if (!selectedFile) {
            Swal.fire({ icon: 'warning', title: 'กรุณาแนบสลิป', text: 'เลือกรูปภาพสลิปโอนเงินก่อนกดตรวจสอบ' });
            return;
        }

        Swal.fire({
            title: 'กำลังตรวจสอบสลิปผ่าน SlipOK...',
            text: 'กรุณารอสักครู่ ระบบกำลังสื่อสารกับระบบธนาคาร',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading() }
        });

        const formData = new FormData();
        formData.append('slip_image', selectedFile);

        $.ajax({
            type: 'POST',
            url: 'system/slip.php',
            data: formData,
            contentType: false,
            processData: false,
        }).done(function (res) {
            if (res.status == 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'เติมเงินสำเร็จ!',
                    text: res.message
                }).then(() => window.navTo ? window.navTo('') : (location.href = ''));
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ตรวจสอบสลิปไม่ผ่าน',
                    text: res.message
                });
            }
        }).fail(function (jqXHR) {
            let res = jqXHR.responseJSON;
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: res ? res.message : 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ตรวจสอบสลิปได้'
            });
        });
    });
</script>

<style>
    .cursor-pointer {
        cursor: pointer;
    }

    .upload-zone {
        transition: all 0.2s ease-in-out;
        background: rgba(255, 193, 7, 0.02);
    }

    .upload-zone:hover {
        background: rgba(255, 193, 7, 0.05);
        border-color: #ffc107 !important;
    }

    .bg-light-soft {
        background-color: rgba(255, 193, 7, 0.05) !important;
    }

    .border-dashed {
        border: 2px dashed #dee2e6 !important;
    }
    
    .bg-warning-soft {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
</style>
