<?php
require_once __DIR__ . '/../app/helpers/funciones.php';

iniciarSesion();
session_destroy();

header('Location: login.php');
exit;


