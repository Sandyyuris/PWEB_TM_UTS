// Load provinsi saat halaman pertama kali dimuat
    $(document).ready(function() {
        console.log('Loading provinsi...');
        loadProvinsi();
    });
    // AJAX: Load data provinsi dari JSON
    function loadProvinsi() {
        $.ajax({
            url: 'Public/get_provinsi.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Response provinsi:', response);
                if (response.success) {
                    let options = '<option value="">Pilih Provinsi</option>';
                    response.provinsi.forEach(provinsi => {
                        options += `<option value="${provinsi}">${provinsi}</option>`;
                    });
                    $('#provinsi').html(options);
                    console.log('Provinsi loaded successfully');
                } else {
                    console.error('Error from server:', response.error);
                    alert('Error: ' + response.error);
                    $('#provinsi').html('<option value="">Error: ' + response.error + '</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                alert('Gagal memuat data provinsi. Cek console untuk detail.');
                $('#provinsi').html('<option value="">Error loading provinsi</option>');
            }
        });
    }
    // AJAX CHAIN COMBO: Provinsi -> Kota (DARI FILE JSON)
    $('#provinsi').on('change', function() {
        const provinsi = $(this).val();
        const $kotaSelect = $('#kota');
        
        console.log('Provinsi dipilih:', provinsi);
        
        if (provinsi) {
            // AJAX Request ke server untuk mendapatkan data kota
            $kotaSelect.prop('disabled', true).html('<option value="">Loading...</option>');
            
            $.ajax({
                url: 'Public/get_kota.php',
                type: 'GET',
                data: { provinsi: provinsi },
                dataType: 'json',
                success: function(response) {
                    console.log('Response kota:', response);
                    if (response.success) {
                        let options = '<option value="">Pilih Kota</option>';
                        response.kota.forEach(kota => {
                            options += `<option value="${kota}">${kota}</option>`;
                        });
                        $kotaSelect.html(options).prop('disabled', false);
                    } else {
                        console.error('Error from server:', response.error);
                        alert('Error: ' + response.error);
                        $kotaSelect.html('<option value="">Error: ' + response.error + '</option>').prop('disabled', true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    alert('Gagal memuat data kota. Cek console untuk detail.');
                    $kotaSelect.html('<option value="">Error loading kota</option>').prop('disabled', true);
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
        // Save signature as base64
        document.getElementById('signatureData').value = canvas.toDataURL();
    });
    canvas.addEventListener('mouseleave', () => {
        isDrawing = false;
    });
    // Touch support untuk mobile
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
    