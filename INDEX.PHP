<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bibliothèque - Accueil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Gestion de Bibliothèque</h1>
        </div>
        <div class="nav">
            <a href="index.php" class="active">Accueil</a>
            <a href="auteurs.php">Auteurs</a>
            <a href="categories.php">Catégories</a>
            <a href="livres.php">Livres</a>
            <a href="emprunts.php">Emprunts</a>
        </div>
        <div class="content">
            <?php
            $totalLivres = count($livre->getAll());
            $totalAuteurs = count($auteur->getAll());
            $totalCategories = count($categorie->getAll());
            $empruntsEnCours = count($emprunt->getEnCours());
            ?>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?= $totalLivres ?></div>
                    <div>Livres</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $totalAuteurs ?></div>
                    <div>Auteurs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $totalCategories ?></div>
                    <div>Catégories</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $empruntsEnCours ?></div>
                    <div>Emprunts en cours</div>
                </div>
            </div>
            
            <h3>Derniers livres ajoutés</h3>
            <table>
                <thead>
                    <tr><th>Titre</th><th>Auteur</th><th>Catégorie</th><th>Année</th></tr>
                </thead>
                <tbody>
                    <?php foreach(array_slice($livre->getAll(), 0, 5) as $l): ?>
                    <tr>
                        <td><?= htmlspecialchars($l['titre']) ?></td>
                        <td><?= htmlspecialchars($l['prenom'] . ' ' . $l['auteur_nom']) ?></td>
                        <td><?= htmlspecialchars($l['categorie_libelle']) ?></td>
                        <td><?= $l['annee'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>