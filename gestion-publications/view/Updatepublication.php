<?php
include '../controller/publicationC.php';
include '../controller/validation.php';

$id = (int) ($_POST['id'] ?? 0);

$erreur = validerPublicationPHP($_POST);
if ($erreur !== '') {
    header('Location: Modifierpublication.php?id=' . $id . '&erreur=' . urlencode($erreur));
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
$pc->updatePublication($p, $id);

header('Location: listepublication.php');
exit;