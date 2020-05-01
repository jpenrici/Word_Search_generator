<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
	<title>Caça Palavra</title>
	<?php include_once 'procura_palavras.php'; ?>
</head>
<body>
	<?php 
		define ("DELIMITADOR" , ';');
		
		$msg = "Digite palavras separadas por ponto e vírgula!";
		if (!isset($_POST["txtConjuntoPalavras"])) {
			header("Location:form_procura_palavra.php?mensagem=$msg");
		}

		$conjuntoPalavras = $_POST["txtConjuntoPalavras"];

		if ($conjuntoPalavras == "") {
			header("Location:form_procura_palavra.php?mensagem=$msg");
		}

		if (substr($conjuntoPalavras, -1) == DELIMITADOR) {
			$conjuntoPalavras = substr($conjuntoPalavras, 0, -1); 
		}

		$conjuntoPalavras = explode(";", $conjuntoPalavras);

		// Gerar Tabuleiro
		$tabuleiro = new Tabuleiro($conjuntoPalavras);
		$tabuleiro->setQuebrarLinha(false);
		$tabuleiro->gerar();
		$resumo = $tabuleiro->resumo();
		$dimensao = $resumo['dimensao'];
		$vetor = $resumo['matriz'];

		// Visualizar Tabuleiro
		echo "<h2>Resultado:</h2>";
		echo "<table border=2>";

		// Cabeçalho
		echo "<tr>";
		echo "<td></td>";
		for ($i=0; $i < $dimensao; $i++) { 
			echo "<td>".$i."</td>";
		}
		echo "</tr>";

		// Linhas
		for ($i=0, $x=0, $y=0; $i < strlen($vetor); $i++) {
			if ($x == 0) {
				echo "<tr>";
				echo "<td>$y</td>";
			}
			echo "<td>".$vetor[$i]."</td>";
			$x++;
			if ($x == $dimensao) {
				echo "</tr>";
				$x = 0;
				$y++;
			}
		}

		// Concluir
		echo "</table>";
	?>
</body>
</html>