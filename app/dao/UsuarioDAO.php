<?php

require_once __DIR__ . '/../config/Conexion.php';

class UsuarioDAO
{
    private PDO $db;

    public function __construct()
    {
        // PDO permite usar consultas preparadas y evitar inyeccion SQL.
        $this->db = (new Conexion())->conectar();
    }

    // Se usa en login y registro para encontrar usuarios por correo.
    public function obtenerPorCorreo(string $correo): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    // Crea un usuario nuevo. La clave se guarda hasheada.
    public function registrar(string $nombre, string $correo, string $password): bool
    {
        $sql = "INSERT INTO usuarios (nombre, correo, password)
                VALUES (:nombre, :correo, :password)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'nombre' => $nombre,
            'correo' => $correo,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }

    // Valida las credenciales y devuelve solo los datos necesarios para la sesion.
    public function validarLogin(string $correo, string $password): ?array
    {
        $usuario = $this->obtenerPorCorreo($correo);

        if (!$usuario) {
            return null;
        }

        // Permite entrar con passwords hasheados y tambien con los datos simples del SQL original.
        $passwordCorrecta = password_verify($password, $usuario['password']) || $password === $usuario['password'];

        if (!$passwordCorrecta) {
            return null;
        }

        return [
            'id_usuario' => (int) $usuario['id_usuario'],
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
        ];
    }
}
