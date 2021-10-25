<?php
namespace ManipulaPalavras {

    // Listar palavras
    function listar($conjuntoPalavras) {
        print_r($conjuntoPalavras);
    }

    // Ordenar palavras na forma natural
    function ordenar($conjuntoPalavras) {
        $resultado = $conjuntoPalavras;
        usort($resultado,"strnatcmp");
        return $resultado;
    }

    // Retirar palavras repetidas
    function retirarDuplicatas($conjuntoPalavras) {
        $conjuntoPalavras = ordenar($conjuntoPalavras);
        $resultado = array_unique($conjuntoPalavras);
        $resultado = array_values($resultado);
        return $resultado;
    }

    // Tornar todas as palavras minúsculas
    function converterMinusculas($conjuntoPalavras) {
        $conjuntoPalavras =  array_map('strtolower', $conjuntoPalavras);
        return $conjuntoPalavras;
    }

    // Tornar todas as palavras minúsculas
    function converterMaiusculas($conjuntoPalavras) {
        $conjuntoPalavras =  array_map('strtoupper', $conjuntoPalavras);
        return $conjuntoPalavras;
    }

    // Retirar espaços no final das palavras
    function retirarEspacosFinais($conjuntoPalavras) {
        $conjuntoPalavras =  array_map('rtrim', $conjuntoPalavras);
        return $conjuntoPalavras;
    }

    // Retirar espaços no início das palavras
    function retirarEspacosIniciais($conjuntoPalavras) {
        $conjuntoPalavras =  array_map('ltrim', $conjuntoPalavras);
        return $conjuntoPalavras;
    }

    // Trocar espaços das palavras por string
    function trocarEspacos($conjuntoPalavras, $texto) {
        $conjuntoPalavras = retirarEspacosFinais($conjuntoPalavras);
        $conjuntoPalavras = retirarEspacosIniciais($conjuntoPalavras);
        for ($i=0; $i < count($conjuntoPalavras); $i++) {
            $espaco = false;
            $novaPalavra = "";
            for ($j=0; $j < strlen($conjuntoPalavras[$i]); $j++) {
                if ($conjuntoPalavras[$i][$j] == ' ' and !$espaco) {
                    $novaPalavra = $novaPalavra.$texto;
                    $espaco = true;
                } 
                if ($conjuntoPalavras[$i][$j] != ' ') {
                    $novaPalavra = $novaPalavra.$conjuntoPalavras[$i][$j];
                    $espaco = false;
                }
            }
            $conjuntoPalavras[$i] = $novaPalavra;
        }
        return $conjuntoPalavras;
    }

    function embaralhar($conjuntoPalavras) {
        shuffle($conjuntoPalavras);
        return $conjuntoPalavras;
    }   

    // Retirar dados inválidos
    function retirarDadosInválidos($conjuntoPalavras) {
        $novoConjunto = array();
        foreach ($conjuntoPalavras as $entrada) {
            if (is_string($entrada)) {          // somente strings
                $entrada = rtrim($entrada);     // limpar espaços a direita
                $entrada = ltrim($entrada);     // limpar espaços a esquerda
                if ($entrada == "") continue;   // retirar vazio

                // entrada válida
                array_push($novoConjunto, $entrada);
            }
        }
        return $novoConjunto;
    }

    // Resumo
    function resumo($conjuntoPalavras) {
        $resultado = array(
            'Menor' => "-", 'PosicaoMenor' => 0, 'TamanhoMenor' => 0,
            'Maior' => "-", 'PosicaoMaior' => 0, 'TamanhoMaior' => 0,
            'TotalPalavras' => 0
        );
        if (!empty($conjuntoPalavras)) {
            $posMaior = 0;
            $posMenor = 0;
            for ($i=0; $i < count($conjuntoPalavras); $i++) { 
                $tamanho = strlen($conjuntoPalavras[$i]);
                if ($tamanho >= strlen($conjuntoPalavras[$posMaior])) {
                    $posMaior = $i;
                }
                if ($tamanho <= strlen($conjuntoPalavras[$posMenor])) {
                    $posMenor = $i;
                }
            }

            $resultado['Menor'] = $conjuntoPalavras[$posMenor];
            $resultado['PosicaoMenor'] = $posMenor;
            $resultado['TamanhoMenor'] = strlen($conjuntoPalavras[$posMenor]);
            $resultado['Maior'] = $conjuntoPalavras[$posMaior];
            $resultado['PosicaoMaior'] = $posMaior;
            $resultado['TamanhoMaior'] = strlen($conjuntoPalavras[$posMaior]);
            $resultado['TotalPalavras'] = count($conjuntoPalavras);
        }
        return $resultado;
    }

    // Preparar conjunto de palavras
    function preparar($conjuntoPalavras) {
        if (!empty($conjuntoPalavras)) {
            $conjuntoPalavras = retirarDadosInválidos($conjuntoPalavras);
            $conjuntoPalavras = trocarEspacos($conjuntoPalavras, "-");
            $conjuntoPalavras = converterMinusculas($conjuntoPalavras);
            $conjuntoPalavras = retirarDuplicatas($conjuntoPalavras);
            $conjuntoPalavras = embaralhar($conjuntoPalavras);
        }
        $resumo = resumo($conjuntoPalavras);
        $resultado = array('Resumo' => $resumo,'Conjunto' => $conjuntoPalavras);
        return $resultado;
    }
}
?>