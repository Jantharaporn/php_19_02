<?php
    $host = 'localhost';          //ชื่อโฮสต์
    $db = 'it67040233119';                 //ชื่อผู้ใช้ฐานข้อมูล
    $pass = 'M1Q8L9N6';                   //รหัสผ่านฐานข้อมูล
    $dbname = 'it67040233119';    //ชื่อฐานข้อมูล

    $conn =new mysqli($host, $db, $pass, $dbname);
    mysqli_set_charset($conn, "utf8");

    if(!$conn){
        die("เชื่อมต่อไม่สำเร็จ" . mysqli_connect_error());
    }
?>