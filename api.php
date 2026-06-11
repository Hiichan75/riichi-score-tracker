<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

require_once 'db.php';

function json_out($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function generate_room_code() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 6; $i++) $code .= $chars[random_int(0, strlen($chars) - 1)];
    return $code;
}

$action = $_GET['action'] ?? '';
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    $db = getDB();

    if ($action === 'new_session' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $code = generate_room_code();
        $check = $db->prepare('SELECT id FROM sessions WHERE room_code = ?');
        $check->execute([$code]);
        if ($check->fetch()) $code = generate_room_code();

        $stmt = $db->prepare('INSERT INTO sessions (room_code, comp_name, comp_date, player1, player2, player3, player4) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $code,
            $body['comp_name'] ?? '',
            $body['comp_date'] ?: null,
            $body['player1'] ?? 'East',
            $body['player2'] ?? 'South',
            $body['player3'] ?? 'West',
            $body['player4'] ?? 'North',
        ]);
        json_out(['room_code' => $code, 'session_id' => $db->lastInsertId()]);
    }

    if ($action === 'update_session' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = $db->prepare('UPDATE sessions SET comp_name=?, comp_date=?, player1=?, player2=?, player3=?, player4=? WHERE room_code=?');
        $stmt->execute([
            $body['comp_name'] ?? '',
            $body['comp_date'] ?: null,
            $body['player1'] ?? 'East',
            $body['player2'] ?? 'South',
            $body['player3'] ?? 'West',
            $body['player4'] ?? 'North',
            $body['room_code'],
        ]);
        json_out(['ok' => true]);
    }

    if ($action === 'get_session' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $code = strtoupper(trim($_GET['room'] ?? ''));
        $stmt = $db->prepare('SELECT * FROM sessions WHERE room_code = ?');
        $stmt->execute([$code]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$session) json_out(['error' => 'Session not found'], 404);

        $hstmt = $db->prepare('SELECT * FROM hanchans WHERE session_id = ? ORDER BY hanchan_num ASC');
        $hstmt->execute([$session['id']]);
        $hanchans = $hstmt->fetchAll(PDO::FETCH_ASSOC);
        json_out(['session' => $session, 'hanchans' => $hanchans]);
    }

    if ($action === 'add_hanchan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $code = strtoupper(trim($body['room_code'] ?? ''));
        $stmt = $db->prepare('SELECT id FROM sessions WHERE room_code = ?');
        $stmt->execute([$code]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$session) json_out(['error' => 'Session not found'], 404);

        $numStmt = $db->prepare('SELECT COUNT(*) FROM hanchans WHERE session_id = ?');
        $numStmt->execute([$session['id']]);
        $num = (int)$numStmt->fetchColumn() + 1;

        $ins = $db->prepare('INSERT INTO hanchans (session_id, hanchan_num, score1, score2, score3, score4, points1, points2, points3, points4) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $ins->execute([
            $session['id'], $num,
            $body['score1'], $body['score2'], $body['score3'], $body['score4'],
            $body['points1'], $body['points2'], $body['points3'], $body['points4'],
        ]);
        json_out(['ok' => true, 'hanchan_num' => $num]);
    }

    if ($action === 'delete_hanchan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $code = strtoupper(trim($body['room_code'] ?? ''));
        $stmt = $db->prepare('SELECT id FROM sessions WHERE room_code = ?');
        $stmt->execute([$code]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$session) json_out(['error' => 'Session not found'], 404);

        $del = $db->prepare('DELETE FROM hanchans WHERE session_id = ? AND hanchan_num = ?');
        $del->execute([$session['id'], $body['hanchan_num']]);
        json_out(['ok' => true]);
    }

    json_out(['error' => 'Unknown action'], 400);

} catch (Exception $e) {
    json_out(['error' => $e->getMessage()], 500);
}
