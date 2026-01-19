<?php
/**
 * Model/Config/sync.php
 *
 * Configuración del módulo de sincronización (API).
 *
 * IMPORTANTE:
 * - No subas este archivo a repositorios públicos si contiene llaves reales.
 */

// Clave para invocar el SyncController
// Header esperado: Authorization: Bearer <SYNC_API_KEY>
const SYNC_API_KEY = 'CAMBIA_ESTA_CLAVE_LARGA_Y_RANDOM';

// Política de resolución de conflictos
// - last_write_wins: gana el registro con updated_at más reciente
// - prefer_local: ante conflicto gana local
// - prefer_external: ante conflicto gana externa
const SYNC_CONFLICT_POLICY = 'last_write_wins';

// Si está en true, el sync intentará crear la tabla sync_control si no existe.
const SYNC_AUTO_CREATE_CONTROL_TABLE = true;
