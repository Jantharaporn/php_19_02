<?php
session_start();
require_once "config/db_connect.php";

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["admin_username"];

/* ดึงข้อมูล admin */
$stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

/* เมื่อกดบันทึก */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $new_username = $_POST["username"];
    $new_password = $_POST["password"];

    if (!empty($new_password)) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE admin_users SET username=?, password=? WHERE id=?");
        $update->bind_param("ssi", $new_username, $hashed, $admin["id"]);
    } else {
        $update = $conn->prepare("UPDATE admin_users SET username=? WHERE id=?");
        $update->bind_param("si", $new_username, $admin["id"]);
    }

    $update->execute();

    $_SESSION["admin_username"] = $new_username;

    $success = "อัปเดตข้อมูลเรียบร้อยแล้ว";
}
?>

<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>แก้ไขข้อมูลผู้ดูแล</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:linear-gradient(135deg,#eef2ff,#f8fafc);
}
.profile-card{
    border:none;
    border-radius:25px;
}
.profile-header{
    background:linear-gradient(135deg,#6366f1,#22d3ee);
    color:#fff;
    border-radius:25px 25px 0 0;
}
html, body{
    height:100%;
}

body{
    display:flex;
    flex-direction:column;
    background:linear-gradient(135deg,#eef2ff,#f8fafc);
}

.main-content{
    flex:1;
}

.profile-card{
    border:none;
    border-radius:25px;
}

.profile-header{
    background:linear-gradient(135deg,#6366f1,#22d3ee);
    color:#fff;
    border-radius:25px 25px 0 0;
}

.site-footer{
    padding:28px 15px;
    background:linear-gradient(135deg,#eef2ff,#f8fafc);
    border-top:1px solid #dbeafe;
    box-shadow:0 -8px 25px rgba(0,0,0,.08);
}

</style>
</head>

<body>

<div class="main-content">

<div class="container py-5">

<div class="card profile-card shadow-sm col-md-6 mx-auto">

<div class="profile-header p-4 text-center">
<h4 class="fw-bold mb-0">
<i class="bi bi-person-circle me-2"></i> แก้ไขข้อมูลผู้ดูแล
</h4>
</div>

<div class="card-body p-4">

<?php if (!empty($success)) : ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label>Username</label>
<input type="text" name="username" class="form-control"
       value="<?= htmlspecialchars($admin["username"]) ?>" required>
</div>

<div class="mb-3">
<label>รหัสผ่านใหม่ (เว้นว่างถ้าไม่เปลี่ยน)</label>
<input type="password" name="password" class="form-control">
</div>

<div class="d-flex justify-content-between">
<a href="cat_backend.php" class="btn btn-secondary rounded-pill px-4">
<i class="bi bi-arrow-left"></i> กลับ
</a>

<button class="btn btn-primary rounded-pill px-4">
<i class="bi bi-save"></i> บันทึก
</button>
</div>

</form>

</div>
</div>
</div>

</div> <!-- ปิด main-content -->

<footer class="site-footer text-center">
    <p class="mb-0 fw-semibold">© <?= date('Y') ?> Cat Breeds System</p>
    <small>
        พัฒนาโดย นางสาวจันทราภรณ์ ผาสีดา 67040233119<br>
        สาขาเทคโนโลยีสารสนเทศ คณะวิทยาศาสตร์ มหาวิทยาลัยราชภัฏอุดรธานี<br>
        All Rights Reserved
    </small>
</footer>

</body>
</html>

</html>
