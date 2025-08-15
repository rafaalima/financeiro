<div class="grid sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="nome" value="Nome" />
        <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full"
            :value="old('nome', $fornecedor->nome ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="documento" value="CNPJ/CPF" />
        <x-text-input id="documento" name="documento" type="text" class="mt-1 block w-full"
            :value="old('documento', $fornecedor->documento ?? '')" />
        <x-input-error :messages="$errors->get('documento')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" value="E-mail" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
            :value="old('email', $fornecedor->email ?? '')" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="telefone" value="Telefone" />
        <x-text-input id="telefone" name="telefone" type="text" class="mt-1 block w-full"
            :value="old('telefone', $fornecedor->telefone ?? '')" />
        <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="endereco" value="Endereço" />
        <x-text-input id="endereco" name="endereco" type="text" class="mt-1 block w-full"
            :value="old('endereco', $fornecedor->endereco ?? '')" />
        <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="cidade" value="Cidade" />
        <x-text-input id="cidade" name="cidade" type="text" class="mt-1 block w-full"
            :value="old('cidade', $fornecedor->cidade ?? '')" />
        <x-input-error :messages="$errors->get('cidade')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="uf" value="UF" />
        <x-text-input id="uf" name="uf" type="text" maxlength="2" class="mt-1 block w-full uppercase"
            :value="old('uf', $fornecedor->uf ?? '')" />
        <x-input-error :messages="$errors->get('uf')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="observacao" value="Observações" />
        <textarea id="observacao" name="observacao" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('observacao', $fornecedor->observacao ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('observacao')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_ativo" value="1"
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                @checked(old('is_ativo', ($fornecedor->is_ativo ?? true)))>
            <span class="text-sm text-gray-700">Ativo</span>
        </label>
    </div>
</div>
