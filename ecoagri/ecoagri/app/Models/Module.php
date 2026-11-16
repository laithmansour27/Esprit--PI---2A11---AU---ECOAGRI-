<?php
class Module
{
    public static function definitions(): array
    {
        return [
            'parcelles'=>['name'=>'Parcelles & cultures','icon'=>'▦','description'=>'Une vision claire de vos terres, de vos sols et de vos cultures.','roles'=>['agriculteur','conseiller','administrateur']],
            'irrigation'=>['name'=>'Irrigation','icon'=>'◉','description'=>'Suivez les apports en eau et les opérations d’irrigation.','roles'=>['agriculteur','conseiller','administrateur']],
            'marketplace'=>['name'=>'Marketplace','icon'=>'▧','description'=>'Découvrez les produits agricoles validés de la communauté.','roles'=>['agriculteur','administrateur']],
            'dechets'=>['name'=>'Déchets agricoles','icon'=>'♧','description'=>'Donnez une seconde vie aux ressources de votre exploitation.','roles'=>['agriculteur']],
            'conseils'=>['name'=>'Conseils & alertes','icon'=>'☀','description'=>'Retrouvez les publications et alertes de vos conseillers.','roles'=>['agriculteur','conseiller','administrateur']],
            'utilisateurs'=>['name'=>'Utilisateurs','icon'=>'♙','description'=>'Centralisez les comptes et consultez les rôles de la plateforme.','roles'=>['administrateur']],
        ];
    }
    public static function available(array $user): array
    {
        return array_filter(self::definitions(), fn($module)=>in_array($user['nom_role'], $module['roles'], true));
    }
    public static function rows(string $key, array $user): array
    {
        $farmer = $user['nom_role']==='agriculteur';
        $queries = [
            'parcelles'=>['SELECT p.nom AS Parcelle, p.surface_ha AS `Surface (ha)`, p.localisation AS Localisation, e.nom AS Exploitation, t.nom AS Sol FROM parcelles p JOIN exploitations e ON e.id_exploitation=p.id_exploitation LEFT JOIN types_sol t ON t.id_type_sol=p.id_type_sol'.($farmer?' WHERE e.id_agriculteur=?':''), $farmer?[$user['id_user']]:[]],
            'irrigation'=>['SELECT i.date_irrigation AS Date, p.nom AS Parcelle, i.volume_m3 AS `Volume (m³)`, i.methode AS Méthode, s.nom AS Source FROM irrigations i JOIN parcelles p ON p.id_parcelle=i.id_parcelle JOIN exploitations e ON e.id_exploitation=p.id_exploitation JOIN sources_eau s ON s.id_source=i.id_source'.($farmer?' WHERE e.id_agriculteur=?':'').' ORDER BY i.date_irrigation DESC', $farmer?[$user['id_user']]:[]],
            'marketplace'=>["SELECT nom AS Produit, categorie AS Catégorie, prix AS `Prix (DT)`, stock AS Stock, unite AS Unité, statut AS Statut FROM produits".($farmer?" WHERE statut='valide' OR id_agriculteur=?":''), $farmer?[$user['id_user']]:[]],
            'dechets'=>['SELECT type AS Type, quantite_kg AS `Quantité (kg)`, localisation AS Localisation FROM dechets WHERE id_agriculteur=?', [$user['id_user']]],
            'conseils'=>['SELECT p.titre AS Titre, p.type AS Type, c.nom AS Catégorie, p.region AS Région, p.niveau AS Niveau, p.contenu AS Contenu FROM publications p JOIN categories_info c ON c.id_categorie=p.id_categorie ORDER BY p.date_publication DESC', []],
            'utilisateurs'=>['SELECT u.nom AS Nom, u.email AS Email, r.nom_role AS Rôle, u.date_creation AS Inscription FROM utilisateurs u JOIN roles r ON r.id_role=u.id_role', []],
        ];
        if (!isset(self::available($user)[$key])) { throw new LogicException('Accès interdit'); }
        [$sql,$params] = $queries[$key];
        $stmt = Database::connection()->prepare($sql.' LIMIT 200');
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
