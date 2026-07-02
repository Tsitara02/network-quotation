<aside class="sidebar">

    <div class="logo">
        <img src="images/logo.jpeg" alt="Logo Akio Service" class="logo-img">

        <div class="logo-text">
            <h2>Akio Service</h2>
            <p>Devis Réseau</p>
        </div>
    </div>

    <nav class="menu">
        <a href="index.php" class="<?php echo ($page_active == 'dashboard') ? 'active' : ''; ?>">
            <span>▣</span>
            Tableau de bord
        </a>

        <a href="nouveau_devis.php" class="<?php echo ($page_active == 'devis') ? 'active' : ''; ?>">
            <span>▤</span>
            Devis
        </a>

        <a href="clients.php" class="<?php echo ($page_active == 'clients') ? 'active' : ''; ?>">
            <span>◉</span>
            Clients
        </a>

        <a href="materiels.php" class="<?php echo ($page_active == 'materiels') ? 'active' : ''; ?>">
            <span>◈</span>
            Matériels
        </a>

        <a href="services.php" class="<?php echo ($page_active == 'services') ? 'active' : ''; ?>">
            <span>◇</span>
            Prestations
        </a>

        <a href="liste_devis.php" class="<?php echo ($page_active == 'liste_devis') ? 'active' : ''; ?>">
            <span>☰</span>
            Liste devis
        </a>
    </nav>

    <div class="sidebar-footer">
        <p>Akio Service</p>
        <small>Réseaux & Sécurité</small>
    </div>

</aside>