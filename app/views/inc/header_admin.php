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
                    <li><a href="<?php echo APP_URL; ?>usuario"><i class="fas fa-users me-1"></i>Usuarios</a></li>
                    <li><a href="#contacto"><i class="fas fa-envelope me-1"></i>Contacto</a></li>
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
                            <a class="dropdown-item" href="LINK_DE_VENTAS_ONLINE" target="_blank">
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
                            <a class="dropdown-item" href="LINK_ENTREGAS_SS" target="_blank">
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
                            <a class="dropdown-item" href="LINK_ENTREGAS_DEPT" target="_blank">
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
                            <a class="dropdown-item" href="LINK_CONSULTAS" target="_blank">
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
                            <a class="dropdown-item" href="LINK_FOTOS" target="_blank">
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
                            <a class="dropdown-item" href="LINK_DEVOLUCIONES" target="_blank">
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
                            <a class="dropdown-item" href="LINK_AGOTADO" target="_blank">
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
                                <?php else: ?>
                                    <div class="p-2 text-center text-muted small">No hay sesión activa</div>
                                <?php endif; ?>
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

<style>
    /* ====================================
   MEJORAS DE ESPACIADO Y DISTRIBUCIÓN
   ==================================== */

    .header {
        padding: 1rem 0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .container {
        max-width: 1400px;
    }

    /* ====================================
   LOGO MEJORADO CON TAGLINE
   ==================================== */

    .logo-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
    }

    .logo h1 {
        font-size: 1.8rem;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .logo-tagline {
        font-size: 0.65rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 500;
        margin-left: 2px;
    }

    /* ====================================
   NAVEGACIÓN CENTRADA Y ESPACIADA
   ==================================== */

    .navmenu {
        flex: 1;
        display: flex;
        justify-content: center;
        margin: 0 2rem;
    }

    .navmenu ul {
        display: flex;
        gap: 0.5rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .navmenu li {
        margin: 0;
    }

    .navmenu a {
        display: flex;
        align-items: center;
        padding: 0.65rem 1.2rem;
        font-size: 0.95rem;
        font-weight: 500;
        color: #2d3748;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .navmenu a i {
        font-size: 0.9rem;
        transition: transform 0.3s ease;
    }

    .navmenu a:hover {
        background: rgba(0, 123, 255, 0.08);
        color: #007bff;
        transform: translateY(-2px);
    }

    .navmenu a:hover i {
        transform: scale(1.15);
    }

    .navmenu a.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .navmenu a.active:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
    }

    /* ====================================
   HEADER ACTIONS CON MEJOR ESPACIADO
   ==================================== */

    .header-actions {
        gap: 1.2rem !important;
    }

    .header-actions>div,
    .header-actions>button {
        position: relative;
    }

    /* ====================================
   ESTILOS PARA ICONO GRUPOS WHATSAPP
   ==================================== */

    .grupos-icon .whatsapp-icon {
        color: #25D366;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 1.4rem;
    }

    .grupos-icon .whatsapp-icon:hover {
        transform: scale(1.15);
        filter: drop-shadow(0 0 8px rgba(37, 211, 102, 0.5));
        animation: pulse 1s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1.15);
        }

        50% {
            transform: scale(1.25);
        }
    }

    /* ====================================
   DROPDOWN MENU DE GRUPOS
   ==================================== */

    .grupos-dropdown-menu {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        padding: 0.5rem 0;
        animation: fadeInDown 0.3s ease;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
    }

    .grupos-dropdown-menu .dropdown-header {
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        color: white;
        border-radius: 8px;
        margin: 0 0.5rem 0.5rem 0.5rem;
        font-size: 1rem;
    }

    /* ====================================
   ITEMS DE GRUPOS
   ==================================== */

    .grupo-item {
        margin: 0.25rem 0.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .grupo-item .dropdown-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0.85rem;
        gap: 0.75rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
    }

    .grupo-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
        color: white;
    }

    .grupo-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
    }

    .grupo-nombre {
        font-weight: 600;
        font-size: 0.9rem;
        color: #2d3748;
        display: block;
        transition: color 0.3s ease;
    }

    .grupo-desc {
        font-size: 0.72rem;
        color: #718096;
        display: block;
        transition: color 0.3s ease;
    }

    .grupo-arrow {
        font-size: 0.75rem;
        color: #a0aec0;
        opacity: 0;
        transform: translateX(-8px);
        transition: all 0.3s ease;
    }

    /* ====================================
   COLORES POR TIPO DE GRUPO
   ==================================== */

    .grupo-item.ventas .grupo-icon {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }

    .grupo-item.ventas:hover .dropdown-item {
        background: linear-gradient(135deg, rgba(72, 187, 120, 0.08) 0%, rgba(56, 161, 105, 0.08) 100%);
    }

    .grupo-item.entregas-ss .grupo-icon {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    }

    .grupo-item.entregas-ss:hover .dropdown-item {
        background: linear-gradient(135deg, rgba(66, 153, 225, 0.08) 0%, rgba(49, 130, 206, 0.08) 100%);
    }

    .grupo-item.entregas-dept .grupo-icon {
        background: linear-gradient(135deg, #9f7aea 0%, #805ad5 100%);
    }

    .grupo-item.entregas-dept:hover .dropdown-item {
        background: linear-gradient(135deg, rgba(159, 122, 234, 0.08) 0%, rgba(128, 90, 213, 0.08) 100%);
    }

    .grupo-item.consultas .grupo-icon {
        background: linear-gradient(135deg, #ecc94b 0%, #d69e2e 100%);
    }

    .grupo-item.consultas:hover .dropdown-item {
        background: linear-gradient(135deg, rgba(236, 201, 75, 0.08) 0%, rgba(214, 158, 46, 0.08) 100%);
    }

    .grupo-item.fotos .grupo-icon {
        background: linear-gradient(135deg, #ed64a6 0%, #d53f8c 100%);
    }

    .grupo-item.fotos:hover .dropdown-item {
        background: linear-gradient(135deg, rgba(237, 100, 166, 0.08) 0%, rgba(213, 63, 140, 0.08) 100%);
    }

    .grupo-item.devoluciones .grupo-icon {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
    }

    .grupo-item.devoluciones:hover .dropdown-item {
        background: linear-gradient(135deg, rgba(237, 137, 54, 0.08) 0%, rgba(221, 107, 32, 0.08) 100%);
    }

    .grupo-item.agotado .grupo-icon {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    }

    .grupo-item.agotado:hover .dropdown-item {
        background: linear-gradient(135deg, rgba(245, 101, 101, 0.08) 0%, rgba(229, 62, 62, 0.08) 100%);
    }

    /* ====================================
   EFECTOS HOVER
   ==================================== */

    .grupo-item:hover .dropdown-item {
        transform: translateX(3px);
    }

    .grupo-item:hover .grupo-icon {
        transform: scale(1.08) rotate(3deg);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .grupo-item:hover .grupo-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    .grupo-item:hover .grupo-nombre {
        color: #1a202c;
    }

    .grupo-item:hover .grupo-desc {
        color: #4a5568;
    }

    /* ====================================
   CARRITO CON MEJORAS VISUALES
   ==================================== */

    .cart-icon {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .cart-icon i {
        font-size: 1.3rem;
        color: #2d3748;
        transition: all 0.3s ease;
    }

    .cart-icon:hover i {
        color: #007bff;
        transform: scale(1.1);
    }

    .cart-count {
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
        border-radius: 50%;
        padding: 0.15rem 0.4rem;
        font-size: 0.7rem;
        font-weight: 600;
        min-width: 18px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(245, 101, 101, 0.4);
    }

    /* ====================================
   USUARIO CON MEJORAS
   ==================================== */

    .user-icon i {
        font-size: 1.5rem;
        color: #2d3748;
        transition: all 0.3s ease;
    }

    .user-icon:hover i {
        color: #667eea;
        transform: scale(1.1);
    }

    .user-icon .dropdown-menu {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        animation: fadeInDown 0.3s ease;
    }

    .user-icon .dropdown-item {
        border-radius: 8px;
        margin: 0.15rem 0.5rem;
        transition: all 0.3s ease;
    }

    .user-icon .dropdown-item:not(.text-danger):hover {
        background: rgba(0, 123, 255, 0.08);
        transform: translateX(3px);
    }

    .user-icon .dropdown-item.text-danger:hover {
        background: linear-gradient(135deg, rgba(245, 101, 101, 0.08) 0%, rgba(229, 62, 62, 0.08) 100%);
        transform: translateX(3px);
    }

    .user-icon .dropdown-item i {
        font-size: 1rem;
        transition: transform 0.3s ease;
    }

    .user-icon .dropdown-item:hover i {
        transform: scale(1.15);
    }

    /* ====================================
   ANIMACIÓN DE ENTRADA
   ==================================== */

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ====================================
   RESPONSIVE
   ==================================== */

    @media (max-width: 1200px) {
        .navmenu {
            margin: 0 1rem;
        }

        .navmenu a {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 992px) {
        .navmenu {
            display: none;
        }

        .logo h1 {
            font-size: 1.5rem;
        }

        .logo-tagline {
            font-size: 0.6rem;
        }

        .header-actions {
            gap: 1rem !important;
        }
    }

    @media (max-width: 768px) {
        .grupos-dropdown-menu {
            min-width: 280px !important;
        }

        .grupo-icon {
            width: 35px;
            height: 35px;
            font-size: 0.95rem;
        }

        .grupo-nombre {
            font-size: 0.85rem;
        }

        .grupo-desc {
            font-size: 0.7rem;
        }

        .logo h1 {
            font-size: 1.3rem;
        }

        .logo-tagline {
            display: none;
        }
    }
</style>