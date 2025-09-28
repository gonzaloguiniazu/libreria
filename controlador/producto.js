// producto.js
/*
// Datos simulados de productos
const productosSimulados = [
    { id: 1, nombre: 'Producto 1', descripcion: 'Descripción breve del producto 1.', precio: 10.00 },
    { id: 2, nombre: 'Producto 2', descripcion: 'Descripción breve del producto 2.', precio: 15.00 },
    { id: 3, nombre: 'Producto 3', descripcion: 'Descripción breve del producto 3.', precio: 20.00 }
];

// Función para cargar productos simulados
function cargarProductos() {
    const productList = document.getElementById('product-list');
    productList.innerHTML = '';

    productosSimulados.forEach(producto => {
        const productDiv = document.createElement('div');
        productDiv.classList.add('product');

        productDiv.innerHTML = `
            <img src="img/product${producto.id}.jpg" alt="${producto.nombre}">
            <h3>${producto.nombre}</h3>
            <p>${producto.descripcion}</p>
            <p class="price">$${producto.precio.toFixed(2)}</p>
            <input type="number" id="cantidad-${producto.id}" min="1" value="1" style="width: 60px; text-align: center;">
            <button onclick="addToCart(${producto.id}, '${producto.nombre}', ${producto.precio})">Agregar al carrito</button>
        `;

        productList.appendChild(productDiv);
    });
}
*/

// Función para cargar productos desde el servidor
function cargarProductos() {
    fetch('producto.php')//solicita los datos
        .then(response => response.json())//convierte la respuesta a JSON
        .then(productos => {
            const productList = document.getElementById('productos-lista');//limpia el contenido del contenedor
            //antes de agregar los nuevos productos para evitar duplicaciones
            productList.innerHTML = '';

            productos.forEach(producto => {
                const productDiv = document.createElement('div');//crea un contenedor para cada producto
                productDiv.classList.add('product');//se llena con html

                //inyeccion de html 
                productDiv.innerHTML = `
                    <img src="proyecto/imagenes/${producto.imagen_url}" alt="${producto.nombre}">
                    <h3>${producto.nombre}</h3>
                    <p>${producto.descripcion}</p>
                    <p class="price">$${producto.precio.toFixed(2)}</p>
                    <input type="number" id="cantidad-${producto.id}" min="0" max="5" value="1">
                    <button onclick="agregarAlCarrito(${producto.id}, '${producto.nombre}', ${producto.precio})">Agregar al carrito</button>
                `;
                //el boton llama a la funcion agregarAlCarrito()
                productList.appendChild(productDiv);
            });
        })
        .catch(error => console.error('Error al cargar los productos:', error));
}
/*
// Función para cargar los productos(version anterior sin cantidad)
function cargarProductos() {
    fetch('productos.php?action=obtener')  // Hace la petición GET a productos.php
        .then(response => response.json())
        .then(productos => {
            const productList = document.getElementById('product-list');
            productList.innerHTML = '';  // Limpiamos el contenedor de productos

            productos.forEach(producto => {
                const productDiv = document.createElement('div');
                productDiv.classList.add('product');

                productDiv.innerHTML = `
                    <h3>${producto.nombre}</h3>
                    <p>${producto.descripcion}</p>
                    <p class="price">$${producto.precio}</p>
                    <input type="text" id="precio-${producto.id}" placeholder="Nuevo precio">
                    <button onclick="actualizarPrecio(${producto.id})">Actualizar Precio</button>
                `;
                productList.appendChild(productDiv);
            });
        })
        .catch(error => console.error('Error al cargar los productos:', error));
}
*/

// Función para agregar un producto al carrito con una cantidad específica
function agregarAlCarrito(id, nombre, precio) {
    const cantidadInput = document.getElementById(`cantidad-${id}`);
    const cantidad = parseInt(cantidadInput.value) || 1; // Default to 1 if not valid

    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    const index = carrito.findIndex(item => item.id === id);

    if (index === -1) {
        carrito.push({ id, nombre, precio, cantidad });
    } else {
        carrito[index].cantidad += cantidad; // Incrementar cantidad si el producto ya está en el carrito
    }

    localStorage.setItem('carrito', JSON.stringify(carrito));
    alert(`Agregado al carrito: ${nombre} x ${cantidad}`);
}
/*
// Función para agregar un producto al carrito
function agregarAlCarrito(id, nombre, precio) {
    const producto = { id, nombre, precio };

    // Verificar si el producto ya está en el carrito
    const index = carrito.findIndex(item => item.id === id);
    if (index === -1) {
        carrito.push(producto);
    } else {
        // Opcional: Si quieres permitir múltiples cantidades, aquí podrías incrementar la cantidad
        // carrito[index].cantidad += 1;
    }

    // Guardar en localStorage
    localStorage.setItem('carrito', JSON.stringify(carrito));
    mostrarCarrito();
}
*/

// Cargar los productos cuando se carga la página
window.onload = cargarProductos;

