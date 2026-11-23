-- ECOAGRI database schema (MySQL / MariaDB, InnoDB, utf8mb4)
-- Legend: [CONFIRMÉ] = stated in the project's diagrams.
--         [À CONFIRMER] = assumed so the app can work; validate with the tutor.
-- Roles (deck v2): agriculteur, conseiller, administrateur. No Acheteur: farmers also buy.
-- No permissions table yet: the deck defines Rôle(id_role, nom_role, description) only.

CREATE DATABASE IF NOT EXISTS ecoagri CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecoagri;

-- ===== Module 1: Gestion des utilisateurs =====
CREATE TABLE roles (
  id_role     INT AUTO_INCREMENT PRIMARY KEY,
  nom_role    VARCHAR(50)  NOT NULL UNIQUE,
  description VARCHAR(255)
) ENGINE=InnoDB;

-- Role 1-N Utilisateur [CONFIRMÉ by the deck's user-management slide]
CREATE TABLE utilisateurs (
  id_user       INT AUTO_INCREMENT PRIMARY KEY,
  nom           VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  mot_de_passe  VARCHAR(255) NOT NULL,          -- password_hash(), never plain text
  telephone     VARCHAR(30),
  id_role       INT NOT NULL,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_role) REFERENCES roles(id_role)
) ENGINE=InnoDB;

-- Agriculteur as a 1-1 extension of Utilisateur [À CONFIRMER: no attributes defined]
CREATE TABLE agriculteurs (
  id_user INT PRIMARY KEY,
  FOREIGN KEY (id_user) REFERENCES utilisateurs(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== Module 2: Parcelles et sols =====
-- Exploitation belongs to an Agriculteur [À CONFIRMER]
CREATE TABLE exploitations (
  id_exploitation INT AUTO_INCREMENT PRIMARY KEY,
  nom             VARCHAR(120) NOT NULL,
  localisation    VARCHAR(150),
  id_agriculteur  INT NOT NULL,
  FOREIGN KEY (id_agriculteur) REFERENCES agriculteurs(id_user)
) ENGINE=InnoDB;

CREATE TABLE types_sol (
  id_type_sol INT AUTO_INCREMENT PRIMARY KEY,
  nom         VARCHAR(80) NOT NULL UNIQUE,
  description VARCHAR(255)
) ENGINE=InnoDB;

-- Exploitation 1-N Parcelle [CONFIRMÉ]; Parcelle -> TypeSol [À CONFIRMER]
CREATE TABLE parcelles (
  id_parcelle     INT AUTO_INCREMENT PRIMARY KEY,
  nom             VARCHAR(100) NOT NULL,
  surface_ha      DECIMAL(8,2) NOT NULL,
  localisation    VARCHAR(150),
  id_exploitation INT NOT NULL,
  id_type_sol     INT NULL,
  FOREIGN KEY (id_exploitation) REFERENCES exploitations(id_exploitation) ON DELETE CASCADE,
  FOREIGN KEY (id_type_sol)     REFERENCES types_sol(id_type_sol) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Parcelle 1-N Culture [deck: Culture.id_parcelle = Parcelle.id_parcelle]
CREATE TABLE cultures (
  id_culture          INT AUTO_INCREMENT PRIMARY KEY,
  nom_culture         VARCHAR(100) NOT NULL,
  date_semis          DATE,
  date_recolte_prevue DATE,
  statut              VARCHAR(40),
  id_parcelle         INT NOT NULL,
  FOREIGN KEY (id_parcelle) REFERENCES parcelles(id_parcelle) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== Module 3: Irrigation =====
CREATE TABLE sources_eau (
  id_source    INT AUTO_INCREMENT PRIMARY KEY,
  nom          VARCHAR(100) NOT NULL,
  type         VARCHAR(50),
  capacite_m3  DECIMAL(10,2),
  localisation VARCHAR(150)
) ENGINE=InnoDB;

-- SourceEau 1-N Irrigation [deck]; Irrigation -> Parcelle by FK instead of
-- the deck's text column nom_parcelle [À CONFIRMER]
CREATE TABLE irrigations (
  id_irrigation   INT AUTO_INCREMENT PRIMARY KEY,
  date_irrigation DATE NOT NULL,
  volume_m3       DECIMAL(10,2) NOT NULL,
  methode         VARCHAR(60),
  duree_min       INT,
  id_source       INT NOT NULL,
  id_parcelle     INT NOT NULL,
  FOREIGN KEY (id_source)   REFERENCES sources_eau(id_source),
  FOREIGN KEY (id_parcelle) REFERENCES parcelles(id_parcelle) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== Resources and ecological practices (from the conceptual model) =====
-- Attributes below are minimal placeholders [À CONFIRMER]
CREATE TABLE ressources (
  id_ressource INT AUTO_INCREMENT PRIMARY KEY,
  nom          VARCHAR(100) NOT NULL,
  type         VARCHAR(60),
  unite        VARCHAR(20)
) ENGINE=InnoDB;

CREATE TABLE utilisations_ressource (
  id_utilisation   INT AUTO_INCREMENT PRIMARY KEY,
  id_ressource     INT NOT NULL,
  id_exploitation  INT NOT NULL,
  quantite         DECIMAL(10,2) NOT NULL,
  date_utilisation DATE NOT NULL,
  FOREIGN KEY (id_ressource)    REFERENCES ressources(id_ressource),
  FOREIGN KEY (id_exploitation) REFERENCES exploitations(id_exploitation) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pratiques_ecologiques (
  id_pratique INT AUTO_INCREMENT PRIMARY KEY,
  nom         VARCHAR(120) NOT NULL,
  description TEXT
) ENGINE=InnoDB;

CREATE TABLE adoptions_pratique (
  id_adoption     INT AUTO_INCREMENT PRIMARY KEY,
  id_pratique     INT NOT NULL,
  id_exploitation INT NOT NULL,
  date_adoption   DATE NOT NULL,
  FOREIGN KEY (id_pratique)     REFERENCES pratiques_ecologiques(id_pratique),
  FOREIGN KEY (id_exploitation) REFERENCES exploitations(id_exploitation) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== Module 4: Marketplace =====
-- Produit 1-N Commande [deck]. Validation status comes from the use case
-- "Valider les produits" (Administrateur) [À CONFIRMER]. The buyer is an Agriculteur
-- ("Passer une commande" is an Agriculteur use case in deck v2).
CREATE TABLE produits (
  id_produit     INT AUTO_INCREMENT PRIMARY KEY,
  nom            VARCHAR(120) NOT NULL,
  categorie      VARCHAR(60),
  prix           DECIMAL(10,2) NOT NULL,
  stock          DECIMAL(10,2) NOT NULL DEFAULT 0,
  unite          VARCHAR(20),
  statut         ENUM('en_attente','valide','refuse') NOT NULL DEFAULT 'en_attente',
  id_agriculteur INT NOT NULL,
  FOREIGN KEY (id_agriculteur) REFERENCES agriculteurs(id_user)
) ENGINE=InnoDB;

CREATE TABLE commandes (
  id_commande   INT AUTO_INCREMENT PRIMARY KEY,
  quantite      DECIMAL(10,2) NOT NULL,
  date_commande DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  statut        VARCHAR(30) NOT NULL DEFAULT 'en_attente',
  id_acheteur   INT NOT NULL,                -- the Agriculteur who orders
  id_produit    INT NOT NULL,
  FOREIGN KEY (id_acheteur) REFERENCES agriculteurs(id_user),
  FOREIGN KEY (id_produit)  REFERENCES produits(id_produit)
) ENGINE=InnoDB;

-- ===== Module 5: Déchets agricoles =====
-- Déchet 1-N Offre [deck]
CREATE TABLE dechets (
  id_dechet      INT AUTO_INCREMENT PRIMARY KEY,
  type           VARCHAR(60) NOT NULL,
  quantite_kg    DECIMAL(10,2) NOT NULL,
  localisation   VARCHAR(150),
  id_agriculteur INT NOT NULL,
  FOREIGN KEY (id_agriculteur) REFERENCES agriculteurs(id_user)
) ENGINE=InnoDB;

CREATE TABLE offres (
  id_offre         INT AUTO_INCREMENT PRIMARY KEY,
  prix             DECIMAL(10,2) NOT NULL,
  date_publication DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  statut           VARCHAR(30) NOT NULL DEFAULT 'disponible',
  id_acheteur      INT NULL,                -- filled when an Agriculteur reserves the offer
  id_dechet        INT NOT NULL,
  FOREIGN KEY (id_acheteur) REFERENCES agriculteurs(id_user) ON DELETE SET NULL,
  FOREIGN KEY (id_dechet)   REFERENCES dechets(id_dechet) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== Module 6: Conseils et alertes =====
-- CatégorieInfo 1-N Publication [deck]; id_auteur = the Conseiller who publishes
-- (deck v2 use case) [À CONFIRMER as a column]
CREATE TABLE categories_info (
  id_categorie INT AUTO_INCREMENT PRIMARY KEY,
  nom          VARCHAR(80) NOT NULL UNIQUE,
  description  VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE publications (
  id_publication   INT AUTO_INCREMENT PRIMARY KEY,
  type             ENUM('conseil','alerte') NOT NULL,
  titre            VARCHAR(150) NOT NULL,
  contenu          TEXT NOT NULL,
  niveau           VARCHAR(30),
  region           VARCHAR(80),
  date_publication DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_categorie     INT NOT NULL,
  id_auteur        INT NULL,
  FOREIGN KEY (id_categorie) REFERENCES categories_info(id_categorie),
  FOREIGN KEY (id_auteur)    REFERENCES utilisateurs(id_user) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ===== Équipements (deck v2: slide "Gestion des équipements") =====
-- Not one of the six numbered modules: scope to confirm with the tutor.
-- Équipement owned by an Agriculteur [À CONFIRMER]; Équipement 1-N Maintenance [deck];
-- nom_technicien kept as text, as in the deck.
CREATE TABLE equipements (
  id_equipement  INT AUTO_INCREMENT PRIMARY KEY,
  nom            VARCHAR(100) NOT NULL,
  type           VARCHAR(60),
  date_achat     DATE,
  etat           VARCHAR(40),
  id_agriculteur INT NOT NULL,
  FOREIGN KEY (id_agriculteur) REFERENCES agriculteurs(id_user)
) ENGINE=InnoDB;

CREATE TABLE maintenances (
  id_maintenance   INT AUTO_INCREMENT PRIMARY KEY,
  date_maintenance DATE NOT NULL,
  description      TEXT,
  cout             DECIMAL(10,2),
  nom_technicien   VARCHAR(100),
  id_equipement    INT NOT NULL,
  FOREIGN KEY (id_equipement) REFERENCES equipements(id_equipement) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== Seed data: the three roles of deck v2 =====
INSERT INTO roles (nom_role, description) VALUES
  ('agriculteur',    'Gère son exploitation, parcelles, cultures, irrigation, équipements, produits et déchets'),
  ('conseiller',     'Consulte et analyse les données agricoles, publie conseils et alertes'),
  ('administrateur', 'Gère les utilisateurs et les rôles, valide les produits, supervise la plateforme');
