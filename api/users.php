<?php

// connecting on database..
$db = DB::connect();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Helper function for responses
function sendResponse($status, $message, $data = null) {
    http_response_code($status);
    echo json_encode(['message' => $message, 'data' => $data]);
    exit;
}

// Validation of inputs
$remov = array("'", "\\", "-", "(", ")");
$id_user = !empty($_GET['user_id']) ? intval($_GET['user_id']) : '';
$name = !empty($_GET['name']) ? preg_replace("/[^\w\s]/u", "", $_GET['name']) : '';
$email = !empty($_GET['email']) ? mb_strtolower(trim(str_replace($remov, "", $_GET['email'])), 'UTF-8') : '';
$password = !empty($_GET['password']) ? $_GET['password'] : '';
$avatar = !empty($_GET['avatar']) ? $_GET['avatar'] : '';

// Validation of email format
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(400, 'Invalid email address');
}

// Method and action validation
$validMethods = ['GET', 'POST', 'PUT', 'DELETE'];
$validActions = ['login', 'register', 'edit', 'list', 'delete'];

if (!in_array($method, $validMethods) || !in_array($action, $validActions)) {
    sendResponse(404, 'Invalid method or action');
}

if ($method == 'POST' && $action == 'login') {

    $query = $db->prepare("SELECT * FROM users WHERE email = :email");
    $query->execute(['email' => $email]);
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if (!$result || !password_verify($password, $result['password'])) {
        sendResponse(401, 'Email or password incorrect');
    }

    sendResponse(200, 'Successfully logged in', $result);

} else if ($method == 'POST' && $action == 'register') {

    if ($name === '' || $email === '' || $password === '') {
        sendResponse(403, 'Provide all credentials');
    }

    $query = $db->prepare("SELECT id FROM users WHERE email = :email");
    $query->execute(['email' => $email]);
    $resultVerify = $query->fetch(PDO::FETCH_ASSOC);

    if ($resultVerify) {
        sendResponse(401, 'User already exists');
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $query = $db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $result = $query->execute(['name' => $name, 'email' => $email, 'password' => $passwordHash]);

    sendResponse(201, 'User was created!');

} else if ($method == 'PUT' && $action == 'edit') {

    if (!$id_user) {
        sendResponse(401, 'User ID is required');
    }

    $setters = [];
    $params = ['id' => $id_user];
    if ($name) {
        $setters[] = "name = :name";
        $params['name'] = $name;
    }
    if ($email) {
        $setters[] = "email = :email";
        $params['email'] = $email;
    }
    if ($password) {
        $setters[] = "password = :password";
        $params['password'] = password_hash($password, PASSWORD_BCRYPT);
    }
    if ($avatar) {
        $setters[] = "avatar = :avatar";
        $params['avatar'] = $avatar;
    }

    $settersString = implode(", ", $setters);

    try {
        $query = $db->prepare("UPDATE users SET $settersString WHERE id = :id");
        $query->execute($params);
    } catch (Exception $e) {
        sendResponse(500, 'Internal server error: ' . $e->getMessage());
    }

    sendResponse(200, 'User was modified!');

} else if ($method == 'GET' && $action == 'list') {

    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $query = $db->prepare("SELECT * FROM users LIMIT :limit OFFSET :offset");
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if (!$result) {
        sendResponse(404, 'No users found');
    }

    sendResponse(200, 'Successfully retrieved users', $result);

} else if ($method == 'DELETE' && $action == 'delete') {

    $query = $db->prepare("SELECT * FROM users WHERE email = :email");
    $query->execute(['email' => $email]);
    $resultVerify = $query->fetch(PDO::FETCH_ASSOC);

    if (!$resultVerify || !password_verify($password, $resultVerify['password'])) {
        sendResponse(401, 'User does not exist or invalid credentials');
    }

    if ($resultVerify['level'] && $id_user) {
        $query = $db->prepare("DELETE FROM users WHERE id = :id");
        $query->execute(['id' => $id_user]);

        sendResponse(200, 'Account was deleted');
    } else {
        $query = $db->prepare("DELETE FROM users WHERE email = :email");
        $query->execute(['email' => $email]);

        sendResponse(200, 'Account was deleted');
    }

} else {
    sendResponse(404, 'Not found');
}
