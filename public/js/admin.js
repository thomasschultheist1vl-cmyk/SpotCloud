// Muestra el nombre del archivo elegido debajo del boton.
document.querySelectorAll('.input-archivo').forEach((input) => {
  if (input.dataset.archivoListo === '1') {
    return;
  }

  input.dataset.archivoListo = '1';
  input.addEventListener('change', () => {
    const textoArchivo = document.getElementById(input.dataset.textoArchivo);

    if (!textoArchivo) {
      return;
    }

    textoArchivo.textContent = input.files.length > 0
      ? input.files[0].name
      : 'Sin archivos seleccionados.';
  });
});

// Muestra solo la funcion elegida del panel de administracion.
document.querySelectorAll('.paso-admin').forEach((boton) => {
  if (boton.dataset.pasoListo === '1') {
    return;
  }

  boton.dataset.pasoListo = '1';
  boton.addEventListener('click', () => {
    const seccion = boton.dataset.seccion;

    document.querySelectorAll('.paso-admin').forEach((item) => {
      item.classList.toggle('activo', item.dataset.seccion === seccion);
    });

    document.querySelectorAll('.seccion-admin').forEach((panel) => {
      panel.classList.toggle('activa', panel.dataset.panel === seccion);
    });
  });
});