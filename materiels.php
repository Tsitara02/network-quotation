<?php
require_once "config.php";

$page_active = "materiels";
$titre_page = "Matériels";
$description_page = "Gestion complète des équipements réseau.";
$page_css = "materiels.css";

/*
    SUPPRESSION MATÉRIEL
*/
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);

    $stmt = $pdo->prepare("DELETE FROM materiels WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: materiels.php?deleted=1");
    exit;
}

/*
    AJOUT OU MODIFICATION MATÉRIEL
*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? "";
    $nom = trim($_POST["nom"]);
    $categorie = trim($_POST["categorie"]);
    $prix_unitaire = trim($_POST["prix_unitaire"]);
    $unite = trim($_POST["unite"]);
    $actif = isset($_POST["actif"]) ? intval($_POST["actif"]) : 1;

    if (!empty($nom) && $prix_unitaire !== "") {

        if (!empty($id)) {
            $stmt = $pdo->prepare("
                UPDATE materiels
                SET nom = ?, categorie = ?, prix_unitaire = ?, unite = ?, actif = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $nom,
                $categorie,
                $prix_unitaire,
                $unite,
                $actif,
                $id
            ]);

            header("Location: materiels.php?updated=1");
            exit;

        } else {
            $stmt = $pdo->prepare("
                INSERT INTO materiels (nom, categorie, prix_unitaire, unite, actif)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $nom,
                $categorie,
                $prix_unitaire,
                $unite,
                $actif
            ]);

            header("Location: materiels.php?success=1");
            exit;
        }
    }
}

/*
    LISTE DES MATÉRIELS
*/
$requete = $pdo->query("
    SELECT *
    FROM materiels
    ORDER BY id DESC
");

$materiels = $requete->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
?>

<section class="content">

    <?php if (isset($_GET["success"])): ?>
        <div class="alert success-alert">Matériel ajouté avec succès.</div>
    <?php endif; ?>

    <?php if (isset($_GET["updated"])): ?>
        <div class="alert success-alert">Matériel modifié avec succès.</div>
    <?php endif; ?>

    <?php if (isset($_GET["deleted"])): ?>
        <div class="alert danger-alert">Matériel supprimé avec succès.</div>
    <?php endif; ?>

    <div class="panel">

        <div class="panel-header">
            <div>
                <h2>Liste des matériels</h2>
                <p class="panel-subtitle">
                    <?php echo count($materiels); ?> matériel(s) enregistré(s)
                </p>
            </div>

            <button type="button" class="btn-submit" onclick="openAddMaterielModal()">
                + Ajouter un matériel
            </button>
        </div>

        <?php if (count($materiels) > 0): ?>

            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix unitaire</th>
                        <th>Unité</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($materiels as $materiel): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($materiel["nom"]); ?></strong>
                            </td>

                            <td>
                                <span class="status">
                                    <?php echo htmlspecialchars($materiel["categorie"]); ?>
                                </span>
                            </td>

                            <td>
                                <?php echo number_format($materiel["prix_unitaire"], 0, ",", " "); ?> Ar
                            </td>

                            <td>
                                <?php echo htmlspecialchars($materiel["unite"]); ?>
                            </td>

                            <td>
                                <?php if ($materiel["actif"] == 1): ?>
                                    <span class="status-green">Actif</span>
                                <?php else: ?>
                                    <span class="status-red">Inactif</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="table-actions">

                                    <button
                                        type="button"
                                        class="btn-edit"
                                        onclick="openEditMaterielModal(
                                            '<?php echo $materiel["id"]; ?>',
                                            '<?php echo htmlspecialchars($materiel["nom"], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($materiel["categorie"], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($materiel["prix_unitaire"], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($materiel["unite"], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($materiel["actif"], ENT_QUOTES); ?>'
                                        )"
                                    >
                                        Modifier
                                    </button>

                                    <button
                                        type="button"
                                        class="btn-delete"
                                        onclick="openDeleteMaterielModal('<?php echo $materiel["id"]; ?>')"
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
                <h3>Aucun matériel enregistré</h3>
                <p>Ajoutez vos équipements réseau pour préparer les futurs devis.</p>
                <button type="button" class="btn-submit" onclick="openAddMaterielModal()">
                    Ajouter un matériel
                </button>
            </div>

        <?php endif; ?>

    </div>

</section>

<!-- MODAL AJOUT / MODIFICATION MATÉRIEL -->
<div class="modal-overlay" id="materielModal">

    <div class="modal-box">

        <div class="modal-header">
            <h2 id="materielModalTitle">Ajouter un matériel</h2>
            <button type="button" class="modal-close" onclick="closeMaterielModal()">×</button>
        </div>

        <form method="POST" class="form">

            <input type="hidden" name="id" id="materiel_id">

            <div class="form-group">
                <label for="materiel_nom">Nom du matériel</label>
                <input type="text" name="nom" id="materiel_nom" placeholder="Ex : MikroTik hAP ac²" required>
            </div>

            <div class="form-group">
                <label for="materiel_categorie">Catégorie</label>
                <select name="categorie" id="materiel_categorie">
                    <option value="Routeur">Routeur</option>
                    <option value="CPE">CPE</option>
                    <option value="Switch">Switch</option>
                    <option value="Caméra">Caméra</option>
                    <option value="Câble">Câble</option>
                    <option value="Connecteur">Connecteur</option>
                    <option value="Support">Support</option>
                    <option value="Protection">Protection</option>
                    <option value="Accessoire">Accessoire</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>

            <div class="form-group">
                <label for="materiel_prix">Prix unitaire</label>
                <input type="number" name="prix_unitaire" id="materiel_prix" placeholder="Ex : 350000" required>
            </div>

            <div class="form-group">
                <label for="materiel_unite">Unité</label>
                <select name="unite" id="materiel_unite">
                    <option value="pièce">pièce</option>
                    <option value="mètre">mètre</option>
                    <option value="rouleau">rouleau</option>
                    <option value="paquet">paquet</option>
                    <option value="service">service</option>
                </select>
            </div>

            <div class="form-group">
                <label for="materiel_actif">État</label>
                <select name="actif" id="materiel_actif">
                    <option value="1">Actif</option>
                    <option value="0">Inactif</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeMaterielModal()">
                    Annuler
                </button>

                <button type="submit" class="btn-submit" id="materielSubmitBtn">
                    Enregistrer
                </button>
            </div>

        </form>

    </div>

</div>

<!-- MODAL CONFIRMATION SUPPRESSION -->
<div class="modal-overlay" id="deleteMaterielModal">

    <div class="modal-box small-modal">

        <div class="modal-header">
            <h2>Supprimer le matériel</h2>
            <button type="button" class="modal-close" onclick="closeDeleteMaterielModal()">×</button>
        </div>

        <p class="delete-text">
            Voulez-vous vraiment supprimer ce matériel ? Cette action est irréversible.
        </p>

        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteMaterielModal()">
                Annuler
            </button>

            <a href="#" class="btn-delete confirm-delete" id="confirmDeleteMaterielBtn">
                Supprimer
            </a>
        </div>

    </div>

</div>

<?php require_once "includes/footer.php"; ?>