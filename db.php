<?php

// db.php

function conectarDB() {
    $host = "localhost";
    $db   = "murbina_db";
    $user = "murbina_user";
    $pass = "Mandarina";
    $charset = "utf8mb4";

    // El DSN (Data Source Name) define el tipo de driver y los datos del servidor
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    // Opciones recomendadas para PDO
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza errores como excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve los datos como array asociativo
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa consultas preparadas reales
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        // En producción, no muestres $e->getMessage() al usuario, regístralo en un log
        die("Error de conexión: " . $e->getMessage());
    }
}


?>