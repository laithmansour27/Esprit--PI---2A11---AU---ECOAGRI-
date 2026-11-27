<?php
// Contrôle de saisie côté serveur: retourne '' si tout est bon, sinon le message d'erreur
function validerPublicationPHP(array $d): string
{
    $titre   = trim($d['titre'] ?? '');
    $region  = trim($d['region'] ?? '');
    $contenu = trim($d['contenu'] ?? '');
    $date    = trim($d['date_publication'] ?? '');

    if (mb_strlen($titre) < 3 || mb_strlen($titre) > 100) {
        return 'Le titre doit contenir entre 3 et 100 caractères.';
    }
    if (!in_array($d['type'] ?? '', ['conseil', 'alerte'], true)) {
        return 'Catégorie invalide.';
    }
    if (mb_strlen($region) < 2 || mb_strlen($region) > 50) {
        return 'La région doit contenir entre 2 et 50 caractères.';
    }
    if (!in_array($d['niveau'] ?? '', ['info', 'moyen', 'urgent'], true)) {
        return 'Niveau invalide.';
    }
    if (!in_array($d['etat'] ?? '', ['publie', 'brouillon'], true)) {
        return 'État invalide.';
    }
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dt || $dt->format('Y-m-d') !== $date) {
        return 'Date de publication invalide.';
    }
    if (mb_strlen($contenu) < 10) {
        return 'Les notes doivent contenir au moins 10 caractères.';
    }
    return '';
}