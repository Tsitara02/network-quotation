<?php
require_once "config.php";

$page_active = "devis";
$titre_page = "Nouveau devis";
$description_page = "Création d’un devis réseau avec calcul automatique.";
$page_css = "devis.css";

/*
    Récupération des clients
*/
$clients = $pdo->query("
    SELECT *
    FROM clients
    ORDER BY nom ASC
")->fetchAll(PDO::FETCH_ASSOC);

/*
    Récupération des matériels actifs
*/
$materiels = $pdo->query("
    SELECT *
    FROM materiels
    WHERE actif = 1
    ORDER BY nom ASC
")->fetchAll(PDO::FETCH_ASSOC);

/*
    Récupération des prestations
*/
$prestations = $pdo->query("
    SELECT *
    FROM prestations
    ORDER BY nom ASC
")->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
?>

<section class="content">

    <form method="POST" action="enregistrer_devis.php" id="devisForm">

        <div class="devis-layout">

            <!-- PARTIE GAUCHE : INFOS DEVIS -->
            <div class="panel">

                <div class="panel-header">
                    <div>
                        <h2>Informations du devis</h2>
                        <p class="panel-subtitle">Client, type d’installation et paramètres.</p>
                    </div>
                </div>

                <div class="form">

                    <div class="form-group">
                        <label for="client_id_select">Client</label>
                        <select name="client_id" id="client_id_select" required>
                            <option value="">-- Choisir un client --</option>

                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client["id"]; ?>">
                                    <?php echo htmlspecialchars($client["nom"]); ?>
                                    <?php if (!empty($client["telephone"])): ?>
                                        - <?php echo htmlspecialchars($client["telephone"]); ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type_installation_select">Type d’installation</label>
                        <select name="type_installation" id="type_installation_select" required>
                            <option value="">-- Choisir --</option>
                            <option value="Installation Starlink">Installation Starlink</option>
                            <option value="Installation Wi-Fi maison">Installation Wi-Fi maison</option>
                            <option value="Installation Wi-Fi hôtel">Installation Wi-Fi hôtel</option>
                            <option value="Installation caméra IP">Installation caméra IP</option>
                            <option value="Liaison CPE point à point">Liaison CPE point à point</option>
                            <option value="Configuration MikroTik">Configuration MikroTik</option>
                            <option value="Audit réseau">Audit réseau</option>
                            <option value="Maintenance réseau">Maintenance réseau</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="taux_tva">Taux TVA (%)</label>
                        <input 
                            type="number" 
                            name="taux_tva" 
                            id="taux_tva" 
                            value="20" 
                            min="0"
                            step="0.01"
                            oninput="calculerDevis()"
                        >
                    </div>

                    <div class="form-group">
                        <label for="remise">Remise (%)</label>
                        <input 
                            type="number" 
                            name="remise" 
                            id="remise" 
                            value="0" 
                            min="0"
                            step="0.01"
                            oninput="calculerDevis()"
                        >
                    </div>

                    <div class="form-group">
                        <label for="statut_select">Statut</label>
                        <select name="statut" id="statut_select">
                            <option value="Brouillon">Brouillon</option>
                            <option value="Envoyé">Envoyé</option>
                            <option value="Accepté">Accepté</option>
                            <option value="Refusé">Refusé</option>
                        </select>
                    </div>

                </div>

            </div>

            <!-- PARTIE DROITE : RÉSUMÉ -->
            <div class="panel summary-panel">

                <div class="panel-header">
                    <div>
                        <h2>Résumé</h2>
                        <p class="panel-subtitle">Calcul automatique du devis.</p>
                    </div>
                </div>

                <div class="summary-line">
                    <span>Total brut</span>
                    <strong id="total_brut_affiche">0 Ar</strong>
                </div>

                <div class="summary-line">
                    <span>Remise</span>
                    <strong id="remise_affiche">0 Ar</strong>
                </div>

                <div class="summary-line">
                    <span>Total HT</span>
                    <strong id="total_ht_affiche">0 Ar</strong>
                </div>

                <div class="summary-line">
                    <span>TVA</span>
                    <strong id="tva_affiche">0 Ar</strong>
                </div>

                <div class="summary-total">
                    <span>Total TTC</span>
                    <strong id="total_ttc_affiche">0 Ar</strong>
                </div>

                <input type="hidden" name="total_ht" id="total_ht">
                <input type="hidden" name="tva" id="tva">
                <input type="hidden" name="total_ttc" id="total_ttc">

                <button type="submit" class="btn-submit full-btn">
                    Enregistrer le devis
                </button>

            </div>

        </div>

        <!-- AJOUT DES LIGNES -->
        <div class="panel">

            <div class="panel-header">
                <div>
                    <h2>Lignes du devis</h2>
                    <p class="panel-subtitle">Ajoutez les matériels et prestations du devis.</p>
                </div>

                <div class="actions-right">
                    <button type="button" class="btn-secondary" onclick="openMaterielChoiceModal()">
                        + Matériel
                    </button>

                    <button type="button" class="btn-secondary" onclick="openPrestationChoiceModal()">
                        + Prestation
                    </button>

                    <button type="button" class="btn-secondary" onclick="ajouterLigneLibre()">
                        + Ligne libre
                    </button>
                </div>
            </div>

            <div class="table-wrapper">

                <table id="lignesTable">
                    <thead>
                        <tr>
                            <th>Désignation</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="lignesBody">
                        <!-- Les lignes seront ajoutées avec JavaScript -->
                    </tbody>
                </table>

            </div>

            <div class="empty" id="emptyLignes">
                <h3>Aucune ligne ajoutée</h3>
                <p>Ajoutez un matériel, une prestation ou une ligne libre.</p>
            </div>

        </div>

    </form>

</section>

<!-- MODAL CHOIX MATÉRIEL -->
<div class="modal-overlay" id="materielChoiceModal">
    <div class="modal-box large-modal">

        <div class="modal-header">
            <h2>Ajouter un matériel</h2>
            <button type="button" class="modal-close" onclick="closeMaterielChoiceModal()">×</button>
        </div>

        <div class="choice-list">

            <?php foreach ($materiels as $materiel): ?>
                <button 
                    type="button" 
                    class="choice-item"
                    onclick="ajouterLigne(
                        '<?php echo htmlspecialchars($materiel["nom"], ENT_QUOTES); ?>',
                        'Matériel',
                        1,
                        '<?php echo htmlspecialchars($materiel["prix_unitaire"], ENT_QUOTES); ?>'
                    )"
                >
                    <div>
                        <strong><?php echo htmlspecialchars($materiel["nom"]); ?></strong>
                        <span><?php echo htmlspecialchars($materiel["categorie"]); ?> — <?php echo htmlspecialchars($materiel["unite"]); ?></span>
                    </div>

                    <p><?php echo number_format($materiel["prix_unitaire"], 0, ",", " "); ?> Ar</p>
                </button>
            <?php endforeach; ?>

        </div>

    </div>
</div>

<!-- MODAL CHOIX PRESTATION -->
<div class="modal-overlay" id="prestationChoiceModal">
    <div class="modal-box large-modal">

        <div class="modal-header">
            <h2>Ajouter une prestation</h2>
            <button type="button" class="modal-close" onclick="closePrestationChoiceModal()">×</button>
        </div>

        <div class="choice-list">

            <?php foreach ($prestations as $prestation): ?>
                <button 
                    type="button" 
                    class="choice-item"
                    onclick="ajouterLigne(
                        '<?php echo htmlspecialchars($prestation["nom"], ENT_QUOTES); ?>',
                        'Prestation',
                        1,
                        '<?php echo htmlspecialchars($prestation["prix_base"], ENT_QUOTES); ?>'
                    )"
                >
                    <div>
                        <strong><?php echo htmlspecialchars($prestation["nom"]); ?></strong>
                        <span><?php echo htmlspecialchars($prestation["description"]); ?></span>
                    </div>

                    <p><?php echo number_format($prestation["prix_base"], 0, ",", " "); ?> Ar</p>
                </button>
            <?php endforeach; ?>

        </div>

    </div>
</div>

<?php require_once "includes/footer.php"; ?>