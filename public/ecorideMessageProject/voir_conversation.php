<?php
require_once 'connexionDb.php';

$email = $_GET['email'] ?? null;

if (!$email) {
    echo "Vous n'avez pas encore de message.";
    exit;
}

try {
    $stmt = $pdoAppi->prepare("
        SELECT c.id, c.subject, u.name
        FROM conversation c
        JOIN (
            SELECT DISTINCT ON (m.conversation_id) m.conversation_id, m.sender_id
            FROM message m
            ORDER BY m.conversation_id, m.sent_at ASC
        ) first_msg ON c.id = first_msg.conversation_id
        JOIN userx u ON first_msg.sender_id = u.id
        JOIN conversation_participant cp ON c.id = cp.conversation_id
        JOIN userx ux ON cp.user_id = ux.id
        WHERE ux.email = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$email]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur DB : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Conversations</title>
    <link rel="stylesheet" href="assets/style.css?v=2">
</head>
<body>
    
    <ul class="conversations">
        <?php foreach ($conversations as $conv): 
            $convId = urlencode($conv['id']);
            $subject = htmlspecialchars($conv['subject']);
            $pseudo = htmlspecialchars($conv['name']);
        ?>
            <li>
                <a href="voir_messages.php?conv_id=<?= $convId ?>&email=<?= urlencode($email) ?>" target='messageFrame'>
                     <?= $subject ?>
                </a>
                <span class="pseudo"> <?= $pseudo ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
