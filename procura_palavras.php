<?php
// Carregar funções gerais
include_once 'funcoes_uteis.php';
use ManipulaPalavras as mp;

class Tabuleiro {
	private $matriz;		// tabuleiro
	private $dimensao;		// matriz quadrada
	private $dados;			// conjunto de palavras
	private $quantidadePalavras;
	private $palavras; 		// procuradas
	private $celulaVazia;

    function __construct($conjuntoPalavras) {

    	// Símbolo para célula não preenchida
    	$this->celulaVazia = "*";

    	// Namespace ManipulaPalavras
    	$conjuntoPalavras = mp\preparar($conjuntoPalavras);

    	// Dados
    	$this->dados = mp\resumo($conjuntoPalavras);
    	$this->quantidadePalavras = $this->dados['TotalPalavras'];
    	$this->dimensao = $this->dados['TamanhoMaior'];

    	// Inicializando tabuleiro
    	$celulas = $this->dimensao * $this->dimensao;    	
    	$this->matriz = array_fill(0, $celulas, 0);

    	// Carregando palavras
    	$palavras = array();
    	foreach ($conjuntoPalavras as $palavra) {
    		array_push($palavras, new Palavra($palavra));
    	}
    	$this->palavras = $palavras;
    }

    function __destruct() {}

    public function gerar() {
    	foreach ($this->palavras as $palavra) {
    		print_r($palavra->resumo());
    		$this->inserir($palavra);
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
				$resultado = $resultado.$matriz[$i];
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
    	// Randomizar coordenada
    	$x = rand(0, $this->dimensao);
    	$y = rand(0, $this->dimensao);
    	echo $x.",".$y."\n";
    	$posicao = $this->posicao($x, $y);
    	echo ($posicao)."\n";
    	print_r($this->coordenada($posicao));
    }
}

class Palavra {
	private $palavra;
	private $tamanho;
	private $posX;
	private $posY;

	function __construct($palavra) {
		$this->palavra = $palavra;
		$this->tamanho = strlen($palavra);
		$this->posX = -1;
		$this->posY = -1;
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