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
        // Cari id_kota berdasarkan nama kota dan provinsi
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
            return false; // Kota tidak ditemukan
        }

        // Update data user dengan nama kolom yang sesuai database
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
        $stmt->bind_param("ssiisssssi",
            $this->nama,
            $this->jenisKelamin,
            $this->usia,
            $idKota,
            $this->pekerjaan,
            $this->hobi,
            $this->bio,
            $this->fotoProfil,
            $this->tandaTangan,
            $this->userId
        );

        return $stmt->execute();
    }

    // Upload foto profil
    public static function uploadFoto($file) {
        $targetDir = "Public/uploads/profiles/";
        
        // Buat folder jika belum ada
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $allowedExtensions = ["jpg", "jpeg", "png", "gif"];

        // Validasi ekstensi file
        if (!in_array($fileExtension, $allowedExtensions)) {
            return ["success" => false, "message" => "Format file tidak didukung. Hanya JPG, JPEG, PNG, GIF."];
        }

        // Validasi ukuran file (max 2MB)
        if ($file["size"] > 2097152) {
            return ["success" => false, "message" => "Ukuran file terlalu besar. Maksimal 2MB."];
        }

        // Generate nama file unik
        $newFileName = uniqid("profile_", true) . "." . $fileExtension;
        $targetFile = $targetDir . $newFileName;

        // Upload file
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return ["success" => true, "filename" => $newFileName];
        } else {
            return ["success" => false, "message" => "Gagal mengupload file."];
        }
    }

    // Simpan tanda tangan (base64)
    public static function simpanTandaTangan($base64Data) {
        $targetDir = "Public/uploads/signatures/";
        
        // Buat folder jika belum ada
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Hapus prefix data:image/png;base64,
        $imageData = explode(',', $base64Data);
        
        if (count($imageData) < 2) {
            return ["success" => false, "message" => "Format tanda tangan tidak valid."];
        }

        $decodedImage = base64_decode($imageData[1]);

        // Generate nama file unik
        $newFileName = uniqid("signature_", true) . ".png";
        $targetFile = $targetDir . $newFileName;

        // Simpan file
        if (file_put_contents($targetFile, $decodedImage)) {
            return ["success" => true, "filename" => $newFileName];
        } else {
            return ["success" => false, "message" => "Gagal menyimpan tanda tangan."];
        }
    }

    // Get data provinsi
    public static function getProvinsi($conn) {
        $query = "SELECT * FROM provinsi ORDER BY nama_provinsi ASC";
        $result = $conn->query($query);
        
        $provinsi = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $provinsi[] = $row;
            }
        }
        
        return $provinsi;
    }

    // Get data kota berdasarkan provinsi
    public static function getKotaByProvinsi($conn, $idProvinsi) {
        $query = "SELECT * FROM kota WHERE id_provinsi = ? ORDER BY nama_kota ASC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $idProvinsi);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $kota = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $kota[] = $row;
            }
        }
        
        return $kota;
    }
}