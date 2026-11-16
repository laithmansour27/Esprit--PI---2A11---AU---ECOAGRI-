# ECOAGRI

Plateforme universitaire de gestion agricole durable, en PHP 8 / MySQL et architecture MVC.

## Lancer avec XAMPP

1. Démarrer Apache et MySQL.
2. Importer `database/schema.sql` dans une base neuve avec phpMyAdmin. Le schéma existant est conservé et ne doit pas être réimporté sur des tables existantes.
3. Copier `app/config/config.example.php` vers `app/config/config.php`, puis adapter les identifiants MySQL.
4. Ouvrir http://localhost/ecoagri/ (redirection vers `public/`).
5. Créer un compte agriculteur depuis Inscription, puis se connecter.

Aucun compte administrateur ni mot de passe par défaut n’est installé. Pour tester les autres profils, attribuer le rôle existant `conseiller` ou `administrateur` au compte souhaité via phpMyAdmin. L’inscription publique reste limitée au rôle agriculteur.

## Fonctionnalités réalisées

- Accueil, présentation du projet, six solutions, connexion, inscription et page 404.
- Interface responsive en français, illustrations locales, sans dépendance CDN ou compilation.
- Front office : `Views/front/layout.php` assemble head, navbar, contenu, footer et scripts réutilisables.
- Back office : `Views/back/layout.php` assemble les partials head, sidebar, header, footer et scripts.
- Navigation active, menu mobile, tableau de bord, tableaux filtrables et états vides.
- Authentification MySQL/PDO, mots de passe hachés, CSRF, sessions renouvelées et limitation des essais par session.
- Inscription transactionnelle dans utilisateurs et agriculteurs.
- Six pages de consultation, filtrées selon le rôle. Parcelles, irrigations et déchets sont limités aux données de l’agriculteur connecté. La marketplace expose les produits validés et ses propres annonces. Les utilisateurs sont réservés aux administrateurs.
- Gestion lisible des indisponibilités MySQL, sans exposition des identifiants.
- Routes compatibles avec les installations sans réécriture via `public/index.php?route=...`.
- Accès HTTP aux dossiers internes bloqué par `.htaccess` sous Apache (AllowOverride doit être autorisé). Pour un autre serveur, servir uniquement `public/`.

## Périmètre restant

Les pages métier sont en lecture seule : les formulaires CRUD, commandes, réservations, gestion des rôles et publications restent à implémenter. Les indicateurs portent sur les 200 premiers enregistrements accessibles de chaque module. Aucun jeu de données fictif n’est affiché comme une mesure réelle.

Le modèle MySQL initial et ses hypothèses sont conservés : voir `docs/class-diagram.md` et `docs/use-cases.md`. Les capteurs IoT, la météo et les recommandations intelligentes restent des perspectives futures.

## Vérification

`php -n tests/smoke.php` vérifie 18 rendus / scénarios d’accès (pages publiques, espaces des trois rôles, six modules, refus d’accès, échappement HTML et 404), ainsi que les jetons CSRF.

Les vérifications CLI ont réussi. Dans la session de développement, les connexions réseau vers localhost/MySQL sont bloquées. Le parcours inscription → connexion → données réelles et la vérification visuelle dans un navigateur restent à exécuter avec les services XAMPP accessibles.

Si le PHP CLI XAMPP indique des chemins `/apps/php` introuvables, lancer les vérifications de syntaxe avec `php -n -l fichier.php`. Pour un serveur PHP CLI avec MySQL, préciser le chemin absolu de `extension_dir` et activer `pdo_mysql`, sans modifier la configuration globale de XAMPP.
