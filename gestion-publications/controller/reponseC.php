<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/reponse.php';

class ReponseC
{
    public function listeReponse($idPublication)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('SELECT * FROM reponse
                                 WHERE id_publication = :id
                                 ORDER BY date_reponse DESC');
            $req->execute(['id' => $idPublication]);
            return $req->fetchAll();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function addReponse($r)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('INSERT INTO reponse (contenu, id_user, id_publication)
                                 VALUES (:contenu, :id_user, :id_publication)');
            $req->execute([
                'contenu'        => $r->getContenu(),
                'id_user'        => $r->getIdUser(),
                'id_publication' => $r->getIdPublication(),
            ]);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function deleteReponse($id)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('DELETE FROM reponse WHERE id_reponse = :id');
            $req->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
}