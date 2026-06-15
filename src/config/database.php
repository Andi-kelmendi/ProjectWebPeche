<?php

class Database
{
    private static ?PDO $instance = null;

    // Configuration de la base de données
    private const DB_HOST   = 'localhost';
    private const DB_NAME   = 'ProjectWebPeche';
    private const DB_USER   = 'root';
    private const DB_PASS   = '';
    private const DB_CHARSET = 'utf8mb4';

    /**
     * Créer la connexion avec la BDD
     */
    public static function connect(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    self::DB_HOST,
                    self::DB_NAME,
                    self::DB_CHARSET
                );

                self::$instance = new PDO(
                    $dsn,
                    self::DB_USER,
                    self::DB_PASS,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                die('Erreur de connexion à la base de données: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
?>
