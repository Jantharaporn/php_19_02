<?php
header("Content-Type: application/json; charset=UTF-8");
include_once "../config/db_connect.php";

$uploadDir = "../Cat/";
$method = $_SERVER["REQUEST_METHOD"];

$inputJSON = file_get_contents("php://input");
$input = json_decode($inputJSON, true);
if ($input) $_POST = $input;


/* ================= GET ================= */
if ($method === "GET") {

    if (isset($_GET["id"])) {

        $id = intval($_GET["id"]);

        $stmt = $conn->prepare("SELECT * FROM CatBreeds WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $breed = $stmt->get_result()->fetch_assoc();

        if ($breed) {
            $imgStmt = $conn->prepare("SELECT image_url FROM CatBreedsImages WHERE breed_id=?");
            $imgStmt->bind_param("i", $id);
            $imgStmt->execute();
            $imgResult = $imgStmt->get_result();

            $images = [];
            while ($img = $imgResult->fetch_assoc()) {
                $images[] = $img["image_url"];
            }

            $breed["images"] = $images;
        }

        echo json_encode($breed, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $result = $conn->query("SELECT * FROM CatBreeds ORDER BY id DESC");
    $data = [];

    while ($row = $result->fetch_assoc()) {

        $breedId = $row["id"];
        $imgResult = $conn->query("SELECT image_url FROM CatBreedsImages WHERE breed_id=$breedId");

        $images = [];
        while ($img = $imgResult->fetch_assoc()) {
            $images[] = $img["image_url"];
        }

        $row["images"] = $images;
        $data[] = $row;
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}


/* ================= ACTION ================= */
$action = $_POST["action"] ?? "";


/* ===== SAVE (ADD / EDIT) ===== */
if ($action === "save") {

    $id = intval($_POST["id"] ?? 0);
    $name_th = $_POST["name_th"] ?? "";
    $name_en = $_POST["name_en"] ?? "";
    $description = $_POST["description"] ?? "";
    $characteristics = $_POST["characteristics"] ?? "";
    $care = $_POST["care_instructions"] ?? "";

    if ($id > 0) {

        $stmt = $conn->prepare("
            UPDATE CatBreeds SET
                name_th=?,
                name_en=?,
                description=?,
                characteristics=?,
                care_instructions=?
            WHERE id=?
        ");
        $stmt->bind_param(
            "sssssi",
            $name_th,
            $name_en,
            $description,
            $characteristics,
            $care,
            $id
        );
        $stmt->execute();

    } else {

        $stmt = $conn->prepare("
            INSERT INTO CatBreeds
            (name_th, name_en, description, characteristics, care_instructions, is_visible)
            VALUES (?,?,?,?,?,1)
        ");
        $stmt->bind_param(
            "sssss",
            $name_th,
            $name_en,
            $description,
            $characteristics,
            $care
        );
        $stmt->execute();

        $id = $stmt->insert_id;
    }


    /* ===== UPLOAD MULTIPLE IMAGES (NO RENAME) ===== */
    if (!empty($_FILES["images"]["name"][0])) {

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($_FILES["images"]["name"] as $key => $imageName) {

            $tmpName = $_FILES["images"]["tmp_name"][$key];
            $fileName = basename($imageName); // ไม่เปลี่ยนชื่อแล้ว

            move_uploaded_file($tmpName, $uploadDir . $fileName);

            $imgStmt = $conn->prepare("
                INSERT INTO CatBreedsImages (breed_id, image_url)
                VALUES (?,?)
            ");
            $imgStmt->bind_param("is", $id, $fileName);
            $imgStmt->execute();
        }
    }

    echo json_encode(["success" => true]);
    exit;
}


/* ===== DELETE ===== */
if ($action === "delete") {

    $id = intval($_POST["id"]);

    $imgResult = $conn->query("SELECT image_url FROM CatBreedsImages WHERE breed_id=$id");
    while ($img = $imgResult->fetch_assoc()) {
        @unlink($uploadDir . $img["image_url"]);
    }

    $conn->query("DELETE FROM CatBreedsImages WHERE breed_id=$id");
    $conn->query("DELETE FROM CatBreeds WHERE id=$id");

    echo json_encode(["success" => true]);
    exit;
}


/* ===== TOGGLE ===== */
if ($action === "toggle_visible") {
    $id = intval($_POST["id"]);
    $conn->query("
        UPDATE CatBreeds
        SET is_visible = IF(is_visible=1,0,1)
        WHERE id=$id
    ");
    echo json_encode(["success" => true]);
    exit;
}

echo json_encode(["error" => "invalid_request"]);
