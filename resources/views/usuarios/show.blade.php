@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalles del Usuario</h2>
    <p><strong>Nombre:</strong> {{ $user->nombre }}</p>
    <p><strong>Apellido:</strong> {{ $user->apellido }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Estado:</strong> {{ $user->estado }}</p>
    <p><strong>Roles:</strong>
        @foreach($user->roles as $role)
            <span class="badge bg-info">{{ $role->name }}</span>
        @endforeach
    </p>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
