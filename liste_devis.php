<?php
require_once "config.php";
require_once "auth.php";

$page_active = "liste_devis";
$titre_page = "Liste des devis";
$description_page = "Historique des devis réseau créés.";
$page_css = "devis.css";

/*
    SUPPRESSION D'UN DEVIS
    On supprime d'abord les lignes, puis le devis.
*/
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);

    try {
        $pdo->beginTransaction();

        $stmt_lignes = $pdo->prepare("DELETE FROM lignes_devis WHERE devis_id = ?");
        $stmt_lignes->execute([$id]);

        $stmt_devis = $pdo->prepare("DELETE FROM devis WHERE id = ?");
        $stmt_devis->execute([$id]);

        $pdo->commit();

        header("Location: liste_devis.php?deleted=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erreur lors de la suppression du devis : " . $e->getMessage());
    }
}

/*
    CHANGEMENT DU STATUT
*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_statut"])) {
    $id = intval($_POST["devis_id"]);
    $statut = trim($_POST["statut"]);

    $stmt = $pdo->prepare("
        UPDATE devis
        SET statut = ?
        WHERE id = ?
    ");

    $stmt->execute([$statut, $id]);

    header("Location: liste_devis.php?updated=1");
    exit;
}

/*
    Récupération de tous les devis avec le nom du client
*/
$requete = $pdo->query("
    SELECT 
        devis.id,
        devis.numero_devis,
        devis.type_installation,
        devis.total_ht,
        devis.tva,
        devis.total_ttc,
        devis.statut,
        devis.date_devis,
        clients.nom AS client_nom,
        clients.telephone AS client_telephone
    FROM devis
    LEFT JOIN clients ON devis.client_id = clients.id
    ORDER BY devis.id DESC
");

$devis_liste = $requete->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
?>

<section class="content">

    <?php if (isset($_GET["deleted"])): ?>
        <div class="alert danger-alert">
            Devis supprimé avec succès.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET["updated"])): ?>
        <div class="alert success-alert">
            Statut du devis modifié avec succès.
        </div>
    <?php endif; ?>

    <div class="panel">

        <div class="panel-header">
            <div>
                <h2>Liste des devis</h2>
                <p class="panel-subtitle">
                    <?php echo count($devis_liste); ?> devis enregistré(s)
                </p>
            </div>

            <a href="nouveau_devis.php" class="btn-submit">
                + Nouveau devis
            </a>
        </div>

        <?php if (count($devis_liste) > 0): ?>

            <div class="table-wrapper">

                <table>
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Client</th>
                            <th>Type d’installation</th>
                            <th>Total HT</th>
                            <th>TVA</th>
                            <th>Total TTC</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($devis_liste as $devis): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?php echo htmlspecialchars($devis["numero_devis"]); ?>
                                    </strong>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($devis["client_nom"] ?? "Client non défini"); ?>

                                    <?php if (!empty($devis["client_telephone"])): ?>
                                        <br>
                                        <small>
                                            <?php echo htmlspecialchars($devis["client_telephone"]); ?>
                                        </small>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($devis["type_installation"]); ?>
                                </td>

                                <td>
                                    <?php echo number_format($devis["total_ht"], 0, ",", " "); ?> Ar
                                </td>

                                <td>
                                    <?php echo number_format($devis["tva"], 0, ",", " "); ?> Ar
                                </td>

                                <td>
                                    <strong>
                                        <?php echo number_format($devis["total_ttc"], 0, ",", " "); ?> Ar
                                    </strong>
                                </td>

                                <td>
                                    <span class="status">
                                        <?php echo htmlspecialchars($devis["statut"]); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php echo date("d/m/Y", strtotime($devis["date_devis"])); ?>
                                </td>

                                <td>
                                    <div class="table-actions">

                                        <a 
                                            href="voir_devis.php?id=<?php echo $devis["id"]; ?>" 
                                            class="small-btn"
                                        >
                                            Voir
                                        </a>

                                        <button
                                            type="button"
                                            class="btn-edit"
                                            onclick="openStatutDevisModal(
                                                '<?php echo $devis["id"]; ?>',
                                                '<?php echo htmlspecialchars($devis["numero_devis"], ENT_QUOTES); ?>',
                                                '<?php echo htmlspecialchars($devis["statut"], ENT_QUOTES); ?>'
                                            )"
                                        >
                                            Statut
                                        </button>

                                        <button
                                            type="button"
                                            class="btn-delete"
                                            onclick="openDeleteDevisModal(
                                                '<?php echo $devis["id"]; ?>',
                                                '<?php echo htmlspecialchars($devis["numero_devis"], ENT_QUOTES); ?>'
                                            )"
                                        >
                                            Supprimer
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>

        <?php else: ?>

            <div class="empty">
                <h3>Aucun devis enregistré</h3>
                <p>Créez votre premier devis réseau depuis la page Nouveau devis.</p>

                <a href="nouveau_devis.php">
                    Créer un devis
                </a>
            </div>

        <?php endif; ?>

    </div>

</section>

<!-- MODAL MODIFIER STATUT -->
<div class="modal-overlay" id="statutDevisModal">

    <div class="modal-box small-modal">

        <div class="modal-header">
            <h2>Modifier le statut</h2>
            <button type="button" class="modal-close" onclick="closeStatutDevisModal()">×</button>
        </div>

        <p class="delete-text">
            Devis : <strong id="statutDevisNumero"></strong>
        </p>

        <form method="POST" class="form">

            <input type="hidden" name="update_statut" value="1">
            <input type="hidden" name="devis_id" id="statut_devis_id">

            <div class="form-group">
                <label>Nouveau statut</label>
                <select name="statut" id="statut_devis_value">
                    <option value="Brouillon">Brouillon</option>
                    <option value="Envoyé">Envoyé</option>
                    <option value="Accepté">Accepté</option>
                    <option value="Refusé">Refusé</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeStatutDevisModal()">
                    Annuler
                </button>

                <button type="submit" class="btn-submit">
                    Enregistrer
                </button>
            </div>

        </form>

    </div>

</div>

<!-- MODAL SUPPRESSION DEVIS -->
<div class="modal-overlay" id="deleteDevisModal">

    <div class="modal-box small-modal">

        <div class="modal-header">
            <h2>Supprimer le devis</h2>
            <button type="button" class="modal-close" onclick="closeDeleteDevisModal()">×</button>
        </div>

        <p class="delete-text">
            Voulez-vous vraiment supprimer le devis 
            <strong id="deleteDevisNumero"></strong> ?
            <br>
            Toutes les lignes liées à ce devis seront aussi supprimées.
        </p>

        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteDevisModal()">
                Annuler
            </button>

            <a href="#" class="btn-delete confirm-delete" id="confirmDeleteDevisBtn">
                Supprimer
            </a>
        </div>

    </div>

</div>

<?php require_once "includes/footer.php"; ?>