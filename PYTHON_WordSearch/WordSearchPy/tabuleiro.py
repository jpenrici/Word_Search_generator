# -*- Mode: Python3; coding: utf-8; indent-tabs-mpythoode: nil; tab-width: 4 -*-

'''
*****************************************
*****   Python - LibreOffice 6.2    *****
*****   adaptado de tabuleiro.bas   *****
*****        usa API (UNO)          *****
*****************************************
'''

from random import randint

# Constantes
CELULAVAZIA = "-"
PORCENTAGEM = 0.5
PLANILHA = "Caça-Palavras"
EOL = '\n'

# Palavra
class Palavra(object):

	def __init__(self, texto):
		self.txt = texto
		self.msg = "falhou"
		self.posX = 0
		self.posY = 0
		self.posH = 0
		self.posV = 0
		self.uso  = 0
		
# Tabuleiro
class Tabuleiro(object):

	def __init__(self):
		self.lista = []
		self.matriz = ""
		self.tamanho = 0
		self.numPalavras = 0
		self.inseridos   = 0

	# Converte posição de vetor em coordenada de matriz
	def coordenada(self, posicao):

		posY = int((posicao - 1) / self.tamanho)
		posX = posicao - self.tamanho * posY

		return [posY + 1, posX]
	
	# Converte coordenada da matriz em posição de vetor
	def posicao(self, x, y):

		return x + self.tamanho * (y - 1)

	# Validar coordenada na matriz
	def validar(self, texto, x, y, h, v):

		tamanho = self.tamanho - 1
		caracteres = len(texto) - 1

		# limites do tabuleiro
		if (x <= 0 or y <= 0):
			return False
		if (x > tamanho or y > tamanho):
			return False
		if (x + caracteres > tamanho and h == 1):
			return False
		if (y + caracteres > tamanho and v == 1):
			return False

		return True

	# Posicionar na matriz
	def dispor(self, texto, x, y, h, v):

		# copiar
		offset = self.matriz

		retorno = self.validar(texto, x, y, h, v)
		if (not retorno):
			return retorno

		for i in range(0, len(texto)):
			pos = self.posicao(x + i * h, y + i * v)
			if (offset[pos] != CELULAVAZIA and offset[pos] != texto[i]):
				return False
			offset = offset[:pos - 1] + texto[i] + offset[pos:]

		# atualizar
		self.matriz = offset

		return retorno

	# Matriz
	def ofuscar(self):

		matriz = ""
		a = "abcdefghijklmnopqrstuvwxyz"
		for c in self.matriz:
			if (c == CELULAVAZIA):
				c =  a[randint(0, len(a) - 1)]
			matriz += c 

		return matriz

	# Tabuleiro modo texto
	def tabuleiro(self, resultado=False):

		if (resultado):
			matriz = self.matriz
		else:
			matriz = self.ofuscar()
		
		resposta = ""
		for i in range(0, len(matriz)):
			if (i % self.tamanho == 0 and i != 0):
				resposta += EOL               
			resposta += matriz[i]                   

		return resposta

	# Informação sobre posições das palavras
	def resumo(self):

		resposta = []
		for i in range(0, len(self.lista)):
			s =  self.info(i)
			resposta += [s]

		return resposta

	def info(self, indice):

		p = self.lista[indice]
		s = p.txt + " (" + str(p.posX) + "," + str(p.posY) + "): " + p.msg

		return s

# Funções de Construção

# Inserir palavras no tabuleiro
def tabular(lista):

	# inicializar tabuleiro
	T = Tabuleiro()
	T.numPalavras = len(lista)

	if (T.numPalavras == 0):
		# retornar tabuleiro vazio
		return T

	for i in range(0, T.numPalavras):
		
		# excluir dados inválidos
		if (lista[i] == ""): continue   
		try:
			# somente letras
			lista[i].isalpha()
		except Exception as e:
			continue

		# maior palavra
		if (T.tamanho < len(lista[i])):
			T.tamanho = len(lista[i])

		# preparar palavra
		p = lista[i].lower()
		T.lista += [Palavra(p)]

	# aumentar tamanho com constante porcentagem
	T.tamanho = T.tamanho + int(T.tamanho * PORCENTAGEM)
	
	# aumentar tamanho se maior palavra for menor que número de palavras
	if (T.tamanho < T.numPalavras):
		T.tamanho = T.numPalavras

	# inicializar matriz
	T.matriz = (T.tamanho * T.tamanho) * CELULAVAZIA

	# inserir
	for p in T.lista:

		# posição aleatória
		x = randint(1, T.tamanho - 1)
		y = randint(1, T.tamanho - 1)

		# tentativa na diagonal para baixo
		if (not p.uso):
			if (T.dispor(p.txt, x, y, 1, 1)):
				p.posH = 1
				p.posV = 1
				p.msg = "Diagonal para baixo"
				p.uso = True

		# tentativa na horizontal para a direita
		if (not p.uso):
			if (T.dispor(p.txt, x, y, 1, 0)):
				p.posH = 1
				p.posV = 0
				p.msg = "Horizontal para direita"
				p.uso = True

		# tentativa na vertical para baixo
		if (not p.uso):
			if (T.dispor(p.txt, x, y, 0, 1)):
				p.posH = 0
				p.posV = 1
				p.msg = "Vertical para baixo"
				p.uso = True

		if (p.uso):
			p.posX = x 
			p.posY = y
			T.inseridos += 1

	# retornar tabuleiro
	return T

# Funções PyMacro LibreOffice
# Requer python3-uno

# Retorna valor da célula
def valor(linha, coluna):

	desktop = XSCRIPTCONTEXT.getDesktop()
	model = desktop.getCurrentComponent()

	texto = ""
	if (not hasattr(model, "Sheets")):
		return ""

	planilha = model.Sheets.getByName(PLANILHA)
	celula = planilha.getCellByPosition(coluna, linha)

	texto = celula.getString()

	return texto

# Altera valor da célula
def inserir(linha, coluna, texto):

	desktop = XSCRIPTCONTEXT.getDesktop()
	model = desktop.getCurrentComponent()

	if (not hasattr(model, "Sheets")):
		return False

	planilha = model.Sheets.getByName(PLANILHA)
	celula = planilha.getCellByPosition(coluna, linha)

	celula.setString(texto)

	return True

# Gerar tabuleiro e inserir na planilha
def gerar(resposta=False):
	
	# entrada de palavras
	linhaPalavras  = 2	# na planilha inicia em 0
	colunaPalavras = 0  # na planilha inicia em 0

	# saída 
	linhaTabuleiro  = 0
	colunaTabuleiro = 3

	# ler conjunto de palavras
	i = 0
	conjuntoPalavras = []
	while (i < 50):	# 50 é um limite para está formatação de planilha
		texto = valor(linhaPalavras + i, colunaPalavras)
		if (texto == ""):
			break
		conjuntoPalavras += [texto]
		i = i + 1

	# construir tabuleiro
	T = tabular(conjuntoPalavras)

	# exibir
	matriz = T.matriz
	if (not resposta):
		matriz = T.ofuscar()

	# inserir na planilha
	if (T.tamanho != 0):
		for i in range(0, len(matriz)):
			# [y, x]
			pos = T.coordenada(i)
			if (i <= T.tamanho):
				inserir(linhaTabuleiro, colunaTabuleiro, "0")
				inserir(linhaTabuleiro + i, colunaTabuleiro, str(i))    # Y
				inserir(linhaTabuleiro, colunaTabuleiro + i, str(i))    # X

			# inserir caractere
			c = matriz[i]
			inserir(linhaTabuleiro + pos[0], colunaTabuleiro + pos[1], c)

		# inserir respostas
		for i in range(0, len(T.lista)):
			if (resposta):
				inserir(linhaPalavras + i, colunaPalavras + 1, T.info(i))
			else:
				inserir(linhaPalavras + i, colunaPalavras + 1, CELULAVAZIA)

	return None

def gerarComRespostas():
	
	gerar(True)

	return None

# Teste no terminal
def teste():

	conjuntoPalavras = [
		# dados válidos
		"amor",
		"carro",
		"jabuticaba",
		"lua",
		"martelo",
		"palhaço",
		"pão",
		"peixe",
		"pera",
		"raio",
		"sapo",
		"sol",
		"vida",
		# dados inválidos
		1.0,
		-10,
		[10]
	]

	T = tabular(conjuntoPalavras)
	linha = T.tamanho * CELULAVAZIA

	print("Tabuleiro:")
	print(T.tabuleiro(True))
	print()
	print(linha)
	print("Palavras :", T.numPalavras)
	print("Inseridos:", T.inseridos)
	print(linha)

	for s in T.resumo():
		print(s)

if __name__ == '__main__':
	# terminal
	teste()