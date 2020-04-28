<?php
// Carregar funções
include_once 'palavra_cruzada.php';
use ManipulaPalavras as mp;

// Passar um conjunto de palavras
$conjuntoPalavras = array(
	// 09 dados válidos
	"Acerola",
	"Ameixa",
	"Amora",
	"Araçá",
	"Banana",
	"Ananás",		
	"Jabuticaba",
	"Abacate     ",
	"Noz pecan",
	"Jabuticaba",
	// 04 dados inválidas
	10.55,
	-2020,	
	array(1, 1),
	array("1", "1")
);

$palavra = new Palavra("Teste");
mp\listar($palavra->resumo());

$tabuleiro = new Tabuleiro($conjuntoPalavras);
mp\listar($tabuleiro->resumo());
mp\listar($tabuleiro->dados);

$tabuleiro->dimensao = 6;
echo "(0,1) : ".($tabuleiro->posicao(0, 1))."\n";
echo "6     :\n";
mp\listar($tabuleiro->coordenada(6));
echo "(1,2) : ".($tabuleiro->posicao(1, 2))."\n";	
echo "13    :\n";
mp\listar($tabuleiro->coordenada(13));	
?>