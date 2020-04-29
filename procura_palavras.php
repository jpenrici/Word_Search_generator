<?php
// Carregar funções gerais
include_once 'funcoes_uteis.php';
use ManipulaPalavras as mp;

define("SUCESSO", 1);
define("FALHOU", 0);
define("VAZIO", -1);

class Tabuleiro {
	private $matriz;		// tabuleiro (vetor)
	private $dimensao;		// matriz quadrada
	private $dados;			// detalhes do conjunto de palavras
	private $quantidadePalavras;
	private $palavras; 		// array de Objetos Palavra
	private $celulaVazia;

    function __construct($conjuntoPalavras) {

    	// Símbolo para célula não preenchida
    	$this->celulaVazia = "*";

    	// Namespace ManipulaPalavras
    	$conjuntoPalavras = mp\preparar($conjuntoPalavras);

    	// Dados
    	$this->dados = mp\resumo($conjuntoPalavras);
    	$this->quantidadePalavras = $this->dados['TotalPalavras'];
    	$this->dimensao = $this->dados['TamanhoMaior'] * 2;

    	// Inicializando tabuleiro
    	$celulas = $this->dimensao * $this->dimensao;    	
    	$this->matriz = array_fill(0, $celulas, VAZIO);

    	// Carregando palavras
    	$palavras = array();
    	foreach ($conjuntoPalavras as $palavra) {
    		array_push($palavras, new Palavra($palavra));
    	}
    	$this->palavras = $palavras;
    }

    function __destruct() {}

    public function gerar() {
    	$tentativas = 10;
    	foreach ($this->palavras as $palavra) {
    		for ($i=0; $i < $tentativas; $i++) {
    			$resultado = $this->inserir($palavra);
    			if ($resultado) break;
    		}
    	}
    }

	public function coordenada($posicao) {
		$y = intval($posicao / $this->dimensao);
		$x = $posicao - $this->dimensao * $y;
		$resultado = array('X' => $x, 'Y' => $y);
		return $resultado;
	}

	public function posicao($x, $y) {
		$resultado = $x + $this->dimensao * $y;
		return $resultado;
	}

	public function getDados() {
		return $this->dados;
	}

	public function getMatriz() {
		return $this->matriz;
	}

	public function getTabuleiro() {
		$resultado = "";
		for ($i=0, $j=0; $i < count($this->matriz); $i++) { 
			if (is_string($this->matriz[$i])) {
				$resultado = $resultado.$this->matriz[$i];
			} else {
				$resultado = $resultado.$this->celulaVazia;
			}

			$j++;
			if ($j == $this->dimensao) {
				$resultado = $resultado."\n";
				$j = 0;
			}
		}
		return $resultado;
	}

	public function resumo() {
		$resultado = array('palavras' => $this->quantidadePalavras);
    	foreach ($this->palavras as $palavra) {
    		array_push($resultado, $palavra->resumo());
    	}
    	return $resultado;
	}

	private function inserir($palavra) {
    	// Testar inserção
    	$x = rand(0, $this->dimensao - 1);
    	$y = rand(0, $this->dimensao - 1);  		
		$resultado = $this->inserirVertical($palavra, $x, $y);
		if ($resultado) return SUCESSO;
		$resultado = $this->inserirHorizontal($palavra, $x, $y);
		if ($resultado) return SUCESSO;
		return FALHOU;
    }

    private function inserirVertical($palavra, $x, $y) {	
    	if (!$this->posicaoValida($x, $y)) return FALHOU;
    	if (!$this->posicaoValida($x, $y + $palavra->tamanho)) return FALHOU;

    	// Offset
		for ($i = 0; $i < strlen($palavra->palavra); $i++) {
			$c = $palavra->palavra[$i];
			$p = $this->posicao($x, $y + $i);
			if ($this->matriz[$p] == $c) continue;
			if (is_string($this->matriz[$p])) return FALHOU;
		} 

		// Inserir
    	$palavra->posX = $x;
    	$palavra->posY = $y;
		for ($i = 0; $i < strlen($palavra->palavra); $i++) {
			$c = $palavra->palavra[$i];
			$p = $this->posicao($x, $y + $i);
			$this->matriz[$p] = $c;
		}

    	return SUCESSO;	
    }

    private function inserirHorizontal($palavra, $x, $y) {	
    	if (!$this->posicaoValida($x, $y)) return FALHOU;
    	if (!$this->posicaoValida($x + $palavra->tamanho, $y)) return FALHOU;

    	// Offset
		for ($i = 0; $i < strlen($palavra->palavra); $i++) {
			$c = $palavra->palavra[$i];
			$p = $this->posicao($x + $i, $y);
			if ($this->matriz[$p] == $c) continue;
			if (is_string($this->matriz[$p])) return FALHOU;
		} 

		// Inserir
    	$palavra->posX = $x;
    	$palavra->posY = $y;
		for ($i = 0; $i < strlen($palavra->palavra); $i++) {
			$c = $palavra->palavra[$i];
			$p = $this->posicao($x + $i, $y);
			$this->matriz[$p] = $c;
		}

    	return SUCESSO;	
    }    

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

	function __construct($palavra) {
		$this->palavra = $palavra;
		$this->tamanho = strlen($palavra);
		$this->posX = VAZIO;
		$this->posY = VAZIO;
	}

	public function resumo() {
		$resultado = array(
			'palavra' => $this->palavra,
			'tamanho' => $this->tamanho,
			'X' => $this->posX,
			'Y' => $this->posY
		);
		return $resultado;
	}
}
?>