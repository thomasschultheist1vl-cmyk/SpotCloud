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

// Filtra visualmente albumes o canciones antes de elegir que borrar.
if (document.documentElement.dataset.filtroEliminarListo !== '1') {
  const normalizarBusqueda = (texto) => texto
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase();

  const filtrarOpcionesEliminar = (input) => {
    const lista = document.getElementById(input.dataset.filtrarLista);
    const busqueda = normalizarBusqueda(input.value.trim());

    if (!lista) {
      return;
    }

    lista.querySelectorAll('.opcion-checkbox').forEach((opcion) => {
      const textoFiltro = opcion.dataset.textoBusqueda || opcion.textContent;
      const coincide = normalizarBusqueda(textoFiltro).includes(busqueda);
      opcion.classList.toggle('opcion-oculta', !coincide);

      if (!coincide) {
        const checkbox = opcion.querySelector('input[type="checkbox"]');
        if (checkbox) {
          checkbox.checked = false;
        }
      }
    });
  };

  document.documentElement.dataset.filtroEliminarListo = '1';
  document.addEventListener('keydown', (event) => {
    if (!event.target.matches('.busqueda-eliminar') || event.key !== 'Enter') {
      return;
    }

    event.preventDefault();
    filtrarOpcionesEliminar(event.target);
  });

  document.addEventListener('input', (event) => {
    if (event.target.matches('.busqueda-eliminar')) {
      filtrarOpcionesEliminar(event.target);
    }
  });

  document.addEventListener('search', (event) => {
    if (event.target.matches('.busqueda-eliminar')) {
      filtrarOpcionesEliminar(event.target);
    }
  });
}
