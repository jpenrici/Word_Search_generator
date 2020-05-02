<?php
// Carregar funções
include_once 'procura_palavras.php';

// Define o local para Português(Brasil)
setlocale(LC_ALL, 'pt_BR.utf8');

function construir($conjuntoPalavras) {
    $tentativas = 5;    // inserção de todas as palavras
    $porcentagem = 0;   // sem aumento

    for ($i=0; $i < $tentativas; $i++) {
        // Objeto Tabuleiro
        $tabuleiro = new Tabuleiro($conjuntoPalavras);
        $tabuleiro->setPorcentagem($porcentagem);

        // Configuração de exibição
        $tabuleiro->setCelulaVazia('*');
        $tabuleiro->setEntreCelulas(';');   // para saída CSV
        // $tabuleiro->setQuebrarLinha(false);  

        // Gerar Tabuleiro
        if (!$tabuleiro->gerar()) return FALHOU;

        // Checar sucesso do preenchimento
        $dados = $tabuleiro->resumo();
        $palavras = $dados['palavras']; 
        $inseridos = $dados['inseridos'];
        if ($inseridos == $palavras) break;
    }

    // Exibição
    echo "Tabuleiro:\n";
    echo "----------\n";
    $tabuleiro->visualizar();
    echo "----------\n";
    echo "Palavras  : $palavras\n";
    echo "Inseridas : $inseridos\n";
    if ($i > 0) {
        echo "Tentativas: $i\n";
    }
    echo "----------\n";

    for ($i=0; $i < $dados['palavras']; $i++) { 
        echo $dados[$i]['palavra']." em (";
        echo $dados[$i]['X'].",".$dados[$i]['Y'].") : ";
        echo $dados[$i]['direcao']."\n";
    }

    return SUCESSO;
}

function resultado($conjuntoPalavras) {
    print_r($conjuntoPalavras);
    $resultado = construir($conjuntoPalavras);
    echo $resultado ? "Sucesso" : "Falhou";
    echo "\n";
}

// Passar um conjunto de palavras 
$conjuntoPalavras = array("Acerola", "Ameixa", "Amora", "Lima", "Banana",
    "Abacaxi", "Jabuticaba", "Abacate", "Noz pecan", "Jabuticaba", "Uva",
    "Manga");
construir($conjuntoPalavras);
echo "\n";

// Passar um conjunto vazio
resultado(array("", "", ""));

// Passar um conjunto com letras
resultado(array("a", "a", "a"));
resultado(array("A", "a", "a"));
resultado(array("A", "a", "b"));

echo "Finalizado.\n";
?>