// Obtén el elemento select
var selectElement = document.querySelector('select[name="by"]');

// Agrega un evento change al elemento select
selectElement.addEventListener('change', function() {
  // Obtén el formulario que contiene el select
  var form = this.closest('form');

  // Envía el formulario
  form.submit();
});