<?php

// Get the product data
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
$code = filter_input(INPUT_POST, 'code');
$name = filter_input(INPUT_POST, 'name');
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

// Validate inputs
if ($product_id == NULL || $product_id == FALSE || $category_id == NULL ||
$category_id == FALSE || empty($code) || empty($name) ||
$price == NULL || $price == FALSE) {
$error = "Invalid product data. Check all fields and try again.";
include('error.php');
} else {

/**************************** Image upload ****************************/

$imgFile = $_FILES['product_image']['name'];
$tmp_dir = $_FILES['product_image']['tmp_name'];
$imgSize = $_FILES['product_image']['size'];
$original_product_image = filter_input(INPUT_POST, 'original_product_image');

if ($imgFile) {
$upload_dir = 'image_uploads/'; // upload directory	
$imgExt = strtolower(pathinfo($imgFile, PATHINFO_EXTENSION)); // get image extension
$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
$product_image = rand(1000, 1000000) . "." . $imgExt;
if (in_array($imgExt, $valid_extensions)) {
if ($imgSize < 5000000) {
if (filter_input(INPUT_POST, 'original_product_image') !== "") {
unlink($upload_dir . $original_product_image);                    
}
move_uploaded_file($tmp_dir, $upload_dir . $product_image);
} else {
$errMSG = "Sorry, your file is too large it should be less then 5MB";
}
} else {
$errMSG = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
}
} else {
// if no image selected the old image remain as it is.
$product_image = $original_product_image; // old image from database
}

/************************** End Image upload **************************/

// If valid, update the product in the database
require_once('database.php');

$query = 'UPDATE products
SET categoryID = :category_id,
productCode = :code,
productName = :name,
listPrice = :price,
productImage = :product_image
WHERE productID = :product_id';
$statement = $db->prepare($query);
$statement->bindValue(':category_id', $category_id);
$statement->bindValue(':code', $code);
$statement->bindValue(':name', $name);
$statement->bindValue(':price', $price);
$statement->bindValue(':product_image', $product_image);
$statement->bindValue(':product_id', $product_id);
$statement->execute();
$statement->closeCursor();

// Display the Product List page
include('index.php');
}
?>