<?php require_once 'config.php'; 

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'ajouter') {
            $auteur->create($_POST['nom'], $_POST['prenom'], $_POST['nationalite']);
            header('Location: auteurs.php?msg=ajoute');
        } elseif ($_POST['action'] === 'modifier') {
            $auteur->update($_POST['id'], $_POST['nom'], $_POST['prenom'], $_POST['nationalite']);
            header('Location: auteurs.php?msg=modifie');
        }
    }
}

if (isset($_GET['supprimer'])) {
    if ($auteur->canDelete($_GET['supprimer'])) {
        $auteur->delete($_GET['supprimer']);
        header('Location: auteurs.php?msg=supprime');
    } else {
        header('Location: auteurs.php?msg=erreur');
    }
}

$auteurs = $auteur->getAll();
$edit = isset($_GET['edit']) ? $auteur->getById($_GET['edit']) : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Auteurs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header"><h1>📚 Gestion des Auteurs</h1></div>
        <div class="nav">
            <a href="index.php">Accueil</a>
            <a href="auteurs.php" class="active">Auteurs</a>
            <a href="categories.php">Catégories</a>
            <a href="livres.php">Livres</a>
            <a href="emprunts.php">Emprunts</a>
        </div>
        <div class="content">
            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success">Opération réussie !</div>
            <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'erreur'): ?>
                <div class="alert alert-danger">Impossible de supprimer (livres associés)</div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="action" value="<?= $edit ? 'modifier' : 'ajouter' ?>">
                <?php if($edit): ?>
                    <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" required value="<?= $edit ? htmlspecialchars($edit['nom']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" required value="<?= $edit ? htmlspecialchars($edit['prenom']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Nationalité</label>
                    <input type="text" name="nationalite" value="<?= $edit ? htmlspecialchars($edit['nationalite']) : '' ?>">
                </div>
                <button type="submit" class="btn btn-primary"><?= $edit ? 'Modifier' : 'Ajouter' ?></button>
                <?php if($edit): ?>
                    <a href="auteurs.php" class="btn">Annuler</a>
                <?php endif; ?>
            </form>
            
            <table>
                <thead><tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Nationalité</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach($auteurs as $a): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['nom']) ?></td>
                        <td><?= htmlspecialchars($a['prenom']) ?></td>
                        <td><?= htmlspecialchars($a['nationalite'] ?: '-') ?></td>
                        <td>
                            <a href="auteurs.php?edit=<?= $a['id'] ?>" class="btn btn-warning">Modifier</a>
                            <a href="auteurs.php?supprimer=<?= $a['id'] ?>" class="btn btn-danger" onclick="return confirm('Confirmer ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>