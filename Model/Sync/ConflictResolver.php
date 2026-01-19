<?php
/**
 * Model/Sync/ConflictResolver.php
 */

require_once __DIR__ . '/../Config/sync.php';

class ConflictResolver
{
    /**
     * Decide qué registro gana ante conflicto.
     * Retorna 'local' o 'external'.
     */
    public function decide(array $localRow, array $externalRow, string $updatedAtCol): string
    {
        $policy = SYNC_CONFLICT_POLICY;

        if ($policy === 'prefer_local') return 'local';
        if ($policy === 'prefer_external') return 'external';

        // last_write_wins
        $l = $localRow[$updatedAtCol] ?? null;
        $e = $externalRow[$updatedAtCol] ?? null;

        // Si alguno no tiene fecha, preferimos el que sí tenga.
        if ($l && !$e) return 'local';
        if (!$l && $e) return 'external';
        if (!$l && !$e) return 'external';

        return (strtotime((string)$l) >= strtotime((string)$e)) ? 'local' : 'external';
    }
}
