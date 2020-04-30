<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
	<title>Caça Palavra</title>
	<?php include_once 'procura_palavras.php'; ?>
</head>
<body>
	<?php 
		if (isset($_POST["txtConjuntoPalavras"])) {

			$conjuntoPalavras = $_POST["txtConjuntoPalavras"];
			$dimensao = 10;		// teste

			$tabuleiro = new Tabuleiro($conjuntoPalavras);

			echo "<h2>Resultado:</h2>";
			echo "<table border=1>";
			echo "<tr>";
			for ($i=0; $i < $dimensao; $i++) { 
				echo "<td>".$i."</td>";
			}
			echo "</tr>";
			for ($i=0; $i < $dimensao; $i++) {
				echo "<tr>";
				for ($j=0; $j < $dimensao; $j++) { 
					echo "<td>".$i."</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
		}
	?>
</body>
</html>