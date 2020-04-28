<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("127.0.0.1", "root", "", "idolmart_db");

$result = $conn->query("SELECT product_code, product_name, price, amount FROM goods");

$outp = [];
while($rs = $result->fetch_array(MYSQLI_ASSOC)) {
    $outp[] = $rs;
}

$conn->close();

die(json_encode($outp));
?>