<?php
$pdo = new PDO("pgsql:host=localhost;dbname=ton_database", "ton_user", "ton_password");

$messageId = $_POST['message_id'] ?? null;
$userId = $_POST['user_id'] ?? null;

if ($messageId && $userId) {
    // Vérifie si déjà lu
    $stmt = $pdo->prepare("SELECT 1 FROM message_read WHERE message_id = :mid AND user_id = :uid");
    $stmt->execute(['mid' => $messageId, 'uid' => $userId]);

    if (!$stmt->fetch()) {
        $insert = $pdo->prepare("
            INSERT INTO message_read (message_id, user_id) 
            VALUES (:mid, :uid)
        ");
        $insert->execute(['mid' => $messageId, 'uid' => $userId]);
        echo "Message marqué comme lu.";
    } else {
        echo "Déjà lu.";
    }
} else {
    echo "Champs manquants.";
}
