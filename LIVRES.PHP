<?php require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'ajouter') {
        $livre->create($_POST['titre'], $_POST['isbn'], $_POST['annee'], $_POST['quantite'], $_POST['auteur_id'], $_POST['categorie_id']);
        header('Location: livres.php?msg=ajoute');
    } elseif ($_POST['action'] === 'modifier') {
        $livre->update($_POST['id'], $_POST['titre'], $_POST['isbn'], $_POST['annee'], $_POST['quantite'], $_POST['auteur_id'], $_POST['categorie_id']);
        header('Location: livres.php?msg=modifie');
    }
}

if (isset($_GET['supprimer'])) {
    $livre->delete($_GET['supprimer']);
    header('Location: livres.php?msg=supprime');
}

$livres = $livre->getAll();
$auteurs = $auteur->getAll();
$categories = $categorie->getAll();
$edit = isset($_GET['edit']) ? $livre->getById($_GET['edit']) : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Livres</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header"><h1>📖 Gestion des Livres</h1></div>
        <div class="nav">
            <a href="index.php">Accueil</a>
            <a href="auteurs.php">Auteurs</a>
            <a href="categories.php">Catégories</a>
            <a href="livres.php" class="active">Livres</a>
            <a href="emprunts.php">Emprunts</a>
        </div>
        <div class="content">
            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success">Opération réussie !</div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="action" value="<?= $edit ? 'modifier' : 'ajouter' ?>">
                <?php if($edit): ?>
                    <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label>Titre</label>
                    <input type="text" name="titre" required value="<?= $edit ? htmlspecialchars($edit['titre']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>ISBN</label>
                    <input type="text" name="isbn" value="<?= $edit ? htmlspecialchars($edit['isbn']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Année</label>
                    <input type="number" name="annee" value="<?= $edit ? $edit['annee'] : '' ?>">
                </div>
                <div class="form-group">
                    <label>Quantité</label>
                    <input type="number" name="quantite" value="<?= $edit ? $edit['quantite'] : '1' ?>">
                </div>
                <div class="form-group">
                    <label>Auteur</label>
                    <select name="auteur_id">
                        <option value="">-- Aucun --</option>
                        <?php foreach($auteurs as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= $edit && $edit['auteur_id'] == $a['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="categorie_id">
                        <option value="">-- Aucune --</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $edit && $edit['categorie_id'] == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><?= $edit ? 'Modifier' : 'Ajouter' ?></button>
                <?php if($edit): ?>
                    <a href="livres.php" class="btn">Annuler</a>
                <?php endif; ?>
            </form>
            
            <table>
                <thead><tr><th>Titre</th><th>ISBN</th><th>Année</th><th>Quantité</th><th>Auteur</th><th>Catégorie</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach($livres as $l): ?>
                    <tr>
                        <td><?= htmlspecialchars($l['titre']) ?></td>
                        <td><?= htmlspecialchars($l['isbn']) ?></td>
                        <td><?= $l['annee'] ?></td>
                        <td><?= $l['quantite'] ?></td>
                        <td><?= htmlspecialchars($l['prenom'] . ' ' . $l['auteur_nom']) ?></td>
                        <td><?= htmlspecialchars($l['categorie_libelle']) ?></td>
                        <td>
                            <a href="livres.php?edit=<?= $l['id'] ?>" class="btn btn-warning">Modifier</a>
                            <a href="livres.php?supprimer=<?= $l['id'] ?>" class="btn btn-danger" onclick="return confirm('Confirmer ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>