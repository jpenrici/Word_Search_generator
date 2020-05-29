# -*- Mode: Python3; coding: utf-8; indent-tabs-mpythoode: nil; tab-width: 4 -*-

'''
*****************************************
*****   Python - LibreOffice 6.2    *****
*****   adaptado de tabuleiro.bas   *****
*****************************************
'''

from random import randint

# Constantes
CELULAVAZIA = "-"
PORCENTAGEM = 0.5

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

		posY = int(posicao - 1) / self.tamanho
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
	def dispor(self, offset, texto, x, y, h, v):

		retorno = self.validar(texto, x, y, h, v)
		if (not retorno):
			return retorno
		for i in range(0, len(texto)):
			pos = self.posicao(x + i * h, y + i * v)
			if (offset[pos] != CELULAVAZIA and offset[pos] != texto[i]):
				return False
			offset = offset[:pos - 1] + texto[i] + offset[pos:]

		# armazenar
		self.matriz = offset

		return retorno

	# Tabuleiro modo texto
	def tabuleiro_txt(self):

		resposta = ""
		for i in range(0, len(self.matriz)):
			if (i % self.tamanho == 0 and i != 0):
				resposta += "\n"				
			resposta += self.matriz[i]					

		return resposta

	# Informacao sobre posicoes das palavras
	def resumo(self):

		resposta = []
		for p in self.lista:
			s = p.txt + " (" + str(p.posX) + "," + str(p.posY) + "): " + p.msg
			resposta += [s]

		return resposta
	
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
		p = lista[i].upper()
		T.lista += [Palavra(p)]

	# aumentar tamanho com constante porcentagem
	T.tamanho = T.tamanho + int(T.tamanho * PORCENTAGEM)
	
	# aumentar tamanho se maior palavra for menor que numero de palavras
	if (T.tamanho < T.numPalavras):
		T.tamanho = T.numPalavras

	# inicializar matriz
	T.matriz = (T.tamanho * T.tamanho) * CELULAVAZIA

	# inserir
	for p in T.lista:
		offset = T.matriz

		# posição aleatória
		x = randint(1, T.tamanho - 1)
		y = randint(1, T.tamanho - 1)

		# tentativa na diagonal para baixo
		if (not p.uso):
			if (T.dispor(offset, p.txt, x, y, 1, 1)):
				p.posH = 1
				p.posV = 1
				p.msg = "Diagonal para baixo"
				p.uso = True

		# tentativa na horizontal para a direita
		if (not p.uso):
			if (T.dispor(offset, p.txt, x, y, 1, 0)):
				p.posH = 1
				p.posV = 0
				p.msg = "Horizontal para direita"
				p.uso = True

		# tentativa na vertical para baixo
		if (not p.uso):
			if (T.dispor(offset, p.txt, x, y, 0, 1)):
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

	print("Tabuleiro:")
	print(T.tabuleiro_txt())
	print(T.tamanho * CELULAVAZIA)
	print("Palavras:", T.numPalavras)
	print("Inseridos:", T.inseridos)
	print(T.tamanho * CELULAVAZIA)

	for s in T.resumo():
		print(s)

if __name__ == '__main__':
	teste()