// Funciones para abrir y cerrar el modal
function openModal() {
  document.getElementById("modal").classList.remove("hidden");
  document.body.classList.add("overflow-hidden");
}

function closeModal() {
  document.getElementById("modal").classList.add("hidden");
  document.body.classList.remove("overflow-hidden");
}