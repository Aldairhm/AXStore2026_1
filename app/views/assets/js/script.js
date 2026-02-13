document.addEventListener('DOMContentLoaded', function() {
    
    console.log('✓ Script.js cargado correctamente');
    
    // ==========================================
    // 1. LÓGICA DEL MENÚ MÓVIL MEJORADA
    // ==========================================
    
    const mobileMenuBtn = document.querySelector('.mobile-nav-toggle');
    const navmenu = document.getElementById('navmenu');
    const navLinks = document.querySelectorAll('.navmenu a');
    const mobileOverlay = document.getElementById('mobile-nav-overlay');
    const body = document.body;

    // [NUEVO] Referencias a los botones de WhatsApp y Usuario
    const whatsappToggleBtn = document.querySelector('.grupos-icon [data-bs-toggle="dropdown"]');
    const userTogleBtn = document.querySelector('.user-icon [data-bs-toggle="dropdown"]');
    
    if (mobileMenuBtn && navmenu) {
        const icon = mobileMenuBtn.querySelector('i');

        // [NUEVO] Función auxiliar para cerrar dropdowns de Bootstrap manualmente
        function cerrarDropdownBootstrap(selectorPadre) {
            const dropdownMenu = document.querySelector(selectorPadre + ' .dropdown-menu');
            const dropdownToggle = document.querySelector(selectorPadre + ' [data-bs-toggle="dropdown"]');
            
            if (dropdownMenu && dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
                if (dropdownToggle) {
                    dropdownToggle.classList.remove('show');
                    dropdownToggle.setAttribute('aria-expanded', 'false');
                }
            }
        }

        // Función para abrir menú
        function openMenu() {
            navmenu.classList.add('mobile-nav-active');
            if (mobileOverlay) mobileOverlay.classList.add('active');
            body.classList.add('mobile-nav-open');
            
            if (icon) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }

            // [MODIFICADO] Cerrar Carrito si está abierto
            const cartModal = document.getElementById('cart-modal');
            if (cartModal) cartModal.style.display = 'none';

            // [NUEVO] Cerrar Dropdowns de Bootstrap si están abiertos
            cerrarDropdownBootstrap('.user-icon');   // Cierra usuario
            cerrarDropdownBootstrap('.grupos-icon'); // Cierra WhatsApp
        }

        // Función para cerrar menú
        function closeMenu() {
            navmenu.classList.remove('mobile-nav-active');
            if (mobileOverlay) mobileOverlay.classList.remove('active');
            body.classList.remove('mobile-nav-open');
            
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }

        // Toggle menu
        function toggleMenu(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            if (navmenu.classList.contains('mobile-nav-active')) {
                closeMenu();
            } else {
                openMenu();
            }
        }

        // Click en botón hamburguesa
        mobileMenuBtn.addEventListener('click', toggleMenu);

        // Click en overlay (cierra el menú)
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', function(e) {
                if (e.target === mobileOverlay) {
                    closeMenu();
                }
            });
        }

        // [NUEVO] Click en icono WhatsApp cierra el menú móvil
        if (whatsappToggleBtn) {
            whatsappToggleBtn.addEventListener('click', function() {
                if (navmenu.classList.contains('mobile-nav-active')) {
                    closeMenu();
                }
                // Opcional: Cerrar también usuario si quisieras exclusividad total entre iconos
                cerrarDropdownBootstrap('.user-icon');
            });
        }

        // [NUEVO] Click en icono Usuario cierra el menú móvil
        if (userTogleBtn) {
            userTogleBtn.addEventListener('click', function() {
                if (navmenu.classList.contains('mobile-nav-active')) {
                    closeMenu();
                }
                // Opcional: Cerrar también WhatsApp
                cerrarDropdownBootstrap('.grupos-icon');
            });
        }

        // Click en enlaces del menú
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Si el enlace no es un dropdown, cerramos el menú
                const parent = this.closest('li');
                if (!parent.classList.contains('dropdown')) {
                    if (window.innerWidth <= 1199 && navmenu.classList.contains('mobile-nav-active')) {
                        setTimeout(() => closeMenu(), 150);
                    }
                }
            });
        });

        // Manejar clicks en dropdowns del menú móvil
        const dropdowns = document.querySelectorAll('.navmenu .dropdown > a');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                if (window.innerWidth <= 1199) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const parent = this.parentElement;
                    const isActive = parent.classList.contains('dropdown-active');
                    
                    // Cerrar todos los dropdowns internos del menú
                    const allDropdowns = navmenu.querySelectorAll('.dropdown.dropdown-active');
                    allDropdowns.forEach(d => d.classList.remove('dropdown-active'));
                    
                    // Abrir el actual si no estaba abierto
                    if (!isActive) {
                        parent.classList.add('dropdown-active');
                    } else {
                        // Si ya estaba abierto, navegamos al enlace
                        window.location.href = this.href;
                    }
                }
            });
        });

        // Cierra menú al redimensionar ventana
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (window.innerWidth > 1199 && navmenu.classList.contains('mobile-nav-active')) {
                    closeMenu();
                }
            }, 250);
        });

        // ESC key para cerrar menú
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && navmenu.classList.contains('mobile-nav-active')) {
                closeMenu();
            }
        });
    }

    // ==========================================
    // 2. HEADER SCROLL EFFECT
    // ==========================================
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // ==========================================
    // 3. LÓGICA DE PRODUCTOS DINÁMICA (CORREGIDA)
    // ==========================================
    const productGrid = document.getElementById('product-grid');
    const categoriesContainer = document.getElementById('categories-container');
    let allProducts = [];

    if (productGrid && categoriesContainer) {
        cargarDatosHome();
    }

    function cargarDatosHome() {
        // 1. Cargar Productos primero para saber cuáles categorías son funcionales
        $.ajax({
            url: 'app/controllers/productoController.php',
            type: 'POST',
            data: { accion: 'obtenerTodosLosProductosConVariantes' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    allProducts = response.data;
                    
                    // Una vez tenemos los productos, cargamos las categorías
                    $.ajax({
                        url: 'app/controllers/productoController.php',
                        type: 'POST',
                        data: { accion: 'obtenerCategorias' },
                        dataType: 'json',
                        success: function(catResponse) {
                            if (catResponse.status === 'success') {
                                // Filtramos solo las categorías que tienen al menos un producto en allProducts
                                const functionalCategories = catResponse.data.filter(cat => 
                                    allProducts.some(p => p.nombre_categoria === cat.nombre)
                                );
                                renderizarCategorias(functionalCategories);
                            }
                        }
                    });

                    renderizarProductosFiltrados(allProducts);
                }
            },
            error: function(err) {
                console.error("Error al cargar productos:", err);
                productGrid.innerHTML = '<div class="col-12 text-center py-5"><p class="text-danger">Error al conectar con el servidor.</p></div>';
            }
        });
    }

    function renderizarCategorias(categorias) {
        // Renderizar el selector de la toolbar
        const $catFilter = $("#category-filter");
        $catFilter.find('option:not([value="all"])').remove();
        
        // Renderizar las Pills de navegación
        categoriesContainer.innerHTML = '<div class="catalog-pill active" data-category="all">Todos los Productos</div>';
        
        categorias.forEach(cat => {
            // Opción en el select
            const option = document.createElement('option');
            option.value = cat.nombre;
            option.textContent = cat.nombre.toUpperCase();
            $catFilter.append(option);

            // Pill de navegación
            const pill = document.createElement('div');
            pill.className = 'catalog-pill';
            pill.dataset.category = cat.nombre;
            pill.textContent = cat.nombre;
            categoriesContainer.appendChild(pill);
        });

        // Eventos para las pills
        document.querySelectorAll('.catalog-pill').forEach(pill => {
            pill.addEventListener('click', function() {
                const category = this.dataset.category;
                document.querySelectorAll('.catalog-pill').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                
                // Sincronizar con el select
                $catFilter.val(category);
                
                aplicarFiltros();
            });
        });

        // Evento para el select
        $catFilter.on('change', function() {
            const category = $(this).val();
            document.querySelectorAll('.catalog-pill').forEach(p => {
                p.classList.toggle('active', p.dataset.category === category);
            });
            aplicarFiltros();
        });
    }

    // Nueva función de filtrado unificada (igual al catálogo)
    function aplicarFiltros() {
        const busqueda = $("#product-search").val().toLowerCase();
        const categoria = $("#category-filter").val();

        const filtrados = allProducts.filter(p => {
            const matchBusqueda = p.nombre.toLowerCase().includes(busqueda) || 
                                 (p.sku && p.sku.toLowerCase().includes(busqueda));
            const matchCategoria = (categoria === 'all' || p.nombre_categoria === categoria);
            return matchBusqueda && matchCategoria;
        });

        renderizarProductosFiltrados(filtrados);
    }

    function renderizarProductosFiltrados(productos) {
        productGrid.innerHTML = '';
        
        if (productos.length === 0) {
            $("#noResults").removeClass("d-none");
            return;
        }

        $("#noResults").addClass("d-none");
        
        const rutaBase = "app/views/assets/images/";
        const maxId = Math.max(...allProducts.map(p => p.id), 0);
        const umbralNuevo = maxId - 12;

        productos.forEach((product, index) => {
            let precioVenta = Number(product.precio_venta);
            let precioFormateado = precioVenta.toFixed(2);
            
            let badgesHtml = '';
            if (product.id > umbralNuevo) badgesHtml += '<span class="badge-premium badge-new">NUEVO</span>';
            if (parseInt(product.ventas_totales) >= 5) badgesHtml += '<span class="badge-premium badge-top">TOP VENTAS</span>';
            if (product.stock > 0 && product.stock <= product.reserva) badgesHtml += '<span class="badge-premium badge-low-stock">STOCK BAJO</span>';

            const mainImg = `${rutaBase}${product.imagen || 'default.png'}`;
            const hoverImg = product.imagen_hover ? `${rutaBase}${product.imagen_hover}` : mainImg;
            let stockColor = product.stock > 5 ? 'text-success' : product.stock > 0 ? 'text-warning' : 'text-danger';

            const col = document.createElement('div');
            col.className = 'col animate__animated animate__fadeIn';
            
            col.innerHTML = `
                <div class="card h-100 border-0 shadow-sm transition-hover product-card">
                    <div class="product-badge-container">${badgesHtml}</div>
                    <div class="product-quick-actions">
                        <button class="btn-action-premium btnQuickViewHome" data-id="${product.id}" title="Vista Rápida">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="product-image-container">
                        <img src="${mainImg}" class="product-img-main" alt="${product.nombre}">
                        <img src="${hoverImg}" class="product-img-hover" alt="${product.nombre} hover">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-light text-dark border">${product.nombre_categoria}</span>
                            <span class="small fw-bold ${stockColor}">${product.stock > 0 ? product.stock + ' un.' : 'AGOTADO'}</span>
                        </div>
                        <p class="text-muted small mb-1">${product.nombre_producto_padre || ''}</p>
                        <h5 class="card-title fw-bold text-dark mb-3">${product.nombre}</h5>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="h4 mb-0 text-primary fw-bold">$${precioFormateado}</span>
                                <span class="small text-muted">SKU: ${product.sku}</span>
                            </div>
                            <a href="catalogo" class="btn btn-dark w-100 py-2">
                                <i class="fas fa-shopping-bag me-1"></i> VER EN CATÁLOGO
                            </a>
                        </div>
                    </div>
                </div>
            `;
            productGrid.appendChild(col);
        });

        $(".btnQuickViewHome").on("click", function() {
            abrirQuickView($(this).data("id"));
        });
    }

    // Eventos de búsqueda
    $("#product-search").on("input", aplicarFiltros);
    $("#btn-refresh").on("click", function() {
        $("#product-search").val("");
        $("#category-filter").val("all").trigger("change");
        cargarDatosHome();
    });

    function abrirQuickView(id) {
        $.ajax({
            url: "app/controllers/productoController.php",
            method: "POST",
            dataType: "json",
            data: { accion: "obtenerDetalleQuickView", id: id },
            beforeSend: function() {
                Swal.fire({ title: 'Cargando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            },
            success: function(response) {
                Swal.close();
                if (response.status === "success") {
                    const data = response.data;
                    const v = data.variante;
                    const ruta = "app/views/assets/images/";
                    
                    $("#qv-name").text(v.nombre);
                    $("#qv-category").text(v.nombre_categoria);
                    $("#qv-sku").text("SKU: " + v.sku);
                    $("#qv-price").text("$" + parseFloat(v.precio_venta).toFixed(2));
                    $("#qv-stock").text(v.stock + " unidades");
                    $("#qv-description").text(v.descripcion || "Sin descripción disponible.");
                    
                    const $gallery = $("#qv-gallery-thumbs");
                    $gallery.empty();
                    if (data.imagenes.length > 0) {
                        $("#qv-main-img").attr("src", ruta + data.imagenes[0].ruta_imagen);
                        data.imagenes.forEach((img, index) => {
                            $gallery.append(`<div class="quick-view-thumb ${index === 0 ? 'active' : ''}"><img src="${ruta}${img.ruta_imagen}" alt="Thumb"></div>`);
                        });
                    } else {
                        $("#qv-main-img").attr("src", ruta + "default.png");
                    }

                    const $attrContainer = $("#qv-attributes");
                    $attrContainer.empty();
                    if (data.atributos.length > 0) {
                        let attrHtml = '<div class="row row-cols-2 g-2">';
                        data.atributos.forEach(attr => {
                            attrHtml += `<div class="col"><div class="p-2 border rounded bg-light small"><span class="text-muted">${attr.nombre}:</span> <strong class="text-dark">${attr.valor}</strong></div></div>`;
                        });
                        attrHtml += '</div>';
                        $attrContainer.append(attrHtml);
                    }
                    $("#modalQuickView").modal("show");
                }
            }
        });
    }

    // Manejador para miniaturas en el modal de Home
    $(document).on("click", ".quick-view-thumb", function() {
        $(".quick-view-thumb").removeClass("active");
        $(this).addClass("active");
        $("#qv-main-img").attr("src", $(this).find("img").attr("src"));
    });

    // ==========================================
    // 4. ANIMACIONES AL SCROLL
    // ==========================================
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.product-card, .feature-box').forEach(el => observer.observe(el));


    // ==========================================
    // [MODIFICADO] LISTENER PARA CERRAR TODO AL ABRIR PERFIL
    // ==========================================
    const userToggleBtn = document.querySelector('.user-icon [data-bs-toggle="dropdown"]');
    if (userToggleBtn) {
        userToggleBtn.addEventListener('click', function() {
            // Cerrar Menú Móvil manualmente
            if (typeof navmenu !== 'undefined' && navmenu.classList.contains('mobile-nav-active')) {
                navmenu.classList.remove('mobile-nav-active');
                if (mobileOverlay) mobileOverlay.classList.remove('active');
                document.body.classList.remove('mobile-nav-open');
                const icon = document.querySelector('.mobile-nav-toggle i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
            
            // Cerrar Carrito
            const cartModal = document.getElementById('cart-modal');
            if (cartModal) cartModal.style.display = 'none';
        });
    }

});

/*

// ==========================================
    // BLOQUEO DE INSPECTOR Y CLIC DERECHO
    // ==========================================
    
    // Deshabilitar clic derecho
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    }, false);

    // Deshabilitar teclas de acceso rápido comunes (F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U)
    document.addEventListener('keydown', function(e) {
        // F12
        if (e.key === 'F12' || e.keyCode === 123) {
            e.preventDefault();
            return false;
        }
        
        // Combinaciones con Ctrl+Shift (I, J, C)
        if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C' || e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+U (Ver código fuente)
        if (e.ctrlKey && (e.key === 'U' || e.keyCode === 85)) {
            e.preventDefault();
            return false;
        }
    });
*/

    //scrip para div de envios y preguntas frecuentes


    document.addEventListener('DOMContentLoaded', function() {
        const accordionButtons = document.querySelectorAll('.accordion-button');
        accordionButtons.forEach(button => {
            button.addEventListener('click', function() {
                accordionButtons.forEach(btn => {
                    btn.closest('.accordion-item').classList.remove('border-luxury');
                });
                
                setTimeout(() => {
                    if (!this.classList.contains('collapsed')) {
                        this.closest('.accordion-item').classList.add('border-luxury');
                    }
                }, 100);
            });
        });
    });

    

// Estilos para notificaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }
`;
document.head.appendChild(style);