class Palavra(
    var texto: String = "",
    var msg: String = "falhou",
    var posX: Int = 0,
    var posY: Int = 0,
    var posH: Int = 0,
    var posV: Int = 0,
    var ativo: Boolean = false
) {
    fun resumo() : String = "$texto ($posX, $posY) : $msg"
}
