<?php
/**
*Connecting database
**/
function connect() {
    $configs = include('config.php');
    $servername = $configs['db_host'];
    $username = $configs['db_username'];
    $password = $configs['db_password'];
    $database = $configs['db_database'];
    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_errno) {
      printf("Connect failed: %s\n", $conn->connect_error);
      exit();
    }
    return $conn;
}
/**
 * Connection close
 * @param $conn
 */
function connection_close($conn) {
    $conn->close();
}
/**
 * Register user into the database
 * @param $data
 * @return array
 */
function register($data) {
    $username = $data['username'];
    $password = $data['password'];
    $email = $data['email'];
    $firstName = $data['firstName'];
    $lastName = $data['lastName'];
    $conn = connect();
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO tbusers (userName, password, email, firstName, lastName)
    VALUES ('$username', '$passwordHash', '$email', '$firstName', '$lastName')";
    if (mysqli_query($conn, $sql)) {
        $response = array('success' => 1, 'message' => 'Data inserted to database');
    } else {
        $response = array('success' => 0, 'message' => mysqli_error($conn));
    }
    connection_close($conn);
    return $response;
}
/**
 * Login by using username and password
 * @param $data
 * @return array
 */
function login($data) {
    $conn = connect();
    $username = $data['username'];
    $password = $data['password'];
    $sql = "SELECT id, password FROM tbusers where username = '$username'";
    $result = $conn->query($sql);
    $row   = mysqli_fetch_row($result);
    if(is_array($row)) {
    $userId = $row[0];
    $passHash = $row[1];
    } else {
    return array('success' => 0, 'message' => 'User Not Exists!');
    }
    if (password_verify($password, $passHash)) {
    $response = array('success' => 1, 'message' => 'Login Successfully', 'Username' => $username);
    } else {
    $response = array('success' => 0, 'message' => 'Password Error');
    }
    return $response;
}
