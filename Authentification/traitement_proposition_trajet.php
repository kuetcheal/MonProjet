<?php
session_start();

if (empty($_SESSION['Id_compte'])) {
    header('Location: /MonProjet/connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /MonProjet/Authentification/proposer_trajet.php');
    exit;
}

if (!in_array($_SESSION['user_role'] ?? '', ['client_chauffeur', 'chauffeur'], true)) {
    $_SESSION['error'] = "Accès refusé.";
    header('Location: /MonProjet/Authentification/mon_compte.php');
    exit;
}

require_once __DIR__ . '/../config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $bdd = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('Erreur de connexion à la base.');
}

$userId = (int) $_SESSION['Id_compte'];

$villeDepart = trim($_POST['villeDepart'] ?? '');
$quartierDepart = trim($_POST['quartierDepart'] ?? '');
$villeArrivee = trim($_POST['villeArrivee'] ?? '');
$quartierArrivee = trim($_POST['quartierArrivee'] ?? '');
$jourDepart = trim($_POST['jourDepart'] ?? '');
$heureDepart = trim($_POST['heureDepart'] ?? '');
$heureArrivee = trim($_POST['heureArrivee'] ?? '');
$nombrePlacesDisponibles = (int) ($_POST['nombre_places_disponibles'] ?? 0);
$prix = (float) ($_POST['prix'] ?? 0);
$commentaireChauffeur = trim($_POST['commentaire_chauffeur'] ?? '');

if (
    $villeDepart === '' ||
    $quartierDepart === '' ||
    $villeArrivee === '' ||
    $quartierArrivee === '' ||
    $jourDepart === '' ||
    $heureDepart === '' ||
    $heureArrivee === '' ||
    $nombrePlacesDisponibles <= 0 ||
    $prix <= 0
) {
    $_SESSION['error'] = "Veuillez remplir correctement tous les champs obligatoires.";
    header('Location: /MonProjet/Authentification/proposer_trajet.php');
    exit;
}

if ($villeDepart === $villeArrivee) {
    $_SESSION['error'] = "La ville de départ doit être différente de la ville d'arrivée.";
    header('Location: /MonProjet/Authentification/proposer_trajet.php');
    exit;
}

$chauffeurStmt = $bdd->prepare("
    SELECT statut_validation, nombre_places
    FROM chauffeur_profile
    WHERE user_id = :user_id
    LIMIT 1
");
$chauffeurStmt->execute([':user_id' => $userId]);
$chauffeur = $chauffeurStmt->fetch(PDO::FETCH_ASSOC);

if (!$chauffeur || ($chauffeur['statut_validation'] ?? '') !== 'valide') {
    $_SESSION['error'] = "Votre profil chauffeur n'est pas validé.";
    header('Location: /MonProjet/Authentification/mon_compte.php');
    exit;
}

$maxPlaces = (int) ($chauffeur['nombre_places'] ?? 0);
if ($nombrePlacesDisponibles > $maxPlaces) {
    $_SESSION['error'] = "Le nombre de places proposées dépasse la capacité déclarée dans votre profil chauffeur.";
    header('Location: /MonProjet/Authentification/proposer_trajet.php');
    exit;
}

/**
 * Encadrement simple du prix
 * Exemple : tu imposes une fourchette minimale et maximale
 */
if ($prix < 1000 || $prix > 50000) {
    $_SESSION['error'] = "Le prix proposé est hors plage autorisée.";
    header('Location: /MonProjet/Authentification/proposer_trajet.php');
    exit;
}

$commissionPlateforme = round($prix * 0.10, 2);
$montantChauffeur = round($prix - $commissionPlateforme, 2);

try {
    $bdd->beginTransaction();

    $insert = $bdd->prepare("
        INSERT INTO voyage (
            villeDepart,
            quartierDepart,
            villeArrivee,
            quartierArrivee,
            typeBus,
            modeTransport,
            chauffeur_id,
            statut_trajet,
            prix,
            prix_par_place,
            commission_plateforme,
            montant_chauffeur,
            nombre_places_disponibles,
            commentaire_chauffeur,
            heureDepart,
            heureArrivee,
            jourDepart
        ) VALUES (
            :villeDepart,
            :quartierDepart,
            :villeArrivee,
            :quartierArrivee,
            :typeBus,
            :modeTransport,
            :chauffeur_id,
            :statut_trajet,
            :prix,
            :prix_par_place,
            :commission_plateforme,
            :montant_chauffeur,
            :nombre_places_disponibles,
            :commentaire_chauffeur,
            :heureDepart,
            :heureArrivee,
            :jourDepart
        )
    ");

    $insert->execute([
        ':villeDepart' => $villeDepart,
        ':quartierDepart' => $quartierDepart,
        ':villeArrivee' => $villeArrivee,
        ':quartierArrivee' => $quartierArrivee,
        ':typeBus' => 'Personnel',
        ':modeTransport' => 'covoiturage',
        ':chauffeur_id' => $userId,
        ':statut_trajet' => 'en_attente',
        ':prix' => $prix,
        ':prix_par_place' => $prix,
        ':commission_plateforme' => $commissionPlateforme,
        ':montant_chauffeur' => $montantChauffeur,
        ':nombre_places_disponibles' => $nombrePlacesDisponibles,
        ':commentaire_chauffeur' => $commentaireChauffeur !== '' ? $commentaireChauffeur : null,
        ':heureDepart' => $heureDepart,
        ':heureArrivee' => $heureArrivee,
        ':jourDepart' => $jourDepart
    ]);

    $notifAdmin = $bdd->prepare("
        INSERT INTO notifications (
            cible_role,
            type_notification,
            titre,
            message,
            lien,
            is_read
        ) VALUES (
            'admin',
            :type_notification,
            :titre,
            :message,
            :lien,
            0
        )
    ");

    $notifAdmin->execute([
        ':type_notification' => 'nouveau_trajet_covoiturage',
        ':titre' => 'Nouveau trajet covoiturage à valider',
        ':message' => 'Un chauffeur a proposé un nouveau trajet covoiturage en attente de validation.',
        ':lien' => '/MonProjet/Admins/covoiturages.php'
    ]);

    $bdd->commit();

    $_SESSION['success'] = "Votre trajet a bien été soumis. Il est en attente de validation par l'administrateur.";
    header('Location: /MonProjet/Authentification/mon_compte.php');
    exit;
} catch (PDOException $e) {
    if ($bdd->inTransaction()) {
        $bdd->rollBack();
    }

    $_SESSION['error'] = "Une erreur est survenue lors de l'enregistrement du trajet.";
    header('Location: /MonProjet/Authentification/proposer_trajet.php');
    exit;
}