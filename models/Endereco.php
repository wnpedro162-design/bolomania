<?php
/**
 * Endereço em atributos simples (cep, logradouro, número, complemento,
 * bairro, cidade e UF) — usado em cliente, fornecedor, cliente_site e pedido.
 * No pedido as colunas têm o sufixo "_entrega" (ex.: logradouro_entrega).
 */
class Endereco
{
    public const CAMPOS = ['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf'];

    /** Lê os campos do formulário. $prefixo é o início do name do input (ex.: "ent_"). */
    public static function doPost(array $post, string $prefixo = ''): array
    {
        $d = [];
        foreach (self::CAMPOS as $c) {
            $d[$c] = trim((string) ($post[$prefixo . $c] ?? ''));
        }
        $d['uf'] = mb_strtoupper(mb_substr($d['uf'], 0, 2));
        $digitos = preg_replace('/\D/', '', $d['cep']);
        $d['cep'] = strlen($digitos) === 8 ? substr($digitos, 0, 5) . '-' . substr($digitos, 5) : $d['cep'];
        return $d;
    }

    /** Pega os campos de uma linha do banco (com ou sem sufixo). */
    public static function daLinha(array $linha, string $sufixo = ''): array
    {
        $d = [];
        foreach (self::CAMPOS as $c) {
            $d[$c] = (string) ($linha[$c . $sufixo] ?? '');
        }
        return $d;
    }

    public static function vazio(array $d): bool
    {
        foreach (self::CAMPOS as $c) {
            if (trim((string) ($d[$c] ?? '')) !== '') return false;
        }
        return true;
    }

    /**
     * Valida o endereço. Se $obrigatorio for falso, aceita tudo vazio.
     * Retorna [campo => mensagem].
     */
    public static function validar(array $d, bool $obrigatorio = true): array
    {
        if (!$obrigatorio && self::vazio($d)) return [];
        $erros = [];
        if (strlen(preg_replace('/\D/', '', $d['cep'])) !== 8) $erros['cep'] = 'CEP com 8 números.';
        if (mb_strlen($d['logradouro']) < 3 || mb_strlen($d['logradouro']) > 150) $erros['logradouro'] = 'Informe a rua / avenida.';
        if ($d['numero'] === '' || mb_strlen($d['numero']) > 10) $erros['numero'] = 'Informe o número (ou s/n).';
        if (mb_strlen($d['complemento']) > 60) $erros['complemento'] = 'Complemento até 60 caracteres.';
        if ($d['bairro'] === '' || mb_strlen($d['bairro']) > 80) $erros['bairro'] = 'Informe o bairro.';
        if ($d['cidade'] === '' || mb_strlen($d['cidade']) > 80) $erros['cidade'] = 'Informe a cidade.';
        if (!preg_match('/^[A-Z]{2}$/', $d['uf'])) $erros['uf'] = 'UF com 2 letras.';
        return $erros;
    }

    /** Valores para gravar: vazio vira NULL (cliente e fornecedor aceitam endereço em branco). */
    public static function paraBanco(array $d, string $sufixo = '', bool $nulos = true): array
    {
        $p = [];
        foreach (self::CAMPOS as $c) {
            $v = trim((string) ($d[$c] ?? ''));
            $p[':' . $c . $sufixo] = ($nulos && $v === '') ? null : $v;
        }
        return $p;
    }

    /** "Rua X, 10 - apto 2 - Centro, Nova Iguaçu/RJ - CEP 26210-000" */
    public static function formatar(array $linha, string $sufixo = ''): string
    {
        $d = self::daLinha($linha, $sufixo);
        if (self::vazio($d)) return '';
        $txt = $d['logradouro'];
        if ($d['numero'] !== '') $txt .= ', ' . $d['numero'];
        if ($d['complemento'] !== '') $txt .= ' - ' . $d['complemento'];
        $local = trim($d['bairro'] . ($d['bairro'] !== '' && $d['cidade'] !== '' ? ', ' : '') . $d['cidade'] . ($d['uf'] !== '' ? '/' . $d['uf'] : ''));
        if ($local !== '') $txt .= ' - ' . $local;
        if ($d['cep'] !== '') $txt .= ' - CEP ' . $d['cep'];
        return $txt;
    }
}
