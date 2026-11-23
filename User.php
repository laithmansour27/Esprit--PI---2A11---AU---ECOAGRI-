
<?php

class User
{
    // Recherche d'un utilisateur pour la connexion
    public static function byEmail(string $email): ?array
    {
        $query = Database::connection()->prepare(
            'SELECT u.*, r.nom_role
             FROM utilisateurs u
             JOIN roles r ON r.id_role = u.id_role
             WHERE u.email = ?'
        );

        $query->execute([$email]);

        return $query->fetch() ?: null;
    }

    // Inscription publique : le rôle est toujours agriculteur
    public static function register(
        string $name,
        string $email,
        string $password
    ): void {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $role = $pdo->query(
                "SELECT id_role FROM roles
                 WHERE nom_role = 'agriculteur'"
            )->fetchColumn();

            if (!$role) {
                throw new RuntimeException(
                    'Rôle agriculteur manquant.'
                );
            }

            $stmt = $pdo->prepare(
                'INSERT INTO utilisateurs
                 (nom, email, mot_de_passe, id_role)
                 VALUES (?, ?, ?, ?)'
            );

            $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $role
            ]);

            $id = $pdo->lastInsertId();

            $stmt = $pdo->prepare(
                'INSERT INTO agriculteurs (id_user) VALUES (?)'
            );

            $stmt->execute([$id]);

            $pdo->commit();

        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $exception;
        }
    }

    // Afficher tous les utilisateurs
    public static function allForAdmin(): array
    {
        $sql = '
            SELECT
                u.id_user,
                u.nom,
                u.email,
                u.telephone,
                u.id_role,
                r.nom_role,
                u.date_creation
            FROM utilisateurs u
            INNER JOIN roles r ON r.id_role = u.id_role
            ORDER BY u.id_user DESC
        ';

        return Database::connection()
            ->query($sql)
            ->fetchAll();
    }

    // Rechercher un utilisateur par son identifiant
    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT
                u.id_user,
                u.nom,
                u.email,
                u.telephone,
                u.id_role,
                r.nom_role
             FROM utilisateurs u
             INNER JOIN roles r ON r.id_role = u.id_role
             WHERE u.id_user = ?'
        );

        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    // Récupérer les rôles disponibles
    public static function allRoles(): array
    {
        return Database::connection()
            ->query(
                'SELECT id_role, nom_role
                 FROM roles
                 ORDER BY id_role'
            )
            ->fetchAll();
    }

    // Ajouter un utilisateur depuis l'administration
    public static function createByAdmin(
        string $name,
        string $email,
        string $password,
        string $telephone,
        int $roleId
    ): void {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT nom_role FROM roles WHERE id_role = ?'
            );
            $stmt->execute([$roleId]);

            $roleName = $stmt->fetchColumn();

            if (!$roleName) {
                throw new RuntimeException('Rôle invalide.');
            }

            $stmt = $pdo->prepare(
                'INSERT INTO utilisateurs
                 (nom, email, mot_de_passe, telephone, id_role)
                 VALUES (?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $telephone ?: null,
                $roleId
            ]);

            $id = $pdo->lastInsertId();

            if ($roleName === 'agriculteur') {
                $stmt = $pdo->prepare(
                    'INSERT INTO agriculteurs (id_user) VALUES (?)'
                );
                $stmt->execute([$id]);
            }

            $pdo->commit();

        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $exception;
        }
    }

    // Modifier un utilisateur
    public static function updateByAdmin(
        int $id,
        string $name,
        string $email,
        string $telephone,
        int $roleId,
        string $password = ''
    ): void {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT nom_role FROM roles WHERE id_role = ?'
            );
            $stmt->execute([$roleId]);

            $roleName = $stmt->fetchColumn();

            if (!$roleName) {
                throw new RuntimeException('Rôle invalide.');
            }

            if ($password !== '') {
                $stmt = $pdo->prepare(
                    'UPDATE utilisateurs
                     SET nom = ?, email = ?, telephone = ?,
                         id_role = ?, mot_de_passe = ?
                     WHERE id_user = ?'
                );

                $stmt->execute([
                    $name,
                    $email,
                    $telephone ?: null,
                    $roleId,
                    password_hash($password, PASSWORD_DEFAULT),
                    $id
                ]);
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE utilisateurs
                     SET nom = ?, email = ?, telephone = ?, id_role = ?
                     WHERE id_user = ?'
                );

                $stmt->execute([
                    $name,
                    $email,
                    $telephone ?: null,
                    $roleId,
                    $id
                ]);
            }

            // Un utilisateur promu agriculteur doit avoir son profil.
            if ($roleName === 'agriculteur') {
                $stmt = $pdo->prepare(
                    'INSERT INTO agriculteurs (id_user)
                     VALUES (?)
                     ON DUPLICATE KEY UPDATE id_user = id_user'
                );

                $stmt->execute([$id]);
            }

            $pdo->commit();

        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $exception;
        }
    }

    // Supprimer un utilisateur
    public static function deleteByAdmin(int $id): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT u.id_role, r.nom_role
                 FROM utilisateurs u
                 JOIN roles r ON r.id_role = u.id_role
                 WHERE u.id_user = ?'
            );

            $stmt->execute([$id]);
            $target = $stmt->fetch();

            if (!$target) {
                throw new RuntimeException(
                    'Utilisateur introuvable.'
                );
            }

            // Ne jamais supprimer le dernier administrateur.
            if ($target['nom_role'] === 'administrateur') {
                $count = $pdo->query(
                    "SELECT COUNT(*)
                     FROM utilisateurs u
                     JOIN roles r ON r.id_role = u.id_role
                     WHERE r.nom_role = 'administrateur'"
                )->fetchColumn();

                if ((int)$count <= 1) {
                    throw new RuntimeException(
                        'Impossible de supprimer le dernier administrateur.'
                    );
                }
            }

            // Supprimer d'abord le profil agriculteur éventuel.
            // Si des données agricoles le référencent, MySQL bloquera
            // la suppression et la transaction sera annulée.
            $stmt = $pdo->prepare(
                'DELETE FROM agriculteurs WHERE id_user = ?'
            );
            $stmt->execute([$id]);

            $stmt = $pdo->prepare(
                'DELETE FROM utilisateurs WHERE id_user = ?'
            );
            $stmt->execute([$id]);

            $pdo->commit();

        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $exception;
        }
    }
}
