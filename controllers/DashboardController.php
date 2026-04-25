<?php
require_once __DIR__ . '/../models/Pasanaku.php';
require_once __DIR__ . '/../models/Participante.php';
require_once __DIR__ . '/../models/Pago.php';
require_once __DIR__ . '/../models/Entrega.php';

class DashboardController {
    public static function index(): void {
        $pasanakus = Pasanaku::activos();

        // Enrich each pasanaku with stats
        foreach ($pasanakus as &$pk) {
            $pk['participantes'] = Participante::byPasanaku($pk['id']);
            $totalParts = count($pk['participantes']);
            $pk['total_participantes'] = $totalParts;

            $rondaActual = Pasanaku::getRondaActual($pk['id']);
            $pk['ronda_actual'] = $rondaActual;

            $pagados = Pago::countPagados($pk['id'], $rondaActual);
            $pk['pagados_ronda'] = $pagados;

            // Receptor de esta ronda (by orden == rondaActual)
            $receptor = null;
            foreach ($pk['participantes'] as $p) {
                if ((int)$p['orden'] === $rondaActual && $p['activo']) {
                    $receptor = $p;
                    break;
                }
            }
            $pk['receptor'] = $receptor;
        }
        unset($pk);

        require __DIR__ . '/../views/dashboard/index.php';
    }
}
