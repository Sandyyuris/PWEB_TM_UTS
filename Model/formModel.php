<?php
class ProfilModel {
    public $userId;
    public $nama;
    public $jenisKelamin;
    public $usia;
    public $provinsi;
    public $kota;
    public $pekerjaan;
    public $hobi;
    public $bio;
    public $fotoProfil;
    public $tandaTangan;

    public function __construct($userId, $nama, $jenisKelamin, $usia, $provinsi, $kota, $pekerjaan, $hobi, $bio, $fotoProfil, $tandaTangan) {
        $this->userId = $userId;
        $this->nama = $nama;
        $this->jenisKelamin = $jenisKelamin;
        $this->usia = $usia;
        $this->provinsi = $provinsi;
        $this->kota = $kota;
        $this->pekerjaan = $pekerjaan;
        $this->hobi = $hobi;
        $this->bio = $bio;
        $this->fotoProfil = $fotoProfil;
        $this->tandaTangan = $tandaTangan;
    }

    // Simpan/Update profil user
    public function simpanProfil($conn) {
        // --- 1. CARI ID KOTA BERDASARKAN NAMA KOTA DAN PROVINSI ---
        $queryKota = "SELECT k.id_kota 
                      FROM kota k 
                      INNER JOIN provinsi p ON k.id_provinsi = p.id_provinsi 
                      WHERE k.nama_kota = ? AND p.nama_provinsi = ?";
        $stmtKota = $conn->prepare($queryKota);
        $stmtKota->bind_param("ss", $this->kota, $this->provinsi);
        $stmtKota->execute();
        $resultKota = $stmtKota->get_result();

        if ($resultKota->num_rows > 0) {
            $dataKota = $resultKota->fetch_assoc();
            $idKota = $dataKota['id_kota'];
        } else {
            // Jika kota tidak ditemukan, hentikan proses (Data Master tidak Match)
            error_log("Gagal menemukan id_kota untuk Provinsi: " . $this->provinsi . " dan Kota: " . $this->kota);
            return false; 
        }

        // --- 2. UPDATE DATA USER ---
        $query = "UPDATE users SET
                    nama_lengkap = ?,
                    jenis_kelamin = ?,
                    umur = ?,
                    id_kota = ?,
                    pekerjaan = ?,
                    hobi = ?,
                    catatan = ?,
                    foto_profil = ?,
                    tanda_tangan = ?
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        // Tipe bind_param: s (nama), s (jenisKelamin), i (usia), i (idKota), s (pekerjaan), s (hobi), s (bio/catatan), s (foto), s (tanda tangan), i (userId)
        $stmt->bind_param("ssiisssssi",
            $this->nama,
            $this->jenisKelamin,
            $this->usia,
            $idKota, // Menggunakan ID Kota
            $this->pekerjaan,
            $this->hobi,
            $this->bio,
            $this->fotoProfil,
            $this->tandaTangan,
            $this->userId
        );

        if ($stmt->execute()) {
             return true;
        } else {
            // Log error MySQL jika gagal
            error_log("MySQL Error during profile update: " . $conn->error);
            return false;
        }
    }
    
    // Metode uploadFoto dan simpanTandaTangan tetap sama
    // ...
    public static function uploadFoto($file) {
        // ... (Kode uploadFoto tetap sama)
        $targetDir = "Public/uploads/profiles/";
        // Buat folder jika belum ada
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        // ... (lanjutan logika upload file)
        $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $allowedExtensions = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($fileExtension, $allowedExtensions)) {
            return ["success" => false, "message" => "Format file tidak didukung. Hanya JPG, JPEG, PNG, GIF."];
        }
        if ($file["size"] > 2097152) {
            return ["success" => false, "message" => "Ukuran file terlalu besar. Maksimal 2MB."];
        }
        $newFileName = uniqid("profile_", true) . "." . $fileExtension;
        $targetFile = $targetDir . $newFileName;
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return ["success" => true, "filename" => $newFileName];
        } else {
            return ["success" => false, "message" => "Gagal mengupload file."];
        }
    }

    public static function simpanTandaTangan($base64Data) {
        // ... (Kode simpanTandaTangan tetap sama)
        $targetDir = "Public/uploads/signatures/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imageData = explode(',', $base64Data);
        if (count($imageData) < 2) {
            return ["success" => false, "message" => "Format tanda tangan tidak valid."];
        }
        $decodedImage = base64_decode($imageData[1]);
        $newFileName = uniqid("signature_", true) . ".png";
        $targetFile = $targetDir . $newFileName;
        if (file_put_contents($targetFile, $decodedImage)) {
            return ["success" => true, "filename" => $newFileName];
        } else {
            return ["success" => false, "message" => "Gagal menyimpan tanda tangan."];
        }
    }
}