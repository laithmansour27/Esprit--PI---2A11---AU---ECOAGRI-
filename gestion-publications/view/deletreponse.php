<?php
include '../controller/reponseC.php';

$rc = new ReponseC();
$rc->deleteReponse($_GET['id']);

header('Location: listereponse.php?id=' . (int) $_GET['idp']);
exit;