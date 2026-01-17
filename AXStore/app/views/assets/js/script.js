document.addEventListener('DOMContentLoaded', function() {
    // Variables para el menú móvil
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mainNav = document.querySelector('.main-nav');
    const navLinks = document.querySelectorAll('.main-nav a'); 

    // Variables para productos
    const productGrid = document.getElementById('product-grid');
    const categoryButtons = document.querySelectorAll('.category-btn');

    // Menú móvil
    mobileMenuBtn.addEventListener('click', () => {
        mainNav.classList.toggle('active');
        
        // Cambiar ícono de hamburguesa a X cuando está activo
        if (mainNav.classList.contains('active')) {
            mobileMenuBtn.innerHTML = '<i class="fas fa-times"></i>';
        } else {
            mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
        }
    });
    
    // Cerrar menú al hacer clic en un enlace (solo en móvil)
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 992) { // Solo si está en vista móvil
                mainNav.classList.remove('active');
                mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>'; // Volver al ícono de hamburguesa
            }
        });
    });
    
    // Filtrar productos por categoría
    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remover active de todos los botones
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            // Agregar active al botón clickeado
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
        
        filteredProducts.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'product-card';
            productCard.innerHTML = `
                <div class="product-img">
                    <img src="${product.image}" alt="${product.name}">
                </div>
                <div class="product-info">
                    <span class="product-category ${product.category}">${product.category}</span>
                    <h3 class="product-title">${product.name}</h3>
                    <p class="product-desc">${product.description}</p>
                    <div class="product-price">$${product.price.toFixed(2)}</div>
                    <div class="product-actions">
                        <button class="add-to-cart" data-id="${product.id}">Añadir al carrito</button>
                        <button class="wishlist"><i class="far fa-heart"></i></button>
                    </div>
                </div>
            `;
            productGrid.appendChild(productCard);
        });
        
        // Agregar eventos a los botones de añadir al carrito
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', addToCart);
        });
    }

    // Inicializar
    renderProducts();
});