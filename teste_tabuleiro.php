<?php
// Carregar funções
include_once 'procura_palavras.php';

// Passar um conjunto de palavras
$conjuntoPalavras = array(
	"Acerola",
	"Ameixa",
	"Amora",
	"Lima",
	"Banana",
	"Abacaxi",		
	"Jabuticaba",
	"Abacate",
	"Noz pecan",
	"Jabuticaba",
	"Uva",
	"Manga"
);

// Tentativas para inserção de todas as palavras
$tentativas = 100;

for ($i=1; $i <= $tentativas; $i++) {
	// Objeto Tabuleiro
	$tabuleiro = new Tabuleiro($conjuntoPalavras);
	// $tabuleiro->setCelulaVazia('*');
	$tabuleiro->setEntreCelulas(';');	// para saída CSV	
	$tabuleiro->gerar();
	$dados = $tabuleiro->resumo();
	$palavras = $dados['palavras']; 
	$inseridos = $dados['inseridos'];
	if ($inseridos == $palavras) break;
}

echo "Tabuleiro:\n";
echo "----------\n";
$tabuleiro->visualizar();
echo "----------\n";
echo "Palavras  : $palavras\n";
echo "Inseridas : $inseridos\n";
echo "Tentativas: $i\n";
echo "----------\n";

for ($i=1; $i < $dados['palavras']; $i++) { 
	echo $dados[$i]['palavra']." em (";
	echo $dados[$i]['X'].",".$dados[$i]['Y'].") : ";
	echo $dados[$i]['direcao']."\n";
}

?>