<?php
// Carregar funções
include_once 'procura_palavras.php';
use ManipulaPalavras as mp;

// Passar um conjunto de palavras
$conjuntoPalavras = array(
	"Acerola",
	"Ameixa",
	"Amora",
	"Banana",
	"Jabuticaba",
	"Abacate",
	"Noz pecan"
);

// Objeto Tabuleiro
$tabuleiro = new Tabuleiro($conjuntoPalavras);
$tabuleiro->gerar();
echo $tabuleiro->getTabuleiro();
?>