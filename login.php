<?php

// index.php
require_once 'db.php'; // Traemos el código del otro archivo



//  Obtenemos los datos del formulario
     $email  = $_POST['email'];
     $pwd = $_POST['pwd'];
     
     // Llamamos a la función y guardamos el objeto en $db
     $db = conectarDB();
      

  try {
  


        $sql = "select id,password,email from usuarios where email= :email";
        $query = $db->prepare($sql);

	

        // Ejecutamos pasando los datos en un array
        $resultado = $query->execute([
            'email'  => $email
        ]);
        $usuario = $query->fetch(PDO::FETCH_ASSOC);
        if($usuario){
        $verify = password_verify($pwd, $usuario['password']);
        if($verify){
            session_start();
            $_SESSION['username'] = $usuario['email']; // Store session data
            $_SESSION['id'] = $usuario['id'];
            header("Location: dashboard.php");
            
        }else{
            echo "La contraseña esta mal...";
        }
        
        
        }else{
            echo "No se encontraron datos!";
        }

        

        

        

    } catch (PDOException $e) {
        // Manejo de errores (ej. si el email ya existe y es único)
        echo "Database Error: " . $e->getMessage();

        
     
    }






?>