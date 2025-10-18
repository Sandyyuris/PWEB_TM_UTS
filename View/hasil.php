<?php
if (!isset($_SESSION['user'])) {
    header("Location: index.php?action=login");
    exit;
}

$user = $_SESSION['user'];

$database = new Database();
$conn = $database->getConnection();
$hasilModel = new HasilModel();
$stats = $hasilModel->getDashboardStats($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cari Jodoh | Hasil</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .profile-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            padding: 30px;
            margin-bottom: 30px;
            color: white;
        }
        
        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .profile-info h3 {
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .profile-info p {
            margin: 5px 0;
            font-size: 0.95rem;
        }
        
        .badge-custom {
            padding: 8px 15px;
            font-size: 0.9rem;
            border-radius: 20px;
        }
        
        .stats-row {
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .table-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .table-card h3 {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 25px;
        }
        
        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0;
        }
        
        #userTable tbody tr {
            transition: all 0.3s ease;
        }
        
        #userTable tbody tr:hover {
            background-color: #f0f0ff !important;
            cursor: pointer;
            transform: scale(1.01);
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.2);
        }
        
        #userTable tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
        
        #userTable tbody tr:nth-child(even) {
            background-color: #ffffff;
        }
        
        #userTable.table-sm td,
        #userTable.table-sm th {
            padding: 8px 12px;
            font-size: 0.9rem;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border: none;
            padding: 15px 10px;
        }
        
        .user-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #667eea;
            transition: transform 0.3s ease;
        }
        
        .user-photo:hover {
            transform: scale(1.3);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .badge-gender {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .btn-action {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .dt-buttons {
            margin-bottom: 20px;
        }
        
        .dt-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 10px !important;
            padding: 10px 20px !important;
            margin-right: 10px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }
        
        .dt-button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4) !important;
        }
        
        @media (max-width: 768px) {
            .profile-card {
                text-align: center;
            }
            
            .stat-card {
                margin-bottom: 15px;
            }
        }
        
        .spinner-border-custom {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        
        <div class="profile-card">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <img src="Public/<?= htmlspecialchars($user['foto_profil']) ?>" 
                        alt="<?= htmlspecialchars($user['nama_lengkap']) ?>" 
                        class="profile-image"
                        onerror="this.src='https://via.placeholder.com/120'">
                </div>
                <div class="col-md-8">
                    <div class="profile-info">
                        <h3>
                            <i class="fas fa-user-circle"></i> 
                            <?= htmlspecialchars($user['nama_lengkap']) ?>
                            <span class="badge bg-light text-dark badge-custom">
                                <?= htmlspecialchars($user['jenis_kelamin']) ?>
                            </span>
                        </h3>
                        <p><i class="fas fa-birthday-cake"></i> <strong>Usia:</strong> <?= htmlspecialchars($user['umur']) ?> tahun</p>
                        <p><i class="fas fa-map-marker-alt"></i> <strong>Lokasi:</strong> <?= htmlspecialchars($user['nama_kota'] ?? 'N/A') ?>, <?= htmlspecialchars($user['nama_provinsi'] ?? 'N/A') ?></p>
                        <p><i class="fas fa-briefcase"></i> <strong>Pekerjaan:</strong> <?= htmlspecialchars($user['pekerjaan']) ?></p>
                        <p><i class="fas fa-heart"></i> <strong>Hobi:</strong> <?= htmlspecialchars($user['hobi']) ?></p>
                    </div>
                </div>
                <div class="col-md-2 text-center">
                    <a href="index.php?action=edit_profil" class="btn btn-light btn-sm mb-2 w-100">
                        <i class="fas fa-edit"></i> Edit Profil
                    </a>
                    <a href="index.php?action=logout" class="btn btn-danger btn-sm w-100">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>


        <div class="row stats-row">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon" style="color: #667eea;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number"><?= $stats['total_users'] ?></div>
                    <div class="stat-label">Total Pengguna</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon" style="color: #4facfe;">
                        <i class="fas fa-male"></i>
                    </div>
                    <div class="stat-number"><?= $stats['total_male'] ?></div>
                    <div class="stat-label">Laki-laki</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon" style="color: #f093fb;">
                        <i class="fas fa-female"></i>
                    </div>
                    <div class="stat-number"><?= $stats['total_female'] ?></div>
                    <div class="stat-label">Perempuan</div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <h3><i class="fas fa-heart"></i> Daftar Pengguna Cari Jodoh</h3>
            
            <table id="userTable" class="table table-striped table-hover table-sm table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Lengkap</th>
                        <th>Gender</th>
                        <th>Usia</th>
                        <th>Kota</th>
                        <th>Provinsi</th>
                        <th>Pekerjaan</th>
                        <th>Hobi</th>
                        <th>Bio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($allUsers as $match):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="text-center">
                            <img src="Public/<?= htmlspecialchars($match['foto_profil']) ?>" 
                                 alt="<?= htmlspecialchars($match['nama_lengkap']) ?>"
                                 class="user-photo"
                                 onerror="this.src='https://via.placeholder.com/50'">
                        </td>
                        <td><strong><?= htmlspecialchars($match['nama_lengkap']) ?></strong></td>
                        <td>
                            <span class="badge <?= $match['jenis_kelamin'] === 'Laki-laki' ? 'bg-primary' : 'bg-danger' ?> badge-gender">
                                <?= htmlspecialchars($match['jenis_kelamin']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($match['umur']) ?> thn</td>
                        <td><?= htmlspecialchars($match['nama_kota'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($match['nama_provinsi'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($match['pekerjaan']) ?></td>
                        <td><?= htmlspecialchars($match['hobi']) ?></td>
                        <td>
                            <?php 
                            $bio = htmlspecialchars($match['catatan']);
                            echo strlen($bio) > 50 ? substr($bio, 0, 50) . '...' : $bio;
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
    
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#userTable').DataTable({
                dom: 'Bfrtip',
                
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Export Excel',
                        className: 'btn-success',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 6, 7, 8, 9] 
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> Export PDF',
                        className: 'btn-danger',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 6, 7, 8] 
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn-info',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> Export CSV',
                        className: 'btn-warning',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 6, 7, 8, 9] 
                        }
                    },
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn-secondary',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 6, 7, 8, 9] 
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fas fa-columns"></i> Kolom',
                        className: 'btn-dark'
                    }
                ],
                
                responsive: true,
                
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    zeroRecords: "Data tidak ditemukan",
                    emptyTable: "Tidak ada data di tabel",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    buttons: {
                        copy: 'Salin',
                        print: 'Cetak',
                        colvis: 'Tampilan Kolom'
                    }
                },
                
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
                
                order: [[2, 'asc']], 
                
                columnDefs: [
                    { 
                        orderable: false, 
                        targets: [1]
                    },
                    {
                        className: 'text-center',
                        targets: [0, 1, 3, 4] 
                    }
                ],
                
                processing: true
            });
        });

        function viewProfile(userId) {
            alert('Fitur detail profile untuk user ID: ' + userId + '\nSegera hadir! 💕');
        }
    </script>
</body>
</html>