<?php
session_start();
require_once "config/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            $_SESSION["admin_logged_in"] = true;
            $_SESSION["admin_username"] = $user["username"];

            header("Location: cat_backend.php");
            exit();
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบผู้ใช้นี้";
    }
}
?>

<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>Admin Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
html, body{
    height:100%;
}

body{
    margin:0;
    display:flex;
    flex-direction:column;
    background:linear-gradient(135deg,#6366f1,#22d3ee);
}

/* ส่วนกลาง */
.wrapper{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px 15px;
}

.login-card{
    width:100%;
    max-width:420px;
    border:none;
    border-radius:28px;
    box-shadow:0 25px 60px rgba(0,0,0,.25);
}

.login-header{
    background:linear-gradient(135deg,#6366f1,#8b5cf6);
    color:#fff;
    border-radius:28px 28px 0 0;
    padding:30px;
    text-align:center;
}

.form-control{
    border-radius:14px;
    padding:12px;
}

.btn-gradient{
    background:linear-gradient(135deg,#6366f1,#8b5cf6);
    border:none;
    border-radius:50px;
    font-weight:600;
    padding:12px;
    transition:.25s;
    color:#fff;
}

.btn-gradient:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 30px rgba(0,0,0,.25);
    color:#fff;
}

/* Footer เต็มความกว้าง */
.site-footer{
    text-align:center;
    padding:20px 10px;
    color:#fff;
    background:rgba(255,255,255,.12);
    backdrop-filter:blur(6px);
}

</style>
</head>

<body>

<div class="wrapper">

    <div class="card login-card">

        <div class="login-header">
            <h4 class="fw-bold mb-1">🐱 Cat Breeds System</h4>
            <small>Admin Login</small>
        </div>

        <div class="card-body p-4">

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-4">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label>Username</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label>Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-gradient w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    เข้าสู่ระบบ
                </button>

            </form>

        </div>
    </div>

</div>

<footer class="site-footer">
    <p class="mb-1 fw-semibold">© <?= date('Y') ?> Cat Breeds System</p>
    <small>
        พัฒนาโดย นางสาวจันทราภรณ์ ผาสีดา 67040233119<br>
        สาขาเทคโนโลยีสารสนเทศ คณะวิทยาศาสตร์ มหาวิทยาลัยราชภัฏอุดรธานี
    </small>
</footer>

</body>
</html>

</html>
