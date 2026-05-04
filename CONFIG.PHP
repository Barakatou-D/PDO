<?php
// Connexion PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bibliotheque;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// CLASSE AUTEUR
class Auteur {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM auteurs ORDER BY nom");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM auteurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($nom, $prenom, $nationalite) {
        $stmt = $this->pdo->prepare("INSERT INTO auteurs (nom, prenom, nationalite) VALUES (?, ?, ?)");
        return $stmt->execute([$nom, $prenom, $nationalite]);
    }
    
    public function update($id, $nom, $prenom, $nationalite) {
        $stmt = $this->pdo->prepare("UPDATE auteurs SET nom=?, prenom=?, nationalite=? WHERE id=?");
        return $stmt->execute([$nom, $prenom, $nationalite, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM auteurs WHERE id=?");
        return $stmt->execute([$id]);
    }
    
    public function canDelete($id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM livres WHERE auteur_id=?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() == 0;
    }
}

// CLASSE CATEGORIE
class Categorie {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY libelle");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($libelle) {
        $stmt = $this->pdo->prepare("INSERT INTO categories (libelle) VALUES (?)");
        return $stmt->execute([$libelle]);
    }
    
    public function update($id, $libelle) {
        $stmt = $this->pdo->prepare("UPDATE categories SET libelle=? WHERE id=?");
        return $stmt->execute([$libelle, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id=?");
        return $stmt->execute([$id]);
    }
    
    public function canDelete($id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM livres WHERE categorie_id=?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() == 0;
    }
}

// CLASSE LIVRE
class Livre {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT l.*, a.nom as auteur_nom, a.prenom, c.libelle as categorie_libelle 
            FROM livres l
            LEFT JOIN auteurs a ON l.auteur_id = a.id
            LEFT JOIN categories c ON l.categorie_id = c.id
            ORDER BY l.titre
        ");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM livres WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($titre, $isbn, $annee, $quantite, $auteur_id, $categorie_id) {
        $stmt = $this->pdo->prepare("
            INSERT INTO livres (titre, isbn, annee, quantite, auteur_id, categorie_id) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$titre, $isbn, $annee, $quantite, $auteur_id ?: null, $categorie_id ?: null]);
    }
    
    public function update($id, $titre, $isbn, $annee, $quantite, $auteur_id, $categorie_id) {
        $stmt = $this->pdo->prepare("
            UPDATE livres SET titre=?, isbn=?, annee=?, quantite=?, auteur_id=?, categorie_id=? 
            WHERE id=?
        ");
        return $stmt->execute([$titre, $isbn, $annee, $quantite, $auteur_id ?: null, $categorie_id ?: null, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM livres WHERE id=?");
        return $stmt->execute([$id]);
    }
    
    public function updateQuantite($id, $quantite) {
        $stmt = $this->pdo->prepare("UPDATE livres SET quantite = quantite + ? WHERE id = ?");
        return $stmt->execute([$quantite, $id]);
    }
}

// CLASSE EMPRUNT
class Emprunt {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT e.*, l.titre as livre_titre 
            FROM emprunts e
            LEFT JOIN livres l ON e.livre_id = l.id
            ORDER BY e.date_emprunt DESC
        ");
        return $stmt->fetchAll();
    }
    
    public function create($livre_id, $emprunteur, $date_retour) {
        $stmt = $this->pdo->prepare("
            INSERT INTO emprunts (livre_id, emprunteur_nom, date_emprunt, date_retour_prevue, statut) 
            VALUES (?, ?, CURDATE(), ?, 'en_cours')
        ");
        return $stmt->execute([$livre_id, $emprunteur, $date_retour]);
    }
    
    public function retour($id) {
        $stmt = $this->pdo->prepare("UPDATE emprunts SET date_retour_reelle = CURDATE(), statut = 'rendu' WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getEnCours() {
        $stmt = $this->pdo->query("
            SELECT e.*, l.titre as livre_titre 
            FROM emprunts e
            LEFT JOIN livres l ON e.livre_id = l.id
            WHERE e.statut = 'en_cours'
        ");
        return $stmt->fetchAll();
    }
}

// Instanciation des classes
$auteur = new Auteur($pdo);
$categorie = new Categorie($pdo);
$livre = new Livre($pdo);
$emprunt = new Emprunt($pdo);
?>