<?php
return [
  'productos' => [
    'local_table'    => 'productos',   // BD del compañero (destino)
    'external_table' => 'productos',   // tu BD externa (origen)
    'key'            => 'sync_uuid',

    // ✅ Esto lo espera tu código actual (BD EXTERNA)
    'updated_at' => 'updated_at',

    // ✅ Esta es la columna real en BD LOCAL (compañero)
    'local_updated_at' => 'fecha_actualizacion',

    'columns' => [
      'sync_uuid'    => 'sync_uuid',
      'id_categoria' => 'id_categoria',
      'nombre'       => 'nombre',
      'precio'       => 'precio',
      'activo'       => 'estado',

      // ⚠️ NO mapees fecha_actualizacion aquí (la maneja MySQL con ON UPDATE)
      // 'fecha_actualizacion' => 'updated_at',
    ],
  ],
];
