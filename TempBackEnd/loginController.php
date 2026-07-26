<?php

header("Content-Type: application/json");

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$username = $data["username"];
$password = $data["password"];

$sql = "SELECT * FROM users WHERE username=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$username);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows > 0){

    $user = $result->fetch_assoc();
    if($password == $user["acc_password"]){

        echo json_encode([
            "success"=>true,
            "role"=>$user["acc_role"]
        ]);

    }else{

        echo json_encode([
            "success"=>false,
            "message"=>"Incorrect Password"
        ]);

    }

}else{

    echo json_encode([
        "success"=>false,
        "message"=>"Username not found"
    ]);

}

?>