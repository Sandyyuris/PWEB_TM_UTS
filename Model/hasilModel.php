<?php
class HasilModel {
    
    // Ambil data user berdasarkan ID dengan JOIN ke tabel kota dan provinsi
    public function getUserById($conn, $userId) {
        $query = "SELECT u.*, k.nama_kota, p.nama_provinsi 
                  FROM users u 
                  LEFT JOIN kota k ON u.id_kota = k.id_kota 
                  LEFT JOIN provinsi p ON k.id_provinsi = p.id_provinsi 
                  WHERE u.id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        
        return null;
    }

    // Ambil semua user kecuali user yang login, hanya yang profilnya lengkap
    public function getAllUsersExcept($conn, $excludeUserId) {
        $query = "SELECT u.*, k.nama_kota, p.nama_provinsi 
                  FROM users u 
                  LEFT JOIN kota k ON u.id_kota = k.id_kota 
                  LEFT JOIN provinsi p ON k.id_provinsi = p.id_provinsi 
                  WHERE u.id != ? 
                  AND u.nama_lengkap IS NOT NULL 
                  AND u.nama_lengkap != ''
                  ORDER BY u.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $excludeUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        
        return $users;
    }

    // Ambil statistik dashboard
    public function getDashboardStats($conn, $userId) {
        $stats = [];
        
        // Total users (exclude self)
        $query = "SELECT COUNT(*) as total FROM users WHERE id != ? AND nama_lengkap IS NOT NULL";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['total_users'] = $result->fetch_assoc()['total'];
        
        // Total laki-laki
        $query = "SELECT COUNT(*) as total FROM users WHERE id != ? AND jenis_kelamin = 'Laki-laki' AND nama_lengkap IS NOT NULL";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['total_male'] = $result->fetch_assoc()['total'];
        
        // Total perempuan
        $query = "SELECT COUNT(*) as total FROM users WHERE id != ? AND jenis_kelamin = 'Perempuan' AND nama_lengkap IS NOT NULL";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['total_female'] = $result->fetch_assoc()['total'];
        
        return $stats;
    }

    // Search users berdasarkan kriteria
    public function searchUsers($conn, $userId, $gender = null, $minAge = null, $maxAge = null, $provinsi = null) {
        $query = "SELECT u.*, k.nama_kota, p.nama_provinsi 
                  FROM users u 
                  LEFT JOIN kota k ON u.id_kota = k.id_kota 
                  LEFT JOIN provinsi p ON k.id_provinsi = p.id_provinsi 
                  WHERE u.id != ? AND u.nama_lengkap IS NOT NULL";
        
        $params = [$userId];
        $types = "i";
        
        if ($gender) {
            $query .= " AND u.jenis_kelamin = ?";
            $params[] = $gender;
            $types .= "s";
        }
        
        if ($minAge) {
            $query .= " AND u.umur >= ?";
            $params[] = $minAge;
            $types .= "i";
        }
        
        if ($maxAge) {
            $query .= " AND u.umur <= ?";
            $params[] = $maxAge;
            $types .= "i";
        }
        
        if ($provinsi) {
            $query .= " AND p.nama_provinsi = ?";
            $params[] = $provinsi;
            $types .= "s";
        }
        
        $query .= " ORDER BY u.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        
        return $users;
    }
}