<?php
session_start();

include './database.php';

$user_id = $_SESSION['user_id'];

$title = $_POST['title'];
$description = $_POST['description'];
$price = $_POST['price'];
$category = $_POST['category'];


$imageName = time() . '_' . $_FILES['image']['name'];

$tmpName = $_FILES['image']['tmp_name'];

$folder = 'uploads/' . $imageName;

move_uploaded_file($tmpName, $folder);


$stmt = $linkConnect->prepare(
    "INSERT INTO products
    (user_id, title, description, price, category, image)
    VALUES (?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "issdss",
    $user_id,
    $title,
    $description,
    $price,
    $category,
    $folder
);

$stmt->execute();


echo "Product published successfully";
