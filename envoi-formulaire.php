<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $destinataire = $_POST['destinataire'] ?? '';

    $nom = htmlspecialchars($_POST['nom'] ?? '');
    $prenom = htmlspecialchars($_POST['prenom'] ?? '');
    $date_naissance = htmlspecialchars($_POST['date_naissance'] ?? '');
    $lieu_naissance = htmlspecialchars($_POST['lieu_naissance'] ?? '');
    $departement = htmlspecialchars($_POST['departement'] ?? '');
    $profession = htmlspecialchars($_POST['profession'] ?? '');
    $adresse = htmlspecialchars($_POST['adresse'] ?? '');
    $code_postal = htmlspecialchars($_POST['code_postal'] ?? '');
    $ville = htmlspecialchars($_POST['ville'] ?? '');
    $telephone = htmlspecialchars($_POST['telephone'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');

    $adverse_nom = htmlspecialchars($_POST['adverse_nom'] ?? '');
    $adverse_adresse = htmlspecialchars($_POST['adverse_adresse'] ?? '');

    $faits = nl2br(htmlspecialchars($_POST['faits'] ?? ''));

    $subject = "Nouveau dossier - $prenom $nom";

    $message = "
    <html>
    <body>
        <h2>Nouveau formulaire de contact</h2>

        <h3>Informations du client</h3>

        <strong>Nom :</strong> $nom<br>
        <strong>Prénom :</strong> $prenom<br>
        <strong>Date de naissance :</strong> $date_naissance<br>
        <strong>Lieu de naissance :</strong> $lieu_naissance<br>
        <strong>Département/Pays :</strong> $departement<br>
        <strong>Profession :</strong> $profession<br>
        <strong>Adresse :</strong> $adresse<br>
        <strong>Code postal :</strong> $code_postal<br>
        <strong>Ville :</strong> $ville<br>
        <strong>Téléphone :</strong> $telephone<br>
        <strong>Email :</strong> $email<br><br>

        <h3>Partie adverse</h3>
        <strong>Nom :</strong> $adverse_nom<br>
        <strong>Adresse :</strong> $adverse_adresse<br><br>

        <h3>Résumé des faits</h3>
        <p>$faits</p>
    </body>
    </html>
    ";

    $boundary = md5(time());

    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $body .= $message . "\r\n";

    if (!empty($_FILES['documents']['name'][0])) {
        for ($i = 0; $i < count($_FILES['documents']['name']); $i++) {
            if ($_FILES['documents']['error'][$i] === 0) {

                $file_tmp = $_FILES['documents']['tmp_name'][$i];
                $file_name = basename($_FILES['documents']['name'][$i]);
                $file_data = chunk_split(base64_encode(file_get_contents($file_tmp)));

                $body .= "--$boundary\r\n";
                $body .= "Content-Type: application/octet-stream; name=\"$file_name\"\r\n";
                $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $body .= $file_data . "\r\n";
            }
        }
    }

    $body .= "--$boundary--";

    if (mail($destinataire, $subject, $body, $headers)) {
        echo "<h1>Votre dossier a été envoyé avec succès.</h1>";
        echo "<p>Le cabinet vous recontactera prochainement.</p>";
        echo "<a href='index.html'>Retour à l'accueil</a>";
    } else {
        echo "<h1>Erreur lors de l'envoi.</h1>";
        echo "<p>Veuillez réessayer ou contacter le cabinet directement.</p>";
    }
}
?>