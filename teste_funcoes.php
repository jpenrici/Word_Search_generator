<?php
// Carregar funções
include_once 'funcoes_uteis.php';
use ManipulaPalavras as mp;

// Passar um conjunto de palavras
$conjuntoPalavras = array(
	"Acerola",
	"Ameixa",
	"Amora",
	"Araçá",
	"Banana",
	"Ananás",		
	"Jabuticaba",
	"Abacate     ",
	"Noz pecan",
	"Jabuticaba"
);

// Listar conjunto de palavras
mp\listar($conjuntoPalavras);

// Ordenar
mp\listar(mp\ordenar($conjuntoPalavras));

// Resumo conjunto original
mp\listar(mp\resumo($conjuntoPalavras));

// Retirar duplicatas
mp\listar(mp\retirarDuplicatas($conjuntoPalavras));

// Resumo conjunto sem duplicatas
mp\listar(mp\resumo(mp\retirarDuplicatas($conjuntoPalavras)));

// Converter para minúsculas
mp\listar(mp\converterMinusculas($conjuntoPalavras));

// Retirar espaços no final das palavras
echo "Comparativo:\n";
echo "conjuntoPalavras[7]: '".$conjuntoPalavras[7]."'\n";
echo "conjuntoPalavras[6]: '".$conjuntoPalavras[6]."'\n";
echo 'listar(resumo($conjuntoPalavras));'."\n";
mp\listar(mp\resumo($conjuntoPalavras));
echo 'listar(resumo(retirarEspacosFinais($conjuntoPalavras)));'."\n";
mp\listar(mp\resumo(mp\retirarEspacosFinais($conjuntoPalavras)));

// Trocar espaços internos por '-'
array_push($conjuntoPalavras, " Limão ");
mp\listar(mp\trocarEspacos($conjuntoPalavras, "-"));

// Preparar conjunto de palavras
mp\listar(mp\preparar($conjuntoPalavras));

// Inserir dados inválidos
array_push($conjuntoPalavras, array(10, 10));
array_push($conjuntoPalavras, 10.45);
array_push($conjuntoPalavras, -10);
mp\listar($conjuntoPalavras);
mp\listar(mp\preparar($conjuntoPalavras));
?>