<?php
require_once "config.php";
require_once "auth.php";

$page_active = "liste_devis";
$titre_page = "Détail du devis";
$description_page = "Affichage et impression du devis réseau.";
$page_css = "devis.css";

/*
    Vérification de l'id du devis
*/
$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    die("Erreur : devis introuvable.");
}

/*
    Récupération du devis avec les informations client
*/
$stmt = $pdo->prepare("
    SELECT 
        devis.*,
        clients.nom AS client_nom,
        clients.telephone AS client_telephone,
        clients.adresse AS client_adresse,
        clients.email AS client_email,
        clients.type_client AS client_type
    FROM devis
    LEFT JOIN clients ON devis.client_id = clients.id
    WHERE devis.id = ?
");

$stmt->execute([$id]);
$devis = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$devis) {
    die("Erreur : ce devis n'existe pas.");
}

/*
    Récupération des lignes du devis
*/
$stmt_lignes = $pdo->prepare("
    SELECT *
    FROM lignes_devis
    WHERE devis_id = ?
    ORDER BY id ASC
");

$stmt_lignes->execute([$id]);
$lignes = $stmt_lignes->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
?>

<section class="content">

    <div class="devis-actions no-print">
        <a href="liste_devis.php" class="btn-secondary">← Retour à la liste</a>
        <button type="button" class="btn-submit" onclick="window.print()">
            Imprimer / Enregistrer en PDF
        </button>
    </div>

    <div class="print-devis">

        <!-- EN-TÊTE DU DEVIS -->
        <div class="devis-header">

            <div class="company-info">
                <div class="company-logo">
                    <img src="images/logo.jpeg" alt="Logo Akio Service">
                </div>

                <div>
                    <h2>Akio Service</h2>
                    <p>Solutions Réseaux & Sécurité</p>
                    <p>Toliara - Madagascar</p>
                    <p>Tél : 034 00 000 00</p>
                    <p>Email : contact@akioservice.mg</p>
                </div>
            </div>

            <div class="devis-info">
                <h1>DEVIS</h1>
                <p>
                    <strong>N° :</strong>
                    <?php echo htmlspecialchars($devis["numero_devis"]); ?>
                </p>
                <p>
                    <strong>Date :</strong>
                    <?php echo date("d/m/Y", strtotime($devis["date_devis"])); ?>
                </p>
                <p>
                    <strong>Statut :</strong>
                    <span class="status">
                        <?php echo htmlspecialchars($devis["statut"]); ?>
                    </span>
                </p>
            </div>

        </div>

        <!-- INFOS CLIENT ET INSTALLATION -->
        <div class="devis-section-grid">

            <div class="devis-box">
                <h3>Client</h3>

                <p>
                    <strong>Nom :</strong>
                    <?php echo htmlspecialchars($devis["client_nom"] ?? "Client non défini"); ?>
                </p>

                <p>
                    <strong>Téléphone :</strong>
                    <?php echo htmlspecialchars($devis["client_telephone"] ?? ""); ?>
                </p>

                <p>
                    <strong>Adresse :</strong>
                    <?php echo htmlspecialchars($devis["client_adresse"] ?? ""); ?>
                </p>

                <p>
                    <strong>Email :</strong>
                    <?php echo htmlspecialchars($devis["client_email"] ?? ""); ?>
                </p>

                <p>
                    <strong>Type :</strong>
                    <?php echo htmlspecialchars($devis["client_type"] ?? ""); ?>
                </p>
            </div>

            <div class="devis-box">
                <h3>Installation</h3>

                <p>
                    <strong>Type :</strong>
                    <?php echo htmlspecialchars($devis["type_installation"]); ?>
                </p>

                <p>
                    <strong>Objet :</strong>
                    Devis pour prestation réseau, fourniture, configuration ou installation.
                </p>

                <p>
                    <strong>Validité :</strong>
                    15 jours à partir de la date du devis.
                </p>
            </div>

        </div>

        <!-- TABLEAU DES LIGNES -->
        <div class="devis-table-section">

            <h3>Détails du devis</h3>

            <?php if (count($lignes) > 0): ?>

                <table class="devis-table">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Désignation</th>
                            <?php if (isset($lignes[0]["type_ligne"])): ?>
                                <th>Type</th>
                            <?php endif; ?>
                            <th>Qté</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($lignes as $index => $ligne): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>

                                <td>
                                    <?php echo htmlspecialchars($ligne["designation"]); ?>
                                </td>

                                <?php if (isset($ligne["type_ligne"])): ?>
                                    <td>
                                        <span class="type-badge">
                                            <?php echo htmlspecialchars($ligne["type_ligne"]); ?>
                                        </span>
                                    </td>
                                <?php endif; ?>

                                <td>
                                    <?php echo number_format($ligne["quantite"], 2, ",", " "); ?>
                                </td>

                                <td>
                                    <?php echo number_format($ligne["prix_unitaire"], 0, ",", " "); ?> Ar
                                </td>

                                <td>
                                    <strong>
                                        <?php echo number_format($ligne["total"], 0, ",", " "); ?> Ar
                                    </strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else: ?>

                <div class="empty">
                    <h3>Aucune ligne dans ce devis</h3>
                    <p>Ce devis ne contient pas encore de détail.</p>
                </div>

            <?php endif; ?>

        </div>

        <!-- TOTAUX -->
        <div class="totaux-zone">

            <div class="conditions-box">
                <h3>Conditions</h3>
                <p>Le présent devis est valable pendant 15 jours.</p>
                <p>Le démarrage des travaux se fait après validation du devis.</p>
                <p>Les prix peuvent varier selon la disponibilité des matériels.</p>
            </div>

            <div class="totaux-box">
                <div class="total-row">
                    <span>Total HT</span>
                    <strong>
                        <?php echo number_format($devis["total_ht"], 0, ",", " "); ?> Ar
                    </strong>
                </div>

                <div class="total-row">
                    <span>TVA</span>
                    <strong>
                        <?php echo number_format($devis["tva"], 0, ",", " "); ?> Ar
                    </strong>
                </div>

                <div class="total-row total-final">
                    <span>Total TTC</span>
                    <strong>
                        <?php echo number_format($devis["total_ttc"], 0, ",", " "); ?> Ar
                    </strong>
                </div>
            </div>

        </div>

        <!-- SIGNATURE -->
        <div class="signature-zone">

            <div>
                <p><strong>Pour Akio Service</strong></p>
                <div class="signature-box"></div>
                <p>Signature et cachet</p>
            </div>

            <div>
                <p><strong>Bon pour accord</strong></p>
                <div class="signature-box"></div>
                <p>Signature du client</p>
            </div>

        </div>

    </div>

</section>

<?php require_once "includes/footer.php"; ?>