<div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Side Nav --}}
        @include('livewire.settings.partials.nav')

        {{-- Content --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="font-bold text-slate-800">Dados da Empresa</h3>
                </div>
                <form wire:submit.prevent="save" class="p-6 space-y-5">
                    <div class="max-w-md space-y-4">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-slate-700 mb-1">Nome da Empresa</label>
                            <input type="text" id="company_name" wire:model="company_name"
                                class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 text-sm py-2.5 transition-shadow">
                            @error('company_name') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50 flex justify-end">
                        <button type="submit" class="btn-primary">Salvar Empresa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
