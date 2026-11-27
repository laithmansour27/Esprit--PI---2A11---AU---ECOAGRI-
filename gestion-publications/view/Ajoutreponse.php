<?php
include '../controller/reponseC.php';

$idp = (int) $_POST['id_publication'];

$rc = new ReponseC();
$r = new Reponse($_POST['contenu'], $idp);
$rc->addReponse($r);

header('Location: listereponse.php?id=' . $idp);
exit;