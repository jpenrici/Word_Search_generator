/*
 * Word Search adaptado.
 * Treinamento em Kotlin.
*/
fun main() {
    testeSimples()
    testeLista()
}

fun testeSimples() {
    val conjuntoPalavras = "Amor;Carro;Jabuticaba;Lua;Martelo;Padaria;Peixe;Pato;Raio;Sapo;Sol;Vida;Kotlin;A;12;@A"
    val teste = Tabuleiro(conjuntoPalavras)
    println(teste.tabuleiro(true))
    println(teste.resumo())
    println()
}

fun testeLista() {
    val conjuntoPalavras = listOf("amor","carro","jabuticaba","lua","martelo","padaria","peixe","pato","raio",
        "sapo","sol","vida","kotlin","A", "34", "@#", "")
    val teste = Tabuleiro(conjuntoPalavras)
    println(teste.tabuleiro())
    println(teste.resumo())
    println(teste.tabuleiro(true))
    println()
}
