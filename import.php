<?php
require "db.php";

$sql = file_get_contents("sql.sql"); // ton dump
$conn->multi_query($sql);

echo "OK";
