<?php
class EquipmentController extends Controller
{
    private function farmer(): array
    {
        $user = Auth::requireUser();
        if (($user['nom_role'] ?? '') !== 'agriculteur') {
            http_response_code(403);
            exit('Accès réservé aux agriculteurs.');
        }
        return $user;
    }

    public function index(): void
    {
        $user = $this->farmer();
        $stmt = Database::connection()->prepare('SELECT * FROM equipements WHERE id_agriculteur=? ORDER BY id_equipement DESC');
        $stmt->execute([$user['id_user']]);
        $equipements = $stmt->fetchAll();
        $stmt = Database::connection()->prepare('SELECT m.*, e.nom AS equipement_nom FROM maintenances m JOIN equipements e ON e.id_equipement=m.id_equipement WHERE e.id_agriculteur=? ORDER BY m.date_maintenance DESC');
        $stmt->execute([$user['id_user']]);
        $maintenances = $stmt->fetchAll();
        $this->render('dashboard/equipements', compact('user','equipements','maintenances') + ['title'=>'Équipements & maintenances','currentPage'=>'equipements'], 'back');
    }

    public function create(): void
    {
        $user = $this->farmer(); Auth::csrf();
        $nom = trim((string)($_POST['nom'] ?? ''));
        $type = trim((string)($_POST['type'] ?? ''));
        $date = trim((string)($_POST['date_achat'] ?? ''));
        $etat = trim((string)($_POST['etat'] ?? ''));
        if ($nom === '' || mb_strlen($nom) > 100 || mb_strlen($type) > 60 || mb_strlen($etat) > 40 || !$this->validDate($date, true)) {
            Session::flash('error', 'Vérifiez le nom, le type, l’état et la date d’achat.'); redirect('/equipements');
        }
        $stmt = Database::connection()->prepare('INSERT INTO equipements(nom,type,date_achat,etat,id_agriculteur) VALUES(?,?,?,?,?)');
        $stmt->execute([$nom, $type ?: null, $date ?: null, $etat ?: null, $user['id_user']]);
        Session::flash('success', 'Équipement ajouté.'); redirect('/equipements');
    }

    public function update(): void
    {
        $user = $this->farmer(); Auth::csrf();
        $id = filter_input(INPUT_POST, 'id_equipement', FILTER_VALIDATE_INT);
        $nom = trim((string)($_POST['nom'] ?? '')); $type = trim((string)($_POST['type'] ?? ''));
        $date = trim((string)($_POST['date_achat'] ?? '')); $etat = trim((string)($_POST['etat'] ?? ''));
        if (!$id || $nom === '' || mb_strlen($nom)>100 || mb_strlen($type)>60 || mb_strlen($etat)>40 || !$this->validDate($date,true)) {
            Session::flash('error','Données invalides pour la modification.'); redirect('/equipements');
        }
        $stmt = Database::connection()->prepare('UPDATE equipements SET nom=?,type=?,date_achat=?,etat=? WHERE id_equipement=? AND id_agriculteur=?');
        $stmt->execute([$nom,$type?:null,$date?:null,$etat?:null,$id,$user['id_user']]);
        Session::flash('success', $stmt->rowCount() ? 'Équipement modifié.' : 'Aucune modification effectuée.'); redirect('/equipements');
    }

    public function delete(): void
    {
        $user = $this->farmer(); Auth::csrf();
        $id = filter_input(INPUT_POST, 'id_equipement', FILTER_VALIDATE_INT);
        if (!$id) { Session::flash('error','Équipement invalide.'); redirect('/equipements'); }
        $stmt = Database::connection()->prepare('DELETE FROM equipements WHERE id_equipement=? AND id_agriculteur=?');
        $stmt->execute([$id,$user['id_user']]);
        Session::flash('success', $stmt->rowCount() ? 'Équipement supprimé (ses maintenances aussi).' : 'Équipement introuvable.'); redirect('/equipements');
    }

    public function createMaintenance(): void
    {
        $user = $this->farmer(); Auth::csrf();
        $id = filter_input(INPUT_POST, 'id_equipement', FILTER_VALIDATE_INT);
        $date = trim((string)($_POST['date_maintenance'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $cout = trim((string)($_POST['cout'] ?? ''));
        $technicien = trim((string)($_POST['nom_technicien'] ?? ''));
        if (!$id || !$this->validDate($date) || mb_strlen($description)>5000 || mb_strlen($technicien)>100 || ($cout!=='' && (!is_numeric($cout) || (float)$cout<0 || (float)$cout>99999999))) {
            Session::flash('error','Vérifiez les informations de maintenance.'); redirect('/equipements');
        }
        $check = Database::connection()->prepare('SELECT id_equipement FROM equipements WHERE id_equipement=? AND id_agriculteur=?');
        $check->execute([$id,$user['id_user']]);
        if (!$check->fetchColumn()) { Session::flash('error','Équipement introuvable.'); redirect('/equipements'); }
        $stmt = Database::connection()->prepare('INSERT INTO maintenances(date_maintenance,description,cout,nom_technicien,id_equipement) VALUES(?,?,?,?,?)');
        $stmt->execute([$date,$description?:null,$cout===''?null:$cout,$technicien?:null,$id]);
        Session::flash('success','Maintenance enregistrée.'); redirect('/equipements');
    }

    public function deleteMaintenance(): void
    {
        $user = $this->farmer(); Auth::csrf();
        $id = filter_input(INPUT_POST, 'id_maintenance', FILTER_VALIDATE_INT);
        if (!$id) { Session::flash('error','Maintenance invalide.'); redirect('/equipements'); }
        $stmt = Database::connection()->prepare('DELETE m FROM maintenances m JOIN equipements e ON e.id_equipement=m.id_equipement WHERE m.id_maintenance=? AND e.id_agriculteur=?');
        $stmt->execute([$id,$user['id_user']]);
        Session::flash('success', $stmt->rowCount() ? 'Maintenance supprimée.' : 'Maintenance introuvable.'); redirect('/equipements');
    }

    private function validDate(string $date, bool $optional = false): bool
    {
        if ($optional && $date === '') return true;
        $d = DateTime::createFromFormat('!Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
