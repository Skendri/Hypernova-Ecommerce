<?php
session_start();

include './database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Please log in before publishing a product.";
    exit();
}

$user_id = $_SESSION['user_id'];

$title = $_POST['title'];
$description = $_POST['description'];
$price = $_POST['price'];
$category = $_POST['category'];
$phone = $_POST['phone'];

$uploadedImages = [];
$maxImages = 5;

if (!isset($_FILES['images']) || !is_array($_FILES['images']['name'])) {
    http_response_code(400);
    echo "Please upload at least one product image.";
    exit();
}

$imageCount = count(array_filter($_FILES['images']['name']));

if ($imageCount < 1 || $imageCount > $maxImages) {
    http_response_code(400);
    echo "Please upload between 1 and 5 images.";
    exit();
}

if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

for ($i = 0; $i < $imageCount; $i++) {
    if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo "One of the images could not be uploaded.";
        exit();
    }

    $tmpName = $_FILES['images']['tmp_name'][$i];
    $fileInfo = pathinfo($_FILES['images']['name'][$i]);
    $extension = strtolower($fileInfo['extension'] ?? '');
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extension, $allowedExtensions, true)) {
        http_response_code(400);
        echo "Only JPG, PNG, GIF, and WEBP images are allowed.";
        exit();
    }

    $imageName = time() . '_' . $i . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $folder = 'uploads/' . $imageName;

    if (!move_uploaded_file($tmpName, $folder)) {
        http_response_code(500);
        echo "Could not save one of the images.";
        exit();
    }

    $uploadedImages[] = $folder;
}

$imagesJson = json_encode($uploadedImages);


$stmt = $linkConnect->prepare(
    "INSERT INTO products
    (user_id, title, description, price, category, image, phone)
    VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "issdsss",
    $user_id,
    $title,
    $description,
    $price,
    $category,
    $imagesJson,
    $phone
);

$stmt->execute();


echo "Product published successfully";
