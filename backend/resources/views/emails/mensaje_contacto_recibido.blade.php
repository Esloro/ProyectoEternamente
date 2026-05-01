@component('mail::message')
# Nuevo mensaje de contacto

Acabas de recibir un nuevo mensaje desde el formulario de la landing.

**Nombre:** {{ $mensaje->nombre }}
**Email:** {{ $mensaje->email }}
**Telefono:** {{ $mensaje->telefono ?? '(no facilitado)' }}

---

{{ $mensaje->mensaje }}

---

Recibido el {{ $mensaje->created_at->format('d/m/Y H:i') }}.

@component('mail::button', ['url' => config('app.frontend_url') . '/admin/mensajes-contacto'])
Ver en el panel
@endcomponent

Wedding Planner
@endcomponent
