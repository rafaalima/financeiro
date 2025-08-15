<div class="grid sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="nome" value="Nome do banco" />
        <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full"
            :value="old('nome', $banco->nome ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="tipo" value="Tipo de conta" />
        <select id="tipo" name="tipo"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @php
                $tipoAtual = old('tipo', $banco->tipo ?? 'corrente');
            @endphp
            <option value="corrente" @selected($tipoAtual==='corrente')>Corrente</option>
            <option value="poupanca" @selected($tipoAtual==='poupanca')>Poupança</option>
            <option value="outro"    @selected($tipoAtual==='outro')>Outro</option>
        </select>
        <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="agencia" value="Agência" />
        <x-text-input id="agencia" name="agencia" type="text" class="mt-1 block w-full"
            :value="old('agencia', $banco->agencia ?? '')" />
        <x-input-error :messages="$errors->get('agencia')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="conta" value="Conta" />
        <x-text-input id="conta" name="conta" type="text" class="mt-1 block w-full"
            :value="old('conta', $banco->conta ?? '')" />
        <x-input-error :messages="$errors->get('conta')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="saldo_inicial" value="Saldo inicial" />
        <x-text-input id="saldo_inicial" name="saldo_inicial" type="number" step="0.01" class="mt-1 block w-full"
            :value="old('saldo_inicial', $banco->saldo_inicial ?? '0')" />
        <x-input-error :messages="$errors->get('saldo_inicial')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="observacao" value="Observações" />
        <textarea id="observacao" name="observacao" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('observacao', $banco->observacao ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('observacao')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_ativo" value="1"
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                   @checked(old('is_ativo', ($banco->is_ativo ?? true)))>
            <span class="text-sm text-gray-700">Ativo</span>
        </label>
    </div>
</div>
