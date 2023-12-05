// Funciones para abrir y cerrar el modal
function openModal() {
  document.getElementById("modal").classList.remove("hidden");
  document.body.classList.add("overflow-hidden");
}

function closeModal() {
  document.getElementById("modal").classList.add("hidden");
  document.body.classList.remove("overflow-hidden");
}

// Funciones para abrir y cerrar el modal de confirmación de logout
function openLogoutModal() {
  document.getElementById("logout-modal").classList.remove("hidden");
  document.body.classList.add("overflow-hidden");
}

function closeLogoutModal() {
  document.getElementById("logout-modal").classList.add("hidden");
  document.body.classList.remove("overflow-hidden");
}

// Funciones para abrir y cerrar el modal para añadir un nuevo miembro
function openAddModal() {
  document.getElementById("add-modal").classList.remove("hidden");
  document.body.classList.add("overflow-hidden");
}

function closeAddModal() {
  document.getElementById("add-modal").classList.add("hidden");
  document.body.classList.remove("overflow-hidden");
}


// Obtén el elemento select
var selectElement = document.querySelector('select[name="by"]');

// Agrega un evento change al elemento select
selectElement.addEventListener('change', function() {
  // Obtén el formulario que contiene el select
  var form = this.closest('form');

  // Envía el formulario
  form.submit();
});