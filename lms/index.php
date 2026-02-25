<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS - TecnoFutura Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .coming-soon-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 600px;
        }
        .icon {
            font-size: 5rem;
            color: #ffc107;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .feature-list {
            text-align: left;
            margin: 30px 0;
        }
        .feature-list li {
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="coming-soon-card">
        <i class="bi bi-mortarboard-fill icon"></i>
        <h1 class="mb-3">Plataforma LMS</h1>
        <p class="lead text-muted mb-4">
            El sistema de gestión de aprendizaje está en desarrollo. Pronto podrás acceder a todos nuestros cursos.
        </p>
        
        <div class="alert alert-warning">
            <i class="bi bi-construction me-2"></i>
            <strong>En construcción</strong>
        </div>

        <div class="feature-list">
            <h5 class="fw-bold mb-3">Características próximas:</h5>
            <ul class="list-unstyled">
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Acceso a cursos interactivos</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Simulador de Arduino integrado</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Proyectos prácticos guiados</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Evaluaciones y certificados</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Foros y comunidad</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Seguimiento de progreso</li>
            </ul>
        </div>

        <hr class="my-4">

        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
            <a href="../index.php" class="btn btn-primary btn-lg">
                <i class="bi bi-house-fill me-2"></i>Volver al inicio
            </a>
            <a href="mailto:contacto@tecnofutura.academy" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-envelope-fill me-2"></i>Contactar
            </a>
        </div>

        <p class="text-muted mt-4 small">
            <i class="bi bi-info-circle me-1"></i>
            Si ya compraste un curso, recibirás un email cuando el LMS esté disponible.
        </p>
    </div>
</body>
</html>
