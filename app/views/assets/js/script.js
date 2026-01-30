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
    
    console.log('Elementos encontrados:', {
        mobileMenuBtn: !!mobileMenuBtn,
        navmenu: !!navmenu,
        navLinks: navLinks.length,
        mobileOverlay: !!mobileOverlay
    });
    
    if (mobileMenuBtn && navmenu) {
        const icon = mobileMenuBtn.querySelector('i');

        // Función para abrir menú
        function openMenu() {
            navmenu.classList.add('mobile-nav-active');
            if (mobileOverlay) mobileOverlay.classList.add('active');
            body.classList.add('mobile-nav-open');
            
            if (icon) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }
            
            console.log('✓ Menú abierto - mobile-nav-active agregado');
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
            
            console.log('✓ Menú cerrado');
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

        // Click en overlay (cierra el menú) - SIN stopPropagation
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', function(e) {
                // Solo cerrar si el click es directamente en el overlay, no en el menú
                if (e.target === mobileOverlay) {
                    closeMenu();
                }
            });
        }

        // Click en enlaces del menú (cierra el menú)
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Permitir que el click pase normalmente
                // Solo cerrar el menú en móvil
                if (window.innerWidth <= 1199 && navmenu.classList.contains('mobile-nav-active')) {
                    setTimeout(() => closeMenu(), 100); // Pequeño delay para que la navegación funcione
                }
            });
        });

        // Manejar clicks en dropdowns del menú móvil
        const dropdowns = document.querySelectorAll('.navmenu .dropdown > a');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                // Solo en móvil
                if (window.innerWidth <= 1199) {
                    e.preventDefault();
                    const parent = this.parentElement;
                    
                    // Toggle dropdown
                    parent.classList.toggle('dropdown-active');
                    
                    // Cerrar otros dropdowns
                    dropdowns.forEach(other => {
                        if (other !== dropdown) {
                            other.parentElement.classList.remove('dropdown-active');
                        }
                    });
                }
            });
        });

        // Cierra menú al cambiar orientación o redimensionar ventana
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

        // Prevenir scroll cuando el menú está abierto
        navmenu.addEventListener('touchmove', function(e) {
            if (navmenu.classList.contains('mobile-nav-active')) {
                e.stopPropagation();
            }
        }, { passive: true });
    }

    // ==========================================
    // 2. HEADER SCROLL EFFECT
    // ==========================================
    
    const header = document.querySelector('header');
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        lastScroll = currentScroll;
    });

    // ==========================================
    // 3. LÓGICA DE PRODUCTOS
    // ==========================================

    const productGrid = document.getElementById('product-grid');
    const categoryButtons = document.querySelectorAll('.category-btn');

    if (productGrid && typeof products !== 'undefined') {
        
        // Filtrar productos por categoría
        categoryButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remover active de todos
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                // Agregar active al clickeado
                button.classList.add('active');
                
                const category = button.dataset.category;
                renderProducts(category);
            });
        });
        
        // Renderizar productos
        function renderProducts(category = 'all') {
            productGrid.innerHTML = '';
            
            const filteredProducts = category === 'all' 
                ? products 
                : products.filter(product => product.category === category);
            
            if (filteredProducts.length === 0) {
                productGrid.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No hay productos en esta categoría</p>
                    </div>
                `;
                return;
            }

            filteredProducts.forEach((product, index) => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';
                productCard.style.animationDelay = `${index * 0.1}s`;
                
                productCard.innerHTML = `
                    <div class="product-img">
                        <img src="${product.image}" alt="${product.name}" loading="lazy">
                    </div>
                    <div class="product-info">
                        <span class="product-category ${product.category}">${product.category}</span>
                        <h3 class="product-title">${product.name}</h3>
                        <p class="product-desc">${product.description}</p>
                        <div class="product-price">$${product.price.toFixed(2)}</div>
                        <div class="product-actions">
                            <button class="add-to-cart" data-id="${product.id}" aria-label="Añadir ${product.name} al carrito">
                                Añadir al carrito
                            </button>
                            <button class="wishlist" aria-label="Añadir a favoritos">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                `;
                productGrid.appendChild(productCard);
            });
            
            // Re-asignar eventos a los nuevos botones
            attachProductEvents();
        }

        // Adjuntar eventos a botones de productos
        function attachProductEvents() {
            // Botones de añadir al carrito
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    if (typeof addToCart === 'function') {
                        addToCart.call(this);
                    } else {
                        console.warn('Función addToCart no encontrada');
                    }
                });
            });

            // Botones de wishlist
            document.querySelectorAll('.wishlist').forEach(button => {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (icon.classList.contains('far')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        this.style.backgroundColor = 'var(--primary-color)';
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        this.style.backgroundColor = '';
                    }
                });
            });
        }

        // Inicializar productos
        renderProducts();
    }

    // ==========================================
    // 4. SMOOTH SCROLL PARA NAVEGACIÓN
    // ==========================================
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            
            // Ignora # solo
            if (href === '#') return;
            
            const target = document.querySelector(href);
            
            if (target) {
                e.preventDefault();
                
                const headerHeight = header ? header.offsetHeight : 80;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ==========================================
    // 5. LAZY LOADING DE IMÁGENES
    // ==========================================
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // ==========================================
    // 6. ANIMACIONES AL SCROLL
    // ==========================================
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observar elementos que queremos animar
    document.querySelectorAll('.product-card, .feature-box').forEach(el => {
        observer.observe(el);
    });

    // ==========================================
    // 7. VALIDACIÓN DE FORMULARIOS
    // ==========================================
    
    const checkoutForm = document.getElementById('checkout-form');
    
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validación básica
            const nombre = this.querySelector('[name="nombre"]').value.trim();
            const telefono = this.querySelector('[name="telefono"]').value.trim();
            const direccion = this.querySelector('[name="direccion"]').value.trim();
            
            if (!nombre || !telefono || !direccion) {
                alert('Por favor complete todos los campos requeridos');
                return;
            }
            
            // Validar teléfono (números básico)
            const telefonoRegex = /^[0-9\s\-\+\(\)]+$/;
            if (!telefonoRegex.test(telefono)) {
                alert('Por favor ingrese un número de teléfono válido');
                return;
            }
            
            // Si todo está bien, procesar pedido
            if (typeof processCheckout === 'function') {
                processCheckout();
            } else {
                console.log('Pedido procesado:', { nombre, telefono, direccion });
            }
        });
    }

    // ==========================================
    // 8. PREVENIR ZOOM EN INPUTS (iOS)
    // ==========================================
    
    if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        const inputs = document.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.style.fontSize = '16px';
            });
        });
    }

    // ==========================================
    // 9. DETECCIÓN DE TOUCH DEVICE
    // ==========================================
    
    if ('ontouchstart' in window) {
        document.body.classList.add('touch-device');
    }

    // ==========================================
    // 10. PERFORMANCE LOGGING (Development)
    // ==========================================
    
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('%c✨ Sitio cargado correctamente', 'color: #0b5ee1; font-size: 14px; font-weight: bold;');
        console.log('%c📱 Dispositivo:', window.innerWidth <= 992 ? 'Móvil' : 'Desktop', 'color: #666;');
        console.log('%c🎯 Z-index del menú:', window.getComputedStyle(navmenu).zIndex, 'color: #0b5ee1;');
        if (mobileOverlay) {
            console.log('%c🎯 Z-index del overlay:', window.getComputedStyle(mobileOverlay).zIndex, 'color: #0b5ee1;');
        }
    }

});

// ==========================================
// FUNCIONES GLOBALES (si son necesarias)
// ==========================================

// Función para mostrar notificaciones
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : '#dc3545'};
        color: white;
        padding: 15px 25px;
        border-radius: 5px;
        z-index: 10000;
        animation: slideInRight 0.3s ease-out;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Debounce para eventos de resize
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Añadir estilos para animaciones de notificaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);