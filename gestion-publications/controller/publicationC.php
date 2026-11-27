<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/publication.php';

class PublicationC
{
    public function listePublication($search = '', $tri = '', $filtre = '')
    {
        $db = config::getConnexion();
        $orders = [
            'recent' => 'id_publication DESC',
            'titre'  => 'titre ASC',
            'type'   => 'type ASC',
            'region' => 'region ASC',
            'niveau' => 'niveau ASC',
            'date'   => 'date_publication DESC',
            'etat'   => 'etat ASC',
        ];
        $order = $orders[$tri] ?? 'id_publication DESC';

        // filtres fixes (pas de valeur utilisateur dans le SQL)
        $cond = '';
        if ($filtre === 'conseil') {
            $cond = " AND type = 'conseil'";
        } elseif ($filtre === 'alerte') {
            $cond = " AND type = 'alerte'";
        } elseif ($filtre === 'urgent') {
            $cond = " AND niveau = 'urgent'";
        } elseif ($filtre === 'brouillon') {
            $cond = " AND etat = 'brouillon'";
        }

        try {
            $req = $db->prepare("SELECT * FROM publication
                                 WHERE (titre LIKE :s1 OR type LIKE :s2 OR region LIKE :s3) $cond
                                 ORDER BY $order");
            $like = '%' . $search . '%';
            $req->execute(['s1' => $like, 's2' => $like, 's3' => $like]);
            return $req;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function getPublication($id)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('SELECT * FROM publication WHERE id_publication = :id');
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function addPublication($p)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('INSERT INTO publication (type, titre, contenu, niveau, region, date_publication, etat, id_user)
                                 VALUES (:type, :titre, :contenu, :niveau, :region, :date, :etat, :id_user)');
            $req->execute([
                'type'    => $p->getType(),
                'titre'   => $p->getTitre(),
                'contenu' => $p->getContenu(),
                'niveau'  => $p->getNiveau(),
                'region'  => $p->getRegion(),
                'date'    => $p->getDatePublication(),
                'etat'    => $p->getEtat(),
                'id_user' => $p->getIdUser(),
            ]);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function updatePublication($p, $id)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('UPDATE publication
                                 SET type=:type, titre=:titre, contenu=:contenu, niveau=:niveau,
                                     region=:region, date_publication=:date, etat=:etat
                                 WHERE id_publication=:id');
            $req->execute([
                'type'    => $p->getType(),
                'titre'   => $p->getTitre(),
                'contenu' => $p->getContenu(),
                'niveau'  => $p->getNiveau(),
                'region'  => $p->getRegion(),
                'date'    => $p->getDatePublication(),
                'etat'    => $p->getEtat(),
                'id'      => $id,
            ]);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function deletePublication($id)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('DELETE FROM publication WHERE id_publication = :id');
            $req->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function statistiques()
    {
        $db = config::getConnexion();
        try {
            $req = $db->query("SELECT
                COUNT(*) AS total,
                COALESCE(SUM(type = 'conseil'), 0) AS conseils,
                COALESCE(SUM(type = 'alerte'), 0) AS alertes,
                COALESCE(SUM(niveau = 'urgent'), 0) AS urgentes,
                COALESCE(SUM(etat = 'brouillon'), 0) AS brouillons
                FROM publication");
            return $req->fetch();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
}