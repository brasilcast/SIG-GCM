<?php
/**
 * =========================================
 * SIG-GCM — Sistema Integrado de Gestão
 * Guarda Civil Municipal
 * =========================================
 * SISTEMA DE TESTE — Projeto acadêmico (TG/TC)
 * Desenvolvido 100% por:
 * Autor   : Rafaela Cristina Alcântara da Silva
 * GitHub  : https://github.com/rafaela13c
 * LinkedIn: https://www.linkedin.com/in/rafaela-cas1308/
 * =========================================
 * Este sistema NÃO é oficial e NÃO será
 * utilizado pela GCM. Uso exclusivo para
 * fins de estudo acadêmico.
 * =========================================
 *//**
 * ConsultasController — Módulo de Consultas Integradas
 *
 * Todas as APIs usadas são públicas, gratuitas e de fontes oficiais.
 * Os dados retornados são exclusivamente públicos e de caráter técnico/institucional.
 */
class ConsultasController extends Controller {

    public function index(): void {
        Auth::requireAuth();
        $this->view('consultas/index', [], 'main');
    }

    // ────────────────────────────────────────
    //  FIPE — Parallelum (Marcas > Modelos > Anos > Valor)
    // ────────────────────────────────────────
    public function fipeMarcas(): void {
        Auth::requireAuth();
        $tipo = $_GET['tipo'] ?? 'carros';
        $data = $this->fetchJson("https://parallelum.com.br/fipe/api/v1/$tipo/marcas");
        $this->json($data ?? ['error' => 'Erro ao consultar marcas']);
    }

    public function fipeModelos(): void {
        Auth::requireAuth();
        $tipo  = $_GET['tipo']  ?? 'carros';
        $marca = $_GET['marca'] ?? '';
        $data = $this->fetchJson("https://parallelum.com.br/fipe/api/v1/$tipo/marcas/$marca/modelos");
        $this->json($data['modelos'] ?? ['error' => 'Erro ao consultar modelos']);
    }

    public function fipeAnos(): void {
        Auth::requireAuth();
        $tipo   = $_GET['tipo']   ?? 'carros';
        $marca  = $_GET['marca']  ?? '';
        $modelo = $_GET['modelo'] ?? '';
        $data = $this->fetchJson("https://parallelum.com.br/fipe/api/v1/$tipo/marcas/$marca/modelos/$modelo/anos");
        $this->json($data ?? ['error' => 'Erro ao consultar anos']);
    }

    public function fipeValor(): void {
        Auth::requireAuth();
        $tipo   = $_GET['tipo']   ?? 'carros';
        $marca  = $_GET['marca']  ?? '';
        $modelo = $_GET['modelo'] ?? '';
        $ano    = $_GET['ano']    ?? '';
        $data = $this->fetchJson("https://parallelum.com.br/fipe/api/v1/$tipo/marcas/$marca/modelos/$modelo/anos/$ano");
        $this->json($data ?? ['error' => 'Erro ao consultar preço na FIPE']);
    }

    // ────────────────────────────────────────
    //  CEP — ViaCEP
    // ────────────────────────────────────────
    public function cep(): void {
        Auth::requireAuth();
        $cep = preg_replace('/\D/', '', $_GET['cep'] ?? '');
        if (strlen($cep) !== 8) { $this->json(['error' => 'CEP deve ter 8 dígitos']); return; }
        $data = $this->fetchJson("https://viacep.com.br/ws/$cep/json/");
        if (isset($data['erro'])) { $this->json(['error' => 'CEP não encontrado']); return; }
        $this->json($data ?? ['error' => 'Erro ao consultar CEP']);
    }

    // ────────────────────────────────────────
    //  CNPJ — BrasilAPI
    // ────────────────────────────────────────
    public function cnpj(): void {
        Auth::requireAuth();
        $cnpj = preg_replace('/\D/', '', $_GET['cnpj'] ?? '');
        if (strlen($cnpj) !== 14) { $this->json(['error' => 'CNPJ deve ter 14 dígitos']); return; }
        $data = $this->fetchJson("https://brasilapi.com.br/api/cnpj/v1/$cnpj");
        $this->json($data ?? ['error' => 'Erro ao consultar CNPJ']);
    }

    // ────────────────────────────────────────
    //  Feriados — BrasilAPI
    // ────────────────────────────────────────
    public function feriados(): void {
        Auth::requireAuth();
        $ano = intval($_GET['ano'] ?? date('Y'));
        if ($ano < 2020 || $ano > 2030) { $this->json(['error' => 'Ano inválido']); return; }
        $data = $this->fetchJson("https://brasilapi.com.br/api/feriados/v1/$ano");
        $this->json($data ?? ['error' => 'Erro ao consultar feriados']);
    }

    // ────────────────────────────────────────
    //  IBGE — Dados Fictícios do município Eldorado dos Ventos/SP
    // ────────────────────────────────────────
    public function ibge(): void {
        Auth::requireAuth();
        $this->json([
            'municipio' => [
                'id' => 3599999,
                'nome' => 'Eldorado dos Ventos',
                'microrregiao' => [
                    'id' => 35001,
                    'nome' => 'Serra da Ventania',
                    'mesorregiao' => [
                        'id' => 3501,
                        'nome' => 'Noroeste Paulista (Fictício)',
                        'UF' => [
                            'id' => 35,
                            'sigla' => 'SP',
                            'nome' => 'São Paulo',
                            'regiao' => [
                                'id' => 3,
                                'sigla' => 'SE',
                                'nome' => 'Sudeste'
                            ]
                        ]
                    ]
                ],
                'regiao-imediata' => [
                    'id' => 350001,
                    'nome' => 'Eldorado dos Ventos',
                    'regiao-intermediaria' => [
                        'id' => 3501,
                        'nome' => 'São José do Rio Preto (Fictício)',
                        'UF' => [
                            'id' => 35,
                            'sigla' => 'SP',
                            'nome' => 'São Paulo',
                            'regiao' => [
                                'id' => 3,
                                'sigla' => 'SE',
                                'nome' => 'Sudeste'
                            ]
                        ]
                    ]
                ]
            ],
            'indicadores' => [
                [
                    'resultados' => [
                        [
                            'series' => [
                                [
                                    'serie' => [
                                        '2022' => '85420'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }


    // ────────────────────────────────────────
    //  Validador CPF/CNPJ (local, sem chamada externa)
    // ────────────────────────────────────────
    public function validar(): void {
        Auth::requireAuth();
        $doc = preg_replace('/\D/', '', $_GET['doc'] ?? '');
        if (strlen($doc) === 11) {
            $this->json(['tipo' => 'CPF', 'valido' => $this->validarCpf($doc), 'formatado' => $this->formatarCpf($doc)]);
        } elseif (strlen($doc) === 14) {
            $this->json(['tipo' => 'CNPJ', 'valido' => $this->validarCnpj($doc), 'formatado' => $this->formatarCnpj($doc)]);
        } else {
            $this->json(['error' => 'Informe um CPF (11 dígitos) ou CNPJ (14 dígitos)']);
        }
    }

    // ────────────────────────────────────────
    //  Privados — helpers
    // ────────────────────────────────────────
    protected function json(mixed $data, int $code = 200): void {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function fetchJson(string $url): ?array {
        $ctx = stream_context_create(['http' => [
            'method'  => 'GET',
            'timeout' => 10,
            'header'  => "User-Agent: SIG-GCM/1.0\r\nAccept: application/json\r\n",
        ]]);
        $raw = @file_get_contents($url, false, $ctx);
        if (!$raw) return null;
        $d = json_decode($raw, true);
        return is_array($d) ? $d : null;
    }

    private function validarCpf(string $cpf): bool {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1+$/', $cpf)) return false;
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) $d += $cpf[$c] * ($t + 1 - $c);
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$t] != $d) return false;
        }
        return true;
    }

    private function validarCnpj(string $cnpj): bool {
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1+$/', $cnpj)) return false;
        $calc = function($cnpj, $n) {
            $s = 0; $p = $n - 7;
            for ($i = 0; $i < $n; $i++) {
                $s += $cnpj[$i] * $p--;
                if ($p < 2) $p = 9;
            }
            return $s % 11 < 2 ? 0 : 11 - $s % 11;
        };
        return $calc($cnpj, 12) == $cnpj[12] && $calc($cnpj, 13) == $cnpj[13];
    }

    private function formatarCpf(string $cpf): string {
        return substr($cpf,0,3).'.'.substr($cpf,3,3).'.'.substr($cpf,6,3).'-'.substr($cpf,9,2);
    }

    private function formatarCnpj(string $cnpj): string {
        return substr($cnpj,0,2).'.'.substr($cnpj,2,3).'.'.substr($cnpj,5,3).'/'.substr($cnpj,8,4).'-'.substr($cnpj,12,2);
    }
}
