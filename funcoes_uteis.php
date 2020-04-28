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

	// Retirar dados inválidos
	function limpar($conjuntoPalavras) {
		$novoConjunto = array();
		foreach ($conjuntoPalavras as $entrada) {
			if (is_string($entrada))
				array_push($novoConjunto, $entrada);
		}
		return $novoConjunto;
	}

	// Tornar todas palavras minúsculas
	function converterMinusculas($conjuntoPalavras) {
		$conjuntoPalavras =  array_map('strtolower', $conjuntoPalavras);
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

	// Resumo
	function resumo($conjuntoPalavras) {
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

		$resultado = array(
			'Menor' => $conjuntoPalavras[$posMenor],
			'PosicaoMenor' => $posMenor,
			'TamanhoMenor' => strlen($conjuntoPalavras[$posMenor]),
			'Maior' => $conjuntoPalavras[$posMaior],
			'PosicaoMaior' => $posMaior,
			'TamanhoMaior' => strlen($conjuntoPalavras[$posMaior]),
			'TotalPalavras' => count($conjuntoPalavras)
		);

		return $resultado;
	}

	// Preparar conjunto de palavras
	function preparar($conjuntoPalavras) {
		$conjuntoPalavras = limpar($conjuntoPalavras);
		$conjuntoPalavras = trocarEspacos($conjuntoPalavras, "-");
		$conjuntoPalavras = retirarDuplicatas($conjuntoPalavras);
		$conjuntoPalavras = converterMinusculas($conjuntoPalavras);
		return $conjuntoPalavras;
	}
}
?>