<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi | Cari Jodoh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #fbc2eb, #a6c1ee);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .register-card {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
        }
        .btn-primary {
            background-color: #5A67D8;
            border: none;
        }
        .btn-primary:hover {
            background-color: #434190;
        }
    </style>
</head>
<body>

<div class="register-card">
    <h3 class="text-center mb-4">Daftar Akun 💌</h3>
    <form action="index.php?action=register" method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100" name="register">Daftar</button>
    </form>

    <div class="text-center mt-3">
        <small>Sudah punya akun? <a href="index.php?action=login">Login</a></small>
    </div>
</div>

</body>
</html>
