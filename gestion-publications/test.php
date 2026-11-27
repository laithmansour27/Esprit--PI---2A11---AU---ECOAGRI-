<?php
include 'controller/publicationC.php';

$pc = new PublicationC();
foreach ($pc->listePublication() as $p) {
    echo $p['id_publication'] . ' - ' . $p['type'] . ' - ' . $p['titre'] . '<br>';
}