<?php

$conn = new mysqli("127.0.0.1", "root", "", "idolmart_db");
if (mysqli_connect_errno()) {
echo "Failed to connect to MySQL: " .
mysqli_connect_error();
}
mysqli_query($conn,"DELETE FROM goods WHERE product_code='".$_GET['product_code']."'");
mysqli_close($conn);

echo("Delete data");
?>