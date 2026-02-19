<?php
session_start();

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit();
}
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>Cat Breeds Manager (API)</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(135deg,#eef2ff,#f8fafc);
}
.card{
    border:none;
    border-radius:20px;
}
.table{
    border-collapse: separate;
    border-spacing: 0 16px;
}
.table tbody tr{
    background:#fff;
    box-shadow:0 8px 22px rgba(0,0,0,.08);
    border-radius:18px;
}
.table td{
    padding:20px;
}
.img-thumb{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:12px;
    border:2px solid #eee;
}
.action-btn{
    width:44px;
    height:44px;
    border-radius:14px;
}
.btn-gradient{
    background:linear-gradient(135deg,#6366f1,#22d3ee);
    color:#fff;
    border:none;
    font-weight:600;
}
.btn-gradient-outline{
    background:transparent;
    border:2px solid #6366f1;
    color:#6366f1;
    font-weight:600;
}
.btn-gradient-outline:hover{
    background:#6366f1;
    color:#fff;
}
.btn-admin{
    background:linear-gradient(135deg,#f59e0b,#fbbf24);
    color:#fff;
    border:none;
    font-weight:600;
}
.btn-admin:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(0,0,0,.15);
    color:#fff;
}
.site-footer{
    margin-top:60px;
    padding:28px 15px;
    background:linear-gradient(135deg,#eef2ff,#f8fafc);
    border-top:1px solid #dbeafe;
}
.site-footer{
    margin-top:60px;
    padding:28px 15px;
    background:linear-gradient(135deg,#eef2ff,#f8fafc);
    border-top:1px solid #dbeafe;
    box-shadow:0 -8px 25px rgba(0,0,0,.08);
}
</style>
</head>

<body>
<div class="container py-4">

<!-- Top Bar -->
<div class="d-flex justify-content-between align-items-center mb-4">

    <!-- แสดงชื่อผู้ใช้ -->
    <div class="fw-semibold text-muted">
        <i class="bi bi-person-circle me-1"></i>
        <?= $_SESSION["admin_username"] ?>
    </div>

    <!-- ปุ่มด้านขวา -->
    <div class="d-flex gap-2">

        <a href="admin_profile.php"
           class="btn btn-admin rounded-pill px-4">
            <i class="bi bi-person-gear me-1"></i> แก้ไข Admin
        </a>

        <a href="cat_frontend.php"
           class="btn btn-gradient-outline rounded-pill px-4">
            <i class="bi bi-eye-fill me-1"></i> ดูหน้าเว็บ
        </a>

        <a href="logout.php"
           class="btn btn-gradient rounded-pill px-4">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>

    </div>

</div>

<!-- Header Card -->
<div class="card shadow-sm mb-4"
     style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff">
<div class="card-body">
<h4 class="mb-0 fw-bold">
🐱 Cat Breeds Management (Multiple Images)
</h4>
</div>
</div>

<!-- Form -->
<div class="card shadow-sm mb-4">
<div class="card-body">

<h5 class="fw-bold text-primary mb-3">
<i class="bi bi-plus-circle"></i> เพิ่ม / แก้ไขสายพันธุ์แมว
</h5>

<form id="catForm" class="row g-3" enctype="multipart/form-data">
<input type="hidden" name="id">
<input type="hidden" name="action" value="save">

<div class="col-md-6">
<label>ชื่อไทย</label>
<input class="form-control" name="name_th" required>
</div>

<div class="col-md-6">
<label>ชื่ออังกฤษ</label>
<input class="form-control" name="name_en" required>
</div>

<div class="col-12">
<label>รายละเอียด</label>
<textarea class="form-control" name="description"></textarea>
</div>

<div class="col-md-6">
<label>ลักษณะ</label>
<textarea class="form-control" name="characteristics"></textarea>
</div>

<div class="col-md-6">
<label>การดูแล</label>
<textarea class="form-control" name="care_instructions"></textarea>
</div>

<div class="col-md-6">
<label>รูปภาพ (เลือกได้หลายรูป)</label>
<input type="file" class="form-control" name="images[]" multiple>
</div>

<div class="col-12 text-end">
<button class="btn btn-success">
<i class="bi bi-save"></i> บันทึกข้อมูล
</button>
</div>
</form>

</div>
</div>

<!-- Table -->
<div class="card shadow-sm">
<div class="card-body">

<h5 class="fw-bold mb-3 text-secondary">
<i class="bi bi-list-ul"></i> รายการสายพันธุ์แมว
</h5>

<table class="table text-center align-middle">
<thead>
<tr>
<th>ID</th>
<th>รูปทั้งหมด</th>
<th>ชื่อไทย</th>
<th>ชื่ออังกฤษ</th>
<th>สถานะ</th>
<th>จัดการ</th>
</tr>
</thead>
<tbody id="catTable"></tbody>
</table>

</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const API_URL = "api/cat_breeds.php";
const IMAGE_BASE = "Cat/";

function loadCats(){
    fetch(API_URL)
    .then(res => res.json())
    .then(data => {

        let html = "";

        data.forEach(cat => {

            let imagesHtml = "";

            if(cat.images && cat.images.length > 0){
                cat.images.forEach(img => {
                    imagesHtml += `
                        <img src="${IMAGE_BASE + img}"
                             class="img-thumb me-1 mb-1"
                             onerror="this.src='https://via.placeholder.com/60?text=Error'">
                    `;
                });
            }else{
                imagesHtml = `
                    <img src="https://via.placeholder.com/60?text=No+Image"
                         class="img-thumb">
                `;
            }

            html += `
            <tr>
                <td>${cat.id}</td>
                <td>${imagesHtml}</td>
                <td class="fw-bold">${cat.name_th}</td>
                <td>${cat.name_en}</td>
                <td>
                    <span class="badge ${cat.is_visible == 1 ? 'bg-success':'bg-secondary'}">
                        ${cat.is_visible == 1 ? 'แสดง':'ซ่อน'}
                    </span>
                </td>
                <td class="d-flex justify-content-center gap-2">
                    <button class="btn action-btn ${cat.is_visible == 1 ? 'btn-success':'btn-secondary'}"
                        onclick="toggleVisible(${cat.id})">
                        <i class="bi ${cat.is_visible == 1 ? 'bi-eye-fill':'bi-eye-slash-fill'}"></i>
                    </button>

                    <button class="btn btn-warning action-btn"
                        onclick="editCat(${cat.id})">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <button class="btn btn-danger action-btn"
                        onclick="deleteCat(${cat.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
        });

        document.getElementById("catTable").innerHTML = html;
    });
}

function editCat(id){
    fetch(API_URL + "?id=" + id)
    .then(res => res.json())
    .then(cat => {
        for(let key in cat){
            const el = document.querySelector(`[name="${key}"]`);
            if(el) el.value = cat[key];
        }
        window.scrollTo({top:0,behavior:'smooth'});
    });
}

function deleteCat(id){
    if(confirm("ยืนยันการลบข้อมูล ?")){
        fetch(API_URL, {
            method: "POST",
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: "action=delete&id=" + id
        }).then(() => loadCats());
    }
}

function toggleVisible(id){
    fetch(API_URL, {
        method: "POST",
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: "action=toggle_visible&id=" + id
    }).then(() => loadCats());
}

document.getElementById("catForm").addEventListener("submit", e=>{
    e.preventDefault();
    const formData = new FormData(document.getElementById("catForm"));

    fetch(API_URL, {
        method: "POST",
        body: formData
    })
    .then(() => {
        document.getElementById("catForm").reset();
        loadCats();
    });
});

loadCats();
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
