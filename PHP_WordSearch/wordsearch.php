<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
    <title>Caça Palavra</title>
    <?php include_once 'tabular_palavras.php'; ?>
</head>
<body>
    <?php 
        define ("DELIMITADOR" , ';');
        $msg = "";
        $erro = false;

        if (!isset($_POST["txtConjuntoPalavras"])) {
            $msg = "Nada a fazer!<br>";
            $erro = true;
        }

        $conjuntoPalavras = $_POST["txtConjuntoPalavras"];

        if ($conjuntoPalavras == "") {
            $msg = "Não foi digitada nenhuma palavra!<br>";
            $erro = true;
        }

        if (!$erro) {   
            if (substr($conjuntoPalavras, -1) == DELIMITADOR) {
                $conjuntoPalavras = substr($conjuntoPalavras, 0, -1); 
            }

            $conjuntoPalavras = explode(DELIMITADOR, $conjuntoPalavras);

            // Gerar Tabuleiro
            $tabuleiro = new Tabuleiro($conjuntoPalavras);
            // $tabuleiro->setCelulaVazia('*'); // visualizar preenchimento
            $tabuleiro->setQuebrarLinha(false);

            $resultado = $tabuleiro->gerar();
            if ($resultado == 0) {
                $msg = "Entrada inváliada!";
                $erro = true;
            } else {
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
                }
        }
            
        if ($erro) {
            echo "<h3><font color='red'>$msg</font><h3>";
        }

        // Retornar
        echo "<a href='wordsearch.html'>Tentar novamente.</a>";
    ?>
</body>
</html>