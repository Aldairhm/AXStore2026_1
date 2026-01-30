<header id="header" class="header sticky-top">

    <div class="branding d-flex align-items-center">
        <div class="container position-relative d-flex align-items-center justify-content-between">
            
            <a href="<?php echo APP_URL; ?>" class="logo d-flex align-items-center">
                <h1 class="mb-0">AX<span>STORE</span></h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="<?php echo APP_URL; ?>home">Inicio</a></li>

                    <li>
                        <a href="productos">
                            <span>Productos</span>
                        </a>
                        </li>

                    <li>
                        <a href="categorias">
                            <span>Categorías</span>
                        </a>
                    </li>

                    <li >
                        <a href="usuario">
                            <span>Usuarios</span>
                            
                        </a>
                    </li>

                    <li><a href="#contacto">Contacto</a></li>
                </ul>
            </nav>

            <div class="header-actions d-flex align-items-center gap-3">
                <button class="mobile-nav-toggle" aria-label="Toggle navigation menu">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="cart-icon position-relative" role="button" tabindex="0" aria-label="Ver carrito">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count position-absolute">0</span>
                </div>

                <div class="user-icon dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo APP_URL; ?>perfil">
                            <i class="fas fa-user me-2"></i>Mi Perfil
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo APP_URL; ?>pedidos">
                            <i class="fas fa-box me-2"></i>Mis Ventas
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo APP_URL; ?>">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="mobile-nav-overlay" id="mobile-nav-overlay"></div>