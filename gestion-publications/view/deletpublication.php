<?php
include '../controller/publicationC.php';

$pc = new PublicationC();
$pc->deletePublication($_GET['id']);

header('Location: listepublication.php');
exit;