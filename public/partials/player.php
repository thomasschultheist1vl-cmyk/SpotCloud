<!-- Reproductor global. El JS carga aca la cancion seleccionada. -->
<footer class="barra-reproductor" id="playerBar" aria-label="Reproductor de musica">
    <div class="cancion-reproductor">
        <img id="playerCover" src="" alt="">
        <div>
            <strong id="playerTitle">Selecciona una canciÃ³n</strong>
            <span id="playerArtist">SpotCloud</span>
        </div>
    </div>

    <div class="centro-reproductor">
        <div class="controles-reproductor">
            <button class="boton-reproductor" id="shuffleBtn" type="button" aria-label="Mezclar">&#8644;</button>
            <button class="boton-reproductor" id="prevBtn" type="button" aria-label="Anterior">&#9664;&#9664;</button>
            <button class="boton-play-reproductor" id="playPauseBtn" type="button" aria-label="Reproducir o pausar">&#9654;</button>
            <button class="boton-reproductor" id="nextBtn" type="button" aria-label="Siguiente">&#9654;&#9654;</button>
            <button class="boton-reproductor" id="closePlayerBtn" type="button" aria-label="Cerrar reproductor">&#215;</button>
        </div>

        <div class="progreso-reproductor">
            <span id="currentTime">0:00</span>
            <input id="progressBar" type="range" min="0" max="100" value="0" aria-label="Progreso">
            <span id="durationTime">0:00</span>
        </div>
    </div>

    <aside class="panel-cola-reproductor" id="queuePanel" aria-hidden="true" style="display: none;" hidden>
        <div class="encabezado-cola-reproductor">
            <div>
                <p class="etiqueta">Tu musica</p>
                <h2>Fila de reproduccion</h2>
            </div>
            <button class="boton-cerrar-cola" id="closeQueueBtn" type="button" aria-label="Cerrar fila">&#215;</button>
        </div>

        <div class="lista-cola-reproductor" id="queuePanelList" data-lista-cola>
            <p class="texto-suave fila-vacia">Cargando tu fila...</p>
        </div>

        <button class="boton boton-secundario js-limpiar-cola boton-limpiar-cola-panel" id="clearQueueBtn" type="button" hidden>Vaciar fila</button>
    </aside>

    <div class="acciones-reproductor">
        <button class="boton-cola-reproductor" id="queueToggleBtn" type="button" aria-expanded="false" aria-controls="queuePanel">Fila</button>
        <div class="volumen-reproductor">
            <span aria-hidden="true">&#9834;</span>
            <input id="volumeBar" type="range" min="0" max="1" step="0.01" value="0.8" aria-label="Volumen">
        </div>
    </div>

    <audio id="mainAudio" preload="metadata" playsinline></audio>
</footer>


