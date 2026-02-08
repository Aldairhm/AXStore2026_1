<!DOCTYPE html>
<html lang="es">

<?php include 'app/views/inc/head.php'; ?>

<body class="bg-light">
    <?php include 'app/views/inc/header.php'; ?>

    <main>
        <section id="inicio" class="hero d-flex align-items-center justify-content-center text-center position-relative vh-100 text-white">
            <!-- Overlay oscuro para mejorar legibilidad -->
            <div class="hero-overlay position-absolute w-100 h-100"></div>
            
            <!-- Contenido centrado -->
            <div class="container position-relative hero-content">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-8">
                        <h1 class="display-2 display-md-1 font-luxury mb-4 text-uppercase hero-title">
                            Todo lo que necesitas
                        </h1>
                        
                        <p class="lead mb-5 hero-subtitle">
                            Variedad, calidad y confianza
                        </p>
                        
                        <a href="#categorias" class="btn btn-outline-light btn-lg rounded-0 px-5 py-3 hero-button">
                            VER COLECCIÓN
                        </a>
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
                        </h2>
                        <p class="text-white-50 mb-4">
                            En nuestra tienda seleccionamos cuidadosamente productos para el hogar, ropa y repuestos de auto que combinan funcionalidad, durabilidad y buen diseño. </p>
                        <p class="text-white-50">
                            Trabajamos con proveedores confiables para garantizar artículos de calidad que se adapten a tus necesidades del dia a dia.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN DE ENVÍOS ACTUALIZADA -->
        <section class="shipping-info py-5 bg-white" id="envios">
            <div class="container">
                <!-- Título Principal -->
                <div class="text-center mb-5">
                    <h2 class="section-title mb-3">Información de Envíos</h2>
                    <p class="text-muted">Entregamos tu pedido de forma segura en toda El Salvador</p>
                </div>

                <!-- Cards de Tipos de Envío -->
                <div class="row g-4 mb-5">
                    <!-- Envío Local -->
                    <div class="col-lg-4">
                        <div class="shipping-card h-100 p-4 bg-light border-0 shadow-sm position-relative overflow-hidden">
                            <div class="shipping-icon-bg position-absolute opacity-10">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="position-relative">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="shipping-icon bg-luxury text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; flex-shrink: 0;">
                                        <i class="fas fa-map-marker-alt fa-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="h4 font-luxury mb-1">Envío Local</h3>
                                        <p class="text-muted small mb-0">San Salvador - Puntos Céntricos</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Costo de envío</span>
                                        <span class="h4 text-luxury mb-0 font-luxury">$3.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Tiempo de entrega</span>
                                        <span class="fw-semibold">Mismo día</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Horario</span>
                                        <span class="fw-semibold">8:00 AM - 4:00 PM</span>
                                    </div>
                                </div>

                                <div class="alert alert-info border-0 bg-white shadow-sm mb-3" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-info-circle text-info mt-1 me-2"></i>
                                        <small class="mb-0">
                                            <strong>Puntos de entrega:</strong> Gasolineras, Hospitales, Parques, Escuelas y Centros Comerciales.
                                        </small>
                                    </div>
                                </div>

                                <!-- Zonas de Entrega -->
                                <div class="zones-container mb-3">
                                    <p class="small fw-bold text-uppercase text-muted mb-2">Zonas de cobertura:</p>
                                    
                                    <div class="zone-detail mb-3 p-3 bg-white border">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-sun text-warning me-2"></i>
                                            <h5 class="h6 mb-0 font-luxury">Zona Occidente - Ruta Matutina</h5>
                                        </div>
                                        <p class="small text-muted mb-2">
                                            <i class="fas fa-map-pin me-1"></i>
                                            Santa Tecla, Merliot, Santa Elena, Escalón, San Marcos, Olímpica, Constitución, Centro S.S.
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center small">
                                            <span class="badge bg-luxury text-white">Entrega: Mañana</span>
                                            <span class="text-danger fw-bold">
                                                <i class="fas fa-clock me-1"></i>Cierre: 11:00 AM
                                            </span>
                                        </div>
                                    </div>

                                    <div class="zone-detail p-3 bg-white border">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-moon text-primary me-2"></i>
                                            <h5 class="h6 mb-0 font-luxury">Zona Oriente - Ruta Vespertina</h5>
                                        </div>
                                        <p class="small text-muted mb-2">
                                            <i class="fas fa-map-pin me-1"></i>
                                            Centro S.S., Mejicanos, Apopa, Soyapango, Ilopango, San Martín
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center small">
                                            <span class="badge bg-dark text-white">Entrega: Tarde</span>
                                            <span class="text-danger fw-bold">
                                                <i class="fas fa-clock me-1"></i>Cierre: 2:00 PM
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Envío Departamental -->
                    <div class="col-lg-4">
                        <div class="shipping-card h-100 p-4 bg-light border-0 shadow-sm position-relative overflow-hidden">
                            <div class="shipping-icon-bg position-absolute opacity-10">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="position-relative">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="shipping-icon bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; flex-shrink: 0;">
                                        <i class="fas fa-truck fa-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="h4 font-luxury mb-1">Envío Departamental</h3>
                                        <p class="text-muted small mb-0">Todo El Salvador - Puntos Céntricos</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Costo de envío</span>
                                        <span class="h4 text-luxury mb-0 font-luxury">$4.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Tiempo de entrega</span>
                                        <span class="fw-semibold">24 a 48 horas</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Horario</span>
                                        <span class="fw-semibold">8:00 AM - 4:00 PM</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Cierre pedidos</span>
                                        <span class="text-danger fw-bold">2:00 PM - 3:00 PM</span>
                                    </div>
                                </div>

                                <div class="alert alert-warning border-0 bg-white shadow-sm mb-3" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-exclamation-triangle text-warning mt-1 me-2"></i>
                                        <small class="mb-0">
                                            <strong>Importante:</strong> San Salvador Sur se considera envío departamental. Incluye puntos céntricos como gasolineras, hospitales, parques y plazas.
                                        </small>
                                    </div>
                                </div>

                                <div class="alert alert-danger border-0 bg-white shadow-sm mb-3" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-calendar-times text-danger mt-1 me-2"></i>
                                        <small class="mb-0">
                                            <strong>No laboramos domingos:</strong> Pedidos hechos sábados después de hora de cierre se entregan el martes.
                                        </small>
                                    </div>
                                </div>

                                <!-- Departamentos -->
                                <div class="departments-container">
                                    <p class="small fw-bold text-uppercase text-muted mb-2">Departamentos:</p>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Ahuachapán</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Santa Ana</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Sonsonate</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">La Libertad</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Chalatenango</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Cuscatlán</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">La Paz</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Cabañas</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">San Vicente</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Usulután</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">San Miguel</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">Morazán</span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">La Unión</span>
                                        <span class="badge bg-luxury text-white px-2 py-1 small">SS Sur</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NUEVO: Envío Personalizado a Domicilio -->
                    <div class="col-lg-4">
                        <div class="shipping-card h-100 p-4 bg-light border-0 shadow-sm position-relative overflow-hidden">
                            <div class="shipping-icon-bg position-absolute opacity-10">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="position-relative">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="shipping-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; flex-shrink: 0;">
                                        <i class="fas fa-home fa-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="h4 font-luxury mb-1">Envío Personalizado</h3>
                                        <p class="text-muted small mb-0">Entrega a Domicilio</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Costo de envío</span>
                                        <span class="h4 text-success mb-0 font-luxury">$5.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Cobertura</span>
                                        <span class="fw-semibold">Todo El Salvador</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Tiempo</span>
                                        <span class="fw-semibold">Mismo día / 24-48h</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Tipo de entrega</span>
                                        <span class="fw-semibold">Dirección exacta</span>
                                    </div>
                                </div>

                                <div class="alert alert-success border-0 bg-white shadow-sm mb-3" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-star text-success mt-1 me-2"></i>
                                        <small class="mb-0">
                                            <strong>Servicio premium:</strong> Entregamos hasta la puerta de tu casa tanto en San Salvador como en otros departamentos.
                                        </small>
                                    </div>
                                </div>

                                <div class="alert alert-warning border-0 bg-white shadow-sm mb-3" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-exclamation-circle text-warning mt-1 me-2"></i>
                                        <small class="mb-0">
                                            <strong>Restricción:</strong> No realizamos envíos a cantones por difícil acceso y limitaciones de encomienda.
                                        </small>
                                    </div>
                                </div>

                                <div class="alert alert-danger border-0 bg-white shadow-sm mb-0" role="alert">
                                    <div class="d-flex">
                                        <i class="fas fa-calendar-times text-danger mt-1 me-2"></i>
                                        <small class="mb-0">
                                            <strong>Domingos:</strong> No realizamos ningún tipo de envío los domingos.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Proceso de Entrega -->
                <div class="delivery-process bg-luxury text-white p-5 mb-5">
                    <div class="row align-items-center">
                        <div class="col-lg-4 text-center text-lg-start mb-4 mb-lg-0">
                            <i class="fas fa-route fa-3x mb-3 text-gold"></i>
                            <h3 class="h4 font-luxury mb-2">Proceso de Entrega</h3>
                            <p class="text-white-50 small mb-0">Así llega tu pedido hasta ti</p>
                        </div>
                        <div class="col-lg-8">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-white bg-opacity-10 h-100">
                                        <div class="step-number bg-gold text-dark rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-2" style="width: 40px; height: 40px;">1</div>
                                        <h5 class="h6 font-luxury mb-1">Confirmas tu pedido</h5>
                                        <p class="text-white-50 small mb-0">Antes del horario de cierre</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-white bg-opacity-10 h-100">
                                        <div class="step-number bg-gold text-dark rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-2" style="width: 40px; height: 40px;">2</div>
                                        <h5 class="h6 font-luxury mb-1">Preparamos el envío</h5>
                                        <p class="text-white-50 small mb-0">Organizamos la ruta</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-white bg-opacity-10 h-100">
                                        <div class="step-number bg-gold text-dark rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-2" style="width: 40px; height: 40px;">3</div>
                                        <h5 class="h6 font-luxury mb-1">Lo entregamos</h5>
                                        <p class="text-white-50 small mb-0">En tu punto o domicilio</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preguntas Frecuentes -->
                <div class="faq-section">
                    <h3 class="h4 font-luxury text-center mb-4">Preguntas Frecuentes</h3>
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="accordion accordion-flush" id="shippingFAQ">
                                <!-- FAQ 1 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Cuál es la diferencia entre los tres tipos de envío?
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            <strong>Envío Local ($3):</strong> San Salvador, entrega en puntos céntricos el mismo día.<br>
                                            <strong>Envío Departamental ($4):</strong> Otros departamentos, entrega en puntos céntricos en 24-48h.<br>
                                            <strong>Envío Personalizado ($5):</strong> Entrega a domicilio en toda dirección exacta de El Salvador (excepto cantones).
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 2 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Por qué no hacen envíos a cantones?
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            Los cantones generalmente tienen difícil acceso y las empresas de encomienda no ingresan a estas zonas. Por seguridad y logística, solo entregamos en zonas urbanas y puntos céntricos accesibles.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 3 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Qué pasa si hago un pedido departamental el sábado?
                                        </button>
                                    </h2>
                                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            Si realizas tu pedido departamental el sábado antes de la hora de cierre, se envia para dia lunes. Si lo haces después de la hora de cierre pasa para dia martes, ya que no laboramos domingos.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 4 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Qué son "puntos céntricos" en envíos departamentales?
                                        </button>
                                    </h2>
                                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            Lugares de fácil acceso y conocidos como: gasolineras principales (Shell, Texaco, Puma), hospitales públicos, parques centrales, plazas comerciales, escuelas o colegios reconocidos. Te ayudamos a coordinar el punto más cercano a ti.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 5 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Hasta qué hora puedo hacer pedidos para envío local en San Salvador?
                                        </button>
                                    </h2>
                                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            Depende de tu zona:<br>
                                            <strong>Zona Occidente:</strong> Hasta las 11:00 AM<br>
                                            <strong>Zona Oriente:</strong> Hasta las 2:00 PM<br>
                                            Pedidos después de estos horarios se procesan para el día siguiente.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 6 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Me pueden confirmar la hora exacta de entrega?
                                        </button>
                                    </h2>
                                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            No manejamos horas exactas de entrega. Nuestro horario general es de 8:00 AM a 4:00 PM. Te contactamos cuando el pedido esté en camino para coordinar mejor.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 7 -->
                                <div class="accordion-item border-0 mb-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿El envío personalizado a domicilio cubre todo El Salvador?
                                        </button>
                                    </h2>
                                    <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            Sí, el envío personalizado de $5.00 cubre todo El Salvador con entrega a domicilio, EXCEPTO cantones. Entregamos en zonas urbanas de todos los departamentos directamente en tu dirección.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 8 -->
                                <div class="accordion-item border-0 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light font-luxury" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                                            <i class="fas fa-question-circle text-luxury me-2"></i>
                                            ¿Por qué San Salvador Sur es envío departamental?
                                        </button>
                                    </h2>
                                    <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                        <div class="accordion-body bg-white">
                                            Por la distancia y logística de rutas, zonas como Planes de Renderos, Panchimalco, San Marcos y otras del sur se consideran envío departamental. El costo es de $4.00 en puntos céntricos o $5.00 a domicilio.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Final -->
                <div class="text-center mt-5 pt-4 border-top">
                    <h4 class="font-luxury mb-3">¿Tienes más preguntas?</h4>
                    <p class="text-muted mb-4">Estamos aquí para ayudarte</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="https://wa.me/50312345678" class="btn btn-success btn-lg rounded-0 px-4" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>Contactar por WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Modales -->
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