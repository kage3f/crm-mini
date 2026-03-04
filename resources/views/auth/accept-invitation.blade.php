@extends('layouts.guest')

@section('content')
<div>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-slate-900">Aceitar Convite</h2>
        <p class="text-slate-500 mt-1">Você foi convidado para participar de uma equipe.</p>
    </div>

    <form method="POST" action="{{ route('invitations.store', $invitation->token) }}" class="space-y-4">
        @csrf
        <div>
            <label class="label">Seu Email (não editável)</label>
            <input type="text" class="input bg-slate-50 cursor-not-allowed" value="{{ $invitation->email }}" disabled>
        </div>

        <div>
            <label class="label" for="name">Seu Nome Completo</label>
            <input name="name" id="name" type="text" class="input @error('name') input-error @enderror" placeholder="Ex: Maria Silva" required autofocus>
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label" for="password">Definir Senha</label>
            <input name="password" id="password" type="password" class="input @error('password') input-error @enderror" placeholder="Mínimo 8 caracteres" required>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label" for="password_confirmation">Confirmar Senha</label>
            <input name="password_confirmation" id="password_confirmation" type="password" class="input" placeholder="Repita a senha" required>
        </div>

        <button type="submit" class="btn-primary w-full justify-center mt-2">
            Aceitar convite e entrar
        </button>
    </form>
</div>
@endsection