<!DOCTYPE html>
<html lang="es">

<?php include 'app/views/inc/head.php'; ?>

<body class="bg-light">
    <?php include 'app/views/inc/header.php'; ?>

    <main>
        <section id="inicio" class="hero d-flex align-items-center justify-content-center text-center vh-100 bg-luxury text-white">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 fade-in">
                        <h2 class="display-3 font-luxury mb-1"> Todo lo que necesitas<br></h2>
                        <p  class=" mb-4 text-white-50">Variedad, calidad y confianza</p>
                        <a href="#categorias" class="btn btn-outline-dark rounded-0 px-4" >Ver Colección</a>
                      
                    </div>
                </div>
            </div>
        </section>

        <section class="categories py-5">
            <div class="container text-center">
                <h2 class="section-title mb-5">Nuestras Categorías</h2>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <button class="btn btn-outline-dark rounded-0 px-4" data-category="all">Todos</button>
                    <button class="btn btn-outline-dark rounded-0 px-4" data-category="autopartes">Autopartes</button>
                    <button class="btn btn-outline-dark rounded-0 px-4" data-category="hogar">Hogar</button>
                    <button class="btn btn-outline-dark rounded-0 px-4" data-category="">....</button>
                </div>
            </div>
        </section>

        <section id="categorias" class="products py-5 bg-white">
            <div class="container">
                <h2 class="section-title text-center mb-5">Nuestros Productos</h2>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="product-grid">
                </div>
            </div>
        </section>

        <section id="nosotros" class="about py-5 bg-luxury text-white">
            <div class="container">
  <div class="row align-items-center g-5">

    <div class="col-12">
      <h2 class="section-title text-white">
        Calidad que Marca la Diferencia
      </h2><p class="text-white-50 mb-4">
        En nuestra tienda seleccionamos cuidadosamente productos para el hogar, ropa y repuestos de auto que combinan funcionalidad, durabilidad y buen diseño. </p>
      <p class="text-white-50">
        Trabajamos con proveedores confiables para garantizar artículos de calidad que se adapten a tus necesidades del dia a dia.</p>
    </div>
    
    
</div>

        </section>

        <section class="features py-5">
            <div class="container">
                <div class="row g-4 text-center">
                    <div class="col-md-4">
                        <div class="p-4 bg-white border h-100 shadow-sm">
                            <i class="fas fa-gem fa-3x text-black mb-3"></i>
                            <h3 class="h5 font-luxury">Materiales Premium</h3>
                            <p class="text-muted small">Solo materiales de la más alta calidad</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-white border h-100 shadow-sm">
                            <i class="fas fa-award fa-3x text-black mb-3"></i>
                            <h3 class="h5 font-luxury">Garantía de Calidad</h3>
                            <p class="text-muted small">Contamos con los mejores proveedores</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-white border h-100 shadow-sm">
                            <i class="fas fa-heart fa-3x text-black mb-3"></i>
                            <h3 class="h5 font-luxury">Hecho con Amor</h3>
                            <p class="text-muted small">Cada pieza es única</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="modal fade" id="cart-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content rounded-0 border-0">
                <div class="modal-header bg-luxury text-gold">
                    <h3 class="modal-title font-luxury h5">Tu Carrito</h3>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="cart-body">
                    <p class="empty-cart text-center py-5">Tu carrito está vacío</p>
                </div>
                <div class="modal-footer d-block border-top-0">
                    <div class="d-flex justify-content-between mb-3 fs-5 font-luxury">
                        <span>Total:</span>
                        <span id="cart-total" class="text-gold">$0.00</span>
                    </div>
                    <button class="btn btn-luxury w-100 py-3" data-bs-target="#checkout-modal" data-bs-toggle="modal">Finalizar Compra</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="checkout-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-0 border-0">
                <div class="modal-header bg-luxury text-gold">
                    <h3 class="modal-title font-luxury h5">Finalizar Compra</h3>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="checkout-form">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Nombre Completo *</label>
                                <input type="text" class="form-control rounded-0 border-gold" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Teléfono *</label>
                                <input type="tel" class="form-control rounded-0 border-gold" name="telefono" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Dirección *</label>
                            <textarea class="form-control rounded-0 border-gold" name="direccion" rows="3" required></textarea>
                        </div>
                        <div class="bg-light p-3 mb-4">
                            <h4 class="h6 font-luxury border-bottom pb-2 mb-3">Resumen del Pedido</h4>
                            <div id="checkout-items" class="small mb-3"></div>
                            <div class="d-flex justify-content-between fw-bold border-top pt-2">
                                <span>Total:</span>
                                <span id="checkout-total" class="text-gold">$0.00</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary w-100 rounded-0" data-bs-target="#cart-modal" data-bs-toggle="modal">Volver</button>
                            <button type="submit" class="btn btn-luxury w-100 rounded-0">Confirmar Pedido</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmation-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-5 border-0 rounded-0">
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-5x text-success"></i>
                </div>
                <h3 class="font-luxury mb-3">¡Pedido Confirmado!</h3>
                <p class="text-muted mb-4" id="confirmation-message">Tu pedido ha sido procesado exitosamente.</p>
                <button class="btn btn-luxury w-100" data-bs-dismiss="modal">Continuar Comprando</button>
            </div>
        </div>
    </div>

    <?php include 'app/views/inc/footer.php'; ?>
    <?php include 'app/views/inc/script.php'; ?>
</body>
</html>