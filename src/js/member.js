/**
 * Carga un archivo de imagen y muestra la imagen en el elemento de salida.
 */
function loadFile(event) {
  const file = event.target.files[0];
  if (
    file &&
    (file.type === "image/png" ||
      file.type === "image/jpg" ||
      file.type === "image/jpeg") ||
      file.type === "image/webp"
  ) {
    const output = document.getElementById("output");
    output.src = URL.createObjectURL(file);
    output.classList.remove("hidden");
    document.getElementById("dropzone-container").classList.add("hidden");
    // Muestra el segundo input
    document.getElementById('second-input').classList.remove('hidden');
  } else {
    Toastify({
      text: "Tipo de archivo no válido",
      duration: 3000,
      close: true,
      gravity: "top", // `top` or `bottom`
      position: 'right', // `left`, `center` or `right`
      stopOnFocus: true, // Prevents dismissing of toast on hover
      style: {
        background: "#fd1d1d",
        borderRadius: "10px"
      }
    }).showToast();
  }
}

function dropHandler(ev) {
   // Evita el comportamiento predeterminado de arrastrar y soltar
   // El navegador no realice ninguna acción predeterminada mientras se arrastra un elemento sobre el área específica
  ev.preventDefault();

  if (ev.dataTransfer.items) {
    // Si hay elementos en la transferencia de datos
    for (var i = 0; i < ev.dataTransfer.items.length; i++) {
      if (ev.dataTransfer.items[i].kind === 'file') {
        // Si el elemento es un archivo
        var file = ev.dataTransfer.items[i].getAsFile(); // Obtiene el archivo
        loadFile({ target: { files: [file] } }); // Llama a la función loadFile con el archivo como argumento
      }
    }
  } else {
    // Si no hay elementos en la transferencia de datos
    for (var i = 0; i < ev.dataTransfer.files.length; i++) {
      // Recorre los archivos en la transferencia de datos
      loadFile({ target: { files: [ev.dataTransfer.files[i]] } }); // Llama a la función loadFile con cada archivo como argumento
    }
  }
}

function dragOverHandler(ev) {
  // Evita el comportamiento predeterminado de arrastrar y soltar
  ev.preventDefault();
}