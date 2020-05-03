<?php
// Carregar funções
include_once 'procura_palavras.php';
use ManipulaPalavras as mp;

// Define o local para Português(Brasil)
setlocale(LC_ALL, 'pt_BR.utf8');

// Passar um conjunto de palavras
$conjuntoPalavras = array(
	// 10 dados válidos
	"Acerola",
	"Ameixa",
	"Amora",
	"Jaca",
	"Banana",
	"Mexerica",       
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

// Objeto Palavra
$palavra = new Palavra("Teste");
mp\listar($palavra->resumo());

// Objeto Tabuleiro
$tabuleiro = new Tabuleiro($conjuntoPalavras);
mp\listar($tabuleiro->resumo());
mp\listar($tabuleiro->getDados());

echo "(0,1) : ".($tabuleiro->posicao(0, 1))."\n";
echo "10    :\n";
mp\listar($tabuleiro->coordenada(10));
echo "(5,2) : ".($tabuleiro->posicao(5, 2))."\n";   
echo "25    :\n";
mp\listar($tabuleiro->coordenada(25));

// Checar matriz
$tabuleiro->gerar();
if (empty($tabuleiro->getMatriz()))
	echo "Matriz vazia. Nada a exibir.";
else {
	mp\listar($tabuleiro->getMatriz());
	echo "\n";
	
	echo "Tabuleiro:\n";    
	echo $tabuleiro->getTabuleiro();
	echo "\n";

	echo "Respostas:\n";
	$tabuleiro->setCelulaVazia('*');
	echo $tabuleiro->getTabuleiro();
	echo "\n";
}

echo "\nFinalizado.\n";
?>