<?php
session_start();

// ¿Existe la sesión? Si no, fuera de aquí.
if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
    <script src="./wwwroot/js/jquery-4.0.0.min.js"></script>
    <script src="./wwwroot/js/script.js"></script>
  </head>
  <body>
    <header>
      <div class="px-3 py-2 text-bg-primary border-bottom">
        <div class="container">
          <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none"> 
              <i class="bi bi-bootstrap fw-bold fs-5 pe-2"></i>
            </a>
            <nav >
            <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
              <li><a class="nav-link text-white" href="#"> <i class="bi bi-house fw-bold fs-5 pe-2"></i>Home</a></li>
              <li><a class="nav-link text-white" href="logout.php"> 
                  <i class="bi bi-box-arrow-in-left fw-bold fs-5 pe-2"></i>Salir
                </a></li>

            </ul>
            </nav>
          </div>

        </div>
      </div>
    </header>
    <div class="container-fluid">

      <div class="row">

        <aside class="col-8 col-sm-6 col-md-3 col-lg-3 col-xl-2 d-none d-lg-block show"
        style="position: fixed; top: 0;bottom: 0;left: 0;border-right: 1px solid var(--bs-border-color-translucent); margin-top:70px; padding: 15px 0 0;z-index: 999; overflow-y: auto;">
        <div class="px-3">
          <nav>
          <ul class="nav nav-pills flex-column mb-auto">            
            <li class="nav-item">
              <a class="nav-link active" href="#" onclick="document.getElementById('lightbulb').src='./wwwroot/img/bulboff.gif'">
              <i class="bi bi-lightbulb fw-bold fs-5 pe-2"></i>
              Apagado</a>
            </li>
            <li class="nav-item">
              <a class="nav-link"  href="#" onclick="document.getElementById('lightbulb').src='./wwwroot/img/bulbon.gif'">
              <i class="bi bi-lightbulb-fill fw-bold fs-5 pe-2"></i>
              Encendido</a>
            </li>
          </ul>
        </nav>
        </div>
        
      </aside>

      <main class="col-lg-9 col-xl-10 offset-lg-3 offset-xl-2">

        <div class="row">
          <div class="col-12 offset-sm-0 offset-lg-1 col-lg-10 offset-xl-2 col-xl-8 mt-5">
            <article id="article">
            <figure>
              <img id="lightbulb" class="img-fluid" src="./wwwroot/img/bulboff.gif">
            </figure>
          </article>

          </div>

        </div>
          
      </main>

      </div>
      
      <div class="row">
        
      </div>
      
    </div>
    
    
    


    <script src="./js/bootstrap.bundle.min.js"></script>
  </body>
</html>



