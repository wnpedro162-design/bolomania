<?php
/**
 * Endpoint interno de CEP (manual 24.3).
 * index.php?controller=cep&action=buscar&cep=26210240  -> JSON
 * O navegador só conversa com o nosso PHP; o PHP consulta o ViaCEP.
 */
class CepController
{
    public function buscar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $cep = preg_replace('/[^0-9]/', '', $_GET['cep'] ?? '');

        if (strlen($cep) !== 8) {
            http_response_code(400);
            echo json_encode(['erro' => 'CEP inválido. Informe 8 dígitos.']);
            exit;
        }

        $url = "https://viacep.com.br/ws/{$cep}/json/";

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $resposta = curl_exec($ch);
            $erroCurl = curl_error($ch);
            curl_close($ch);
        } else {
            // plano B se a extensão cURL estiver desativada no PHP
            $resposta = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 5]]));
            $erroCurl = $resposta === false ? 'falha' : '';
        }

        if ($erroCurl || $resposta === false) {
            http_response_code(502);
            echo json_encode(['erro' => 'Não foi possível consultar o serviço de CEP.']);
            exit;
        }

        $dados = json_decode($resposta, true);

        if (!is_array($dados) || isset($dados['erro'])) {
            http_response_code(404);
            echo json_encode(['erro' => 'CEP não encontrado.']);
            exit;
        }

        echo json_encode([
            'cep'        => $dados['cep'] ?? '',
            'logradouro' => $dados['logradouro'] ?? '',
            'bairro'     => $dados['bairro'] ?? '',
            'cidade'     => $dados['localidade'] ?? '',
            'estado'     => $dados['uf'] ?? '',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
