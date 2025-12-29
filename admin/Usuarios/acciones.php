<?php
session_start();
require_once __DIR__ . "/../../Model/db.php";

$pdo = obtenerConexion();
$accion = $_POST["accion"] ?? "";


switch ($accion) {

    /* ======================
       CAMBIAR ROL
    ====================== */
    case "cambiar_rol":

        $idUsuario = (int)$_POST["id_usuario"];
        $idRol     = (int)$_POST["id_rol"];
        $idSesion  = (int)$_SESSION["id"]; 

        // ❌ NO permitir cambiarse el rol a sí mismo
        if ($idUsuario === $idSesion) {
            header("Location: index.php?error=self_role");
            exit;
        }

        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET id_rol = ?
            WHERE id_usuario = ?
        ");
        $stmt->execute([$idRol, $idUsuario]);

        header("Location: index.php?rol=ok");
        exit;


    /* ======================
       ACTIVAR / DESACTIVAR
    ====================== */
    case "toggle":

        $idUsuario = (int)$_POST["id_usuario"];
        $idSesion  = (int)$_SESSION["id"];// ❌ NO permitirse desactivarse
        if ($idUsuario === $idSesion) {
            header("Location: index.php?error=self_toggle");
            exit;
        }

        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET activo = IF(activo = 1, 0, 1)
            WHERE id_usuario = ?
        ");
        $stmt->execute([$idUsuario]);

        header("Location: index.php?toggle=1");
        exit;


    /* ======================
       EDITAR USUARIO
    ====================== */
    case "editar":

        $stmt = $pdo->prepare("
            UPDATE usuarios SET
                nombre = ?,
                apellido = ?,
                email = ?,
                telefono = ?,
                id_rol = ?,
                activo = ?
            WHERE id_usuario = ?
        ");

        $stmt->execute([
            $_POST["nombre"],
            $_POST["apellido"],
            $_POST["email"],
            $_POST["telefono"],
            $_POST["id_rol"],
            isset($_POST["activo"]) ? 1 : 0,
            $_POST["id_usuario"]
        ]);

        header("Location: index.php?edit=1");
        exit;
}

header("Location: index.php");
exit;
