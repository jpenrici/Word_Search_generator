-- Gerador de Caça-palavras em Lua 5.3

-- Constantes
CELULAVAZIA = "-"
PORCENTAGEM = 0.7
EOL = '\n'

-- Estruturas
local Palavra = {
	txt  = "",		-- palavra
	posX = 0, posY = 0,
	posH = 0, posV = 0,
	uso  = false,
	msg  = "falhou"	
}
Palavra.__index = Palavra

local Tabuleiro = {
	lista   = {},	-- palavras
    matriz  = "", 	-- vetor
    tamanho =  0,	-- matriz quadrada
    numPalavras  = 0,
    inseridos    = 0	
}
Tabuleiro.__index = Tabuleiro

-- Palavra
function Palavra.new(texto)
	local self = setmetatable({}, Palavra)
	self.txt  = texto
	return self
end

function Palavra.resumo(self)
	local r = self.txt .." (" .. self.posX .. "," .. self.posY .. ") : " ..
		self.msg .. "."
	return r
end

-- Tabuleiro
function Tabuleiro.new()
	local self = setmetatable({}, Tabuleiro)
    return self
end

function Tabuleiro.resumo(self)
	local r = "Tabuleiro:" .. EOL
	r = r .. tabuleiro(self.matriz, self.tamanho, "")
	r = r .. "Palavras: " .. self.numPalavras .. EOL .. "Inseridas: " ..
		self.inseridos .. EOL
	for i, palavra in ipairs(self.lista) do
		r = r .. "[" .. i .. "] " .. palavra:resumo() .. EOL
	end
	return r
end

-- Funções Comuns

-- converte posiçao de vetor em coordenada de matriz
function coordenada(posicao, tamanho)
	local y = math.floor((posicao - 1) / tamanho)
	local x = posicao - tamanho * y
	return {x = x, y = y}
end

-- converte coordenada da matriz em posiçao de vetor
function posicao(x, y, tamanho)
	return (x + tamanho * (y -1))
end

-- validar coordenada na matriz
function validar(texto, x, y, h, v, tamanho)
	local caracteres = string.len(texto) - 1

	-- limites do Tabuleiro
    if (x <= 0 or y <= 0) then
    	return false
    end
    if (x > tamanho or y > tamanho) then
    	return false
    end
    if (x + caracteres > tamanho and h == 1) then
    	return false
    end
    if (y + caracteres > tamanho and v == 1) then
    	return false
    end

    return true
end

-- posicionar na matriz
function dispor(offset, texto, x, y, h, v, tamanho)
	local vazio = ""
	if (not validar(texto, x, y, h, v, tamanho)) then
		return vazio
	end

	for i = 1, string.len(texto) do
        local pos = posicao(x + (i - 1) * h, y + (i - 1) * v, tamanho)
        if (string.sub(offset, pos, pos) ~= CELULAVAZIA) and 
           (string.sub(offset, pos, pos) ~= string.sub(texto, i, i)) then
            return vazio
        end
        offset = string.sub(offset, 1, pos - 1) .. string.sub(texto, i, i) ..
        		string.sub(offset, pos + 1, -1)
    end

	return offset
end

-- troca caracter da matriz por letras aleatórias
function ofuscar(matriz, caracter)
	local a = "abcdefghijklmnopqrstuvwxyz"
	local resposta = ""

	math.randomseed(os.time())
	for i = 1, string.len(matriz) do
		local n = math.random(1, string.len(a))
		if (string.sub(matriz, i, i) == caracter) then
			resposta = resposta .. string.sub(a, n, n)
		else
			resposta = resposta .. string.sub(matriz, i, i)
		end
	end

	return resposta
end

-- retorna tabuleiro em string
function tabuleiro(matriz, tamanho, separador)
	local r = ""
	for i = 1, string.len(matriz) do
		r = r .. string.sub(matriz, i, i) .. separador
		if (i % tamanho == 0 and i ~= 0) then
			r = r .. EOL
		end
	end
	return r
end

-- Função Principal

-- construir o tabuleiro
function gerar(conjuntoPalavras)

	-- validar conjunto de palavras
	if next(conjuntoPalavras) == nil then
		-- nada a fazer
		return nil
	end

	-- inicializar tabuleiro
	local T = Tabuleiro:new()
	T.lista = {}
	
	-- ler conjunto de palavras
	for i, valor in ipairs(conjuntoPalavras) do
		if (valor == "") then
			valor = -1	-- anula inserção
		end
		if (type(valor) == "string") then
			-- inserir
			local P = Palavra:new()
			P.txt = valor
			table.insert(T.lista, P)

			-- atualizar resumo
			T.numPalavras = T.numPalavras + 1
			if (T.tamanho < string.len(valor)) then
				T.tamanho = string.len(valor)
			end
		end
	end

	if (T.numPalavras == 0) then
		-- tabuleiro vazio
		return T
	end

	-- inicializar matriz
	T.tamanho = math.floor(T.tamanho + T.tamanho * PORCENTAGEM)
	if (T.tamanho < T.numPalavras) then
		T.tamanho = T.numPalavras
	end
	T.matriz = string.rep(CELULAVAZIA, T.tamanho * T.tamanho)

	-- inserir palavras
	math.randomseed(os.time())
	for i = 1, T.numPalavras do
		-- palavra
		a = T.lista[i]

		-- sortear		
		x = math.random(1, T.tamanho)
		y = math.random(1, T.tamanho)
	
		-- tentativa na diagonal para baixo
		if (not a.uso) then
			offset = dispor(T.matriz, a.txt, x, y, 1, 1, T.tamanho)
			if (offset ~= "") then
				a.posX = x
				a.posY = y
				a.posH = 1
				a.posV = 1
				a.msg = "Diagonal para baixo"
				a.uso = true
				T.matriz = offset
				T.inseridos = T.inseridos + 1
			end
		end

		-- tentativa na horizontal para direita
		if (not a.uso) then
			offset = dispor(T.matriz, a.txt, x, y, 1, 0, T.tamanho)
			if (offset ~= "") then
				a.posX = x
				a.posY = y				
				a.posH = 1
				a.posV = 0
				a.msg = "Horizontal para direita"
				a.uso = true
				T.matriz = offset
				T.inseridos = T.inseridos + 1
			end
		end

		-- tentativa na vertical para baixo
		if (not a.uso) then
			offset = dispor(T.matriz, a.txt, x, y, 0, 1, T.tamanho)
			if (offset ~= "") then
				a.posX = x
				a.posY = y				
				a.posH = 0
				a.posV = 1
				a.msg = "Vertical para baixo"
				a.uso = true
				T.matriz = offset
				T.inseridos = T.inseridos + 1
			end
		end
	end

	-- tabuleiro construído
	return T
end

-----------------------------------
--            Teste              --
-----------------------------------
local conjuntoPalavras = {
	-- entradas válidas
	"lua",
	"linux",
	"debian",
	"openbox",
	"github",
	-- entradas inválidas
	"",
	100,
	4.5,
	{"lua", 1},
}

local T = gerar(conjuntoPalavras)
print("Caça-palavras:")
print(tabuleiro(ofuscar(T.matriz, CELULAVAZIA), T.tamanho, ","))
print(T:resumo())