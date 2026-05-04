<?php require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'ajouter') {
            $categorie->create($_POST['libelle']);
            header('Location: categories.php?msg=ajoute');
        } elseif ($_POST['action'] === 'modifier') {
            $categorie->update($_POST['id'], $_POST['libelle']);
            header('Location: categories.php?msg=modifie');
        }
    }
}

if (isset($_GET['supprimer'])) {
    if ($categorie->canDelete($_GET['supprimer'])) {
        $categorie->delete($_GET['supprimer']);
        header('Location: categories.php?msg=supprime');
    } else {
        header('Location: categories.php?msg=erreur');
    }
}

$categories = $categorie->getAll();
$edit = isset($_GET['edit']) ? $categorie->getById($_GET['edit']) : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catégories</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header"><h1>📑 Gestion des Catégories</h1></div>
        <div class="nav">
            <a href="index.php">Accueil</a>
            <a href="auteurs.php">Auteurs</a>
            <a href="categories.php" class="active">Catégories</a>
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
                    <label>Libellé</label>
                    <input type="text" name="libelle" required value="<?= $edit ? htmlspecialchars($edit['libelle']) : '' ?>">
                </div>
                <button type="submit" class="btn btn-primary"><?= $edit ? 'Modifier' : 'Ajouter' ?></button>
                <?php if($edit): ?>
                    <a href="categories.php" class="btn">Annuler</a>
                <?php endif; ?>
            </form>
            
            <table>
                <thead><tr><th>ID</th><th>Libellé</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach($categories as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['libelle']) ?></td>
                        <td>
                            <a href="categories.php?edit=<?= $c['id'] ?>" class="btn btn-warning">Modifier</a>
                            <a href="categories.php?supprimer=<?= $c['id'] ?>" class="btn btn-danger" onclick="return confirm('Confirmer ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>