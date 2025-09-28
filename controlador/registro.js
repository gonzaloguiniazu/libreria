document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-registro');

  
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
        mensajeDiv.textContent = message;
        mensajeDiv.style.color = message.toLowerCase().includes("error") ? "red" : "green";
        mensajeDiv.style.fontWeight = "bold";
        mensajeDiv.style.margin = "10px 0";
    }
  });
  