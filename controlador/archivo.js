document.addEventListener("DOMContentLoaded", function() {
    const buttonsAgregar = document.querySelectorAll('.btn-agregar');

    buttonsAgregar.forEach(button => {
        button.addEventListener('click', function() {
            const idProducto = this.getAttribute('data-id');

            // Hacer una petición AJAX para añadir el producto al carrito
            fetch('../modelo/agregar_carrito.php', { // Cambia la ruta según tu estructura
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_producto: idProducto })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Producto añadido al carrito');
                } else {
                    alert('Error al añadir el producto');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        });
    });
});
