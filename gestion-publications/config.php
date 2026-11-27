<?php
class config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=gestion_conseils;charset=utf8mb4',
                    'root',
                    '',
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (Exception $e) {
                die('Erreur connexion: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
