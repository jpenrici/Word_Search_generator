<?php
// Carregar funções gerais
include_once 'funcoes_uteis.php';
use ManipulaPalavras as mp;

class Tabuleiro {
	public $matriz;		
	public $dimensao;	// matriz quadrada
	public $dados;
	public $quantidadePalavras;
	public $conjuntoPalavras; 

    function __construct($conjuntoPalavras) {

    	// Namespace ManipulaPalavras
    	$conjuntoPalavras = mp\preparar($conjuntoPalavras);
    	$this->dados = mp\resumo($conjuntoPalavras);

    	$palavras = array();
    	foreach ($conjuntoPalavras as $palavra) {
    		array_push($palavras, new Palavra($palavra));
    	}
    	$this->conjuntoPalavras = $palavras;
    }

    function __destruct() {}

	function coordenada($posicao) {
		$y = intval($posicao / $this->dimensao);
		$x = $posicao - $this->dimensao * $y;
		return (array('X' => $x, 'Y' => $y));
	}

	function posicao($x, $y) {
		return ($x + $this->dimensao * $y);
	}

	function resumo() {
		$resultado = array('palavras' => $this->quantidadePalavras);
    	foreach ($this->conjuntoPalavras as $palavra) {
    		array_push($resultado, $palavra->resumo());
    	}
    	return $resultado;
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
		$this->posX = -1;
		$this->posY = -1;
	}

	function resumo() {
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