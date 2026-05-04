-- A exécuter une seule fois dans MySQL
-- mysql -u root -p gestion_stock < setup.sql

CREATE TABLE IF NOT EXISTS produits (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    nom      VARCHAR(50)  NOT NULL,
    quantite INT          NOT NULL,
    prix     FLOAT        NOT NULL
);

CREATE TABLE IF NOT EXISTS historique (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    produit_id     INT,
    ancien_nom     VARCHAR(50),
    nouveau_nom    VARCHAR(50),
    ancien_stock   INT,
    nouveau_stock  INT,
    ancien_prix    FLOAT,
    nouveau_prix   FLOAT,
    date_modif     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Trigger : enregistre automatiquement chaque modification d'un produit
DELIMITER //
CREATE TRIGGER trigger_stock
AFTER UPDATE ON produits
FOR EACH ROW
BEGIN
    IF OLD.nom != NEW.nom OR OLD.quantite != NEW.quantite OR OLD.prix != NEW.prix THEN
        INSERT INTO historique (produit_id, ancien_nom, nouveau_nom, ancien_stock, nouveau_stock, ancien_prix, nouveau_prix)
        VALUES (OLD.id, OLD.nom, NEW.nom, OLD.quantite, NEW.quantite, OLD.prix, NEW.prix);
    END IF;
END //
DELIMITER ;
