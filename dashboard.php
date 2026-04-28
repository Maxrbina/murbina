<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Biblioteca — Dashboard</title>
  <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Lato', sans-serif; background: #f5ede0; margin: 0; }

    header {
      background: #7a4f1e;
      padding: 0 2rem;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 100;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .header-logo {
      font-family: 'Playfair Display', serif;
      color: #f5d98a;
      font-size: 20px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }
    .header-nav a {
      color: #f5d98a;
      text-decoration: none;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-left: 1.5rem;
      opacity: 0.85;
      transition: opacity 0.2s;
    }
    .header-nav a:hover { opacity: 1; }

    aside {
      position: fixed;
      top: 60px; left: 0; bottom: 0;
      width: 220px;
      background: #fffdf8;
      border-right: 1px solid #c9a96e;
      padding: 1.5rem 0;
      overflow-y: auto;
    }
    aside .section-label {
      font-size: 10px;
      font-weight: 700;
      color: #c9a96e;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      padding: 0 1.2rem;
      margin-bottom: 6px;
      margin-top: 1rem;
    }
    aside a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 1.2rem;
      font-size: 14px;
      color: #4a2f0e;
      text-decoration: none;
      font-weight: 400;
      border-left: 3px solid transparent;
      transition: all 0.15s;
    }
    aside a:hover, aside a.active {
      background: #f5ede0;
      border-left-color: #7a4f1e;
      color: #7a4f1e;
      font-weight: 700;
    }
    aside i { font-size: 16px; }

    main {
      margin-left: 220px;
      margin-top: 60px;
      padding: 2rem;
      min-height: calc(100vh - 60px);
    }

    .welcome-card {
      background: #fffdf8;
      border: 1px solid #c9a96e;
      border-radius: 12px;
      padding: 2rem;
      margin-bottom: 1.5rem;
    }
    .welcome-card h2 {
      font-family: 'Playfair Display', serif;
      color: #4a2f0e;
      font-size: 22px;
      margin: 0 0 4px;
    }
    .welcome-card p { color: #9e7a50; margin: 0; font-size: 14px; }

    .stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    .stat-card {
      background: #fffdf8;
      border: 1px solid #c9a96e;
      border-radius: 10px;
      padding: 1.2rem 1.5rem;
      text-align: center;
    }
    .stat-card .num {
      font-family: 'Playfair Display', serif;
      font-size: 32px;
      color: #7a4f1e;
      font-weight: 600;
    }
    .stat-card .lbl {
      font-size: 12px;
      color: #9e7a50;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 700;
    }

    .quick-links {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
    }
    .quick-link {
      background: #7a4f1e;
      color: #f5d98a;
      border-radius: 10px;
      padding: 1.5rem;
      text-decoration: none;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      font-weight: 700;
      font-size: 13px;
      letter-spacing: 1px;
      text-transform: uppercase;
      transition: background 0.2s;
    }
    .quick-link:hover { background: #5c3a14; color: #f5d98a; }
    .quick-link i { font-size: 28px; }
  </style>
</head>
<body>

<header>
  <span class="header-logo">📚 Biblioteca</span>
  <nav class="header-nav">
    <a href="dashboard.php"><i class="bi bi-house"></i> Inicio</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a>
  </nav>
</header>

<aside>
  <div class="section-label">Menú</div>
  <a href="dashboard.php" class="active"><i class="bi bi-house"></i> Inicio</a>
  <div class="section-label">Catálogo</div>
  <a href="autores.php"><i class="bi bi-person-lines-fill"></i> Autores</a>
  <a href="libros.php"><i class="bi bi-book"></i> Libros</a>
  <div class="section-label">Préstamos</div>
  <a href="prestamos.php"><i class="bi bi-bookmark-check"></i> Mis préstamos</a>
</aside>

<main>
  <div class="welcome-card">
    <h2>Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h2>
    <p>¿Qué quieres hacer hoy?</p>
  </div>

  <?php
  require_once 'db.php';
  $db = conectarDB();
  $nAutores   = $db->query("SELECT COUNT(*) FROM autores")->fetchColumn();
  $nLibros    = $db->query("SELECT COUNT(*) FROM libros")->fetchColumn();
  $nDisponibles = $db->query("SELECT COUNT(*) FROM libros WHERE disponible=1")->fetchColumn();
  $nPrestamos = $db->query("SELECT COUNT(*) FROM prestamos WHERE estado='activo'")->fetchColumn();
  ?>

  <div class="stat-grid">
    <div class="stat-card">
      <div class="num"><?= $nAutores ?></div>
      <div class="lbl">Autores</div>
    </div>
    <div class="stat-card">
      <div class="num"><?= $nLibros ?></div>
      <div class="lbl">Libros</div>
    </div>
    <div class="stat-card">
      <div class="num"><?= $nDisponibles ?></div>
      <div class="lbl">Disponibles</div>
    </div>
    <div class="stat-card">
      <div class="num"><?= $nPrestamos ?></div>
      <div class="lbl">Préstamos activos</div>
    </div>
  </div>

  <div class="quick-links">
    <a class="quick-link" href="autores.php"><i class="bi bi-person-plus"></i> Agregar autor</a>
    <a class="quick-link" href="libros.php"><i class="bi bi-book"></i> Agregar libro</a>
    <a class="quick-link" href="prestamos.php"><i class="bi bi-bookmark-plus"></i> Pedir libro</a>
  </div>
</main>

</body>
</html>
