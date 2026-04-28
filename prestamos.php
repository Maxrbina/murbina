<?php
session_start();
if (!isset($_SESSION['id'])) { header("Location: index.html"); exit(); }
require_once 'db.php';
$db = conectarDB();
$msg = '';
$usuario_id = $_SESSION['id'];

// Pedir libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['libro_id'])) {
    $libro_id = (int) $_POST['libro_id'];
    // Verificar disponibilidad
    $libro = $db->prepare("SELECT disponible FROM libros WHERE id=?");
    $libro->execute([$libro_id]);
    $lib = $libro->fetch();
    if ($lib && $lib['disponible']) {
        $db->prepare("INSERT INTO prestamos (usuario_id, libro_id) VALUES (?, ?)")->execute([$usuario_id, $libro_id]);
        $db->prepare("UPDATE libros SET disponible=0 WHERE id=?")->execute([$libro_id]);
        $msg = 'success:Libro solicitado correctamente.';
    } else {
        $msg = 'error:El libro no está disponible.';
    }
}

// Devolver libro
if (isset($_GET['devolver'])) {
    $prestamo_id = (int) $_GET['devolver'];
    $p = $db->prepare("SELECT libro_id FROM prestamos WHERE id=? AND usuario_id=?");
    $p->execute([$prestamo_id, $usuario_id]);
    $pr = $p->fetch();
    if ($pr) {
        $db->prepare("UPDATE prestamos SET estado='devuelto', fecha_devolucion=CURDATE() WHERE id=?")->execute([$prestamo_id]);
        $db->prepare("UPDATE libros SET disponible=1 WHERE id=?")->execute([$pr['libro_id']]);
        $msg = 'success:Libro devuelto. ¡Gracias!';
    }
}

// Libros disponibles
$disponibles = $db->query("
    SELECT l.id, l.titulo, a.nombre AS autor
    FROM libros l
    JOIN autores a ON l.autor_id = a.id
    WHERE l.disponible = 1
    ORDER BY l.titulo ASC
")->fetchAll();

// Mis préstamos
$misprestamos = $db->prepare("
    SELECT p.id, l.titulo, a.nombre AS autor, p.fecha_prestamo, p.fecha_devolucion, p.estado
    FROM prestamos p
    JOIN libros l ON p.libro_id = l.id
    JOIN autores a ON l.autor_id = a.id
    WHERE p.usuario_id = ?
    ORDER BY p.fecha_prestamo DESC
");
$misprestamos->execute([$usuario_id]);
$misprestamos = $misprestamos->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Préstamos — Biblioteca</title>
  <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Lato', sans-serif; background: #f5ede0; margin: 0; }
    header { background: #7a4f1e; padding: 0 2rem; height: 60px; display: flex; align-items: center; justify-content: space-between; position: fixed; top: 0; left: 0; right: 0; z-index: 100; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
    .header-logo { font-family: 'Playfair Display', serif; color: #f5d98a; font-size: 20px; font-weight: 600; }
    .header-nav a { color: #f5d98a; text-decoration: none; font-size: 13px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-left: 1.5rem; opacity: 0.85; }
    .header-nav a:hover { opacity: 1; }
    aside { position: fixed; top: 60px; left: 0; bottom: 0; width: 220px; background: #fffdf8; border-right: 1px solid #c9a96e; padding: 1.5rem 0; overflow-y: auto; }
    aside .section-label { font-size: 10px; font-weight: 700; color: #c9a96e; text-transform: uppercase; letter-spacing: 1.5px; padding: 0 1.2rem; margin-bottom: 6px; margin-top: 1rem; }
    aside a { display: flex; align-items: center; gap: 10px; padding: 9px 1.2rem; font-size: 14px; color: #4a2f0e; text-decoration: none; font-weight: 400; border-left: 3px solid transparent; transition: all 0.15s; }
    aside a:hover, aside a.active { background: #f5ede0; border-left-color: #7a4f1e; color: #7a4f1e; font-weight: 700; }
    aside i { font-size: 16px; }
    main { margin-left: 220px; margin-top: 60px; padding: 2rem; }
    .page-title { font-family: 'Playfair Display', serif; color: #4a2f0e; font-size: 24px; margin: 0 0 1.5rem; }
    .bib-card { background: #fffdf8; border: 1px solid #c9a96e; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; }
    .bib-label { font-size: 11px; font-weight: 700; color: #7a4f1e; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; display: block; }
    .bib-select { width: 100%; border: 1px solid #d4b483; border-radius: 7px; padding: 10px 14px; font-size: 14px; font-family: 'Lato', sans-serif; background: #fdf8f0; color: #4a2f0e; outline: none; margin-bottom: 12px; }
    .bib-select:focus { border-color: #7a4f1e; }
    .bib-btn { background: #7a4f1e; color: #f5d98a; border: none; border-radius: 7px; padding: 10px 20px; font-size: 12px; font-family: 'Lato', sans-serif; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; cursor: pointer; }
    .bib-btn:hover { background: #5c3a14; }
    table { width: 100%; border-collapse: collapse; }
    th { font-size: 11px; font-weight: 700; color: #7a4f1e; text-transform: uppercase; letter-spacing: 1px; padding: 10px 12px; border-bottom: 2px solid #c9a96e; text-align: left; }
    td { padding: 10px 12px; border-bottom: 1px solid #f0e0c8; font-size: 14px; color: #4a2f0e; }
    tr:hover td { background: #fdf8f0; }
    .badge-activo { background: #faeeda; color: #854f0b; border: 1px solid #c9a96e; border-radius: 20px; padding: 2px 10px; font-size: 11px; font-weight: 700; }
    .badge-devuelto { background: #f0f7ee; color: #2e5e1a; border: 1px solid #90c07a; border-radius: 20px; padding: 2px 10px; font-size: 11px; font-weight: 700; }
    .btn-dev { background: none; border: 1px solid #c9a96e; color: #7a4f1e; border-radius: 5px; padding: 4px 10px; font-size: 12px; cursor: pointer; font-weight: 700; }
    .btn-dev:hover { background: #f5ede0; }
    .alert-ok { background: #f0f7ee; border: 1px solid #90c07a; border-radius: 7px; padding: 10px 14px; color: #2e5e1a; font-size: 13px; margin-bottom: 1rem; }
    .alert-err { background: #fdf0e8; border: 1px solid #e8a070; border-radius: 7px; padding: 10px 14px; color: #7a3010; font-size: 13px; margin-bottom: 1rem; }
  </style>
</head>
<body>

<header>
  <span class="header-logo">Biblioteca</span>
  <nav class="header-nav">
    <a href="dashboard.php"><i class="bi bi-house"></i> Inicio</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a>
  </nav>
</header>

<aside>
  <div class="section-label">Menú</div>
  <a href="dashboard.php"><i class="bi bi-house"></i> Inicio</a>
  <div class="section-label">Catálogo</div>
  <a href="autores.php"><i class="bi bi-person-lines-fill"></i> Autores</a>
  <a href="libros.php"><i class="bi bi-book"></i> Libros</a>
  <div class="section-label">Préstamos</div>
  <a href="prestamos.php" class="active"><i class="bi bi-bookmark-check"></i> Mis préstamos</a>
</aside>

<main>
  <h1 class="page-title">Préstamos</h1>

  <?php if ($msg): ?>
    <?php [$tipo, $texto] = explode(':', $msg, 2); ?>
    <div class="<?= $tipo === 'success' ? 'alert-ok' : 'alert-err' ?>"><?= $texto ?></div>
  <?php endif; ?>

  <div class="bib-card">
    <h5 style="color:#4a2f0e;font-family:'Playfair Display',serif;margin:0 0 1rem;">Pedir un libro</h5>
    <?php if (empty($disponibles)): ?>
      <p style="color:#9e7a50;font-size:14px;">No hay libros disponibles en este momento.</p>
    <?php else: ?>
    <form method="POST">
      <label class="bib-label">Selecciona un libro disponible</label>
      <select class="bib-select" name="libro_id" required>
        <option value="">— Elige un libro —</option>
        <?php foreach ($disponibles as $l): ?>
          <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['titulo']) ?> — <?= htmlspecialchars($l['autor']) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="bib-btn" type="submit">Pedir préstamo</button>
    </form>
    <?php endif; ?>
  </div>

  <div class="bib-card">
    <h5 style="color:#4a2f0e;font-family:'Playfair Display',serif;margin:0 0 1rem;">Mis préstamos</h5>
    <?php if (empty($misprestamos)): ?>
      <p style="color:#9e7a50;font-size:14px;">No has pedido ningún libro todavía.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr><th>Libro</th><th>Autor</th><th>Fecha</th><th>Estado</th><th>Acción</th></tr>
        </thead>
        <tbody>
          <?php foreach ($misprestamos as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['titulo']) ?></td>
            <td><?= htmlspecialchars($p['autor']) ?></td>
            <td><?= date('d/m/Y', strtotime($p['fecha_prestamo'])) ?></td>
            <td><span class="<?= $p['estado'] === 'activo' ? 'badge-activo' : 'badge-devuelto' ?>"><?= ucfirst($p['estado']) ?></span></td>
            <td>
              <?php if ($p['estado'] === 'activo'): ?>
                <a href="prestamos.php?devolver=<?= $p['id'] ?>" onclick="return confirm('¿Devolver este libro?')">
                  <button class="btn-dev">↩ Devolver</button>
                </a>
              <?php else: ?>
                <span style="color:#9e7a50;font-size:12px;"><?= $p['fecha_devolucion'] ?></span>
              <?php endif; ?>
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
