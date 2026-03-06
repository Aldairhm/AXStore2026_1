<header id="header" class="header">
    <div class="branding d-flex align-items-center">
        <div class="container position-relative d-flex align-items-center justify-content-between">

            <!-- Logo mejorado -->
            <a class="logo d-flex align-items-center">
                <div class="logo-wrapper">
                    <h1 class="mb-0">AX<span>STORE</span></h1>
                    <p class="logo-tagline mb-0">Tu tienda online</p>
                </div>
            </a>

            <!-- Navegación centrada con más opciones -->
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="<?php echo APP_URL; ?>home" class="active"><i class="fas fa-home me-1"></i>Inicio</a></li>

                    <li><a href="<?php echo APP_URL; ?>productos"><i class="fas fa-shopping-bag me-1"></i>Productos</a></li>
                    <li><a href="<?php echo APP_URL; ?>categorias"><i class="fas fa-folder-open me-1"></i>Categorías</a></li>
                    <li><a href="<?php echo APP_URL; ?>catalogo"><i class="fas fa-clipboard-list me-1"></i>Catálogo</a></li>
                    <li><a href="<?php echo APP_URL; ?>salidas"><i class="fas fa-truck me-1"></i>Salidas</a></li>
                    <li><a href="<?php echo APP_URL; ?>usuario"><i class="fas fa-users me-1"></i>Usuarios</a></li>
                </ul>
            </nav>

            <!-- Acciones del header -->
            <div class="header-actions d-flex align-items-center gap-3">
                <button class="mobile-nav-toggle" aria-label="Toggle navigation menu">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Icono de Grupos WhatsApp -->
                <div class="grupos-icon dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        title="Grupos WhatsApp">
                        <i class="fab fa-whatsapp fa-lg whatsapp-icon"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow grupos-dropdown-menu" style="min-width: 320px;">
                        <li class="dropdown-header d-flex align-items-center justify-content-center py-2">
                            <i class="fab fa-whatsapp me-2 text-white"></i>
                            <strong>Grupos WhatsApp</strong>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li class="grupo-item ventas">
                            <a class="dropdown-item" href="https://chat.whatsapp.com/KL4yYuJFvCGJ6Kv1notGG4?mode=gi_t" target="_blank">
                                <div class="grupo-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="grupo-content">
                                    <span class="grupo-nombre">Ventas Online</span>
                                    <small class="grupo-desc">Realiza tus pedidos</small>
                                </div>
                                <i class="fas fa-external-link-alt grupo-arrow"></i>
                            </a>
                        </li>

                        <li class="grupo-item entregas-ss">
                            <a class="dropdown-item" href="https://chat.whatsapp.com/CvaX87e1rweHpN17d7Z3DT?mode=hq1tswa" target="_blank">
                                <div class="grupo-icon">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <div class="grupo-content">
                                    <span class="grupo-nombre">Entregas San Salvador</span>
                                    <small class="grupo-desc">Seguimiento SS</small>
                                </div>
                                <i class="fas fa-external-link-alt grupo-arrow"></i>
                            </a>
                        </li>

                        <li class="grupo-item entregas-dept">
                            <a class="dropdown-item" href="https://chat.whatsapp.com/KiwDZlHO3LHHFWJ02H4vhB?mode=gi_t" target="_blank">
                                <div class="grupo-icon">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                                <div class="grupo-content">
                                    <span class="grupo-nombre">Entregas Departamentales</span>
                                    <small class="grupo-desc">Envíos a todo el país</small>
                                </div>
                                <i class="fas fa-external-link-alt grupo-arrow"></i>
                            </a>
                        </li>

                        <li class="grupo-item consultas">
                            <a class="dropdown-item" href="https://chat.whatsapp.com/JMjwo6P73evJ7vzYllO3Nm?mode=hq2tswa" target="_blank">
                                <div class="grupo-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div class="grupo-content">
                                    <span class="grupo-nombre">Consultas</span>
                                    <small class="grupo-desc">Resuelve tus dudas</small>
                                </div>
                                <i class="fas fa-external-link-alt grupo-arrow"></i>
                            </a>
                        </li>

                        <li class="grupo-item fotos">
                            <a class="dropdown-item" href="https://chat.whatsapp.com/LoHOGrP5cfx0ElXwrkXLR4?mode=hqctswa" target="_blank">
                                <div class="grupo-icon">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div class="grupo-content">
                                    <span class="grupo-nombre">Fotos de Paquetes</span>
                                    <small class="grupo-desc">Evidencias de entrega</small>
                                </div>
                                <i class="fas fa-external-link-alt grupo-arrow"></i>
                            </a>
                        </li>

                        <li class="grupo-item devoluciones">
                            <a class="dropdown-item" href="https://chat.whatsapp.com/I66pPbxESDAIbyxxMrHsZU?mode=gi_t" target="_blank">
                                <div class="grupo-icon">
                                    <i class="fas fa-undo-alt"></i>
                                </div>
                                <div class="grupo-content">
                                    <span class="grupo-nombre">Devoluciones</span>
                                    <small class="grupo-desc">Gestión de retornos</small>
                                </div>
                                <i class="fas fa-external-link-alt grupo-arrow"></i>
                            </a>
                        </li>

                        <li class="grupo-item agotado">
                            <a class="dropdown-item" href="#categorias" target="_blank">
                                <div class="grupo-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="grupo-content">
                                    <span class="grupo-nombre">Producto Agotado</span>
                                    <small class="grupo-desc">Reportar sin stock</small>
                                </div>
                                <i class="fas fa-external-link-alt grupo-arrow"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Icono de Carrito -->
                <div class="cart-icon position-relative" role="button" tabindex="0" aria-label="Ver carrito">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count position-absolute">0</span>
                </div>

                <!-- Icono de Usuario -->
                <div class="user-icon dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside">
                        <i class="fas fa-user-circle fa-lg"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 220px;">
                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center"
                                data-bs-toggle="collapse"
                                href="#infoPerfil"
                                role="button"
                                aria-expanded="false"
                                aria-controls="infoPerfil">
                                <span><i class="fas fa-user me-2"></i>Mi Perfil</span>
                                <i class="fas fa-chevron-down" style="font-size: 0.8em;"></i>
                            </a>

                            <div class="collapse" id="infoPerfil">
                                <?php if (isset($_SESSION['usuario'])): ?>
                                    <div class="bg-light p-3 mx-2 rounded border mt-1 shadow-sm">
                                        <div class="fw-bold text-dark text-break">
                                            <?php echo $_SESSION['usuario']['nombre_real']; ?>
                                        </div>
                                        <div class="small text-muted mb-2">
                                            <?php echo $_SESSION['usuario']['username']; ?>
                                        </div>
                                    </div>
                                <?php
else: ?>
                                    <div class="p-2 text-center text-muted small">No hay sesión activa</div>
                                <?php
endif; ?>
                            </div>
                        </li>

                        <li>
                            <a class="dropdown-item" href="<?php echo APP_URL; ?>#">
                                <i class="fas fa-box me-2"></i>Mis Ventas
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item text-danger" href="<?php echo APP_URL; ?>login?opcion=cerrar">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="mobile-nav-overlay" id="mobile-nav-overlay"></div>

