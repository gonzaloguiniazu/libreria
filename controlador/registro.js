document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
  
    form.addEventListener('submit', function(event) {
        const confirmMessage = confirm("Estás a punto de darte de alta como usuario. ¿Deseas continuar?");
        
        if (!confirmMessage) {
            event.preventDefault(); // Evita el envío del formulario si el usuario cancela
        } 
        // Si el usuario acepta, permitimos que el formulario se envíe normalmente
    });
  
    // Mostrar mensaje si hay uno en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    if (message) {
        alert(message); // Muestra el mensaje de registro exitoso
    }
  });
  