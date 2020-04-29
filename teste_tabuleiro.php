<?php
// Carregar funções
include_once 'procura_palavras.php';

// Passar um conjunto de palavras
$conjuntoPalavras = array(
	"Acerola",
	"Ameixa",
	"Amora",
	"Banana",
	"Jabuticaba",
	"Abacate",
	"Noz pecan",
);

// Objeto Tabuleiro
$tabuleiro = new Tabuleiro($conjuntoPalavras);
$tabuleiro->gerar();
// $tabuleiro->setCelulaVazia('*');
$tabuleiro->setEntreCelulas(' ');

echo "Tabuleiro:\n";
$tabuleiro->visualizar();

$dados = $tabuleiro->resumo();
echo "Palavras: ".$dados['palavras']."\n";
for ($i=1; $i < count($dados) - 1; $i++) { 
	echo $dados[$i]['palavra']." em (";
	echo $dados[$i]['X'].",".$dados[$i]['Y'].") na ";
	echo $dados[$i]['direcao']."\n";
}
?>