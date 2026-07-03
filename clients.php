<?php
require_once "config.php";
require_once "auth.php";

$page_active = "clients";
$titre_page = "Clients";
$description_page = "Gestion complète des clients avec fenêtres modales.";
$page_css = "clients.css";

/*
    SUPPRESSION CLIENT
*/
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);

    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: clients.php?deleted=1");
    exit;
}

/*
    AJOUT OU MODIFICATION CLIENT
*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? "";
    $nom = trim($_POST["nom"]);
    $telephone = trim($_POST["telephone"]);
    $adresse = trim($_POST["adresse"]);
    $email = trim($_POST["email"]);
    $type_client = trim($_POST["type_client"]);

    if (!empty($nom)) {

        if (!empty($id)) {
            $stmt = $pdo->prepare("
                UPDATE clients
                SET nom = ?, telephone = ?, adresse = ?, email = ?, type_client = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $nom,
                $telephone,
                $adresse,
                $email,
                $type_client,
                $id
            ]);

            header("Location: clients.php?updated=1");
            exit;

        } else {
            $stmt = $pdo->prepare("
                INSERT INTO clients (nom, telephone, adresse, email, type_client)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $nom,
                $telephone,
                $adresse,
                $email,
                $type_client
            ]);

            header("Location: clients.php?success=1");
            exit;
        }
    }
}

/*
    LISTE DES CLIENTS
*/
$requete = $pdo->query("
    SELECT *
    FROM clients
    ORDER BY id DESC
");

$clients = $requete->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
?>

<section class="content">

    <?php if (isset($_GET["success"])): ?>
        <div class="alert success-alert">Client ajouté avec succès.</div>
    <?php endif; ?>

    <?php if (isset($_GET["updated"])): ?>
        <div class="alert success-alert">Client modifié avec succès.</div>
    <?php endif; ?>

    <?php if (isset($_GET["deleted"])): ?>
        <div class="alert danger-alert">Client supprimé avec succès.</div>
    <?php endif; ?>

    <div class="panel">

        <div class="panel-header">
            <div>
                <h2>Liste des clients</h2>
                <p class="panel-subtitle">
                    <?php echo count($clients); ?> client(s) enregistré(s)
                </p>
            </div>

            <button type="button" class="btn-submit" onclick="openAddClientModal()">
                + Ajouter un client
            </button>
        </div>

        <?php if (count($clients) > 0): ?>

            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th>Type</th>
                        <th>Adresse</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($client["nom"]); ?></strong>
                            </td>

                            <td><?php echo htmlspecialchars($client["telephone"]); ?></td>

                            <td>
                                <span class="status">
                                    <?php echo htmlspecialchars($client["type_client"]); ?>
                                </span>
                            </td>

                            <td><?php echo htmlspecialchars($client["adresse"]); ?></td>

                            <td><?php echo htmlspecialchars($client["email"]); ?></td>

                            <td>
                                <div class="table-actions">

                                    <button
                                        type="button"
                                        class="btn-edit"
                                        onclick="openEditClientModal(
                                            '<?php echo $client["id"]; ?>',
                                            '<?php echo htmlspecialchars($client["nom"], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($client["telephone"], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($client["adresse"], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($client["email"], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($client["type_client"], ENT_QUOTES); ?>'
                                        )"
                                    >
                                        Modifier
                                    </button>

                                    <button
                                        type="button"
                                        class="btn-delete"
                                        onclick="openDeleteClientModal('<?php echo $client["id"]; ?>')"
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
                <h3>Aucun client enregistré</h3>
                <p>Ajoutez votre premier client pour commencer à créer des devis.</p>
                <button type="button" class="btn-submit" onclick="openAddClientModal()">
                    Ajouter un client
                </button>
            </div>

        <?php endif; ?>

    </div>

</section>

<!-- MODAL AJOUT / MODIFICATION CLIENT -->
<div class="modal-overlay" id="clientModal">

    <div class="modal-box">

        <div class="modal-header">
            <h2 id="clientModalTitle">Ajouter un client</h2>
            <button type="button" class="modal-close" onclick="closeClientModal()">×</button>
        </div>

        <form method="POST" class="form">

            <input type="hidden" name="id" id="client_id">

            <div class="form-group">
                <label for="client_nom">Nom du client</label>
                <input type="text" name="nom" id="client_nom" placeholder="Ex : Hôtel Toliara" required>
            </div>

            <div class="form-group">
                <label for="client_telephone">Téléphone</label>
                <input type="text" name="telephone" id="client_telephone" placeholder="Ex : 034 00 000 00">
            </div>

            <div class="form-group">
                <label for="client_adresse">Adresse</label>
                <input type="text" name="adresse" id="client_adresse" placeholder="Ex : Toliara">
            </div>

            <div class="form-group">
                <label for="client_email">Email</label>
                <input type="email" name="email" id="client_email" placeholder="Ex : client@email.com">
            </div>

            <div class="form-group">
                <label for="client_type">Type de client</label>
                <select name="type_client" id="client_type">
                    <option value="Particulier">Particulier</option>
                    <option value="Entreprise">Entreprise</option>
                    <option value="Hôtel">Hôtel</option>
                    <option value="École">École</option>
                    <option value="ONG">ONG</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeClientModal()">
                    Annuler
                </button>

                <button type="submit" class="btn-submit" id="clientSubmitBtn">
                    Enregistrer
                </button>
            </div>

        </form>

    </div>

</div>

<!-- MODAL CONFIRMATION SUPPRESSION -->
<div class="modal-overlay" id="deleteClientModal">

    <div class="modal-box small-modal">

        <div class="modal-header">
            <h2>Supprimer le client</h2>
            <button type="button" class="modal-close" onclick="closeDeleteClientModal()">×</button>
        </div>

        <p class="delete-text">
            Voulez-vous vraiment supprimer ce client ? Cette action est irréversible.
        </p>

        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteClientModal()">
                Annuler
            </button>

            <a href="#" class="btn-delete confirm-delete" id="confirmDeleteClientBtn">
                Supprimer
            </a>
        </div>

    </div>

</div>

<?php require_once "includes/footer.php"; ?>