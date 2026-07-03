/*
    Fonction utilitaire : échapper le HTML pour éviter les injections XSS
    quand on insère des valeurs dynamiques (nom de matériel, prestation, etc.)
    dans le DOM via innerHTML/template strings.
*/
function escapeHtml(valeur) {
    const div = document.createElement("div");
    div.textContent = valeur;
    return div.innerHTML;
}

function openAddClientModal() {
    document.getElementById("clientModalTitle").textContent = "Ajouter un client";
    document.getElementById("clientSubmitBtn").textContent = "Enregistrer";
    document.getElementById("clientSubmitBtn").disabled = false;

    document.getElementById("client_id").value = "";
    document.getElementById("client_nom").value = "";
    document.getElementById("client_telephone").value = "";
    document.getElementById("client_adresse").value = "";
    document.getElementById("client_email").value = "";
    document.getElementById("client_type").value = "Particulier";

    document.getElementById("clientModal").classList.add("show");
}

function openEditClientModal(id, nom, telephone, adresse, email, typeClient) {
    document.getElementById("clientModalTitle").textContent = "Modifier le client";
    document.getElementById("clientSubmitBtn").textContent = "Modifier";
    document.getElementById("clientSubmitBtn").disabled = false;

    document.getElementById("client_id").value = id;
    document.getElementById("client_nom").value = nom;
    document.getElementById("client_telephone").value = telephone;
    document.getElementById("client_adresse").value = adresse;
    document.getElementById("client_email").value = email;
    document.getElementById("client_type").value = typeClient;

    document.getElementById("clientModal").classList.add("show");
}

function closeClientModal() {
    document.getElementById("clientModal").classList.remove("show");
}

function openDeleteClientModal(id) {
    document.getElementById("confirmDeleteClientBtn").href = "clients.php?delete=" + id;
    document.getElementById("deleteClientModal").classList.add("show");
}

function closeDeleteClientModal() {
    document.getElementById("deleteClientModal").classList.remove("show");
}

/*
    Fermer les modals quand on clique sur le fond sombre
*/
document.addEventListener("click", function(event) {
    if (event.target.classList.contains("modal-overlay")) {
        event.target.classList.remove("show");
    }
});

/*
    Fermer les modals avec la touche Échap
*/
document.addEventListener("keydown", function(event) {
    if (event.key === "Escape") {
        let modals = document.querySelectorAll(".modal-overlay");
        modals.forEach(function(modal) {
            modal.classList.remove("show");
        });
    }
});

// Modal javascrip materiel

function openAddMaterielModal() {
    document.getElementById("materielModalTitle").textContent = "Ajouter un matériel";
    document.getElementById("materielSubmitBtn").textContent = "Enregistrer";
    document.getElementById("materielSubmitBtn").disabled = false;

    document.getElementById("materiel_id").value = "";
    document.getElementById("materiel_nom").value = "";
    document.getElementById("materiel_categorie").value = "Routeur";
    document.getElementById("materiel_prix").value = "";
    document.getElementById("materiel_unite").value = "pièce";
    document.getElementById("materiel_actif").value = "1";

    document.getElementById("materielModal").classList.add("show");
}

function openEditMaterielModal(id, nom, categorie, prix, unite, actif) {
    document.getElementById("materielModalTitle").textContent = "Modifier le matériel";
    document.getElementById("materielSubmitBtn").textContent = "Modifier";
    document.getElementById("materielSubmitBtn").disabled = false;

    document.getElementById("materiel_id").value = id;
    document.getElementById("materiel_nom").value = nom;
    document.getElementById("materiel_categorie").value = categorie;
    document.getElementById("materiel_prix").value = prix;
    document.getElementById("materiel_unite").value = unite;
    document.getElementById("materiel_actif").value = actif;

    document.getElementById("materielModal").classList.add("show");
}

function closeMaterielModal() {
    document.getElementById("materielModal").classList.remove("show");
}

function openDeleteMaterielModal(id) {
    document.getElementById("confirmDeleteMaterielBtn").href = "materiels.php?delete=" + id;
    document.getElementById("deleteMaterielModal").classList.add("show");
}

function closeDeleteMaterielModal() {
    document.getElementById("deleteMaterielModal").classList.remove("show");
}

// Fin modal javascript materiel

// debut modal javascript service
function openAddServiceModal() {
    document.getElementById("serviceModalTitle").textContent = "Ajouter une prestation";
    document.getElementById("serviceSubmitBtn").textContent = "Enregistrer";
    document.getElementById("serviceSubmitBtn").disabled = false;

    document.getElementById("service_id").value = "";
    document.getElementById("service_nom").value = "";
    document.getElementById("service_prix").value = "";
    document.getElementById("service_description").value = "";

    document.getElementById("serviceModal").classList.add("show");
}

function openEditServiceModal(id, nom, prix, description) {
    document.getElementById("serviceModalTitle").textContent = "Modifier la prestation";
    document.getElementById("serviceSubmitBtn").textContent = "Modifier";
    document.getElementById("serviceSubmitBtn").disabled = false;

    document.getElementById("service_id").value = id;
    document.getElementById("service_nom").value = nom;
    document.getElementById("service_prix").value = prix;
    document.getElementById("service_description").value = description;

    document.getElementById("serviceModal").classList.add("show");
}

function closeServiceModal() {
    document.getElementById("serviceModal").classList.remove("show");
}

function openDeleteServiceModal(id) {
    document.getElementById("confirmDeleteServiceBtn").href = "services.php?delete=" + id;
    document.getElementById("deleteServiceModal").classList.add("show");
}

function closeDeleteServiceModal() {
    document.getElementById("deleteServiceModal").classList.remove("show");
}
// Fin modal javascript service

// javascript pour le nouveau devis
let ligneIndex = 0;

function openMaterielChoiceModal() {
    document.getElementById("materielChoiceModal").classList.add("show");
}

function closeMaterielChoiceModal() {
    document.getElementById("materielChoiceModal").classList.remove("show");
}

function openPrestationChoiceModal() {
    document.getElementById("prestationChoiceModal").classList.add("show");
}

function closePrestationChoiceModal() {
    document.getElementById("prestationChoiceModal").classList.remove("show");
}

function ajouterLigne(designation, type, quantite, prix) {
    const body = document.getElementById("lignesBody");
    const empty = document.getElementById("emptyLignes");

    if (empty) {
        empty.style.display = "none";
    }

    // Échappement des valeurs texte avant insertion dans le DOM (anti-XSS)
    const designationSafe = escapeHtml(designation);
    const typeSafe = escapeHtml(type);

    const tr = document.createElement("tr");

    tr.innerHTML = `
        <td>
            <input 
                type="text" 
                name="lignes[${ligneIndex}][designation]" 
                value="${designationSafe}" 
                required
                oninput="calculerDevis()"
            >
        </td>

        <td>
            <span class="type-badge">${typeSafe}</span>
            <input 
                type="hidden" 
                name="lignes[${ligneIndex}][type]" 
                value="${typeSafe}"
            >
        </td>

        <td>
            <input 
                type="number" 
                name="lignes[${ligneIndex}][quantite]" 
                class="ligne-quantite"
                value="${quantite}" 
                min="0"
                step="0.01"
                oninput="calculerDevis()"
            >
        </td>

        <td>
            <input 
                type="number" 
                name="lignes[${ligneIndex}][prix_unitaire]" 
                class="ligne-prix"
                value="${prix}" 
                min="0"
                step="0.01"
                oninput="calculerDevis()"
            >
        </td>

        <td>
            <strong class="ligne-total">0 Ar</strong>
        </td>

        <td>
            <button type="button" class="remove-line" onclick="supprimerLigne(this)">
                Supprimer
            </button>
        </td>
    `;

    body.appendChild(tr);
    ligneIndex++;

    calculerDevis();

    closeMaterielChoiceModal();
    closePrestationChoiceModal();
}

function ajouterLigneLibre() {
    ajouterLigne("Ligne personnalisée", "Libre", 1, 0);
}

function supprimerLigne(button) {
    const tr = button.closest("tr");
    tr.remove();

    const body = document.getElementById("lignesBody");
    const empty = document.getElementById("emptyLignes");

    if (body.children.length === 0 && empty) {
        empty.style.display = "block";
    }

    calculerDevis();
}

function calculerDevis() {
    const lignes = document.querySelectorAll("#lignesBody tr");

    let totalBrut = 0;

    lignes.forEach(function(ligne) {
        const quantite = parseFloat(ligne.querySelector(".ligne-quantite").value) || 0;
        const prix = parseFloat(ligne.querySelector(".ligne-prix").value) || 0;
        const totalLigne = quantite * prix;

        ligne.querySelector(".ligne-total").textContent = formatAriary(totalLigne);

        totalBrut += totalLigne;
    });

    const tauxTva = parseFloat(document.getElementById("taux_tva").value) || 0;
    const tauxRemise = parseFloat(document.getElementById("remise").value) || 0;

    const montantRemise = totalBrut * tauxRemise / 100;
    const totalHT = totalBrut - montantRemise;
    const tva = totalHT * tauxTva / 100;
    const totalTTC = totalHT + tva;

    document.getElementById("total_brut_affiche").textContent = formatAriary(totalBrut);
    document.getElementById("remise_affiche").textContent = formatAriary(montantRemise);
    document.getElementById("total_ht_affiche").textContent = formatAriary(totalHT);
    document.getElementById("tva_affiche").textContent = formatAriary(tva);
    document.getElementById("total_ttc_affiche").textContent = formatAriary(totalTTC);

    document.getElementById("total_ht").value = totalHT;
    document.getElementById("tva").value = tva;
    document.getElementById("total_ttc").value = totalTTC;
}

function formatAriary(nombre) {
    return new Intl.NumberFormat("fr-FR", {
        maximumFractionDigits: 0
    }).format(nombre) + " Ar";
}
// fin du javascript pour le nouveau devis

function openDeleteDevisModal(id, numero) {
    document.getElementById("deleteDevisNumero").textContent = numero;
    document.getElementById("confirmDeleteDevisBtn").href = "liste_devis.php?delete=" + id;
    document.getElementById("deleteDevisModal").classList.add("show");
}

function closeDeleteDevisModal() {
    document.getElementById("deleteDevisModal").classList.remove("show");
}

function openStatutDevisModal(id, numero, statut) {
    document.getElementById("statut_devis_id").value = id;
    document.getElementById("statutDevisNumero").textContent = numero;
    document.getElementById("statut_devis_value").value = statut;
    document.getElementById("statutDevisModal").classList.add("show");
}

function closeStatutDevisModal() {
    document.getElementById("statutDevisModal").classList.remove("show");
}

/*
    ================================
    RECHERCHE : FILTRAGE DU TABLEAU
    ================================
    Filtre les lignes du tableau visible sur la page courante
    (clients, matériels, prestations, liste des devis...) en fonction
    du texte tapé dans la barre de recherche du header.
*/

function filtrerTableau(terme) {
    const texteRecherche = terme.trim().toLowerCase();

    // On cible uniquement les tableaux de données affichés dans le contenu,
    // pas le tableau de saisie des lignes d'un devis (#lignesTable).
    const tableau = document.querySelector(".content table:not(#lignesTable)");

    if (!tableau) {
        return;
    }

    const lignes = tableau.querySelectorAll("tbody tr");
    let resultatsVisibles = 0;

    lignes.forEach(function(ligne) {
        const texteLigne = ligne.textContent.toLowerCase();
        const correspond = texteLigne.includes(texteRecherche);

        ligne.style.display = correspond ? "" : "none";

        if (correspond) {
            resultatsVisibles++;
        }
    });

    afficherMessageAucunResultat(tableau, resultatsVisibles, texteRecherche);
}

function afficherMessageAucunResultat(tableau, resultatsVisibles, texteRecherche) {
    let messageVide = document.getElementById("searchNoResults");

    if (resultatsVisibles === 0 && texteRecherche !== "") {
        if (!messageVide) {
            messageVide = document.createElement("div");
            messageVide.id = "searchNoResults";
            messageVide.className = "empty";
            tableau.insertAdjacentElement("afterend", messageVide);
        }

        messageVide.innerHTML = `
            <h3>Aucun résultat</h3>
            <p>Aucune ligne ne correspond à votre recherche.</p>
        `;
        messageVide.style.display = "block";
        tableau.style.display = "none";

    } else {
        if (messageVide) {
            messageVide.style.display = "none";
        }
        tableau.style.display = "";
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");

    if (searchInput) {
        searchInput.addEventListener("input", function() {
            filtrerTableau(this.value);
        });
    }
});

/*
    ================================
    PROTECTION ANTI DOUBLE-SUBMIT
    ================================
*/

// Formulaire client (dans la modal d'ajout/modification)
document.addEventListener("DOMContentLoaded", function() {
    const clientForm = document.querySelector("#clientModal form");

    if (clientForm) {
        clientForm.addEventListener("submit", function() {
            const btn = document.getElementById("clientSubmitBtn");
            if (btn) {
                btn.disabled = true;
            }
        });
    }

    // Formulaire nouveau devis : vérifie qu'il y a au moins une ligne,
    // puis désactive le bouton pour éviter un double enregistrement.
    const devisForm = document.getElementById("devisForm");

    if (devisForm) {
        devisForm.addEventListener("submit", function(event) {
            const lignes = document.querySelectorAll("#lignesBody tr");

            if (lignes.length === 0) {
                event.preventDefault();
                alert("Ajoutez au moins une ligne avant d'enregistrer le devis.");
                return;
            }

            const submitBtn = devisForm.querySelector("button[type=submit]");
            if (submitBtn) {
                submitBtn.disabled = true;
            }
        });
    }
});