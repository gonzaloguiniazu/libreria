// ============================================
// CARRITO DE COMPRAS UNIFICADO
// Sistema integrado con Base de Datos
// ============================================

var carritoVisible = false;

// Esperamos que todos los elementos carguen
if(document.readyState == 'loading'){
    document.addEventListener('DOMContentLoaded', ready)
} else {
    ready();
}

function ready(){
    // Cargar carrito desde BD al iniciar
    cargarCarritoDesdeDB();
    
    // Funcionalidad botones eliminar
    var botonesEliminarItem = document.getElementsByClassName('btn-eliminar');
    for(var i=0; i<botonesEliminarItem.length; i++){
        var button = botonesEliminarItem[i];
        button.addEventListener('click', eliminarItemCarrito);
    }

    // Funcionalidad sumar cantidad
    var botonesSumarCantidad = document.getElementsByClassName('sumar-cantidad');
    for(var i=0; i<botonesSumarCantidad.length; i++){
        var button = botonesSumarCantidad[i];
        button.addEventListener('click', sumarCantidad);
    }

    // Funcionalidad restar cantidad
    var botonesRestarCantidad = document.getElementsByClassName('restar-cantidad');
    for(var i=0; i<botonesRestarCantidad.length; i++){
        var button = botonesRestarCantidad[i];
        button.addEventListener('click', restarCantidad);
    }

    // Funcionalidad Agregar al carrito
    var botonesAgregarAlCarrito = document.getElementsByClassName('boton-item');
    for(var i=0; i<botonesAgregarAlCarrito.length; i++){
        var button = botonesAgregarAlCarrito[i];
        button.addEventListener('click', agregarAlCarritoClicked);
    }

    // Funcionalidad botón pagar
    var btnPagar = document.getElementsByClassName('btn-pagar')[0];
    if (btnPagar) {
        btnPagar.addEventListener('click', pagarClicked);
    }
}

// ========== CARGAR CARRITO DESDE BD ==========
function cargarCarritoDesdeDB() {
    fetch('carrito_unificado.php?action=obtener', {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.items.length > 0) {
            var itemsCarrito = document.getElementsByClassName('carrito-items')[0];
            itemsCarrito.innerHTML = '';
            
            data.items.forEach(item => {
                agregarItemAlCarritoHTML(
                    item.descripcion,
                    item.precio_unitario,
                    item.imagen,
                    item.cantidad,
                    item.id_producto
                );
            });
            
            actualizarTotalCarrito();
            hacerVisibleCarrito();
        }
    })
    .catch(error => console.error('Error al cargar carrito:', error));
}

// ========== FINALIZAR COMPRA ==========
function pagarClicked(){
    var carritoItems = document.getElementsByClassName('carrito-items')[0];
    
    if (!carritoItems || carritoItems.children.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    if (!confirm('¿Deseas finalizar la compra?')) {
        return;
    }

    fetch('carrito_unificado.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            action: 'finalizar'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
             alert('¡Compra realizada con éxito!\nTotal: $' + data.total.toFixed(2));

// ========== AGREGAR PRODUCTO AL CARRITO ==========
function agregarAlCarritoClicked(event){
    var button = event.target;
    var item = button.parentElement;
    var titulo = item.getElementsByClassName('titulo-item')[0].innerText;
    var precioElemento = item.getElementsByClassName('precio-item')[0].innerText;
    var imagenSrc = item.getElementsByClassName('img-item')[0].src;
    var idProducto = button.getAttribute('data-id-producto');
    
    if (!idProducto) {
        alert('Error: ID de producto no encontrado');
        console.error('Botón sin data-id-producto:', button);
        return;
    }

    console.log('Agregando producto al carrito:', {
        id: idProducto,
        titulo: titulo,
        precio: precioElemento
    });

    // Enviar a BD
    fetch('carrito_unificado.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            action: 'agregar',
            id_producto: parseInt(idProducto),
            cantidad: 1
        })
    })
    .then(response => {
        console.log('Respuesta del servidor:', response);
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);
        if (data.success) {
            // Limpiar el precio (quitar $ y puntos)
            var precioLimpio = precioElemento.replace('$', '').replace(/\./g, '').replace(',', '.');
            
            agregarItemAlCarritoHTML(titulo, precioLimpio, imagenSrc, 1, idProducto);
            hacerVisibleCarrito();
            alert('Producto agregado al carrito');
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        alert('Error al agregar producto');
    });
}

// ========== AGREGAR ITEM AL HTML ==========
function agregarItemAlCarritoHTML(titulo, precio, imagenSrc, cantidad, idProducto){
    var itemsCarrito = document.getElementsByClassName('carrito-items')[0];

    if (!itemsCarrito) {
        console.error('No se encontró el contenedor del carrito.');
        return;
    }

    // Verificar si ya existe
    var nombresItemsCarrito = itemsCarrito.getElementsByClassName('carrito-item-titulo');
    for(var i=0; i < nombresItemsCarrito.length; i++){
        if(nombresItemsCarrito[i].innerText == titulo){
            // Ya existe, solo actualizar cantidad
            var cantidadInput = nombresItemsCarrito[i].parentElement.getElementsByClassName('carrito-item-cantidad')[0];
            var nuevaCantidad = parseInt(cantidadInput.value) + 1;
            cantidadInput.value = nuevaCantidad;
            actualizarTotalCarrito();
            return;
        }
    }

    var item = document.createElement('div');
    item.classList.add('item');
    
    // Asegurarse de que el precio sea un número
    var precioNumerico = typeof precio === 'string' ? parseFloat(precio.replace(/[^0-9.-]+/g,"")) : precio;
    var precioFormateado = precioNumerico.toLocaleString('es-AR');
    
    var itemCarritoContenido = `
        <div class="carrito-item" data-id-producto="${idProducto}">
            <img src="${imagenSrc}" width="60px" alt="">
            <div class="carrito-item-detalles">
                <span class="carrito-item-titulo">${titulo}</span>
                <div class="selector-cantidad">
                    <i class="fa-solid fa-minus restar-cantidad"></i>
                    <input type="text" value="${cantidad}" class="carrito-item-cantidad" disabled>
                    <i class="fa-solid fa-plus sumar-cantidad"></i>
                </div>
                <span class="carrito-item-precio">$${precioFormateado}</span>
            </div>
            <button class="btn-eliminar">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    `;
    item.innerHTML = itemCarritoContenido;
    itemsCarrito.append(item);

    // Agregar eventos
    item.getElementsByClassName('btn-eliminar')[0].addEventListener('click', eliminarItemCarrito);
    
    var botonRestarCantidad = item.getElementsByClassName('restar-cantidad')[0];
    botonRestarCantidad.addEventListener('click', restarCantidad);

    var botonSumarCantidad = item.getElementsByClassName('sumar-cantidad')[0];
    botonSumarCantidad.addEventListener('click', sumarCantidad);

    actualizarTotalCarrito();
}

// ========== SUMAR CANTIDAD ==========
function sumarCantidad(event){
    var buttonClicked = event.target;
    var selector = buttonClicked.parentElement;
    var cantidadInput = selector.getElementsByClassName('carrito-item-cantidad')[0];
    var cantidadActual = parseInt(cantidadInput.value);
    cantidadActual++;
    
    var carritoItem = buttonClicked.closest('.carrito-item');
    var idProducto = carritoItem.getAttribute('data-id-producto');
    
    // Actualizar en BD
    fetch('carrito_unificado.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            action: 'actualizar',
            id_producto: parseInt(idProducto),
            cantidad: cantidadActual
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cantidadInput.value = cantidadActual;
            actualizarTotalCarrito();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

// ========== RESTAR CANTIDAD ==========
function restarCantidad(event){
    var buttonClicked = event.target;
    var selector = buttonClicked.parentElement;
    var cantidadInput = selector.getElementsByClassName('carrito-item-cantidad')[0];
    var cantidadActual = parseInt(cantidadInput.value);
    
    if(cantidadActual <= 1) return;
    
    cantidadActual--;
    
    var carritoItem = buttonClicked.closest('.carrito-item');
    var idProducto = carritoItem.getAttribute('data-id-producto');
    
    // Actualizar en BD
    fetch('carrito_unificado.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            action: 'actualizar',
            id_producto: parseInt(idProducto),
            cantidad: cantidadActual
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cantidadInput.value = cantidadActual;
            actualizarTotalCarrito();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

// ========== ELIMINAR ITEM ==========
function eliminarItemCarrito(event){
    var buttonClicked = event.target;
    var carritoItem = buttonClicked.closest('.carrito-item');
    var idProducto = carritoItem.getAttribute('data-id-producto');
    
    // Eliminar de BD
    fetch('carrito_unificado.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            action: 'eliminar',
            id_producto: parseInt(idProducto)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            carritoItem.parentElement.remove();
            actualizarTotalCarrito();
            ocultarCarrito();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

// ========== FUNCIONES AUXILIARES ==========
function hacerVisibleCarrito(){
    carritoVisible = true;
    var carrito = document.getElementsByClassName('carrito')[0];
    carrito.style.marginRight = '0';
    carrito.style.opacity = '1';

    var items = document.getElementsByClassName('contenedor-items')[0];
    if (items) {
        items.style.width = '60%';
    }
}

function ocultarCarrito(){
    var carritoItems = document.getElementsByClassName('carrito-items')[0];
    if(carritoItems.childElementCount == 0){
        var carrito = document.getElementsByClassName('carrito')[0];
        carrito.style.marginRight = '-100%';
        carrito.style.opacity = '0';
        carritoVisible = false;
    
        var items = document.getElementsByClassName('contenedor-items')[0];
        if (items) {
            items.style.width = '100%';
        }
    }
}

function actualizarTotalCarrito(){
    var carritoContenedor = document.getElementsByClassName('carrito')[0];
    if (!carritoContenedor) return;
    
    var carritoItems = carritoContenedor.getElementsByClassName('carrito-item');
    var total = 0;
    
    for(var i=0; i< carritoItems.length; i++){
        var item = carritoItems[i];
        var precioElemento = item.getElementsByClassName('carrito-item-precio')[0];
        // Limpiar el precio de cualquier formato
        var precioTexto = precioElemento.innerText.replace('$','').replace(/\./g, '').replace(',','.');
        var precio = parseFloat(precioTexto);
        
        var cantidadItem = item.getElementsByClassName('carrito-item-cantidad')[0];
        var cantidad = parseInt(cantidadItem.value);
        
        total = total + (precio * cantidad);
    }
    
    total = Math.round(total * 100)/100;
    
    var totalElement = document.getElementsByClassName('carrito-precio-total')[0];
    if (totalElement) {
        totalElement.innerText = '$' + total.toLocaleString("es-AR", {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
}
 
            
            // Vaciar carrito visual
            while (carritoItems.hasChildNodes()) {
                carritoItems.removeChild(carritoItems.firstChild);
            }
            actualizarTotalCarrito();
            ocultarCarrito();
            
            // ✅ ACTUALIZAR STOCK DE PRODUCTOS EN LA PÁGINA
            actualizarStockProductos();
        } else {
            alert('Error al procesar el pago: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al conectar con el servidor');
    });
}

// ========== ACTUALIZAR STOCK DE PRODUCTOS ==========
function actualizarStockProductos() {
    fetch('obtener_stock.php', {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recorrer cada producto y actualizar su stock en la página
            data.productos.forEach(producto => {
                actualizarStockProducto(producto.id_producto, producto.stock, producto.stock_minimo);
            });
        }
    })
    .catch(error => console.error('Error al actualizar stock:', error));
}

// ========== ACTUALIZAR STOCK DE UN PRODUCTO ESPECÍFICO ==========
function actualizarStockProducto(idProducto, stock, stockMinimo) {
    // Buscar el botón del producto
    var boton = document.querySelector(`button[data-id-producto="${idProducto}"]`);
    
    if (!boton) return;
    
    var item = boton.parentElement;
    var stockDisponible = stock - stockMinimo;
    
    // Buscar o crear el elemento de stock
    var stockElement = item.querySelector('.stock-info');
    
    if (stockDisponible <= 0) {
        // Sin stock - deshabilitar botón
        boton.disabled = true;
        boton.textContent = 'Sin Stock';
        boton.style.backgroundColor = '#ccc';
        boton.style.cursor = 'not-allowed';
        
        if (stockElement) {
            stockElement.textContent = 'Stock no disponible';
            stockElement.style.color = 'red';
        }
    } else {
        // Hay stock - habilitar botón
        boton.disabled = false;
        boton.textContent = 'Agregar al Carrito';
        boton.style.backgroundColor = '';
        boton.style.cursor = 'pointer';
        
        if (stockElement) {
            stockElement.textContent = `Stock disponible: ${stockDisponible}`;
            stockElement.style.color = 'green';
        }
    }
}

// ========== AGREGAR PRODUCTO AL CARRITO ==========
function agregarAlCarritoClicked(event){
    var button = event.target;
    var item = button.parentElement;
    var titulo = item.getElementsByClassName('titulo-item')[0].innerText;
    var precioElemento = item.getElementsByClassName('precio-item')[0].innerText;
    var imagenSrc = item.getElementsByClassName('img-item')[0].src;
    var idProducto = button.getAttribute('data-id-producto');
    
    if (!idProducto) {
        alert('Error: ID de producto no encontrado');
        console.error('Botón sin data-id-producto:', button);
        return;
    }

    console.log('Agregando producto al carrito:', {
        id: idProducto,
        titulo: titulo,
        precio: precioElemento
    });

    // Enviar a BD
    fetch('carrito_unificado.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            action: 'agregar',
            id_producto: parseInt(idProducto),
            cantidad: 1
        })
    })
    .then(response => {
        console.log('Respuesta del servidor:', response);
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);
        if (data.success) {
            // Limpiar el precio (quitar $ y puntos)
            var precioLimpio = precioElemento.replace('$', '').replace(/\./g, '').replace(',', '.');
            
            agregarItemAlCarritoHTML(titulo, precioLimpio, imagenSrc, 1, idProducto);
            hacerVisibleCarrito();
            alert('Producto agregado al carrito');
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        alert('Error al agregar producto');
    });
}

// ========== AGREGAR ITEM AL HTML ==========
function agregarItemAlCarritoHTML(titulo, precio, imagenSrc, cantidad, idProducto){
    var itemsCarrito = document.getElementsByClassName('carrito-items')[0];

    if (!itemsCarrito) {
        console.error('No se encontró el contenedor del carrito.');
        return;
    }

    // Verificar si ya existe
    var nombresItemsCarrito = itemsCarrito.getElementsByClassName('carrito-item-titulo');
    for(var i=0; i < nombresItemsCarrito.length; i++){
        if(nombresItemsCarrito[i].innerText == titulo){
            // Ya existe, solo actualizar cantidad
            var cantidadInput = nombresItemsCarrito[i].parentElement.getElementsByClassName('carrito-item-cantidad')[0];
            var nuevaCantidad = parseInt(cantidadInput.value) + 1;
            cantidadInput.value = nuevaCantidad;
            actualizarTotalCarrito();
            return;
        }
    }

    var item = document.createElement('div');
    item.classList.add('item');
    
    // Asegurarse de que el precio sea un número
    var precioNumerico = typeof precio === 'string' ? parseFloat(precio.replace(/[^0-9.-]+/g,"")) : precio;
    var precioFormateado = precioNumerico.toLocaleString('es-AR');
    
    var itemCarritoContenido = `
        <div class="carrito-item" data-id-producto="${idProducto}">
            <img src="${imagenSrc}" width="60px" alt="">
            <div class="carrito-item-detalles">
                <span class="carrito-item-titulo">${titulo}</span>
                <div class="selector-cantidad">
                    <i class="fa-solid fa-minus restar-cantidad"></i>
                    <input type="text" value="${cantidad}" class="carrito-item-cantidad" disabled>
                    <i class="fa-solid fa-plus sumar-cantidad"></i>
                </div>
                <span class="carrito-item-precio">$${precioFormateado}</span>
            </div>
            <button class="btn-eliminar">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    `;
    item.innerHTML = itemCarritoContenido;
    itemsCarrito.append(item);

    // Agregar eventos
    item.getElementsByClassName('btn-eliminar')[0].addEventListener('click', eliminarItemCarrito);
    
    var botonRestarCantidad = item.getElementsByClassName('restar-cantidad')[0];
    botonRestarCantidad.addEventListener('click', restarCantidad);

    var botonSumarCantidad = item.getElementsByClassName('sumar-cantidad')[0];
    botonSumarCantidad.addEventListener('click', sumarCantidad);

    actualizarTotalCarrito();
}

// ========== SUMAR CANTIDAD ==========
function sumarCantidad(event){
    var buttonClicked = event.target;
    var selector = buttonClicked.parentElement;
    var cantidadInput = selector.getElementsByClassName('carrito-item-cantidad')[0];
    var cantidadActual = parseInt(cantidadInput.value);
    cantidadActual++;
    
    var carritoItem = buttonClicked.closest('.carrito-item');
    var idProducto = carritoItem.getAttribute('data-id-producto');
    
    // Actualizar en BD
    fetch('carrito_unificado.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            action: 'actualizar',
            id_producto: parseInt(idProducto),
            cantidad: cantidadActual
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cantidadInput.value = cantidadActual;
            actualizarTotalCarrito();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

// ========== RESTAR CANTIDAD ==========
function restarCantidad(event){
    var buttonClicked = event.target;
    var selector = buttonClicked.parentElement;
    var cantidadInput = selector.getElementsByClassName('carrito-item-cantidad')[0];
    var cantidadActual = parseInt(cantidadInput.value);
    
    if(cantidadActual <= 1) return;
    
    cantidadActual--;
    
    var carritoItem = buttonClicked.closest('.carrito-item');
    var idProducto = carritoItem.getAttribute('data-id-producto');
    
    // Actualizar en BD
    fetch('carrito_unificado.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            action: 'actualizar',
            id_producto: parseInt(idProducto),
            cantidad: cantidadActual
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cantidadInput.value = cantidadActual;
            actualizarTotalCarrito();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

// ========== ELIMINAR ITEM ==========
function eliminarItemCarrito(event){
    var buttonClicked = event.target;
    var carritoItem = buttonClicked.closest('.carrito-item');
    var idProducto = carritoItem.getAttribute('data-id-producto');
    
    // Eliminar de BD
    fetch('carrito_unificado.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            action: 'eliminar',
            id_producto: parseInt(idProducto)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            carritoItem.parentElement.remove();
            actualizarTotalCarrito();
            ocultarCarrito();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

// ========== FUNCIONES AUXILIARES ==========
function hacerVisibleCarrito(){
    carritoVisible = true;
    var carrito = document.getElementsByClassName('carrito')[0];
    carrito.style.marginRight = '0';
    carrito.style.opacity = '1';

    var items = document.getElementsByClassName('contenedor-items')[0];
    if (items) {
        items.style.width = '60%';
    }
}

function ocultarCarrito(){
    var carritoItems = document.getElementsByClassName('carrito-items')[0];
    if(carritoItems.childElementCount == 0){
        var carrito = document.getElementsByClassName('carrito')[0];
        carrito.style.marginRight = '-100%';
        carrito.style.opacity = '0';
        carritoVisible = false;
    
        var items = document.getElementsByClassName('contenedor-items')[0];
        if (items) {
            items.style.width = '100%';
        }
    }
}

function actualizarTotalCarrito(){
    var carritoContenedor = document.getElementsByClassName('carrito')[0];
    if (!carritoContenedor) return;
    
    var carritoItems = carritoContenedor.getElementsByClassName('carrito-item');
    var total = 0;
    
    for(var i=0; i< carritoItems.length; i++){
        var item = carritoItems[i];
        var precioElemento = item.getElementsByClassName('carrito-item-precio')[0];
        // Limpiar el precio de cualquier formato
        var precioTexto = precioElemento.innerText.replace('$','').replace(/\./g, '').replace(',','.');
        var precio = parseFloat(precioTexto);
        
        var cantidadItem = item.getElementsByClassName('carrito-item-cantidad')[0];
        var cantidad = parseInt(cantidadItem.value);
        
        total = total + (precio * cantidad);
    }
    
    total = Math.round(total * 100)/100;
    
    var totalElement = document.getElementsByClassName('carrito-precio-total')[0];
    if (totalElement) {
        totalElement.innerText = '$' + total.toLocaleString("es-AR", {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
}