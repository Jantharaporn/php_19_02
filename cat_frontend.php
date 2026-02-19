<?php
session_start();
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>สายพันธุ์แมว</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:linear-gradient(135deg,#eef2ff,#f8fafc);
}
.hero{
    background:linear-gradient(135deg,#6366f1,#22d3ee);
    color:#fff;
    border-radius:28px;
}
.cat-card{
    border:none;
    border-radius:22px;
    overflow:hidden;
    transition:.3s;
}
.cat-card:hover{
    transform:translateY(-8px);
    box-shadow:0 25px 50px rgba(0,0,0,.15);
}
.cat-img{
    width:100%;
    height:240px;
    object-fit:cover;
}
.search-box{
    border-radius:50px;
    padding-left:50px;
}
.search-icon{
    position:absolute;
    left:20px;
    top:50%;
    transform:translateY(-50%);
    color:#6366f1;
}
.modal-content{
    border:none;
    border-radius:24px;
    overflow:hidden;
}
.modal-left,
.carousel-item,
.modal-img{
    height:520px;
}
.modal-img{
    width:100%;
    object-fit:cover;
}
.modal-detail{
    height:520px;
    overflow-y:auto;
    padding:35px;
    background:#ffffff;
}
.detail-card{
    background:#f8fafc;
    border-radius:16px;
    padding:18px 20px;
    margin-bottom:18px;
    box-shadow:0 6px 18px rgba(0,0,0,.05);
}
.detail-card h6{
    font-weight:700;
    color:#6366f1;
    margin-bottom:8px;
}
.btn-gradient{
    background:linear-gradient(135deg,#6366f1,#22d3ee);
    color:#fff;
    border:none;
    font-weight:600;
}
.btn-gradient:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(0,0,0,.25);
    color:#fff;
}
.site-footer{
    margin-top:60px;
    padding:28px 15px;
    background:linear-gradient(135deg,#eef2ff,#f8fafc);
    border-top:1px solid #dbeafe;
}
</style>
</head>

<body>
<div class="container py-5">

<!-- ===== ปุ่มมุมขวาบน ===== -->
<div class="d-flex justify-content-end mb-3">

<?php if(isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true): ?>
    
    <div class="d-flex align-items-center gap-3">
        <span class="badge bg-success px-3 py-2 rounded-pill fs-6">
            <i class="bi bi-person-check-fill me-1"></i>
            Login อยู่ (<?= htmlspecialchars($_SESSION["admin_username"]); ?>)
        </span>

        <a href="cat_backend.php" class="btn btn-outline-primary rounded-pill px-3">
            จัดการข้อมูล
        </a>

        <a href="logout.php" class="btn btn-danger rounded-pill px-3">
            Logout
        </a>
    </div>

<?php else: ?>

    <a href="login.php" class="btn btn-gradient px-4 py-2 rounded-pill shadow-sm">
        <i class="bi bi-person-lock me-2"></i>Admin Login
    </a>

<?php endif; ?>

</div>

<!-- ===== HERO ===== -->
<div class="hero p-5 mb-5 text-center shadow">
    <h1 class="fw-bold">🐱 สายพันธุ์แมว</h1>
    <p class="mb-0">รวมสายพันธุ์แมวจาก REST API</p>
</div>

<form class="mb-5 position-relative" onsubmit="event.preventDefault(); loadCats();">
    <i class="bi bi-search search-icon"></i>
    <input type="text" id="searchBox" class="form-control form-control-lg search-box"
           placeholder="ค้นหาสายพันธุ์แมว...">
</form>

<div class="row g-4" id="catContainer"></div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function loadCats(){

    const keyword = document.getElementById("searchBox").value.toLowerCase();

    fetch("api/cat_breeds.php")
    .then(res => res.json())
    .then(data => {

        const container = document.getElementById("catContainer");
        container.innerHTML = "";

        data.forEach(cat => {

            if(parseInt(cat.is_visible) !== 1) return;

            const nameTH = (cat.name_th ?? "").toLowerCase();
            const nameEN = (cat.name_en ?? "").toLowerCase();
            if(!nameTH.includes(keyword) && !nameEN.includes(keyword)) return;

            let firstImage = "https://via.placeholder.com/400x240?text=No+Image";
            if(cat.images && cat.images.length > 0){
                firstImage = "Cat/" + cat.images[0];
            }

            container.innerHTML += `
            <div class="col-lg-4 col-md-6">
                <div class="card cat-card h-100 shadow-sm">
                    <img src="${firstImage}" class="cat-img">
                    <div class="card-body">
                        <h5 class="fw-bold">${cat.name_th}</h5>
                        <div class="text-muted">${cat.name_en}</div>
                        <p class="small text-muted">
                            ${(cat.description ?? "").substring(0,80)}...
                        </p>

                        <button class="btn btn-gradient w-100 rounded-pill"
                            data-bs-toggle="modal"
                            data-bs-target="#modal${cat.id}">
                            ดูรายละเอียด
                        </button>
                    </div>
                </div>
            </div>
            `;
        });
    });
}

loadCats();
document.getElementById("searchBox").addEventListener("keyup", loadCats);
</script>

<footer class="site-footer text-center">
    <p class="mb-0 fw-semibold">© <?= date('Y') ?> Cat Breeds System</p>
    <small>
        พัฒนาโดย นางสาวจันทราภรณ์ ผาสีดา 67040233119<br>
        สาขาเทคโนโลยีสารสนเทศ คณะวิทยาศาสตร์ มหาวิทยาลัยราชภัฏอุดรธานี
    </small>
</footer>

</body>
</html>
