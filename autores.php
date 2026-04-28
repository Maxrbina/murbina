<?php
session_start();
if (!isset($_SESSION['id'])) { header("Location: index.html"); exit(); }
require_once 'db.php';
$db = conectarDB();
$msg = '';

// Agregar autor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);
    if ($nombre) {
        $db->prepare("INSERT INTO autores (nombre) VALUES (?)")->execute([$nombre]);
        $msg = 'success:Autor agregado correctamente.';
    }
}

// Eliminar autor
if (isset($_GET['delete'])) {
    try {
        $db->prepare("DELETE FROM autores WHERE id=?")->execute([$_GET['delete']]);
        $msg = 'success:Autor eliminado.';
    } catch (Exception $e) {
        $msg = 'error:No se puede eliminar, tiene libros asociados.';
    }
}

$autores = $db->query("SELECT * FROM autores ORDER BY nombre ASC")->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Autores — Biblioteca</title>
  <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Lato', sans-serif; background: #f5ede0; margin: 0; }
    header {
      background: #7a4f1e; padding: 0 2rem; height: 60px;
      display: flex; align-items: center; justify-content: space-between;
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .header-logo { font-family: 'Playfair Display', serif; color: #f5d98a; font-size: 20px; font-weight: 600; }
    .header-nav a { color: #f5d98a; text-decoration: none; font-size: 13px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-left: 1.5rem; opacity: 0.85; }
    .header-nav a:hover { opacity: 1; }
    aside {
      position: fixed; top: 60px; left: 0; bottom: 0; width: 220px;
      background: #fffdf8; border-right: 1px solid #c9a96e; padding: 1.5rem 0; overflow-y: auto;
    }
    aside .section-label { font-size: 10px; font-weight: 700; color: #c9a96e; text-transform: uppercase; letter-spacing: 1.5px; padding: 0 1.2rem; margin-bottom: 6px; margin-top: 1rem; }
    aside a { display: flex; align-items: center; gap: 10px; padding: 9px 1.2rem; font-size: 14px; color: #4a2f0e; text-decoration: none; font-weight: 400; border-left: 3px solid transparent; transition: all 0.15s; }
    aside a:hover, aside a.active { background: #f5ede0; border-left-color: #7a4f1e; color: #7a4f1e; font-weight: 700; }
    aside i { font-size: 16px; }
    main { margin-left: 220px; margin-top: 60px; padding: 2rem; }
    .page-title { font-family: 'Playfair Display', serif; color: #4a2f0e; font-size: 24px; margin: 0 0 1.5rem; }
    .bib-card { background: #fffdf8; border: 1px solid #c9a96e; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; }
    .bib-label { font-size: 11px; font-weight: 700; color: #7a4f1e; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; display: block; }
    .bib-input { width: 100%; border: 1px solid #d4b483; border-radius: 7px; padding: 10px 14px; font-size: 14px; font-family: 'Lato', sans-serif; background: #fdf8f0; color: #4a2f0e; outline: none; }
    .bib-input:focus { border-color: #7a4f1e; }
    .bib-btn { background: #7a4f1e; color: #f5d98a; border: none; border-radius: 7px; padding: 10px 20px; font-size: 12px; font-family: 'Lato', sans-serif; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; cursor: pointer; margin-top: 10px; }
    .bib-btn:hover { background: #5c3a14; }
    table { width: 100%; border-collapse: collapse; }
    th { font-size: 11px; font-weight: 700; color: #7a4f1e; text-transform: uppercase; letter-spacing: 1px; padding: 10px 12px; border-bottom: 2px solid #c9a96e; text-align: left; }
    td { padding: 10px 12px; border-bottom: 1px solid #f0e0c8; font-size: 14px; color: #4a2f0e; }
    tr:hover td { background: #fdf8f0; }
    .btn-del { background: none; border: 1px solid #e8a070; color: #7a3010; border-radius: 5px; padding: 4px 10px; font-size: 12px; cursor: pointer; }
    .btn-del:hover { background: #fdf0e8; }
    .alert-ok { background: #f0f7ee; border: 1px solid #90c07a; border-radius: 7px; padding: 10px 14px; color: #2e5e1a; font-size: 13px; margin-bottom: 1rem; }
    .alert-err { background: #fdf0e8; border: 1px solid #e8a070; border-radius: 7px; padding: 10px 14px; color: #7a3010; font-size: 13px; margin-bottom: 1rem; }
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
  <a href="dashboard.php"><i class="bi bi-house"></i> Inicio</a>
  <div class="section-label">Catálogo</div>
  <a href="autores.php" class="active"><i class="bi bi-person-lines-fill"></i> Autores</a>
  <a href="libros.php"><i class="bi bi-book"></i> Libros</a>
  <div class="section-label">Préstamos</div>
  <a href="prestamos.php"><i class="bi bi-bookmark-check"></i> Mis préstamos</a>
</aside>

<main>
  <h1 class="page-title">Autores</h1>

  <?php if ($msg): ?>
    <?php [$tipo, $texto] = explode(':', $msg, 2); ?>
    <div class="<?= $tipo === 'success' ? 'alert-ok' : 'alert-err' ?>"><?= $texto ?></div>
  <?php endif; ?>

  <div class="bib-card">
    <h5 style="color:#4a2f0e;font-family:'Playfair Display',serif;margin:0 0 1rem;">Agregar autor</h5>
    <form method="POST">
      <label class="bib-label">Nombre del autor</label>
      <input class="bib-input" type="text" name="nombre" placeholder="Ej: Gabriel García Márquez" required>
      <button class="bib-btn" type="submit">+ Agregar</button>
    </form>
  </div>

  <div class="bib-card">
    <h5 style="color:#4a2f0e;font-family:'Playfair Display',serif;margin:0 0 1rem;">Lista de autores</h5>
    <?php if (empty($autores)): ?>
      <p style="color:#9e7a50;font-size:14px;">Aún no hay autores registrados.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr><th>#</th><th>Nombre</th><th>Registrado</th><th>Acción</th></tr>
        </thead>
        <tbody>
          <?php foreach ($autores as $a): ?>
          <tr>
            <td><?= $a['id'] ?></td>
            <td><?= htmlspecialchars($a['nombre']) ?></td>
            <td><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
            <td>
              <a href="autores.php?delete=<?= $a['id'] ?>" onclick="return confirm('¿Eliminar este autor?')">
                <button class="btn-del">🗑 Eliminar</button>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</main>

</body>
</html>
