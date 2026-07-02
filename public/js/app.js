// Tomamos todas las filas de canciones que tienen datos para reproducir.
const tracks = Array.from(document.querySelectorAll('.js-cancion'));
const playerBar = document.getElementById('playerBar');
const audio = document.getElementById('mainAudio');

// Si la pagina tiene canciones, se activa el reproductor personalizado.
if (playerBar && audio && tracks.length > 0) {
  // Elementos visibles de la barra inferior.
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
  let currentIndex = -1;
  let shuffle = false;
  const savedTracks = new Set();

  // Convierte segundos del audio a formato minuto:segundo.
  const formatTime = (seconds) => {
    if (!Number.isFinite(seconds)) {
      return '0:00';
    }

    const minutes = Math.floor(seconds / 60);
    const rest = Math.floor(seconds % 60).toString().padStart(2, '0');
    return `${minutes}:${rest}`;
  };

  // Sincroniza botones, texto y clase visual de la cancion activa.
  const updateButtons = () => {
    playPauseBtn.innerHTML = audio.paused ? '&#9654;' : '&#10074;&#10074;';
    shuffleBtn.classList.toggle('activo', shuffle);

    if ('mediaSession' in navigator) {
      navigator.mediaSession.playbackState = audio.paused ? 'paused' : 'playing';
    }

    tracks.forEach((track, index) => {
      const button = track.querySelector('.js-reproducir-cancion');
      const active = index === currentIndex && !audio.paused;
      track.classList.toggle('reproduciendo', active);

      if (button) {
        button.textContent = active ? 'Pausar' : 'Reproducir';
      }
    });
  };

  // Mejora los controles de audio en celulares, especialmente Android.
  const updateMediaSession = (track) => {
    if (!('mediaSession' in navigator) || !window.MediaMetadata) {
      return;
    }

    const artwork = track.dataset.cover ? [{ src: track.dataset.cover, sizes: '512x512' }] : [];

    navigator.mediaSession.metadata = new MediaMetadata({
      title: track.dataset.title || 'SpotCloud',
      artist: track.dataset.artist || 'SpotCloud',
      album: 'SpotCloud',
      artwork,
    });
  };

  // Guarda historial una sola vez por cancion durante la visita actual.
  const saveHistory = (track) => {
    const id = track.dataset.id;

    if (!id || savedTracks.has(id)) {
      return;
    }

    savedTracks.add(id);
    const data = new FormData();
    data.append('id_cancion', id);

    fetch('guardar_historial.php', {
      method: 'POST',
      body: data,
    }).catch(() => {
      savedTracks.delete(id);
    });
  };

  // Carga una cancion en el audio principal y actualiza la barra.
  const loadTrack = (index, autoplay = true) => {
    const track = tracks[index];

    if (!track) {
      return;
    }

    currentIndex = index;
    audio.src = track.dataset.src;
    playerCover.src = track.dataset.cover;
    playerCover.alt = `Portada de ${track.dataset.title}`;
    playerTitle.textContent = track.dataset.title;
    playerArtist.textContent = track.dataset.artist || 'SpotCloud';
    playerBar.classList.add('visible');
    progressBar.value = 0;
    currentTime.textContent = '0:00';
    updateMediaSession(track);

    if (autoplay) {
      audio.play();
      saveHistory(track);
    }

    updateButtons();
  };

  // Avanza a la proxima cancion o elige una al azar si shuffle esta activo.
  const playNext = () => {
    if (shuffle && tracks.length > 1) {
      let nextIndex = currentIndex;

      while (nextIndex === currentIndex) {
        nextIndex = Math.floor(Math.random() * tracks.length);
      }

      loadTrack(nextIndex);
      return;
    }

    loadTrack((currentIndex + 1) % tracks.length);
  };

  // Cada boton "Reproducir" de la lista controla la barra inferior.
  tracks.forEach((track, index) => {
    const button = track.querySelector('.js-reproducir-cancion');

    if (!button) {
      return;
    }

    button.addEventListener('click', () => {
      if (currentIndex === index) {
        if (audio.paused) {
          audio.play();
          saveHistory(track);
        } else {
          audio.pause();
        }
      } else {
        loadTrack(index);
      }

      updateButtons();
    });
  });

  // Boton central de play/pausa del reproductor.
  playPauseBtn.addEventListener('click', () => {
    if (currentIndex === -1) {
      loadTrack(0);
      return;
    }

    if (audio.paused) {
      audio.play();
      saveHistory(tracks[currentIndex]);
    } else {
      audio.pause();
    }

    updateButtons();
  });

  // Botones de navegacion entre canciones.
  prevBtn.addEventListener('click', () => {
    const nextIndex = currentIndex <= 0 ? tracks.length - 1 : currentIndex - 1;
    loadTrack(nextIndex);
  });

  nextBtn.addEventListener('click', playNext);

  if ('mediaSession' in navigator) {
    navigator.mediaSession.setActionHandler('play', () => {
      if (currentIndex === -1) {
        loadTrack(0);
        return;
      }

      audio.play();
      saveHistory(tracks[currentIndex]);
      updateButtons();
    });

    navigator.mediaSession.setActionHandler('pause', () => {
      audio.pause();
      updateButtons();
    });

    navigator.mediaSession.setActionHandler('previoustrack', () => {
      const nextIndex = currentIndex <= 0 ? tracks.length - 1 : currentIndex - 1;
      loadTrack(nextIndex);
    });

    navigator.mediaSession.setActionHandler('nexttrack', playNext);
  }

  // Activa o desactiva reproduccion aleatoria.
  shuffleBtn.addEventListener('click', () => {
    shuffle = !shuffle;
    updateButtons();
  });

  // Cierra la barra y limpia el audio cargado.
  closePlayerBtn.addEventListener('click', () => {
    audio.pause();
    audio.removeAttribute('src');
    audio.load();
    currentIndex = -1;
    playerBar.classList.remove('visible');
    updateButtons();
  });

  // Actualiza la barra de progreso mientras suena la cancion.
  audio.addEventListener('timeupdate', () => {
    if (!audio.duration) {
      return;
    }

    progressBar.value = (audio.currentTime / audio.duration) * 100;
    currentTime.textContent = formatTime(audio.currentTime);
    durationTime.textContent = formatTime(audio.duration);
  });

  // Cuando el navegador lee la duracion, la mostramos a la derecha.
  audio.addEventListener('loadedmetadata', () => {
    durationTime.textContent = formatTime(audio.duration);
  });

  audio.addEventListener('ended', playNext);
  audio.addEventListener('play', updateButtons);
  audio.addEventListener('pause', updateButtons);

  // Permite mover manualmente el progreso de la cancion.
  progressBar.addEventListener('input', () => {
    if (audio.duration) {
      audio.currentTime = (progressBar.value / 100) * audio.duration;
    }
  });

  // Control de volumen de la barra derecha.
  volumeBar.addEventListener('input', () => {
    audio.volume = volumeBar.value;
  });

  audio.volume = volumeBar.value;
}




