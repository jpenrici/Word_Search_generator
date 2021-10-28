private const val PORCENTAGEM = 0.5F
private const val DELIMITADOR = ';'
private const val CELULA_VAZIA = "-"
private const val EOL = '\n'

class Tabuleiro {

    private var lista : MutableList<Palavra>    // palavras
    private var matriz: String                  // vetor tabuleiro
    private var tamanho: Int                    // matriz quadrada
    private var numPalavras : Int               // lista
    private var inseridos : Int                 // tabuleiro

    init {
        matriz = ""
        tamanho = 0
        numPalavras = 0
        inseridos = 0
        lista = ArrayList()
    }

    constructor(lista: String) {
        if (!gerarTabuleiro(lista.split(DELIMITADOR))) {
            println("Erro ao gerar o Tabuleiro!")
        }
    }

    constructor(lista: List<String>) {
        if (!gerarTabuleiro(lista)) {
            println("Erro ao gerar o Tabuleiro!")
        }
    }

    /* Tabuleiro */
    private fun gerarTabuleiro(lista: List<String>) : Boolean {

        // validar lista
        for (valor in lista) {
            if (valor.length == 1) {
                println("item '$valor' não é válido!")
                continue
            }
            if (!valor.all{it.isLetter()}) {
                println("item '$valor' não é válido!")
                continue
            }
            if (valor.isNotEmpty() && valor.isNotBlank()) {
                // adicionar palavra a lista principal
                this.lista.add(Palavra(texto = valor.lowercase()))
                // atualizar tamanho com maior palavra
                if (tamanho < valor.length) {
                    tamanho = valor.length
                }
            }
        }

        // atualizar numero de palavras
        numPalavras = this.lista.size
        if (numPalavras == 0) {
            // nada a fazer
            return false
        }

        // aumentar tamanho se maior palavra for menor que número de palavras
        if (tamanho < numPalavras) {
            tamanho = numPalavras
        }

        // aumentar tamanho com constante porcentagem
        tamanho += (tamanho * PORCENTAGEM).toInt()

        // inicializar matriz - string
        matriz = CELULA_VAZIA.repeat(tamanho * tamanho)

        // inserir
        for (palavra in this.lista) {
            // posição aleatória
            val x = (0 until tamanho - 1).random()
            val y = (0 until tamanho - 1).random()

            // tentativa 1
            if (!palavra.ativo) {
                if (dispor(palavra.texto, x, y, 1, 1)) {
                    palavra.msg = "Diagonal para baixo"
                    palavra.ativo = true
                    palavra.posH = 1
                    palavra.posV = 1
                }
            }

            // tentativa 2
            if (!palavra.ativo) {
                if (dispor(palavra.texto, x, y, 1, 0)) {
                    palavra.msg = "Horizontal para direita"
                    palavra.ativo = true
                    palavra.posH = 1
                    palavra.posV = 0
                }
            }

            // tentativa 3
            if (!palavra.ativo) {
                if (dispor(palavra.texto, x, y, 0, 1)) {
                    palavra.msg = "Vertical para baixo"
                    palavra.ativo = true
                    palavra.posH = 0
                    palavra.posV = 1
                }
            }

            // atualizar
            if (palavra.ativo) {
                palavra.posX = x
                palavra.posY = y
                inseridos += 1
            }
        }

        // tabuleiro ok
        return true
    }

    /* Posicionar na matriz */
    private fun dispor(texto: String, x: Int, y: Int, h: Int, v: Int) : Boolean {

        if (!validar(texto, x, y, h, v)) {
            // posição inválida
            return false
        }

        var offset = matriz
        for (i in texto.indices) {
            val pos = posicao(x + i * h, y + i * v)
            if (offset[pos] != CELULA_VAZIA[0] && offset[pos] != texto[i]) {
                // ocupada com letra de outra palavra
                return false
            }
            // organizar
            var txt = ""
            for (j in offset.indices) {
                txt += if (j == pos) {
                    texto[i]
                } else {
                    offset[j]
                }
            }
            // atualizar
            offset = txt
        }

        // atualizar
        matriz = offset

        // inserção ok
        return true
    }

    /* Validar coordenada na matriz */
    private fun validar(texto: String, x: Int, y: Int, h: Int, v: Int) : Boolean {

        // limites do tabuleiro
        if (x < 0 || y < 0) {
            return false
        }
        if (x > tamanho || y > tamanho) {
            return false
        }
        if (x + texto.length > tamanho && h == 1) {
            return false
        }
        if (y + texto.length > tamanho && v == 1) {
            return false
        }

        return true
    }

    /* Converte coordenada da matriz em posição de vetor */
    private fun posicao(x: Int, y: Int) : Int = x + tamanho * y

    /* Word Search */
    private fun ofuscar() : String {
        var matriz = ""
        val letras = "abcdefghijklmnopqrstuvwxyz"
        for (caracter in this.matriz) {
            matriz += if (caracter == CELULA_VAZIA[0])
                letras[(letras.indices).random()]
            else
                caracter
        }

        return matriz
    }

    /* Tabuleiro modo texto */
    fun tabuleiro(respota: Boolean = false) : String {

        val matriz: String = if (respota) {
            this.matriz
        } else {
            this.ofuscar()
        }

        var saida = ""
        for (i in matriz.indices) {
            if (i % tamanho == 0 && i != 0) {
                saida += EOL
            }
            saida += matriz[i]
        }

        return saida
    }

    /* Informação sobre posições das palavras */
    fun resumo() : String {

        var resposta = "Respostas:$EOL"
        for (palavra in this.lista) {
            resposta += palavra.resumo() + EOL
        }
        resposta += """Palavras: $numPalavras ${EOL}Inseridas: $inseridos $EOL"""

        return resposta
    }
}
