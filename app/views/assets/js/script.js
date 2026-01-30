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
                    
                    // Cerrar todos los dropdowns
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
    // 3. LÓGICA DE PRODUCTOS
    // ==========================================
    const productGrid = document.getElementById('product-grid');
    const categoryButtons = document.querySelectorAll('.category-btn');

    if (productGrid && typeof products !== 'undefined') {
        categoryButtons.forEach(button => {
            button.addEventListener('click', () => {
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                renderProducts(button.dataset.category);
            });
        });
        
        function renderProducts(category = 'all') {
            productGrid.innerHTML = '';
            const filteredProducts = category === 'all' 
                ? products 
                : products.filter(p => p.category === category);
            
            if (filteredProducts.length === 0) {
                productGrid.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">No hay productos</p></div>';
                return;
            }

            filteredProducts.forEach((product, index) => {
                const card = document.createElement('div');
                card.className = 'product-card';
                card.style.animationDelay = `${index * 0.1}s`;
                card.innerHTML = `
                    <div class="product-img"><img src="${product.image}" alt="${product.name}"></div>
                    <div class="product-info">
                        <span class="product-category">${product.category}</span>
                        <h3 class="product-title">${product.name}</h3>
                        <p class="product-desc">${product.description}</p>
                        <div class="product-price">$${product.price.toFixed(2)}</div>
                        <div class="product-actions">
                            <button class="add-to-cart" data-id="${product.id}">Añadir al carrito</button>
                            <button class="wishlist"><i class="far fa-heart"></i></button>
                        </div>
                    </div>`;
                productGrid.appendChild(card);
            });
            attachProductEvents();
        }

        function attachProductEvents() {
            document.querySelectorAll('.add-to-cart').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (typeof addToCart === 'function') addToCart.call(this);
                });
            });
        }
        renderProducts();
    }

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

});

// Estilos para notificaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }
`;
document.head.appendChild(style);