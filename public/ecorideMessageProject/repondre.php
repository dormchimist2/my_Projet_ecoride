<?php

require_once 'connexionDb.php';

if (!isset($_POST['conversation_id'], $_POST['sender_email'], $_POST['message'])) {
    die('❌ Données manquantes.');
}

$cid = intval($_POST['conversation_id']);
$email = trim($_POST['sender_email']);
$content = trim($_POST['message']);

// Validation de base
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("❌ Adresse e-mail invalide.");
}
if (empty($content)) {
    die("❌ Le message ne peut pas être vide.");
}
if (strlen($content) > 5000) {
    die("❌ Le message est trop long (limite de 5000 caractères).");
}

try {
    // Vérification de l'utilisateur par email
    $stmt = $pdoAppi->prepare("SELECT id FROM userx WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $userId = $stmt->fetchColumn();

    if (!$userId) {
        throw new Exception("Utilisateur non trouvé.");
    }

    // Insertion du message
    $stmt = $pdoAppi->prepare("
        INSERT INTO message (conversation_id, sender_id, content)
        VALUES (:cid, :uid, :content)
    ");
    $stmt->execute([
        'cid' => $cid,
        'uid' => $userId,
        'content' => htmlspecialchars($content, ENT_QUOTES, 'UTF-8') // Empêche injection HTML
    ]);

    header("Location: voir_conversation.php?conv_id=$cid&email=$email");

    exit;

} catch (Exception $e) {
    die("❌ Erreur : " . htmlspecialchars($e->getMessage()));
}

