<?php
require_once 'connexionDb.php'; // Connexion à Appi + Symfony

$convId = $_GET['conv_id'] ?? null;
$email = $_GET['email'] ?? null;

if (!$convId || !$email) {
    echo "❌ Données manquantes.";
    exit;
}

try {
    // Vérifie que l'utilisateur est bien participant
    $stmt = $pdoAppi->prepare("
    SELECT c.id, c.subject
    FROM conversation c
    JOIN conversation_participant cp ON c.id = cp.conversation_id
    JOIN userx u ON cp.user_id = u.id
    WHERE u.email = ? AND c.id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$email, $convId]); // ✅ Les deux paramètres sont maintenant attendus

    $participant = $stmt->fetch();

    if (!$participant) {
        echo "⛔ Accès refusé à cette conversation.";
        exit;
    }

    // Récupération du sujet
    $stmt = $pdoAppi->prepare("SELECT subject FROM conversation WHERE id = ?");
    $stmt->execute([$convId]);
    $conversation = $stmt->fetch();

    // Récupération des messages
    $stmt = $pdoAppi->prepare("
    SELECT m.id, m.sender_id, u.email AS sender_email, m.content, m.sent_at AS created_at
    FROM message m
    JOIN userx u ON m.sender_id = u.id
    WHERE m.conversation_id = ?
    ORDER BY m.sent_at ASC
");
    $stmt->execute([$convId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des utilisateurs via leurs IDs
    $userIds = array_unique(array_column($messages, 'sender_id'));
    $userMap = [];

    if (!empty($userIds)) {
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        $stmt = $pdoSymfony->prepare("SELECT id, first_name, last_name, email FROM userx WHERE id IN ($placeholders)");
        $stmt->execute($userIds);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $user) {
            $userMap[$user['id']] = $user;
        }
    }

} catch (Exception $e) {
    die("❌ Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Voir la conversation</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<h2 class="object">Objet : <?= htmlspecialchars($conversation['subject']) ?></h2>


<div id="messages">
    <?php foreach ($messages as $msg): 
        $user = $userMap[$msg['sender_id']] ?? null;
        $nameDisplay = $user
            ? "{$user['first_name']} {$user['last_name']} ({$user['email']})"
            : "Utilisateur inconnu (ID: {$msg['sender_id']})";
    ?>
        <div class="message">
            <strong><?= htmlspecialchars($nameDisplay) ?></strong> :
            <p><?= nl2br(htmlspecialchars($msg['content'])) ?></p>
            <small><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></small>
            <button class="reply-btn" data-sender="<?= htmlspecialchars($msg['sender_email']) ?>" data-id="<?= $msg['id'] ?>">Répondre</button>
        </div>
    <?php endforeach; ?>
</div>

<div id="reply-box" style="display:none;">
    <h3>✏️ Répondre</h3>
    <form action="repondre.php" method="POST">
        <input type="hidden" name="conversation_id" value="<?= htmlspecialchars($convId) ?>">
        <input type="hidden" name="sender_email" value="<?= htmlspecialchars($email) ?>">
        <textarea name="message" placeholder="Votre réponse..." required></textarea><br>
        <button type="submit">Envoyer</button>
    </form>
</div>

<script src="assets/script.js"></script>
</body>
</html>
