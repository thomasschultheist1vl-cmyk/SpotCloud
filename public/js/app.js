// Reproductor global y navegacion interna de SpotCloud.
// La idea es que el audio siga vivo aunque el usuario cambie entre Inicio, Buscar, Album o Cargar.
const playerBar = document.getElementById('playerBar');
const audio = document.getElementById('mainAudio');
const estadoKey = 'spotcloudPlayerState';

if (playerBar && audio) {
  const playerCover = document.getElementById('playerCover');
  const playerTitle = document.getElementById('playerTitle');
  const playerArtist = document.getElementById('playerArtist');
  const playPauseBtn = document.getElementById('playPauseBtn');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const shuffleBtn = document.getElementById('shuffleBtn');
  const closePlayerBtn = document.getElementById('closePlayerBtn');
  const progressBar = document.getElementById('progressBar');
  const currentTime = document.getElementById('currentTime');
  const durationTime = document.getElementById('durationTime');
  const volumeBar = document.getElementById('volumeBar');

  let tracks = [];
  let currentIndex = -1;
  let currentTrack = null;
  let shuffle = false;
  let pendingTime = 0;
  let restoring = false;
  const vistaSinControlVolumen = window.matchMedia('(max-width: 900px)');
  const savedTracks = new Set();

  const formatTime = (seconds) => {
    if (!Number.isFinite(seconds)) {
      return '0:00';
    }

    const minutes = Math.floor(seconds / 60);
    const rest = Math.floor(seconds % 60).toString().padStart(2, '0');
    return `${minutes}:${rest}`;
  };

  const datosDeCancion = (track) => ({
    id: track.dataset.id || '',
    title: track.dataset.title || 'Selecciona una cancion',
    artist: track.dataset.artist || 'SpotCloud',
    cover: track.dataset.cover || '',
    src: track.dataset.src || '',
    enCola: track.dataset.enCola === '1',
  });

  const datosDesdeCola = (cancion) => ({
    id: String(cancion.id_cancion || ''),
    title: cancion.titulo || 'Selecciona una cancion',
    artist: cancion.artista || 'SpotCloud',
    cover: cancion.ruta_portada || '',
    src: cancion.ruta_archivo_mp3 || '',
    enCola: true,
  });
  const buscarIndiceActual = () => {
    if (!currentTrack) {
      currentIndex = -1;
      return;
    }

    currentIndex = tracks.findIndex((track) => {
      return track.dataset.id === currentTrack.id || track.dataset.src === currentTrack.src;
    });
  };

  const guardarEstado = () => {
    const estado = {
      track: currentTrack,
      time: audio.currentTime || pendingTime || 0,
      playing: currentTrack ? !audio.paused : false,
      volume: vistaSinControlVolumen.matches ? 1 : audio.volume,
      shuffle,
    };

    localStorage.setItem(estadoKey, JSON.stringify(estado));
  };

  const borrarEstado = () => {
    localStorage.removeItem(estadoKey);
  };

  const updateMediaSession = (track) => {
    if (!track || !('mediaSession' in navigator) || !window.MediaMetadata) {
      return;
    }

    const artwork = track.cover ? [{ src: track.cover, sizes: '512x512' }] : [];

    navigator.mediaSession.metadata = new MediaMetadata({
      title: track.title || 'SpotCloud',
      artist: track.artist || 'SpotCloud',
      album: 'SpotCloud',
      artwork,
    });
  };

  const updateButtons = () => {
    playPauseBtn.innerHTML = audio.paused ? '&#9654;' : '&#10074;&#10074;';
    shuffleBtn.classList.toggle('activo', shuffle);

    if ('mediaSession' in navigator) {
      navigator.mediaSession.playbackState = audio.paused ? 'paused' : 'playing';
    }

    tracks.forEach((track, index) => {
      const button = track.querySelector('.js-reproducir-cancion');
      const esActual = currentIndex === index;
      const active = esActual && !audio.paused;

      track.classList.toggle('reproduciendo', active);

      if (button) {
        button.textContent = active ? 'Pausar' : 'Reproducir';
      }
    });
  };

  const pintarCancion = (track) => {
    currentTrack = track;
    playerCover.src = track.cover;
    playerCover.alt = `Portada de ${track.title}`;
    playerTitle.textContent = track.title;
    playerArtist.textContent = track.artist || 'SpotCloud';
    playerBar.classList.add('visible');
    updateMediaSession(track);
    buscarIndiceActual();
    updateButtons();
  };

  const saveHistory = (track) => {
    if (!track || !track.id || savedTracks.has(track.id)) {
      return;
    }

    savedTracks.add(track.id);
    const data = new FormData();
    data.append('id_cancion', track.id);

    fetch('guardar_historial.php', {
      method: 'POST',
      body: data,
    }).catch(() => {
      savedTracks.delete(track.id);
    });
  };

  const reproducirActual = () => {
    if (!currentTrack || !audio.src) {
      return;
    }

    audio.play()
      .then(() => {
        saveHistory(currentTrack);
        guardarEstado();
      })
      .catch(() => {
        guardarEstado();
        updateButtons();
      });
  };

  const loadTrackData = (track, autoplay = true) => {
    currentIndex = -1;
    pendingTime = 0;
    audio.src = track.src;
    progressBar.value = 0;
    currentTime.textContent = '0:00';
    durationTime.textContent = '0:00';
    pintarCancion(track);

    if (autoplay) {
      reproducirActual();
    } else {
      guardarEstado();
    }
  };
  const loadTrack = (index, autoplay = true) => {
    const fila = tracks[index];

    if (!fila) {
      return;
    }

    const track = datosDeCancion(fila);
    currentIndex = index;
    pendingTime = 0;
    audio.src = track.src;
    progressBar.value = 0;
    currentTime.textContent = '0:00';
    durationTime.textContent = '0:00';
    pintarCancion(track);

    if (autoplay) {
      reproducirActual();
    } else {
      guardarEstado();
    }
  };

  const playNext = () => {
    if (!tracks.length) {
      return;
    }

    if (shuffle && tracks.length > 1) {
      let nextIndex = currentIndex < 0 ? 0 : currentIndex;

      while (nextIndex === currentIndex) {
        nextIndex = Math.floor(Math.random() * tracks.length);
      }

      loadTrack(nextIndex);
      return;
    }

    loadTrack(currentIndex < 0 ? 0 : (currentIndex + 1) % tracks.length);
  };

  const pausarFinDeCola = () => {
    audio.pause();
    audio.removeAttribute('src');
    audio.load();
    currentIndex = -1;
    currentTrack = null;
    pendingTime = 0;
    progressBar.value = 0;
    currentTime.textContent = '0:00';
    durationTime.textContent = '0:00';
    borrarEstado();
    updateButtons();
  };

  const quitarFilaTerminada = async () => {
    if (!currentTrack || !currentTrack.enCola) {
      playNext();
      return;
    }

    const idCancion = currentTrack.id;
    const indiceTerminado = currentIndex;
    const listaCola = document.querySelector('[data-lista-cola]');

    let resultado = null;

    try {
      resultado = await enviarAccionCola('quitar', idCancion);
    } catch (error) {
      // Aunque falle la peticion, evitamos que la cola local quede en loop.
    }

    const cancionesRestantes = Array.isArray(resultado?.canciones) ? resultado.canciones : [];

    if (!listaCola) {
      if (cancionesRestantes.length > 0) {
        loadTrackData(datosDesdeCola(cancionesRestantes[0]));
      } else {
        pausarFinDeCola();
      }

      return;
    }

    Array.from(listaCola.querySelectorAll('.js-cancion')).forEach((fila) => {
      if (fila.dataset.id === idCancion) {
        fila.remove();
      }
    });

    actualizarNumerosFila();
    mostrarFilaVacia();
    inicializarCanciones();

    if (!tracks.length) {
      pausarFinDeCola();
      return;
    }

    loadTrack(Math.min(indiceTerminado, tracks.length - 1));
  };

  const inicializarCanciones = () => {
    tracks = Array.from(document.querySelectorAll('.js-cancion'));
    buscarIndiceActual();

    tracks.forEach((track, index) => {
      const button = track.querySelector('.js-reproducir-cancion');

      if (!button || button.dataset.playerListo === '1') {
        return;
      }

      button.dataset.playerListo = '1';
      button.addEventListener('click', () => {
        if (currentIndex === index && currentTrack) {
          if (audio.paused) {
            reproducirActual();
          } else {
            audio.pause();
            guardarEstado();
          }
        } else {
          loadTrack(index);
        }

        updateButtons();
      });
    });

    updateButtons();
  };

  const inicializarAdmin = () => {
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
  };

  const mostrarRespuestaCola = (button, mensaje) => {
    if (!button) {
      return;
    }

    const textoOriginal = button.dataset.textoOriginal || button.textContent;
    button.dataset.textoOriginal = textoOriginal;
    button.textContent = mensaje;
    button.disabled = true;

    setTimeout(() => {
      button.textContent = textoOriginal;
      button.disabled = false;
    }, 1400);
  };

  const enviarAccionCola = async (accion, idCancion = '') => {
    const data = new FormData();
    data.append('accion', accion);

    if (idCancion) {
      data.append('id_cancion', idCancion);
    }

    const respuesta = await fetch('gestionar_cola.php', {
      method: 'POST',
      body: data,
      credentials: 'same-origin',
    });

    return respuesta.json();
  };

  const actualizarNumerosFila = () => {
    document.querySelectorAll('[data-lista-cola] .fila-cancion').forEach((fila, index) => {
      const numero = fila.querySelector('.numero-cancion');

      if (numero) {
        numero.textContent = index + 1;
      }
    });
  };

  const mostrarFilaVacia = () => {
    const lista = document.querySelector('[data-lista-cola]');

    if (!lista || lista.querySelector('.fila-cancion')) {
      return;
    }

    lista.innerHTML = '<p class="texto-suave fila-vacia">Todavia no agregaste canciones a tu fila.</p>';

    const botonLimpiar = document.querySelector('.js-limpiar-cola');
    if (botonLimpiar) {
      botonLimpiar.remove();
    }
  };

  const inicializarCola = () => {
    document.querySelectorAll('.js-agregar-cola').forEach((button) => {
      if (button.dataset.colaLista === '1') {
        return;
      }

      button.dataset.colaLista = '1';
      button.addEventListener('click', async () => {
        const idCancion = button.dataset.idCancion;
        mostrarRespuestaCola(button, 'Agregando...');

        try {
          const resultado = await enviarAccionCola('agregar', idCancion);
          mostrarRespuestaCola(button, resultado.mensaje || 'Listo');
        } catch (error) {
          mostrarRespuestaCola(button, 'Error');
        }
      });
    });

    document.querySelectorAll('.js-quitar-cola').forEach((button) => {
      if (button.dataset.colaLista === '1') {
        return;
      }

      button.dataset.colaLista = '1';
      button.addEventListener('click', async () => {
        const idCancion = button.dataset.idCancion;

        try {
          const resultado = await enviarAccionCola('quitar', idCancion);

          if (resultado.ok) {
            button.closest('.fila-cancion')?.remove();
            actualizarNumerosFila();
            mostrarFilaVacia();
          }
        } catch (error) {
          mostrarRespuestaCola(button, 'Error');
        }
      });
    });

    document.querySelectorAll('.js-limpiar-cola').forEach((button) => {
      if (button.dataset.colaLista === '1') {
        return;
      }

      button.dataset.colaLista = '1';
      button.addEventListener('click', async () => {
        if (!confirm('Seguro que queres vaciar tu fila de reproduccion?')) {
          return;
        }

        try {
          const resultado = await enviarAccionCola('limpiar');

          if (resultado.ok) {
            document.querySelectorAll('[data-lista-cola] .fila-cancion').forEach((fila) => fila.remove());
            mostrarFilaVacia();
          }
        } catch (error) {
          mostrarRespuestaCola(button, 'Error');
        }
      });
    });
  };

  const inicializarVista = () => {
    inicializarCanciones();
    inicializarAdmin();
    inicializarCola();
  };

  const restaurarEstado = () => {
    const guardado = localStorage.getItem(estadoKey);

    if (!guardado) {
      return;
    }

    try {
      const estado = JSON.parse(guardado);

      if (!estado.track || !estado.track.src) {
        return;
      }

      restoring = true;
      shuffle = Boolean(estado.shuffle);
      if (!vistaSinControlVolumen.matches) {
        volumeBar.value = estado.volume ?? volumeBar.value;
      }
      audio.volume = vistaSinControlVolumen.matches ? 1 : volumeBar.value;
      pendingTime = Number(estado.time) || 0;
      audio.src = estado.track.src;
      pintarCancion(estado.track);

      if (estado.playing) {
        reproducirActual();
      }
    } catch (error) {
      borrarEstado();
    } finally {
      restoring = false;
    }
  };

  const urlInterna = (url) => {
    return url.origin === window.location.origin && url.pathname.includes('/SpotCloud/public/');
  };

  const cargarPagina = async (url, guardarHistorial = true) => {
    const respuesta = await fetch(url.href, { credentials: 'same-origin' });

    if (respuesta.redirected || !respuesta.ok) {
      window.location.href = url.href;
      return;
    }

    const html = await respuesta.text();
    const doc = new DOMParser().parseFromString(html, 'text/html');
    const nuevoMain = doc.querySelector('main');
    const mainActual = document.querySelector('main');

    if (!nuevoMain || !mainActual) {
      window.location.href = url.href;
      return;
    }

    mainActual.replaceWith(nuevoMain);
    document.title = doc.title || document.title;

    if (guardarHistorial) {
      history.pushState(null, '', url.href);
    }

    window.scrollTo({ top: 0, behavior: 'auto' });
    inicializarVista();
    guardarEstado();
  };

  document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');

    if (!link || event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
      return;
    }

    if (link.target === '_blank' || link.hasAttribute('download')) {
      return;
    }

    const url = new URL(link.href, window.location.href);

    if (!urlInterna(url) || url.pathname.endsWith('/logout.php')) {
      return;
    }

    event.preventDefault();
    cargarPagina(url).catch(() => {
      window.location.href = url.href;
    });
  });

  document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!form.classList.contains('form-busqueda') || form.method.toLowerCase() !== 'get') {
      return;
    }

    event.preventDefault();
    const url = new URL(form.action || window.location.href, window.location.href);
    const data = new FormData(form);
    url.search = new URLSearchParams(data).toString();

    cargarPagina(url).catch(() => {
      window.location.href = url.href;
    });
  });

  window.addEventListener('popstate', () => {
    cargarPagina(new URL(window.location.href), false).catch(() => {
      window.location.reload();
    });
  });

  playPauseBtn.addEventListener('click', () => {
    if (!currentTrack) {
      loadTrack(currentIndex >= 0 ? currentIndex : 0);
      return;
    }

    if (audio.paused) {
      reproducirActual();
    } else {
      audio.pause();
      guardarEstado();
    }

    updateButtons();
  });

  prevBtn.addEventListener('click', () => {
    if (!tracks.length) {
      return;
    }

    const nextIndex = currentIndex <= 0 ? tracks.length - 1 : currentIndex - 1;
    loadTrack(nextIndex);
  });

  nextBtn.addEventListener('click', playNext);

  if ('mediaSession' in navigator) {
    navigator.mediaSession.setActionHandler('play', reproducirActual);

    navigator.mediaSession.setActionHandler('pause', () => {
      audio.pause();
      guardarEstado();
      updateButtons();
    });

    navigator.mediaSession.setActionHandler('previoustrack', () => {
      if (!tracks.length) {
        return;
      }

      const nextIndex = currentIndex <= 0 ? tracks.length - 1 : currentIndex - 1;
      loadTrack(nextIndex);
    });

    navigator.mediaSession.setActionHandler('nexttrack', playNext);
  }

  shuffleBtn.addEventListener('click', () => {
    shuffle = !shuffle;
    guardarEstado();
    updateButtons();
  });

  closePlayerBtn.addEventListener('click', () => {
    audio.pause();
    audio.removeAttribute('src');
    audio.load();
    currentIndex = -1;
    currentTrack = null;
    pendingTime = 0;
    playerBar.classList.remove('visible');
    progressBar.value = 0;
    currentTime.textContent = '0:00';
    durationTime.textContent = '0:00';
    borrarEstado();
    updateButtons();
  });

  audio.addEventListener('loadedmetadata', () => {
    if (pendingTime && audio.duration) {
      audio.currentTime = Math.min(pendingTime, audio.duration - 1);
      pendingTime = 0;
    }

    durationTime.textContent = formatTime(audio.duration);
  });

  audio.addEventListener('timeupdate', () => {
    if (!audio.duration) {
      return;
    }

    progressBar.value = (audio.currentTime / audio.duration) * 100;
    currentTime.textContent = formatTime(audio.currentTime);
    durationTime.textContent = formatTime(audio.duration);

    if (!restoring) {
      guardarEstado();
    }
  });

  audio.addEventListener('ended', quitarFilaTerminada);
  audio.addEventListener('play', () => {
    guardarEstado();
    updateButtons();
  });
  audio.addEventListener('pause', () => {
    guardarEstado();
    updateButtons();
  });

  progressBar.addEventListener('input', () => {
    if (audio.duration) {
      audio.currentTime = (progressBar.value / 100) * audio.duration;
      guardarEstado();
    }
  });

  const aplicarVolumenSegunVista = (guardar = true) => {
    audio.volume = vistaSinControlVolumen.matches ? 1 : volumeBar.value;

    if (guardar && currentTrack) {
      guardarEstado();
    }
  };

  volumeBar.addEventListener('input', () => aplicarVolumenSegunVista());

  if (vistaSinControlVolumen.addEventListener) {
    vistaSinControlVolumen.addEventListener('change', () => aplicarVolumenSegunVista());
  } else {
    vistaSinControlVolumen.addListener(() => aplicarVolumenSegunVista());
  }

  aplicarVolumenSegunVista(false);
  inicializarVista();
  restaurarEstado();
}
