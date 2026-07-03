<?php
/*
    Définition du menu sous forme de tableau : évite de dupliquer
    la logique "active" et facilite l'ajout/suppression d'un lien.
    Chaque icône est un SVG inline (style outline, 20x20, stroke uniforme).
*/
$menu_items = [
    [
        'key'   => 'dashboard',
        'href'  => 'index.php',
        'label' => 'Tableau de bord',
        'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>',
    ],
    [
        'key'   => 'devis',
        'href'  => 'nouveau_devis.php',
        'label' => 'Nouveau devis',
        'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>',
    ],
    [
        'key'   => 'clients',
        'href'  => 'clients.php',
        'label' => 'Clients',
        'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    ],
    [
        'key'   => 'materiels',
        'href'  => 'materiels.php',
        'label' => 'Matériels',
        'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="14" width="20" height="8" rx="2"/><path d="M6 18h.01M10 18h.01"/><path d="M6 14V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v7"/></svg>',
    ],
    [
        'key'   => 'services',
        'href'  => 'services.php',
        'label' => 'Prestations',
        'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"/><path d="M9 13h6M9 17h6"/></svg>',
    ],
    [
        'key'   => 'liste_devis',
        'href'  => 'liste_devis.php',
        'label' => 'Liste devis',
        'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13"/><path d="M3 6h.01M3 12h.01M3 18h.01"/></svg>',
    ],
];
?>

<aside class="sidebar">

    <div class="logo">
        <img src="images/logo.jpeg" alt="Logo Akio Service" class="logo-img">

        <div class="logo-text">
            <h2>Akio Service</h2>
            <p>Devis Réseau</p>
        </div>
    </div>

    <nav class="menu" aria-label="Navigation principale">
        <?php foreach ($menu_items as $item): ?>
            <?php $est_actif = ($page_active === $item['key']); ?>

            <a
                href="<?php echo $item['href']; ?>"
                class="<?php echo $est_actif ? 'active' : ''; ?>"
                <?php echo $est_actif ? 'aria-current="page"' : ''; ?>
            >
                <span class="menu-icon"><?php echo $item['icon']; ?></span>
                <span class="menu-label"><?php echo $item['label']; ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <p>Akio Service</p>
        <small>Réseaux & Sécurité</small>
    </div>

</aside>