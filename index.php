<?php
require_once "config.php";
require_once "auth.php";

$page_active = "dashboard";
$titre_page = "Tableau de bord";
$description_page = "Vue générale de vos clients, matériels, prestations et devis réseau.";
$page_css = "dashboard.css";

$total_clients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$total_materiels = $pdo->query("SELECT COUNT(*) FROM materiels")->fetchColumn();
$total_prestations = $pdo->query("SELECT COUNT(*) FROM prestations")->fetchColumn();
$total_devis = $pdo->query("SELECT COUNT(*) FROM devis")->fetchColumn();

$total_chiffre = $pdo->query("SELECT COALESCE(SUM(total_ttc), 0) FROM devis")->fetchColumn();

$requete = $pdo->query("
    SELECT 
        devis.id,
        devis.numero_devis,
        devis.total_ttc,
        devis.statut,
        devis.date_devis,
        clients.nom AS nom_client
    FROM devis
    LEFT JOIN clients ON devis.client_id = clients.id
    ORDER BY devis.id DESC
    LIMIT 5
");

$derniers_devis = $requete->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
?>

<section class="content">

    <div class="welcome-card">
        <div>
            <p class="badge">Application professionnelle</p>
            <h2>Gestion des devis réseau</h2>
            <p>
                Créez et suivez vos devis pour Starlink, MikroTik, Wi-Fi,
                caméras IP, CPE, câblage et maintenance réseau.
            </p>
        </div>

        <a href="nouveau_devis.php" class="btn-primary">
            + Nouveau devis
        </a>
    </div>

    <div class="stats-grid">

        <div class="stat-card">
            <p>Clients</p>
            <h3><?php echo $total_clients; ?></h3>
            <span>Clients enregistrés</span>
        </div>

        <div class="stat-card">
            <p>Matériels</p>
            <h3><?php echo $total_materiels; ?></h3>
            <span>Équipements disponibles</span>
        </div>

        <div class="stat-card">
            <p>Prestations</p>
            <h3><?php echo $total_prestations; ?></h3>
            <span>Services configurés</span>
        </div>

        <div class="stat-card">
            <p>Devis</p>
            <h3><?php echo $total_devis; ?></h3>
            <span>Devis créés</span>
        </div>

        <div class="stat-card large">
            <p>Montant total TTC</p>
            <h3><?php echo number_format($total_chiffre, 0, ",", " "); ?> Ar</h3>
            <span>Total cumulé des devis</span>
        </div>

    </div>

    <div class="quick-actions">

        <a href="nouveau_devis.php">
            <h4>Créer un devis</h4>
            <p>Préparer rapidement une estimation réseau.</p>
        </a>

        <a href="clients.php">
            <h4>Ajouter un client</h4>
            <p>Enregistrer un nouveau client.</p>
        </a>

        <a href="materiels.php">
            <h4>Ajouter un matériel</h4>
            <p>Mettre à jour les équipements et prix.</p>
        </a>

        <a href="services.php">
            <h4>Ajouter une prestation</h4>
            <p>Modifier les services proposés.</p>
        </a>

    </div>

    <div class="panel">

        <div class="panel-header">
            <h2>Derniers devis</h2>
            <a href="liste_devis.php">Voir tout</a>
        </div>

        <?php if (count($derniers_devis) > 0): ?>

            <table>
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Client</th>
                        <th>Total TTC</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($derniers_devis as $devis): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($devis["numero_devis"]); ?></td>
                            <td><?php echo htmlspecialchars($devis["nom_client"] ?? "Client non défini"); ?></td>
                            <td><?php echo number_format($devis["total_ttc"], 0, ",", " "); ?> Ar</td>
                            <td>
                                <span class="status">
                                    <?php echo htmlspecialchars($devis["statut"]); ?>
                                </span>
                            </td>
                            <td><?php echo date("d/m/Y", strtotime($devis["date_devis"])); ?></td>
                            <td>
                                <a class="small-btn" href="voir_devis.php?id=<?php echo $devis["id"]; ?>">
                                    Voir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>

            <div class="empty">
                <h3>Aucun devis pour le moment</h3>
                <p>Commencez par créer votre premier devis réseau.</p>
                <a href="nouveau_devis.php">Créer un devis</a>
            </div>

        <?php endif; ?>

    </div>

</section>

<?php require_once "includes/footer.php"; ?>