<?php
$pdo = require __DIR__ . '/../config/connection.php';
require __DIR__ . '/../src/validation.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        if (!ctype_digit($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                'message' => 'ID transaksi tidak valid.'
            ]);
            exit();
        }
        $stmt = $pdo->prepare('SELECT * FROM transactions WHERE id = :id');
        $stmt->execute([
            ':id' => $_GET['id'],
        ]);

        $transaction = $stmt->fetch();
        if (!$transaction) {
            http_response_code(404);
            echo json_encode([
                'message' => 'Transaksi tidak ditemukan',    
            ]);
            exit();
        }

        echo json_encode([
            'message' => 'Data transaksi berhasil ditemukan.',
            'data' => $transaction,
        ]);

        exit();
    }
    $stmt = $pdo->query('SELECT * FROM transactions ORDER BY id ASC');
    $transactions = $stmt->fetchAll();

    echo json_encode([
        'message' => 'Data transaksi berhasil diambil.',
        'data' => $transactions,
    ]);

    exit();
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode([
            'message' => 'Data harus berupa JSON yang valid',
        ]);
        exit();
    }
            
    $errors = validateTransaction($input);

    if ($errors !== []) {
        http_response_code(422);
        echo json_encode([
            'message' => 'Data tidak valid.',
            'errors' => $errors,
        ]);
        exit();
    }

    $stmt = $pdo->prepare('INSERT INTO transactions (type, amount, transaction_date, note) VALUES (:type, :amount, :transaction_date, :note) RETURNING *');
    $stmt->execute([
        ':type' => $input['type'],
        ':amount' => $input['amount'],
        ':transaction_date' => $input['transaction_date'],
        ':note' => $input['note'] ?? null,
    ]);

    $transaction = $stmt->fetch();

    http_response_code(201);
    echo json_encode([
        'message' => 'Transaksi berhasil ditambahkan.',
        'data' => $transaction,
    ]);
    exit();
}

if ($method === 'PUT') {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'ID transaksi tidak diberikan']);
        exit();
    }

    if (!ctype_digit($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'message' => 'ID transaksi tidak valid.'
        ]);
        exit();
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode([
            'message' => 'Data harus berupa JSON yang valid',
        ]);
        exit();
    }

    $errors = validateTransaction($input);

    if ($errors !== []) {
        http_response_code(422);
        echo json_encode([
            'message' => 'Data tidak valid.',
            'errors' => $errors,
        ]);
        exit();
    }

    $stmt = $pdo->prepare('UPDATE transactions SET type = :type, amount = :amount, transaction_date = :transaction_date, note = :note WHERE id = :id RETURNING *');
    $stmt->execute([
        ':id' => $_GET['id'],
        ':type' => $input['type'],
        ':amount' => $input['amount'],
        ':transaction_date' => $input['transaction_date'],
        ':note' => $input['note'] ?? null,
    ]);

    $transaction = $stmt->fetch();

    if (!$transaction) {
        http_response_code(404);
        echo json_encode(['message' => 'Data transaksi tidak ditemukan']);
        exit();
    }

    echo json_encode([
        'message' => 'Transaksi berhasil diperbarui.',
        'data' => $transaction,
    ]);
    exit();
}

if ($method === 'DELETE') {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'ID transaksi tidak diberikan']);
        exit();
    }

    if (!ctype_digit($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'message' => 'ID transaksi tidak valid.'
        ]);
        exit();
    }

    $stmt = $pdo->prepare('DELETE FROM transactions WHERE id = :id RETURNING *');
    $stmt->execute([
        ':id' => $_GET['id'],
    ]);

    $transaction = $stmt->fetch();

    if (!$transaction) {
        http_response_code(404);
        echo json_encode([
            'message' => 'Data transaksi tidak ditemukan',
        ]);
        exit();
    }

    echo json_encode([
        'message' => 'Transaksi berhasil dihapus.',
        'data' => $transaction,
    ]);
    exit();
}

http_response_code(405);
echo json_encode(['message' => 'Method tidak diizinkan']);
