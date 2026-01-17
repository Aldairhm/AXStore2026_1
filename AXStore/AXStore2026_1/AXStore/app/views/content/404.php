<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once "app/views/inc/head.php"; ?>
</head>

<body>
    <main>
        <div class="container">

            <section class="section error-404 min-vh-100 d-flex flex-column align-items-center justify-content-center">
                <h1>404</h1>
                <h2>La página que estás buscando no existe.</h2>
                <a class="btn" href="inicio">Volver al inicio</a>
                <img src="<?php echo APP_URL; ?>app/views/assets/images/not-found.svg" class="img-fluid py-3" alt="Page Not Found" style="max-width: 350px; height: auto;">
            </section>
        </div>
    </main>
</body>

</html>