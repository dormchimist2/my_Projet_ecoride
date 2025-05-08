<?php
/*
require_once 'connexionDb.php';

if (!isset($_POST['subject'], $_POST['participants'], $_POST['sender_email'], $_POST['message'])) {
    die('❌ Formulaire incomplet.');
}

$subject = trim($_POST['subject']);
$emails = array_filter(array_map('trim', explode(',', $_POST['participants'])));
$senderEmail = trim($_POST['sender_email']);
$firstMessage = trim($_POST['message']);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT id FROM userx WHERE email = :email");
    $stmt->execute(['email' => $senderEmail]);
    $senderId = $stmt->fetchColumn();

    if (!$senderId) {
        throw new Exception("L'expéditeur n'existe pas.");
    }

    // Insérer la conversation avec le sujet
    $stmt = $pdo->prepare("INSERT INTO conversation (subject) VALUES (:subject) RETURNING id");
    $stmt->execute(['subject' => $subject]);
    $conversationId = $stmt->fetchColumn();

    $stmtUserId = $pdo->prepare("SELECT id FROM userx WHERE email = :email");
    $stmtInsert = $pdo->prepare("
        INSERT INTO conversation_participant (conversation_id, user_id)
        VALUES (:conversation_id, :user_id)
    ");

    foreach ($emails as $email) {
        $stmtUserId->execute(['email' => $email]);
        $userId = $stmtUserId->fetchColumn();

        if ($userId) {
            $stmtInsert->execute([
                'conversation_id' => $conversationId,
                'user_id' => $userId
            ]);
        } else {
            throw new Exception("Utilisateur non trouvé : $email");
        }
    }

    if (!in_array($senderEmail, $emails)) {
        $stmtInsert->execute([
            'conversation_id' => $conversationId,
            'user_id' => $senderId
        ]);
    }

    $stmt = $pdo->prepare("
        INSERT INTO message (conversation_id, sender_id, content)
        VALUES (:conversation_id, :sender_id, :content)
    ");
    $stmt->execute([
        'conversation_id' => $conversationId,
        'sender_id' => $senderId,
        'content' => $firstMessage
    ]);

    $pdo->commit();
    echo "✅ Conversation créée avec succès. ID : " . $conversationId;

} catch (Exception $e) {
    $pdo->rollBack();
    echo "❌ Erreur : " . $e->getMessage();
}
?>
*/

require_once 'connexionDb.php'; // Définit $pdoAppi et $pdoSymfony

if (!isset($_POST['subject'], $_POST['participants'], $_POST['sender_email'], $_POST['message'])) {
    die('❌ Formulaire incomplet.');
}

$subject = trim($_POST['subject']);
$emails = array_filter(array_map('trim', explode(',', $_POST['participants'])));
$senderEmail = trim($_POST['sender_email']);
$firstMessage = trim($_POST['message']);

try {
    $pdoAppi->beginTransaction();

    // Vérifier l'expéditeur dans Symfony
    $stmt = $pdoSymfony->prepare("SELECT id, email, first_name, last_name FROM userx WHERE email = :email");
    $stmt->execute(['email' => $senderEmail]);
    $sender = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sender) {
        throw new Exception("❌ L'expéditeur n'existe pas dans la base principale.");
    }

    // Insérer l'expéditeur dans APPI s'il n'existe pas déjà
    $stmt = $pdoAppi->prepare("INSERT INTO userx (id, email, name) VALUES (:id, :email, :name) ON CONFLICT (id) DO NOTHING");
    $stmt->execute([
        'id' => $sender['id'],
        'email' => $sender['email'],
        'name' => $sender['first_name'] . ' ' . $sender['last_name']
    ]);
    $senderId = $sender['id'];

    // Créer la conversation
    $stmt = $pdoAppi->prepare("INSERT INTO conversation (subject) VALUES (:subject) RETURNING id");
    $stmt->execute(['subject' => $subject]);
    $conversationId = $stmt->fetchColumn();

    // Préparer insertion des participants
    $stmtSymfonyUser = $pdoSymfony->prepare("SELECT id, email, first_name, last_name FROM userx WHERE email = :email");
    $stmtInsertUserAppi = $pdoAppi->prepare("INSERT INTO userx (id, email, name) VALUES (:id, :email, :name) ON CONFLICT (id) DO NOTHING");
    $stmtInsertParticipant = $pdoAppi->prepare("
        INSERT INTO conversation_participant (conversation_id, user_id)
        VALUES (:conversation_id, :user_id)
    ");

    foreach ($emails as $email) {
        $stmtSymfonyUser->execute(['email' => $email]);
        $user = $stmtSymfonyUser->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Insérer dans APPI.userx si nécessaire
            $stmtInsertUserAppi->execute([
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['first_name'] . ' ' . $user['last_name']
            ]);

            // Lier à la conversation
            $stmtInsertParticipant->execute([
                'conversation_id' => $conversationId,
                'user_id' => $user['id']
            ]);
        } else {
            throw new Exception("❌ Participant introuvable dans la base principale : $email");
        }
    }

    // Ajouter l'expéditeur comme participant si pas déjà
    if (!in_array($senderEmail, $emails)) {
        $stmtInsertParticipant->execute([
            'conversation_id' => $conversationId,
            'user_id' => $senderId
        ]);
    }

    // Ajouter le premier message (sans user_id)
    $stmt = $pdoAppi->prepare("
        INSERT INTO message (conversation_id, sender_id, content)
        VALUES (:conversation_id, :sender_id, :content)
    ");
    $stmt->execute([
        'conversation_id' => $conversationId,
        'sender_id' => $senderId,
        'content' => $firstMessage
    ]);

    $pdoAppi->commit();
    echo "Conversation envoyée avec succès." ;

} catch (Exception $e) {
    $pdoAppi->rollBack();
    echo "❌ Erreur : " . $e->getMessage();
}
?>
