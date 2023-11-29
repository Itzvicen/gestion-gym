var loadFile = function (event) {
  var output = document.getElementById("output");
  var dropzoneContainer = document.getElementById("dropzone-container");

  output.src = URL.createObjectURL(event.target.files[0]);
  output.onload = function () {
    URL.revokeObjectURL(output.src); // Libera la memoria de la URL
  };
  output.classList.remove("hidden"); // Muestra la imagen de vista previa
  dropzoneContainer.classList.add("hidden"); // Oculta el input de subida
};

// Funciones para abrir y cerrar el modal
function openModal() {
  document.getElementById("modal").classList.remove("hidden");
  document.body.classList.add("overflow-hidden");
}

function closeModal() {
  document.getElementById("modal").classList.add("hidden");
  document.body.classList.remove("overflow-hidden");
}
