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