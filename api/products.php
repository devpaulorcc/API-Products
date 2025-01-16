<?php

// connecting on database..
$db = DB::connect();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Helper function for responses
function sendResponse($status, $message, $data = null)
{
    http_response_code($status);
    echo json_encode(['message' => $message, 'data' => $data]);
    exit;
}

// Validation of inputs
$id_prod = !empty($_GET['prod_id']) ? intval($_GET['prod_id']) : '';
$id_user = !empty($_GET['user_id']) ? intval($_GET['user_id']) : '';
$name = !empty($_GET['name']) ? mb_strtolower(preg_replace("/[^\w\s]/u", "", $_GET['name']), 'UTF-8') : '';
$amount = !empty($_GET['amount']) ? intval($_GET['amount']) : '';
$metric = !empty($_GET['metric']) ? mb_strtolower(preg_replace('/[^\wç]/iu', "", $_GET['metric']), 'UTF-8') : '';
$value = !empty($_GET['value']) ? floatval(str_replace(',', '.', $_GET['value'])) : 0.0;

// Validation of user ID
if ($id_user && !filter_var($id_user, FILTER_VALIDATE_INT)) {
    sendResponse(400, 'Invalid user ID');
}

if ($method == 'POST' && $action == 'register') {

    $query = $db->prepare("SELECT id FROM products WHERE name = :name AND fk_user = :user_id");
    $query->execute(['name' => $name, 'user_id' => $id_user]);
    $resultVerify = $query->fetch(PDO::FETCH_ASSOC);

    if ($resultVerify) {
        sendResponse(201, 'Product already exists.');
    }

    $query = $db->prepare("INSERT INTO products (fk_user, name, amount, metric, value) VALUES (:user_id, :name, :amount, :metric, :value)");
    $result = $query->execute([
        'user_id' => $id_user,
        'name' => $name,
        'amount' => $amount,
        'metric' => $metric,
        'value' => $value
    ]);

    sendResponse(201, 'Product successfully added.');
} else if ($method == 'PUT' && $action == 'edit') {

    if (!$id_prod || !$id_user) {
        sendResponse(400, 'Product ID and User ID are required');
    }

    $setters = [];
    $params = ['id' => $id_prod, 'user_id' => $id_user];

    if ($name) {
        $setters[] = "name = :name";
        $params['name'] = $name;
    }
    if ($amount) {
        $setters[] = "amount = :amount";
        $params['amount'] = $amount;
    }
    if ($metric) {
        $setters[] = "metric = :metric";
        $params['metric'] = $metric;
    }
    if ($value) {
        $setters[] = "value = :value";
        $params['value'] = $value;
    }

    $settersString = implode(", ", $setters);

    try {
        $query = $db->prepare("UPDATE products SET $settersString WHERE id = :id AND fk_user = :user_id");
        $query->execute($params);
    } catch (Exception $e) {
        sendResponse(500, 'Internal server error: ' . $e->getMessage());
    }

    sendResponse(200, 'Product was modified!');
} else if ($method == 'GET' && $action == 'list') {

    if (!$id_user) {
        sendResponse(400, 'User ID is required');
    }

    $params = ['user_id' => $id_user];
    $sql = "SELECT * FROM products WHERE fk_user = :user_id";

    if ($name) {
        $sql .= " AND name LIKE :name";
        $params['name'] = '%' . $name . '%';
    }

    try {
        $query = $db->prepare($sql);
        $query->execute($params);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result) {
            sendResponse(404, 'No products found');
        }

        sendResponse(200, 'Successfully retrieved products', $result);
    } catch (PDOException $e) {
        sendResponse(500, 'Internal server error: ' . $e->getMessage());
    }
} else if ($method == 'GET' && $action == 'total') {

    if (!$id_user) {
        sendResponse(400, 'User ID is required');
    }

    $query = $db->prepare("SELECT SUM(amount * value) as total FROM products WHERE fk_user = :user_id");
    $query->execute(['user_id' => $id_user]);
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        sendResponse(404, 'No products found');
    }

    $total = number_format($result['total'], 2, '.', '');
    sendResponse(200, 'Total value calculated successfully', ['total' => $total]);
} else if ($method == 'DELETE' && $action == 'delete') {

    if (!$id_prod || !$id_user) {
        sendResponse(400, 'Product ID and User ID are required');
    }

    $query = $db->prepare("SELECT id FROM products WHERE fk_user = :user_id AND id = :id");
    $query->execute(['user_id' => $id_user, 'id' => $id_prod]);
    $resultVerify = $query->fetch(PDO::FETCH_ASSOC);

    if (!$resultVerify) {
        sendResponse(404, 'No product found');
    }

    $query = $db->prepare("DELETE FROM products WHERE id = :id AND fk_user = :user_id");
    $query->execute(['id' => $id_prod, 'user_id' => $id_user]);

    sendResponse(200, 'Product deleted successfully');
} else {
    sendResponse(404, 'Not found');
}
