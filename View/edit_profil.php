<?php
if (!isset($user)) {
    header("Location: index.php?action=login");
    exit;
}

$hobiArr = explode(', ', $user['hobi'] ?? '');
$currentProvinsi = $user['nama_provinsi'] ?? '';
$currentKota = $user['nama_kota'] ?? '';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil | Cari Jodoh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #a1c4fd, #c2e5cf); /* Warna background yang berbeda untuk membedakan */
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
            background-color: #007bff; /* Warna biru untuk update */
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
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
        .profile-preview {
            max-width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #007bff;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <h3 class="text-center mb-4">Edit Profil Anda ✍️</h3>
            <p class="text-center text-muted mb-4">Perbarui data diri Anda</p>
            
            <form method="POST" action="index.php?action=update" enctype="multipart/form-data">
                
                <div class="text-center mb-4">
                    <img src="Public/<?= htmlspecialchars($user['foto_profil'] ?? 'uploads/profiles/default.png') ?>" 
                         alt="Foto Profil" 
                         class="profile-preview"
                         onerror="this.src='https://via.placeholder.com/150'">
                </div>

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap" value="<?= htmlspecialchars($user['nama_lengkap'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="laki" value="Laki-laki" 
                                <?= ($user['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="perempuan" value="Perempuan" 
                                <?= ($user['jenis_kelamin'] ?? '') === 'Perempuan' ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="perempuan">Perempuan</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="usia" class="form-label">Usia <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="usia" name="usia" placeholder="Masukkan usia" min="17" max="100" value="<?= htmlspecialchars($user['umur'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="provinsi" class="form-label">Provinsi <span class="text-danger">*</span></label>
                    <select class="form-select" id="provinsi" name="provinsi" required>
                        <option value="<?= htmlspecialchars($currentProvinsi) ?>"><?= $currentProvinsi ?: 'Pilih Provinsi' ?></option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="kota" class="form-label">Kota <span class="text-danger">*</span></label>
                    <select class="form-select" id="kota" name="kota" required>
                        <option value="<?= htmlspecialchars($currentKota) ?>"><?= $currentKota ?: 'Pilih Kota' ?></option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="pekerjaan" class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" placeholder="Contoh: Software Engineer" value="<?= htmlspecialchars($user['pekerjaan'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hobi <span class="text-danger">*</span></label>
                    <div class="row">
                        <div class="col-md-6">
                            <?php $hobiOptions1 = ['Membaca', 'Traveling', 'Olahraga']; ?>
                            <?php foreach ($hobiOptions1 as $hobiName): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="hobi[]" value="<?= $hobiName ?>" id="hobi<?= $hobiName ?>" 
                                        <?= in_array($hobiName, $hobiArr) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="hobi<?= $hobiName ?>"><?= $hobiName ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-md-6">
                            <?php $hobiOptions2 = ['Memasak', 'Musik', 'Gaming']; ?>
                            <?php foreach ($hobiOptions2 as $hobiName): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="hobi[]" value="<?= $hobiName ?>" id="hobi<?= $hobiName ?>" 
                                        <?= in_array($hobiName, $hobiArr) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="hobi<?= $hobiName ?>"><?= $hobiName ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="bio" class="form-label">Bio Singkat <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="bio" name="bio" rows="4" placeholder="Ceritakan tentang diri Anda, minat, dan pasangan yang Anda cari..." required><?= htmlspecialchars($user['catatan'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Foto Profil (Kosongkan jika tidak ingin diubah)</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <small class="text-muted">Format: JPG, PNG, max 2MB</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanda Tangan Digital (Wajib diisi ulang saat update) <span class="text-danger">*</span></label>
                    <div class="signature-container">
                        <canvas id="signatureCanvas" width="700" height="200"></canvas>
                        <div class="mt-2">
                            <button type="button" class="btn btn-secondary btn-sm" id="clearSignature">🗑️ Hapus Tanda Tangan</button>
                        </div>
                    </div>
                    <input type="hidden" id="signatureData" name="signature" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-4">Update Profil</button>
                <div class="text-center mt-2">
                    <a href="index.php?action=dashboard" class="btn btn-outline-secondary w-100">Batalkan Edit</a>
                </div>
            </form>

            <div class="text-center mt-3">
                <small><a href="index.php?action=logout">Logout</a></small>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const currentProvinsi = "<?= $currentProvinsi ?>";
        const currentKota = "<?= $currentKota ?>";
        
        $(document).ready(function() {
            loadProvinsi(currentProvinsi, currentKota);
        });

        function loadProvinsi(selectedProvinsi = '', selectedKota = '') {
            $.ajax({
                url: 'Public/get_provinsi.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let options = '<option value="">Pilih Provinsi</option>';
                        response.provinsi.forEach(provinsi => {
                            const isSelected = provinsi === selectedProvinsi ? 'selected' : '';
                            options += `<option value="${provinsi}" ${isSelected}>${provinsi}</option>`;
                        });
                        $('#provinsi').html(options);
                        
                        if (selectedProvinsi) {
                            loadKota(selectedProvinsi, selectedKota);
                        }
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
        function loadKota(provinsi, selectedKota = '') {
            const $kotaSelect = $('#kota');
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
                            const isSelected = kota === selectedKota ? 'selected' : '';
                            options += `<option value="${kota}" ${isSelected}>${kota}</option>`;
                        });
                        $kotaSelect.html(options).prop('disabled', false);
                    } else {
                        alert('Error: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Gagal memuat data kota');
                    $kotaSelect.html(`<option value="${selectedKota}" selected>${selectedKota || 'Error loading kota'}</option>`).prop('disabled', false);
                }
            });
        }

        $('#provinsi').on('change', function() {
            const provinsi = $(this).val();
            if (provinsi) {
                loadKota(provinsi);
            } else {
                $('#kota').html('<option value="">Pilih Provinsi Terlebih Dahulu</option>').prop('disabled', true);
            }
        });


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

        document.getElementById('clearSignature').addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('signatureData').value = '';
        });

        document.querySelector('form').addEventListener('submit', (e) => {
            const signature = document.getElementById('signatureData').value;
            if (!signature) {
                e.preventDefault();
                alert('Mohon isi tanda tangan digital!');
            }
            if ($('input[name="hobi[]"]:checked').length === 0) {
                 e.preventDefault();
                 alert('Mohon pilih minimal satu hobi!');
            }
        });
    </script>
</body>
</html>