<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MensajeContacto;
use Illuminate\Http\Request;

class MensajeContactoController extends Controller
{
    public function index(Request $request)
    {
        $mensajes = MensajeContacto::query()
            ->when($request->query('solo_no_leidos'), fn ($q) => $q->where('leido', false))
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->ok($mensajes);
    }

    public function marcarLeido(int $id)
    {
        $mensaje = MensajeContacto::findOrFail($id);
        $mensaje->update(['leido' => true]);

        return $this->ok(['mensaje' => $mensaje], 'Mensaje marcado como leido.');
    }

    public function destroy(int $id)
    {
        $mensaje = MensajeContacto::findOrFail($id);
        $mensaje->delete();

        return $this->ok(null, 'Mensaje eliminado.');
    }
}
