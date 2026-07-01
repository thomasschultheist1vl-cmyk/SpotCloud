# SpotCloud - Explicacion del proyecto

SpotCloud es una pagina web dinamica de musica. El sistema permite registrar usuarios, iniciar sesion, ver albumes, entrar al detalle de cada album, reproducir canciones desde enlaces de Supabase y guardar un historial simple de reproducciones.

## Tecnologias usadas

- PHP orientado a objetos.
- MySQL como base de datos.
- PDO para conectarse a la base de datos.
- Modelo DAO para separar las consultas SQL del resto del codigo.
- HTML, CSS y JavaScript para la interfaz.
- Supabase Storage para alojar portadas y archivos MP3 mediante URLs guardadas en la base de datos.

## Estructura de carpetas

```text
SpotCloud/
  app/
    config/Conexion.php
    dao/
    helpers/funciones.php
    models/
  database/spotcloud.sql
  public/
    css/estilos.css
    js/app.js
    index.php
    album.php
    buscar.php
    login.php
    registro.php
    admin.php
```

## Como funciona el codigo

`Conexion.php` crea la conexion a MySQL usando PDO. Esta clase centraliza los datos de acceso a la base: servidor, nombre de base, usuario y contrasena.

La carpeta `models` contiene una clase por tabla principal de la base de datos. Por ejemplo, `Album`, `Artista`, `Cancion`, `Usuario`, `Historial` y `ColaReproduccion`. Estas clases representan los datos del sistema.

La carpeta `dao` contiene las clases que hacen consultas SQL. Por ejemplo, `AlbumDAO` obtiene albumes, `CancionDAO` obtiene canciones y busca musica, `UsuarioDAO` valida login y registra usuarios, y `HistorialDAO` guarda canciones reproducidas.

Las paginas de `public` usan los DAO para pedir datos y mostrarlos. De esta forma, la pagina no escribe consultas SQL directamente, sino que llama a metodos como `obtenerTodos()`, `obtenerPorAlbum()` o `validarLogin()`.

## Flujo principal

1. El usuario entra a `login.php`.
2. Si no tiene cuenta, puede registrarse en `registro.php`.
3. Al iniciar sesion, entra a `index.php`, donde ve albumes y canciones.
4. Al elegir un album, entra a `album.php?id=...`.
5. Las canciones se reproducen con la etiqueta HTML `<audio>`.
6. Cuando se reproduce una cancion, `app.js` llama a `guardar_historial.php`.
7. `guardar_historial.php` usa `HistorialDAO` para guardar la reproduccion en la tabla `historial`.

## Base de datos

El archivo `database/spotcloud.sql` crea la base `spotcloud` y sus tablas:

- `usuarios`
- `artistas`
- `albumes`
- `canciones`
- `historial`
- `cola_reproduccion`
- `cola_canciones`
- `usuarios_artistas`

Las canciones y portadas no se guardan como archivos dentro del proyecto. Se guardan como URLs de Supabase en los campos `ruta_archivo_mp3` y `ruta_portada`.

## Como instalarlo en XAMPP

1. Copiar la carpeta `SpotCloud` dentro de `htdocs`.
2. Abrir XAMPP y activar Apache y MySQL.
3. Entrar a phpMyAdmin.
4. Importar el archivo `database/spotcloud.sql`.
5. Abrir en el navegador: `http://localhost/SpotCloud/public/login.php`.

Usuarios de prueba incluidos en la base:

- `mateo@spotcloud.com` / `123456`
- `morena@spotcloud.com` / `654321`

## Archivos mas importantes

- `public/index.php`: pantalla principal con albumes, canciones e historial.
- `public/album.php`: detalle de album con lista de canciones.
- `public/login.php`: inicio de sesion.
- `public/registro.php`: registro de usuarios.
- `public/admin.php`: carga simple de artistas, albumes y canciones.
- `public/css/estilos.css`: colores y estilos visuales de SpotCloud.
- `public/js/app.js`: pausa otros audios y guarda historial al reproducir.

## Idea para explicar en clase

El proyecto usa POO porque cada tabla importante tiene su propia clase. Tambien usa DAO porque las consultas SQL estan separadas en clases especiales, lo que hace que el codigo sea mas ordenado y facil de mantener. La pagina no guarda archivos MP3 localmente: solo muestra y reproduce las URLs almacenadas en la base de datos, que apuntan a Supabase.
