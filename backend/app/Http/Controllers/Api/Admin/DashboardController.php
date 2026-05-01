<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Boda;
use App\Models\Mensaje;
use App\Models\MensajeContacto;
use App\Models\Usuario;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Estadisticas resumen para el dashboard del admin.
     */
    public function index(Request $request)
    {
        $admin = $request->user();
        $bodasPorEstado = Boda::query()
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $proximasBodas = Boda::query()
            ->where('estado', Boda::ESTADO_ACTIVA)
            ->where('fecha_boda', '>=', now())
            ->orderBy('fecha_boda')
            ->with('usuario:id,nombre,apellidos')
            ->limit(5)
            ->get();

        $ingresosEstimados = Boda::query()
            ->where('estado', Boda::ESTADO_ACTIVA)
            ->sum('presupuesto_estimado');

        $chatsNoLeidos = Mensaje::query()
            ->where('receptor_id', $admin->id)
            ->where('leido', false)
            ->count();

        return $this->ok([
            'bodas' => [
                'pendiente_reunion' => (int) ($bodasPorEstado['pendiente_reunion'] ?? 0),
                'activas'           => (int) ($bodasPorEstado['activa'] ?? 0),
                'finalizadas'       => (int) ($bodasPorEstado['finalizada'] ?? 0),
                'canceladas'        => (int) ($bodasPorEstado['cancelada'] ?? 0),
            ],
            'clientes_total'        => Usuario::where('rol', 'cliente')->count(),
            'mensajes_no_leidos'    => MensajeContacto::where('leido', false)->count(),
            'chats_no_leidos'       => $chatsNoLeidos,
            'ingresos_estimados'    => (float) $ingresosEstimados,
            'proximas_bodas'        => $proximasBodas,
        ]);
    }
}
