<?php
require_once __DIR__ . '/../models/Persona.php';

class PersonaController {
    public static function index(): void {
        $personas = Persona::all();
        // Add active group count to each persona
        foreach ($personas as &$p) {
            $p['grupos_activos'] = Persona::activeGroupCount($p['id']);
        }
        unset($p);
        require __DIR__ . '/../views/personas/index.php';
    }

    public static function store(): void {
        $nombre   = trim($_POST['nombre'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');

        if (!$nombre) {
            $_SESSION['flash_error'] = 'El nombre es requerido.';
        } else {
            Persona::create($nombre, $telefono ?: null);
            $_SESSION['flash'] = 'Persona agregada.';
        }
        header('Location: ?page=personas');
        exit;
    }

    public static function update(): void {
        $id       = (int)($_POST['id'] ?? 0);
        $nombre   = trim($_POST['nombre'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');

        if (!$nombre) {
            $_SESSION['flash_error'] = 'El nombre es requerido.';
        } else {
            Persona::update($id, $nombre, $telefono ?: null);
            $_SESSION['flash'] = 'Persona actualizada.';
        }
        header('Location: ?page=personas');
        exit;
    }

    public static function delete(): void {
        $id = (int)($_POST['id'] ?? 0);

        if (Persona::isActiveInPasanaku($id)) {
            $_SESSION['flash_error'] = 'No se puede eliminar: la persona está en un pasanaku activo.';
        } else {
            Persona::delete($id);
            $_SESSION['flash'] = 'Persona eliminada.';
        }
        header('Location: ?page=personas');
        exit;
    }
}
