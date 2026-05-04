<?php require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $emprunt->create($_POST['livre_id'], $_POST['emprunteur'], $_POST['date_retour']);
    $livre->updateQuantite($_POST['livre_id'], -1);
    header('Location: emprunts.php?msg=ajoute');
}

if (isset($_GET['retour'])) {
    $e = $emprunt->getAll();
    foreach($e as $emp) {
        if($emp['id'] == $_GET['retour']) {
            $livre->updateQuantite($emp['livre_id'], 1);
            break;
        }
    }
    $emprunt->retour($_GET['retour']);
    header('Location: emprunts.php?msg=retour');
}

$emprunts = $emprunt->getAll();
$livresDispo = array_filter($livre->getAll(), function($l) { return $l['quantite'] > 0; });
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Emprunts</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header"><h1>📌 Gestion des Emprunts</h1></div>
        <div class="nav">
            <a href="index.php">Accueil</a>
            <a href="auteurs.php">Auteurs</a>
            <a href="categories.php">Catégories</a>
            <a href="livres.php">Livres</a>
            <a href="emprunts.php" class="active">Emprunts</a>
        </div>
        <div class="content">
            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success">Opération réussie !</div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="action" value="ajouter">
                <div class="form-group">
                    <label>Livre</label>
                    <select name="livre_id" required>
                        <option value="">-- Choisir --</option>
                        <?php foreach($livresDispo as $l): ?>
                            <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['titre']) ?> (<?= $l['quantite'] ?> dispo)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nom de l'emprunteur</label>
                    <input type="text" name="emprunteur" required>
                </div>
                <div class="form-group">
                    <label>Date de retour prévue</label>
                    <input type="date" name="date_retour" required value="<?= date('Y-m-d', strtotime('+14 days')) ?>">
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer l'emprunt</button>
            </form>
            
            <table style="margin-top: 30px;">
                <thead><tr><th>Livre</th><th>Emprunteur</th><th>Date emprunt</th><th>Retour prévu</th><th>Statut</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach($emprunts as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['livre_titre']) ?></td>
                        <td><?= htmlspecialchars($e['emprunteur_nom']) ?></td>
                        <td><?= date('d/m/Y', strtotime($e['date_emprunt'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($e['date_retour_prevue'])) ?></td>
                        <td>
                            <?php if($e['statut'] == 'rendu'): ?>
                                <span style="color:green">Rendu</span>
                            <?php elseif($e['date_retour_prevue'] < date('Y-m-d') && $e['statut'] != 'rendu'): ?>
                                <span style="color:red">En retard</span>
                            <?php else: ?>
                                <span style="color:blue">En cours</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($e['statut'] != 'rendu'): ?>
                                <a href="emprunts.php?retour=<?= $e['id'] ?>" class="btn btn-success" onclick="return confirm('Confirmer le retour ?')">Retour</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>