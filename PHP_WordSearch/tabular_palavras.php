<?php
// Carregar funções gerais
include_once 'funcoes_uteis.php';
use ManipulaPalavras as mp;

define("SUCESSO", 1);
define("FALHOU", 0);
define("VAZIO", -1);
define("TENTATIVAS",  50);  // inserção de palavra
define("PORCENTAGEM", 25);  // acrescimo na dimensão da matriz
define("INDEFINIDO", "não definido");

class Tabuleiro {

    // Tabuleiro
    private $matriz;        // vetor 
    private $dimensao;      // matriz quadrada
    private $dados;         // detalhes do conjunto de palavras
    private $palavras;      // array de Objetos Palavra
    private $quantidadePalavras;

    // Configuração de preenchimento
    private $porcentagem;       // aumento da dimensao

    // Configuração para Função getTabuleiro
    private $celulaVazia;       // marcador de célula vazia
    private $entreCelulas;      // caracter extra
    private $quebrarLinha;

    function __construct($conjuntoPalavras) {

        // Configuração de Visualização
        $this->celulaVazia = "";    // randomize letras
        $this->entreCelulas = "";
        $this->incluirIndices = false;
        $this->quebrarLinha = true;

        // Configuração para Dimensão
        $this->porcentagem = PORCENTAGEM;

        // Namespace ManipulaPalavras
        $conjuntoPalavras = mp\preparar($conjuntoPalavras);

        // Dados
        $this->dados = $conjuntoPalavras['Resumo'];
        $this->quantidadePalavras = $this->dados['TotalPalavras'];
        $this->dimensao = $this->dados['TamanhoMaior'];

        if ($this->dimensao == 1) { // conjunto de letras
            $this->dimensao *= $this->quantidadePalavras;
        }

        // Carregando conjunto de palavras
        $palavras = array();
        foreach ($conjuntoPalavras['Conjunto'] as $palavra) {
            array_push($palavras, new Palavra($palavra));
        }
        $this->palavras = $palavras;
    }

    function __destruct() {}

    // Inserir as palavras na matriz de forma aleatória
    public function gerar() {
        if (empty($this->palavras)) // conjunto palavras vazio
            return FALHOU;

        // Dimensionar tabuleiro
        $aumento = $this->dimensao * $this->porcentagem / 100;
        $this->dimensao += intval($aumento);    

        // Inicializando tabuleiro (matriz quadrada)
        $celulas = $this->dimensao * $this->dimensao;
        $this->matriz = array_fill(0, $celulas, VAZIO);

        // Preencher
        foreach ($this->palavras as $palavra) {
            for ($i=0; $i < TENTATIVAS; $i++) {
                $resultado = $this->inserir($palavra);
                if ($resultado) break;
            }
        }

        return SUCESSO;
    }

    // Converte posição de vetor em coordenada de matriz
    public function coordenada($posicao) {
        $y = intval($posicao / $this->dimensao);
        $x = $posicao - $this->dimensao * $y;
        $resultado = array('X' => $x, 'Y' => $y);
        return $resultado;
    }

    // Converte coordenada da matriz em posição de vetor
    public function posicao($x, $y) {
        $resultado = $x + $this->dimensao * $y;
        return $resultado;
    }

    public function setCelulaVazia($caracter) {
        if (is_string($caracter)) {
            $this->celulaVazia = $caracter;
            $this->randomizeCaracter = FALHOU;
        }
    }

    public function setEntreCelulas($caracter) {
        if (is_string($caracter)) 
            $this->entreCelulas = $caracter;
    }

    public function setQuebrarLinha($status) {
        $this->quebrarLinha = $status;          // True : usar \n
    }

    public function setPorcentagem($porcentagem) {
        if (!is_int($porcentagem) or            // preferencialmente INT
            $porcentagem < PORCENTAGEM or       // valor mínimo
            $porcentagem > 300)                 // três vezes maior
            $this->porcentagem = PORCENTAGEM;   // valor padrão
        else
            $this->porcentagem = $porcentagem;
    }   

    // Retorna os detalhes do conjunto de palavras
    public function getDados() {
        return $this->dados;
    }

    // Retorna a matriz (vetor)
    public function getMatriz() {
        return $this->matriz;
    }

    /* 
     * Retorna a matriz (tabuleiro) com as palavras
     * Preenche os espaços vazios (células vazias)
     * Insere caracteres extras entre as células
     *
     */
    public function getTabuleiro() {
        $resultado = "";
        $letras = "abcdefghijklmnopqrstuvwxyz";
        $caracter = $this->celulaVazia;

        if (is_array($this->matriz)) {
            for ($i=0, $j=0; $i < count($this->matriz); $i++) { 
                if (is_string($this->matriz[$i])) {
                    $resultado = $resultado.$this->matriz[$i];
                } else {
                    if ($this->celulaVazia == "") {
                        $caracter = $letras[rand(0, strlen($letras) - 1)];
                    }
                    $resultado = $resultado.$caracter;
                }

                $j++;
                if ($j == $this->dimensao and $this->quebrarLinha) {
                    $resultado = $resultado."\n";
                    $j = 0;
                    continue;
                }

                $resultado = $resultado.$this->entreCelulas;
            }
        }
        return $resultado;
    }

    // Visualiza a matriz em formato de texto
    public function visualizar() {
        echo $this->getTabuleiro();
    }

    // Retorna resumo da matriz (tabuleiro)
    public function resumo() {
        $resultado = array(
            'matriz' => $this->getTabuleiro(),
            'dimensao' => $this->dimensao, 
            'palavras' => $this->quantidadePalavras,
            'inseridos' => 0,
            'falhas' => 0);
        $inseridos = 0;
        foreach ($this->palavras as $palavra) {
            array_push($resultado, $palavra->resumo());
            if ($palavra->direcao != INDEFINIDO) {
                $inseridos++;
            }
        }
        $resultado['inseridos'] = $inseridos;
        $resultado['falhas'] = $resultado['palavras'] - $inseridos;
        return $resultado;
    }

    private function inserir($palavra) {
        // Testar inserção
        $x = rand(0, $this->dimensao - 1);
        $y = rand(0, $this->dimensao - 1); 

        $resultado = $this->inserirDiagonal($palavra, $x, $y);
        if ($resultado) return SUCESSO;  

        $resultado = $this->inserirVertical($palavra, $x, $y);
        if ($resultado) return SUCESSO;  

        $resultado = $this->inserirHorizontal($palavra, $x, $y);
        if ($resultado) return SUCESSO;  

        return FALHOU;
    }

    /*
     * $palavras   : objeto Palavra
     * $x,$y       : coordenada na matriz
     * $horizontal : salto na horizontal
     * $vertical   : salto na vertical
     * $texto      : mensagem de direção
     */
    private function dispor($palavra, $x, $y, $horizontal, $vertical, $texto) {
        $limiteV = $palavra->tamanho * $vertical;
        $limiteH = $palavra->tamanho * $horizontal; 
        if (!$this->posicaoValida($x, $y)) return FALHOU;
        if (!$this->posicaoValida($x + $limiteH, $y + $limiteV)) return FALHOU;

        // Offset
        $offset = $this->matriz;
        for ($i = 0; $i < strlen($palavra->palavra); $i++) {
            // caracter e posição teste
            $c = $palavra->palavra[$i];
            $p = $this->posicao($x + $i * $horizontal, $y + $i * $vertical);

            // interseção entre caracteres iguais
            if ($offset[$p] == $c) continue;

            // preenchimento com outro caracter, outra palavra
            if (is_string($offset[$p])) return FALHOU;

            // teste ok - incluir no molde
            $offset[$p] = $c;
        }

        // Inserir
        $palavra->posX = $x;
        $palavra->posY = $y;
        $palavra->direcao = $texto;
        $this->matriz = $offset;

        return SUCESSO; 
    }      

    // Função dispor($palavra, $x, $y, $horizontal, $vertical, $texto)
    private function inserirVertical($palavra, $x, $y) {
        $direcao = "vertical para baixo";
        $res = $this->dispor($palavra, $x, $y, 0, 1, $direcao);
        return $res;
    }

    private function inserirHorizontal($palavra, $x, $y) {  
        $direcao = "horizontal para direira";
        $res = $this->dispor($palavra, $x, $y, 1, 0, $direcao);
        return $res;    
    }

    private function inserirDiagonal($palavra, $x, $y) {
        $direcao = "diagonal para baixo e para direira";
        $res = $this->dispor($palavra, $x, $y, 1, 1, $direcao);
        return $res;
    }

    // Verifica se coordenada está dentro dos limites da matriz
    private function posicaoValida($x, $y) {
        if ($x < 0 or $x > $this->dimensao) return FALHOU;
        if ($y < 0 or $y > $this->dimensao) return FALHOU;
        return SUCESSO;
    }
}

class Palavra {
    public $palavra;
    public $tamanho;
    public $posX;
    public $posY;
    public $direcao;

    function __construct($palavra) {
        $this->palavra = $palavra;
        $this->tamanho = strlen($palavra);
        $this->posX = VAZIO;
        $this->posY = VAZIO;
        $this->direcao = INDEFINIDO;
    }

    public function resumo() {
        $resultado = array(
            'palavra' => $this->palavra,
            'tamanho' => $this->tamanho,
            'X' => $this->posX,
            'Y' => $this->posY,
            'direcao' => $this->direcao
        );
        return $resultado;
    }
}
?>