<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost","root","12345678", "tbl_marks");

$sqlQuery = "SELECT * FROM tbl_marks ORDER BY student_id";

$result = mysqli_query($conn,$sqlQuery);

$data = array();
foreach ($result as $row) {
	$data[] = $row;
}

mysqli_close($conn);

echo json_encode($data);
?>