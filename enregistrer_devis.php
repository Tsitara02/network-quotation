<?php
require_once "config.php";

/*
    Vérifier que le formulaire vient bien de nouveau_devis.php
*/
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: nouveau_devis.php");
    exit;
}

/*
    Récupération des informations principales
*/
$client_id = intval($_POST["client_id"] ?? 0);
$type_installation = trim($_POST["type_installation"] ?? "");
$total_ht = floatval($_POST["total_ht"] ?? 0);
$tva = floatval($_POST["tva"] ?? 0);
$total_ttc = floatval($_POST["total_ttc"] ?? 0);
$statut = trim($_POST["statut"] ?? "Brouillon");
$lignes = $_POST["lignes"] ?? [];

/*
    Vérification simple
*/
if ($client_id <= 0 || empty($type_installation) || empty($lignes)) {
    die("Erreur : données du devis incomplètes.");
}

/*
    Génération du numéro de devis
    Exemple : DEV-2026-0001
*/
$annee = date("Y");

$stmt_count = $pdo->query("SELECT COUNT(*) FROM devis");
$nombre_devis = $stmt_count->fetchColumn();

$numero_devis = "DEV-" . $annee . "-" . str_pad($nombre_devis + 1, 4, "0", STR_PAD_LEFT);

try {
    /*
        Transaction : si une erreur arrive, tout est annulé
    */
    $pdo->beginTransaction();

    /*
        Enregistrement du devis principal
    */
    $stmt_devis = $pdo->prepare("
        INSERT INTO devis (
            numero_devis,
            client_id,
            type_installation,
            total_ht,
            tva,
            total_ttc,
            statut
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt_devis->execute([
        $numero_devis,
        $client_id,
        $type_installation,
        $total_ht,
        $tva,
        $total_ttc,
        $statut
    ]);

    /*
        ID du devis créé
    */
    $devis_id = $pdo->lastInsertId();

    /*
        Enregistrement des lignes avec type_ligne
    */
    $stmt_ligne = $pdo->prepare("
        INSERT INTO lignes_devis (
            devis_id,
            designation,
            type_ligne,
            quantite,
            prix_unitaire,
            total
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($lignes as $ligne) {
        $designation = trim($ligne["designation"] ?? "");
        $type_ligne = trim($ligne["type"] ?? "Libre");
        $quantite = floatval($ligne["quantite"] ?? 0);
        $prix_unitaire = floatval($ligne["prix_unitaire"] ?? 0);
        $total_ligne = $quantite * $prix_unitaire;

        if (!empty($designation) && $quantite > 0) {
            $stmt_ligne->execute([
                $devis_id,
                $designation,
                $type_ligne,
                $quantite,
                $prix_unitaire,
                $total_ligne
            ]);
        }
    }

    /*
        Validation de l'enregistrement
    */
    $pdo->commit();

    /*
        Redirection vers le devis créé
    */
    header("Location: voir_devis.php?id=" . $devis_id);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();

    die("Erreur lors de l'enregistrement du devis : " . $e->getMessage());
}