<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titre_page ?? "Akio Devis Réseau"; ?></title>

    <!-- CSS global -->
    <link rel="stylesheet" href="css/style.css">

    <!-- CSS spécifique à la page -->
    <?php if (!empty($page_css)): ?>
        <link rel="stylesheet" href="css/<?php echo $page_css; ?>">
    <?php endif; ?>
</head>
<body>

<div class="app">

    <?php require_once "includes/sidebar.php"; ?>

    <main class="main">

        <header class="topbar">

            <div>
                <h1><?php echo $titre_page ?? "Tableau de bord"; ?></h1>
                <p><?php echo $description_page ?? "Gestion des devis réseau et installations."; ?></p>
            </div>

            <div class="topbar-right">
                <div class="search-box">
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Rechercher..."
                        autocomplete="off"
                    >
                </div>

                <div class="user-box">
                    <div class="avatar">A</div>
                    <div>
                        <strong>Administrateur</strong>
                        <p>Akio Service</p>
                    </div>
                </div>
            </div>

        </header>