@php
    $t = $transacao ?? null;
@endphp

<div class="grid sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="descricao" value="Descrição" />
        <x-text-input id="descricao" name="descricao" type="text" class="mt-1 block w-full"
            :value="old('descricao', $t->descricao ?? '')" required />
        <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="data" value="Data" />
        <x-text-input id="data" name="data" type="date" class="mt-1 block w-full"
            :value="old('data', isset($t)? $t->data->format('Y-m-d') : now()->format('Y-m-d'))" required />
        <x-input-error :messages="$errors->get('data')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="valor" value="Valor" />
        <x-text-input id="valor" name="valor" type="number" step="0.01" class="mt-1 block w-full"
            :value="old('valor', $t->valor ?? '')" required />
        <x-input-error :messages="$errors->get('valor')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="categoria_id" value="Categoria" />
        <select id="categoria_id" name="categoria_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Selecione…</option>
            @foreach($categorias as $c)
                <option value="{{ $c->id }}" @selected(old('categoria_id', $t->categoria_id ?? null) == $c->id)>
                    {{ $c->nome }} ({{ $c->tipo }})
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="banco_id" value="Banco" />
        <select id="banco_id" name="banco_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">—</option>
            @foreach($bancos as $b)
                <option value="{{ $b->id }}" @selected(old('banco_id', $t->banco_id ?? null) == $b->id)>{{ $b->nome }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('banco_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="fornecedor_id" value="Fornecedor" />
        <select id="fornecedor_id" name="fornecedor_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">—</option>
            @foreach($fornecedores as $f)
                <option value="{{ $f->id }}" @selected(old('fornecedor_id', $t->fornecedor_id ?? null) == $f->id)>{{ $f->nome }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('fornecedor_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" value="Status" />
        <select id="status" name="status"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            @php $st = old('status', $t->status ?? 'pendente'); @endphp
            <option value="pendente" @selected($st==='pendente')>Pendente</option>
            <option value="pago"     @selected($st==='pago')>Pago</option>
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="observacao" value="Observações" />
        <textarea id="observacao" name="observacao" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('observacao', $t->observacao ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('observacao')" class="mt-2" />
    </div>
</div>

{{-- Parcelamento (apenas no CREATE) --}}
@isset($modoParcelas)
<div x-data="{ on: false }" class="mt-6 border-t pt-4">
    <label class="inline-flex items-center gap-3">
        <input type="checkbox" name="parcelar" value="1" x-model="on"
               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="text-sm text-gray-700">Parcelar</span>
    </label>

    <div x-show="on" class="mt-3">
        <x-input-label for="parcelas" value="Número de parcelas" />
        <x-text-input id="parcelas" name="parcelas" type="number" min="1" max="240" class="mt-1 w-40"
            :value="old('parcelas', 2)" />
        <p class="mt-1 text-xs text-gray-500">A 1ª parcela usa a data informada; as demais serão mês a mês.</p>
    </div>
</div>
@endisset
