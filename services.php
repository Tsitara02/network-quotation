<?php
require_once "config.php";
require_once "auth.php";

$page_active = "services";
$titre_page = "Prestations";
$description_page = "Gestion complète des services et prestations réseau.";
$page_css = "services.css";

/*
    SUPPRESSION PRESTATION
*/
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);

    $stmt = $pdo->prepare("DELETE FROM prestations WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: services.php?deleted=1");
    exit;
}

/*
    AJOUT OU MODIFICATION PRESTATION
*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? "";
    $nom = trim($_POST["nom"]);
    $prix_base = trim($_POST["prix_base"]);
    $description = trim($_POST["description"]);

    if (!empty($nom) && $prix_base !== "") {

        if (!empty($id)) {
            $stmt = $pdo->prepare("
                UPDATE prestations
                SET nom = ?, prix_base = ?, description = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $nom,
                $prix_base,
                $description,
                $id
            ]);

            header("Location: services.php?updated=1");
            exit;

        } else {
            $stmt = $pdo->prepare("
                INSERT INTO prestations (nom, prix_base, description)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $nom,
                $prix_base,
                $description
            ]);

            header("Location: services.php?success=1");
            exit;
        }
    }
}

/*
    LISTE DES PRESTATIONS
*/
$requete = $pdo->query("
    SELECT *
    FROM prestations
    ORDER BY id DESC
");

$prestations = $requete->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
?>

<section class="content">

    <?php if (isset($_GET["success"])): ?>
        <div class="alert success-alert">Prestation ajoutée avec succès.</div>
    <?php endif; ?>

    <?php if (isset($_GET["updated"])): ?>
        <div class="alert success-alert">Prestation modifiée avec succès.</div>
    <?php endif; ?>

    <?php if (isset($_GET["deleted"])): ?>
        <div class="alert danger-alert">Prestation supprimée avec succès.</div>
    <?php endif; ?>

    <div class="panel">

        <div class="panel-header">
            <div>
                <h2>Liste des prestations</h2>
                <p class="panel-subtitle">
                    <?php echo count($prestations); ?> prestation(s) enregistrée(s)
                </p>
            </div>

            <button type="button" class="btn-submit" onclick="openAddServiceModal()">
                + Ajouter une prestation
            </button>
        </div>

        <?php if (count($prestations) > 0): ?>

            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prix de base</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($prestations as $prestation): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($prestation["nom"]); ?></strong>
                            </td>

                            <td>
                                <?php echo number_format($prestation["prix_base"], 0, ",", " "); ?> Ar
                            </td>

                            <td>
                                <?php echo htmlspecialchars($prestation["description"]); ?>
                            </td>

                            <td>
                                <div class="table-actions">

                                    <button
                                        type="button"
                                        class="btn-edit"
                                        onclick="openEditServiceModal(
                                            '<?php echo $prestation["id"]; ?>',
                                            '<?php echo htmlspecialchars($prestation["nom"], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($prestation["prix_base"], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($prestation["description"], ENT_QUOTES); ?>'
                                        )"
                                    >
                                        Modifier
                                    </button>

                                    <button
                                        type="button"
                                        class="btn-delete"
                                        onclick="openDeleteServiceModal('<?php echo $prestation["id"]; ?>')"
                                    >
                                        Supprimer
                                    </button>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>

            <div class="empty">
                <h3>Aucune prestation enregistrée</h3>
                <p>Ajoutez vos services réseau pour préparer les futurs devis.</p>

                <button type="button" class="btn-submit" onclick="openAddServiceModal()">
                    Ajouter une prestation
                </button>
            </div>

        <?php endif; ?>

    </div>

</section>

<!-- MODAL AJOUT / MODIFICATION PRESTATION -->
<div class="modal-overlay" id="serviceModal">

    <div class="modal-box">

        <div class="modal-header">
            <h2 id="serviceModalTitle">Ajouter une prestation</h2>
            <button type="button" class="modal-close" onclick="closeServiceModal()">×</button>
        </div>

        <form method="POST" class="form">

            <input type="hidden" name="id" id="service_id">

            <div class="form-group">
                <label for="service_nom">Nom de la prestation</label>
                <input 
                    type="text" 
                    name="nom" 
                    id="service_nom"
                    placeholder="Ex : Installation Starlink" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="service_prix">Prix de base</label>
                <input 
                    type="number" 
                    name="prix_base" 
                    id="service_prix"
                    placeholder="Ex : 150000" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="service_description">Description</label>
                <textarea 
                    name="description" 
                    id="service_description"
                    placeholder="Ex : Pose, fixation, configuration et test de débit"
                ></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeServiceModal()">
                    Annuler
                </button>

                <button type="submit" class="btn-submit" id="serviceSubmitBtn">
                    Enregistrer
                </button>
            </div>

        </form>

    </div>

</div>

<!-- MODAL CONFIRMATION SUPPRESSION -->
<div class="modal-overlay" id="deleteServiceModal">

    <div class="modal-box small-modal">

        <div class="modal-header">
            <h2>Supprimer la prestation</h2>
            <button type="button" class="modal-close" onclick="closeDeleteServiceModal()">×</button>
        </div>

        <p class="delete-text">
            Voulez-vous vraiment supprimer cette prestation ? Cette action est irréversible.
        </p>

        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteServiceModal()">
                Annuler
            </button>

            <a href="#" class="btn-delete confirm-delete" id="confirmDeleteServiceBtn">
                Supprimer
            </a>
        </div>

    </div>

</div>

<?php require_once "includes/footer.php"; ?>