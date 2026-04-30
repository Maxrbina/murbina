<?php

require_once 'db.php';

// Obtenemos los datos del formulario
$email    = $_POST['email'];
$pwd      = $_POST['pwd'];
$recordar = isset($_POST['recordar']) ? true : false;

// Llamamos a la función y guardamos el objeto en $db
$db = conectarDB();

try {
    $sql = "SELECT id, password, email FROM usuarios WHERE email = :email";
    $query = $db->prepare($sql);
    $resultado = $query->execute(['email' => $email]);
    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $verify = password_verify($pwd, $usuario['password']);

        if ($verify) {
            session_start();
            $_SESSION['username'] = $usuario['email'];
            $_SESSION['id']       = $usuario['id'];

            // --- COOKIE ---
            if ($recordar) {
                // Guardar email por 30 días
                setcookie('recordar_email', $email, time() + (30 * 24 * 60 * 60), '/');
            } else {
                // Si desmarcó el checkbox, borrar la cookie
                setcookie('recordar_email', '', time() - 3600, '/');
            }
            // --------------

            header("Location: dashboard.php");
            exit();

        } else {
            echo "La contraseña está mal... <a href='index.html'>Volver</a>";
        }

    } else {
        echo "No se encontraron datos. <a href='index.html'>Volver</a>";
    }

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>
