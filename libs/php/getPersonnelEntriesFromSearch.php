<?php

	// example use from browser
	// http://localhost/companydirectory/libs/php/getAll.php

	// remove next two lines for production
	
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);

	$executionStartTime = microtime(true);

	include("config.php");

	header('Content-Type: application/json; charset=UTF-8');

	$conn = new mysqli($cd_host, $cd_user, $cd_password, $cd_dbname, $cd_port, $cd_socket);

	if (mysqli_connect_errno()) {
		
		$output['status']['code'] = "300";
		$output['status']['name'] = "failure";
		$output['status']['description'] = "database unavailable";
		$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
		$output['data'] = [];

		mysqli_close($conn);

		echo json_encode($output);

		exit;

	}	

	$storeSearch =  $_POST['searchResult'];


	$query = "SELECT DISTINCT(personnel.id), firstName, lastName,jobTitle,email,departmentID,department.name,location.name FROM personnel,department,location where (firstName LIKE '%$storeSearch%' OR lastName LIKE '%$storeSearch%' OR jobTitle LIKE '%$storeSearch%' OR email LIKE '%$storeSearch%' OR  department.name LIKE '%$storeSearch%' OR location.name LIKE '%$storeSearch%') AND(departmentID = department.id) AND(locationID = location.id) group by (personnel.firstName ) ASC";

	$result = $conn->query($query);
	
	if (!$result) {

		$output['status']['code'] = "400";
		$output['status']['name'] = "executed";
		$output['status']['description'] = "query failed";	
		$output['data'] = [];

		mysqli_close($conn);

		echo json_encode($output); 

		exit;

	}
   
   	$data = [];
   	$data2 = [];
   	$data3 = [];


	while ($row = mysqli_fetch_assoc($result)) {

		array_push($data, $row);

	}


	for($i = 0; $i< count($data);$i++) {
		$getDepartmentID = $data[$i]['departmentID'];
		$query = "SELECT * FROM department where id = '$getDepartmentID'";
	$result = $conn->query($query);

			if (!$result) {
		$output['status']['code'] = "400";
		$output['status']['name'] = "executed";
		$output['status']['description'] = "query failed";	
		$output['data'] = [];
		mysqli_close($conn);
		echo json_encode($output); 
		exit;
		break;
	}
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($data2, $row);
	}
	}


		for($i = 0; $i< count($data2);$i++) {
		$getLocationID = $data2[$i]['locationID'];
		$query = "SELECT * FROM location where id = '$getLocationID'";
	    $result = $conn->query($query);

			if (!$result) {
		$output['status']['code'] = "400";
		$output['status']['name'] = "executed";
		$output['status']['description'] = "query failed";	
		$output['data'] = [];
		mysqli_close($conn);
		echo json_encode($output); 
		exit;
		break;
	}
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($data3, $row);
	}
	}

	$output['status']['code'] = "200";
	$output['status']['name'] = "ok";
	$output['status']['description'] = "success";
	$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
	$output['data'] = $data;
	$output['data2'] = $data2;
	$output['data3'] = $data3;



	
	mysqli_close($conn);

	echo json_encode($output); 

?>