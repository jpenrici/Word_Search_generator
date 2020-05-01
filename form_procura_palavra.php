<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
	<title>Caça Palavra</title>
</head>
<body>
	<form name="palavras" method="post" accept-charset="utf-8"
		action="tabuleiro.php" enctype="multipart/form-data">
		<table width="100%">
			<tr>
				<td></td>
				<td>
					<?php 
						// Exibir a mensagem de erro ou sucesso
						if (isset($_GET["mensagem"])) {
							$msg = $_GET["mensagem"];
							echo "<font color='red'>$msg</font><br>";
						} else {
							echo "<font color='blue'>Preencha as palavras separadas por vírgula.</font>";
						}
					?>					
				</td>
			</tr>
			<tr>
				<th>Conjunto<br>de<br>Palavras</th>
				<td><textarea name="txtConjuntoPalavras" cols="30" rows="4"></textarea></td>
			</tr>
			<tr>
				<th></th>
				<td>
					<input type="submit" name="btnEnviar" value="Enviar">
					<input type="reset" name="btnLimpar" value="Limpar">
				</td>				
			</tr>
		</table>		
	</form>
</body>
</html>