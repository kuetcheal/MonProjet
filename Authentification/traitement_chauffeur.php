<?php
session_start();

if (empty($_SESSION['Id_compte'])) {
    header('Location: /MonProjet/connexion.php');
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

$numero_permis = trim($_POST['numero_permis'] ?? '');
$numero_cni = trim($_POST['numero_cni'] ?? '');
$marque_vehicule = trim($_POST['marque_vehicule'] ?? '');
$modele_vehicule = trim($_POST['modele_vehicule'] ?? '');
$immatriculation = trim($_POST['immatriculation'] ?? '');
$couleur_vehicule = trim($_POST['couleur_vehicule'] ?? '');
$nombre_places = (int) ($_POST['nombre_places'] ?? 0);

if (
    $numero_permis === '' ||
    $numero_cni === '' ||
    $marque_vehicule === '' ||
    $modele_vehicule === '' ||
    $immatriculation === '' ||
    $couleur_vehicule === '' ||
    $nombre_places <= 0
) {
    $_SESSION['error'] = 'Veuillez remplir correctement tous les champs obligatoires.';
    header('Location: /MonProjet/Authentification/devenir_chauffeur.php');
    exit;
}

$check = $bdd->prepare("SELECT id, statut_validation FROM chauffeur_profile WHERE user_id = :user_id LIMIT 1");
$check->execute([':user_id' => $userId]);
$existingProfile = $check->fetch(PDO::FETCH_ASSOC);

if ($existingProfile && $existingProfile['statut_validation'] === 'en_attente') {
    $_SESSION['error'] = 'Vous avez déjà une demande chauffeur en attente.';
    header('Location: /MonProjet/Authentification/devenir_chauffeur.php');
    exit;
}

$uploadDir = __DIR__ . '/../uploads/chauffeurs/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

function uploadFile(string $fileInputName, string $uploadDir): ?string
{
    if (
        !isset($_FILES[$fileInputName]) ||
        $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK
    ) {
        return null;
    }

    $tmpName = $_FILES[$fileInputName]['tmp_name'];
    $originalName = basename($_FILES[$fileInputName]['name']);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
    if (!in_array($extension, $allowedExtensions, true)) {
        return null;
    }

    $newFileName = uniqid($fileInputName . '_', true) . '.' . $extension;
    $destination = $uploadDir . $newFileName;

    if (move_uploaded_file($tmpName, $destination)) {
        return 'uploads/chauffeurs/' . $newFileName;
    }

    return null;
}

$photoPermis = uploadFile('photo_permis', $uploadDir);
$photoCarteGrise = uploadFile('photo_carte_grise', $uploadDir);

if ($existingProfile) {
    $update = $bdd->prepare("
        UPDATE chauffeur_profile
        SET
            numero_permis = :numero_permis,
            numero_cni = :numero_cni,
            marque_vehicule = :marque_vehicule,
            modele_vehicule = :modele_vehicule,
            immatriculation = :immatriculation,
            couleur_vehicule = :couleur_vehicule,
            nombre_places = :nombre_places,
            photo_permis = COALESCE(:photo_permis, photo_permis),
            photo_carte_grise = COALESCE(:photo_carte_grise, photo_carte_grise),
            statut_validation = 'en_attente'
        WHERE user_id = :user_id
    ");

    $update->execute([
        ':numero_permis' => $numero_permis,
        ':numero_cni' => $numero_cni,
        ':marque_vehicule' => $marque_vehicule,
        ':modele_vehicule' => $modele_vehicule,
        ':immatriculation' => $immatriculation,
        ':couleur_vehicule' => $couleur_vehicule,
        ':nombre_places' => $nombre_places,
        ':photo_permis' => $photoPermis,
        ':photo_carte_grise' => $photoCarteGrise,
        ':user_id' => $userId
    ]);
} else {
    $insert = $bdd->prepare("
        INSERT INTO chauffeur_profile (
            user_id,
            numero_permis,
            numero_cni,
            marque_vehicule,
            modele_vehicule,
            immatriculation,
            couleur_vehicule,
            nombre_places,
            photo_permis,
            photo_carte_grise,
            statut_validation
        ) VALUES (
            :user_id,
            :numero_permis,
            :numero_cni,
            :marque_vehicule,
            :modele_vehicule,
            :immatriculation,
            :couleur_vehicule,
            :nombre_places,
            :photo_permis,
            :photo_carte_grise,
            'en_attente'
        )
    ");

    $insert->execute([
        ':user_id' => $userId,
        ':numero_permis' => $numero_permis,
        ':numero_cni' => $numero_cni,
        ':marque_vehicule' => $marque_vehicule,
        ':modele_vehicule' => $modele_vehicule,
        ':immatriculation' => $immatriculation,
        ':couleur_vehicule' => $couleur_vehicule,
        ':nombre_places' => $nombre_places,
        ':photo_permis' => $photoPermis,
        ':photo_carte_grise' => $photoCarteGrise
    ]);
}

$updateUser = $bdd->prepare("
    UPDATE user
    SET role = 'client_chauffeur'
    WHERE id = :id
");
$updateUser->execute([':id' => $userId]);

$_SESSION['user_role'] = 'client_chauffeur';
$_SESSION['success'] = 'Votre demande chauffeur a bien été envoyée. Elle est en attente de validation.';

header('Location: /MonProjet/Authentification/mon_compte.php');
exit;