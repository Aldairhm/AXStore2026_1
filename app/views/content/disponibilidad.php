<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once "./app/views/inc/head.php"; ?>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/assets/css/ss.css" />
</head>

<body class="bg-light">
    <?php require_once "./app/views/inc/header.php"; ?>
    
    <main class="py-5 mt-5">
        <section class="container fade-in">
            
          
            <div class="row mb-4">
           
                <div class="col-md-6 mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="searchInput" 
                               placeholder="Buscar productos por nombre o SKU...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

            
                <div class="col-md-6 mb-3">
                    <select class="form-select" id="categoryFilter">
                        <option value="all">Todas las categorías</option>
                      
                    </select>
                </div>
            </div>

           
            <div class="mb-3">
                <small class="text-muted">
                    <span id="resultCount">0</span> productos encontrados
                </small>
            </div>

            <!-- Grid de productos -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="product-grid">
             
            </div>

            <!-- Mensaje cuando no hay resultados -->
            <div id="noResults" class="text-center py-5 d-none">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron productos</h5>
                <p class="text-muted">Intenta con otros términos de búsqueda</p>
            </div>

        </section>
    </main>

    <?php require_once "./app/views/inc/script.php"; ?>
    <?php require_once "./app/views/inc/footer.php"; ?>
    <script src="app/views/assets/js/disponibilidad.js"></script>
</body>

</html>