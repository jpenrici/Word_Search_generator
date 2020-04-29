<?php
// Carregar funções gerais
include_once 'funcoes_uteis.php';
use ManipulaPalavras as mp;

define("SUCESSO", 1);
define("FALHOU", 0);
define("VAZIO", -1);
define("TENTATIVAS", 1000);
define("PORCENTAGEM", 10);

class Tabuleiro {
	private $matriz;		// tabuleiro (vetor)
	private $dimensao;		// matriz quadrada
	private $dados;			// detalhes do conjunto de palavras
	private $quantidadePalavras;
	private $palavras; 		// array de Objetos Palavra
	private $celulaVazia;
	private $entreCelulas;

    function __construct($conjuntoPalavras) {

    	// Configuração de Visualização
    	$this->celulaVazia = "";	// randomize letras
    	$this->entreCelulas = "";

    	// Namespace ManipulaPalavras
    	$conjuntoPalavras = mp\preparar($conjuntoPalavras);

    	// Dados
    	$this->dados = mp\resumo($conjuntoPalavras);
    	$this->quantidadePalavras = $this->dados['TotalPalavras'];
    	$this->dimensao = $this->dados['TamanhoMaior'];
    	$this->dimensao += intval($this->dimensao * PORCENTAGEM / 100);

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
    	foreach ($this->palavras as $palavra) {
    		for ($i=0; $i < TENTATIVAS; $i++) {
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

	public function getDados() {
		return $this->dados;
	}

	public function getMatriz() {
		return $this->matriz;
	}

	public function getTabuleiro() {
		$resultado = "";
		$letras = "abcdefghijklmnopqrstuvwxyz";
		$caracter = $this->celulaVazia;

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
			if ($j == $this->dimensao) {
				$resultado = $resultado."\n";
				$j = 0;
				continue;
			}

			$resultado = $resultado.$this->entreCelulas;
		}
		return $resultado;
	}

	public function visualizar() {
		echo $this->getTabuleiro();
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

		$resultado = $this->inserirDiagonal($palavra, $x, $y);
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
    	$palavra->direcao = "vertical para baixo";
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
    	$palavra->direcao = "horizontal para direita";
		for ($i = 0; $i < strlen($palavra->palavra); $i++) {
			$c = $palavra->palavra[$i];
			$p = $this->posicao($x + $i, $y);
			$this->matriz[$p] = $c;
		}

    	return SUCESSO;	
    }

    private function inserirDiagonal($palavra, $x, $y) {	
    	if (!$this->posicaoValida($x, $y)) return FALHOU;
    	if (!$this->posicaoValida($x + $palavra->tamanho, $y + $palavra->tamanho)) return FALHOU;

    	// Offset
		for ($i = 0; $i < strlen($palavra->palavra); $i++) {
			$c = $palavra->palavra[$i];
			$p = $this->posicao($x + $i, $y + $i);
			if ($this->matriz[$p] == $c) continue;
			if (is_string($this->matriz[$p])) return FALHOU;
		} 

		// Inserir
    	$palavra->posX = $x;
    	$palavra->posY = $y;
    	$palavra->direcao = "diagonal para baixo e para direira";
		for ($i = 0; $i < strlen($palavra->palavra); $i++) {
			$c = $palavra->palavra[$i];
			$p = $this->posicao($x + $i, $y + $i);
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
	public $direcao;

	function __construct($palavra) {
		$this->palavra = $palavra;
		$this->tamanho = strlen($palavra);
		$this->posX = VAZIO;
		$this->posY = VAZIO;
		$this->direcao = "não definido";
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