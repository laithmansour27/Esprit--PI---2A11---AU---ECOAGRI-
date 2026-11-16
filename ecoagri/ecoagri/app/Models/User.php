<?php
class User
{
    public static function byEmail(string $email): ?array
    {
        $query = Database::connection()->prepare('SELECT u.*, r.nom_role FROM utilisateurs u JOIN roles r ON r.id_role=u.id_role WHERE u.email=?');
        $query->execute([$email]);
        return $query->fetch() ?: null;
    }
    public static function register(string $name, string $email, string $password): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $role = $pdo->query("SELECT id_role FROM roles WHERE nom_role='agriculteur'")->fetchColumn();
            if (!$role) { throw new RuntimeException('Rôle agriculteur manquant.'); }
            $stmt = $pdo->prepare('INSERT INTO utilisateurs(nom,email,mot_de_passe,id_role) VALUES(?,?,?,?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
            $pdo->prepare('INSERT INTO agriculteurs(id_user) VALUES(?)')->execute([$pdo->lastInsertId()]);
            $pdo->commit();
        } catch (Throwable $exception) { $pdo->rollBack(); throw $exception; }
    }
}
