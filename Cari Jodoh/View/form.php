<?php
// Session sudah di-start di controller, jadi tidak perlu session_start() lagi

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: index.php?action=login");
    exit;
}

$user = $_SESSION['user'];

// Cek apakah profil sudah lengkap (sudah isi nama_lengkap)
$isProfileComplete = !empty($user['nama_lengkap']);

// Jika profil sudah lengkap, redirect ke hasil
if ($isProfileComplete) {
    header("Location: index.php?action=hasil");
    exit;
}

// Jika profil belum lengkap, tampilkan form
if (!$isProfileComplete) {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil | Cari Jodoh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            min-height: 100vh;
            padding: 40px 0;
        }
        .form-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .btn-primary {
            background-color: #5A67D8;
            border: none;
        }
        .btn-primary:hover {
            background-color: #434190;
        }
        #signatureCanvas {
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: crosshair;
        }
        .signature-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <h3 class="text-center mb-4">Lengkapi Profil Anda 💑</h3>
            <p class="text-center text-muted mb-4">Isi data diri Anda untuk mulai mencari jodoh</p>
            
            <form method="POST" action="index.php?action=submit" enctype="multipart/form-data">
                <!-- INPUT TEXT -->
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                </div>

                <!-- RADIO BUTTON -->
                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="laki" value="Laki-laki" required>
                            <label class="form-check-label" for="laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="perempuan" value="Perempuan" required>
                            <label class="form-check-label" for="perempuan">Perempuan</label>
                        </div>
                    </div>
                </div>

                <!-- INPUT NUMBER -->
                <div class="mb-3">
                    <label for="usia" class="form-label">Usia <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="usia" name="usia" placeholder="Masukkan usia" min="17" max="100" required>
                </div>

                <!-- SELECT PROVINSI -->
                <div class="mb-3">
                    <label for="provinsi" class="form-label">Provinsi <span class="text-danger">*</span></label>
                    <select class="form-select" id="provinsi" name="provinsi" required>
                        <option value="">Loading provinsi...</option>
                    </select>
                </div>

                <!-- SELECT KOTA -->
                <div class="mb-3">
                    <label for="kota" class="form-label">Kota <span class="text-danger">*</span></label>
                    <select class="form-select" id="kota" name="kota" required disabled>
                        <option value="">Pilih Provinsi Terlebih Dahulu</option>
                    </select>
                </div>

                <!-- INPUT TEXT -->
                <div class="mb-3">
                    <label for="pekerjaan" class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" placeholder="Contoh: Software Engineer" required>
                </div>

                <!-- CHECKBOX -->
                <div class="mb-3">
                    <label class="form-label">Hobi <span class="text-danger">*</span></label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="hobi[]" value="Membaca" id="hobi1">
                                <label class="form-check-label" for="hobi1">Membaca</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="hobi[]" value="Traveling" id="hobi2">
                                <label class="form-check-label" for="hobi2">Traveling</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="hobi[]" value="Olahraga" id="hobi3">
                                <label class="form-check-label" for="hobi3">Olahraga</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="hobi[]" value="Memasak" id="hobi4">
                                <label class="form-check-label" for="hobi4">Memasak</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="hobi[]" value="Musik" id="hobi5">
                                <label class="form-check-label" for="hobi5">Musik</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="hobi[]" value="Gaming" id="hobi6">
                                <label class="form-check-label" for="hobi6">Gaming</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TEXTAREA -->
                <div class="mb-3">
                    <label for="bio" class="form-label">Bio Singkat <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="bio" name="bio" rows="4" placeholder="Ceritakan tentang diri Anda, minat, dan pasangan yang Anda cari..." required></textarea>
                </div>

                <!-- INPUT FILE -->
                <div class="mb-3">
                    <label for="image" class="form-label">Foto Profil <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                    <small class="text-muted">Format: JPG, PNG, max 2MB</small>
                </div>

                <!-- CANVAS TANDA TANGAN -->
                <div class="mb-3">
                    <label class="form-label">Tanda Tangan Digital <span class="text-danger">*</span></label>
                    <div class="signature-container">
                        <canvas id="signatureCanvas" width="700" height="200"></canvas>
                        <div class="mt-2">
                            <button type="button" class="btn btn-secondary btn-sm" id="clearSignature">🗑️ Hapus Tanda Tangan</button>
                        </div>
                    </div>
                    <input type="hidden" id="signatureData" name="signature" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-4">Simpan Profil 💖</button>
            </form>

            <div class="text-center mt-3">
                <small><a href="index.php?action=logout">Logout</a></small>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load provinsi saat halaman dimuat
        $(document).ready(function() {
            loadProvinsi();
        });

        // AJAX: Load data provinsi
        function loadProvinsi() {
            $.ajax({
                url: 'Public/get_provinsi.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let options = '<option value="">Pilih Provinsi</option>';
                        response.provinsi.forEach(provinsi => {
                            options += `<option value="${provinsi}">${provinsi}</option>`;
                        });
                        $('#provinsi').html(options);
                    } else {
                        alert('Error: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Gagal memuat data provinsi');
                }
            });
        }

        // AJAX: Load kota berdasarkan provinsi
        $('#provinsi').on('change', function() {
            const provinsi = $(this).val();
            const $kotaSelect = $('#kota');

            if (provinsi) {
                $kotaSelect.prop('disabled', true).html('<option value="">Loading...</option>');
                
                $.ajax({
                    url: 'Public/get_kota.php',
                    type: 'GET',
                    data: { provinsi: provinsi },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let options = '<option value="">Pilih Kota</option>';
                            response.kota.forEach(kota => {
                                options += `<option value="${kota}">${kota}</option>`;
                            });
                            $kotaSelect.html(options).prop('disabled', false);
                        } else {
                            alert('Error: ' + response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        alert('Gagal memuat data kota');
                    }
                });
            } else {
                $kotaSelect.html('<option value="">Pilih Provinsi Terlebih Dahulu</option>').prop('disabled', true);
            }
        });

        // CANVAS TANDA TANGAN
        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';

        canvas.addEventListener('mousedown', (e) => {
            isDrawing = true;
            [lastX, lastY] = [e.offsetX, e.offsetY];
        });

        canvas.addEventListener('mousemove', (e) => {
            if (!isDrawing) return;
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(e.offsetX, e.offsetY);
            ctx.stroke();
            [lastX, lastY] = [e.offsetX, e.offsetY];
        });

        canvas.addEventListener('mouseup', () => {
            isDrawing = false;
            document.getElementById('signatureData').value = canvas.toDataURL();
        });

        canvas.addEventListener('mouseleave', () => {
            isDrawing = false;
        });

        // Touch support
        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            isDrawing = true;
            const rect = canvas.getBoundingClientRect();
            [lastX, lastY] = [e.touches[0].clientX - rect.left, e.touches[0].clientY - rect.top];
        });

        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (!isDrawing) return;
            const rect = canvas.getBoundingClientRect();
            const x = e.touches[0].clientX - rect.left;
            const y = e.touches[0].clientY - rect.top;
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(x, y);
            ctx.stroke();
            [lastX, lastY] = [x, y];
        });

        canvas.addEventListener('touchend', () => {
            isDrawing = false;
            document.getElementById('signatureData').value = canvas.toDataURL();
        });

        // Clear signature
        document.getElementById('clearSignature').addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('signatureData').value = '';
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', (e) => {
            const signature = document.getElementById('signatureData').value;
            if (!signature) {
                e.preventDefault();
                alert('Mohon isi tanda tangan digital!');
            }
        });
    </script>
</body>
</html>
<?php
}