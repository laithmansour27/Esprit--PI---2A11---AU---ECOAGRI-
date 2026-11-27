<?php
include '../controller/publicationC.php';
include '../controller/validation.php';

$erreur = validerPublicationPHP($_POST);
if ($erreur !== '') {
    header('Location: Addpublication.php?erreur=' . urlencode($erreur));
    exit;
}

$pc = new PublicationC();
$p = new Publication(
    $_POST['type'],
    trim($_POST['titre']),
    trim($_POST['contenu']),
    $_POST['niveau'],
    trim($_POST['region']),
    $_POST['date_publication'],
    $_POST['etat']
);
$pc->addPublication($p);

header('Location: listepublication.php');
exit;