'***************************************
'*****   BASIC - LibreOffice 6.2   *****
'*****  adaptado de tabuleiro.bas  *****
'***************************************

'**********************
'***** Estruturas *****
'**********************

' Palavra
Type Palavra
    txt As String
    msg As String
    posX As Integer
    posY As Integer
    posH As Integer
    posV As Integer
    uso As Boolean
End Type

' Tabuleiro
Type Tabuleiro
    lista() As Palavra
    matriz As String        ' vetor
    tamanho As Integer      ' matriz quadrada
    numPalavras As Integer
    maiorPalavra As Integer
    inseridos As Integer
End Type

'**********************
'***** Constantes *****
'**********************

Const CELULAVAZIA As String = "-"
Const PORCENTAGEM As Double = 0.5

'*******************
'***** Funções *****
'*******************

' Retorna valor da célula - libreOffice
Function valor(ByVal linha As Integer, _
			   ByVal coluna As Integer) As String
			   		   
	Dim planilha As Object
	Dim celula As Object
	
	' Planilha no LibreOffice
	' linha inicial  = 0
	' coluna inicial = 0	
	planilha = ThisComponent.CurrentController.ActiveSheet
	celula = planilha.getCellByPosition(coluna, linha)
	
	valor = celula.String
	
End Function

' Altera valor na célula - libreOffice
Function inserir(ByVal linha As Integer, _
			   	 ByVal coluna As Integer, _
			   	 ByVal texto As String)
			   	 
	Dim planilha As Object
	
	' Planilha no LibreOffice
	' linha inicial  = 0
	' coluna inicial = 0	
	planilha = ThisComponent.CurrentController.ActiveSheet
	planilha.getCellByPosition(coluna, linha).String = texto
				   				  
End Function	

' Converte posição de vetor em coordenada de matriz
Function coordenada(ByVal posicao As Integer, _
                    ByVal tamanho As Integer) As Variant
    
    Dim pos(0 To 1) As Integer
    
    pos(0) = Int((posicao - 1) / tamanho)   ' Y
    pos(1) = posicao - tamanho * pos(0)     ' X
    pos(0) = pos(0) + 1

    coordenada = pos
    
End Function

'Converte coordenada da matriz em posição de vetor
Function posicao(ByVal x As Integer, _
                 ByVal y As Integer, _
                 ByVal tamanho As Integer) As Integer

    posicao = x + tamanho * (y - 1)
    
End Function

' Validar coordenada na matriz
Function validar(ByVal texto As String, _
                 ByVal x As Integer, _
                 ByVal y As Integer, _
                 ByVal h As Integer, _
                 ByVal v As Integer, _
                 ByVal tamanho As Integer) As Boolean
    
    Dim retorno As Boolean
    Dim caracteres As Integer
    
    retorno = True
    caracteres = Len(texto) - 1
    
    ' limites do tabuleiro
    If (x <= 0 Or y <= 0) Then retorno = False
    If (x > tamanho Or y > tamanho) Then retorno = False
    If (x + caracteres > tamanho And h = 1) Then retorno = False
    If (y + caracteres > tamanho And v = 1) Then retorno = False
        
    validar = retorno

End Function

' Posicionar na matriz
Function dispor(ByRef offset As String, _
                ByVal texto As String, _
                ByVal x As Integer, _
                ByVal y As Integer, _
                ByVal h As Integer, _
                ByVal v As Integer, _
                ByVal tamanho As Integer) As Boolean
    
    Dim i, j, pos As Integer
    Dim retorno As Boolean
    
    retorno = validar(texto, x, y, h, v, tamanho)
    If (retorno = True) Then
        For i = 1 To Len(texto)
            pos = posicao(x + (i - 1) * h, y + (i - 1) * v, tamanho)	
            If (Mid(offset, pos, 1) <> CELULAVAZIA) And _
               (Mid(offset, pos, 1) <> Mid(texto, i, 1)) Then
                retorno = False
                i = Len(texto) + 1 ' break
            End If
            If (retorno = True) Then
                Mid(offset, pos, 1) = Mid(texto, i, 1)
            End If
        Next i
    End If
    
    dispor = retorno

End Function

' Inserir palavras no tabuleiro
Function tabular(ByVal lista As Variant) As Tabuleiro

    Dim T As Tabuleiro
    Dim P As Palavra
    Dim offset As String
    Dim i, j As Integer
    Dim x, y As Integer
    
    ' inicializar tabuleiro
    T.matriz = ""
    T.tamanho = 0
    T.inseridos = 0
    T.numPalavras = UBound(lista) - LBound(lista)
    
    ' adaptação
    Dim Tlista(T.numPalavras) As Palavra
    T.Lista = TLista
    
    ' construir
    If (T.numPalavras > 0) Then
        ' maior palavra
        For i = 0 To T.numPalavras - 1
            ' inserir palavra na T.Lista
            T.lista(i).txt = lista(i)
            T.lista(i).msg = "falhou"
            T.lista(i).posX = 0
            T.lista(i).posY = 0
            T.lista(i).posH = 0
            T.lista(i).posV = 0
            T.lista(i).uso = False
            
            j = Len(lista(i))
            If (T.tamanho < j) Then
                T.tamanho = j
                T.maiorPalavra = i
            End If

        Next i
    
        ' aumentar tamanho com constante porcentagem
        T.tamanho = T.tamanho + CInt(T.tamanho * PORCENTAGEM)
        
        ' aumentar tamanho se maior palavra for menor que numero de palavras
        If (T.tamanho < T.numPalavras) Then T.tamanho = T.numPalavras
    
        ' inicializar matriz
        If (T.tamanho > 1) Then
            For i = 1 To T.tamanho * T.tamanho
                T.matriz = T.matriz + CELULAVAZIA
            Next i
        End If

        ' inserir palavras
        Randomize
        For i = 0 To T.numPalavras - 1
            offset = T.matriz
            x = Int((T.tamanho * Rnd) + 1)
            y = Int((T.tamanho * Rnd) + 1)
        
            ' tentativa na diagonal para baixo
            If (T.lista(i).uso = False) Then
                If (dispor(offset, T.lista(i).txt, x, y, 1, 1, T.tamanho) = True) Then
                    T.lista(i).posH = 1
                    T.lista(i).posV = 1
                    T.lista(i).msg = "Diagonal para baixo"
                    T.lista(i).uso = True
                End If
            End If

            ' tentativa na horizontal para a direita
            If (T.lista(i).uso = False) Then
                If (dispor(offset, T.lista(i).txt, x, y, 1, 0, T.tamanho) = True) Then
                    T.lista(i).posH = 1
                    T.lista(i).posV = 0
                    T.lista(i).msg = "Horizontal para direita"
                    T.lista(i).uso = True
                End If
            End If

            ' tentativa na vertical para baixo
            If (T.lista(i).uso = False) Then
                If (dispor(offset, T.lista(i).txt, x, y, 0, 1, T.tamanho) = True) Then
                    T.lista(i).posH = 0
                    T.lista(i).posV = 1
                    T.lista(i).msg = "Vertical para baixo"
                    T.lista(i).uso = True
                End If
            End If
                   
            If (T.lista(i).uso = True) Then
                T.matriz = offset
                T.lista(i).posX = x
                T.lista(i).posY = y
                T.inseridos = T.inseridos + 1
            End If
            
        Next i
    End If

    tabular = T ' retornar tabuleiro

End Function

' Construir o tabuleiro
Function gerar(ByVal linha As Integer, _
               ByVal coluna As Integer) As Tabuleiro

    Dim i As Integer
    Dim T As Tabuleiro
    Dim lista() As String

    i = 0   ' contar palavras
    Do While (valor(linha + i, coluna) <> "")
        i = i + 1
    Loop
    
    ' armazenar
    ReDim lista(i)
    i = 0
    Do While (valor(linha + i, coluna) <> "")
        lista(i) = valor(linha + i, coluna)
        i = i + 1
    Loop
    
    ' gerar tabuleiro
    gerar = tabular(lista)
    
End Function

' Informacao sobre posicoes das palavras
Function resumo(ByRef T As Tabuleiro) As Variant

    Dim i As Integer
    Dim resposta() As String
    
    ReDim resposta(T.numPalavras)
    For i = 0 To T.numPalavras
        resposta(i) = T.lista(i).posX _
                      & "," & T.lista(i).posY _
                      & ": " & T.lista(i).msg
    Next i
    
    resumo = resposta

End Function

' Exibir na planilha atual
Function exibir(ByRef T As Tabuleiro, _
                ByVal linha As Integer, _
                ByVal coluna As Integer, _
                ByVal resultado As Boolean) As Boolean

    Dim i As Integer
    Dim c, a As String
    Dim pos() As Integer
    Dim retorno As Boolean
    
    Randomize
    retorno = False
    a = "abcdefghijklmnopqrstuvwxyz"
    
    If (T.tamanho <> 0) Then
        For i = 1 To Len(T.matriz)
            ' Y = p(0) e X = p(1)
            pos = coordenada(i, T.tamanho)
            If (i <= T.tamanho) Then
                inserir(linha, coluna, 0)
                inserir(linha + i, coluna, i)    ' Y
                inserir(linha, coluna + i, i)    ' X
            End If
            ' inserir caractere
            c = Mid(T.matriz, i, 1)
            If (c = CELULAVAZIA And resultado = False) Then
                c = Mid(a, Int((Len(a) * Rnd) + 1), 1)
            End If
            inserir(linha + pos(0), coluna + pos(1), c)
        Next i
        
        retorno = True
    End If
    
    exibir = retorno
    
End Function

'***********************
'***** Macro teste *****
'***********************

' Teste simples
' Planilha unica
Sub teste()

    Dim i As Integer
    Dim T As Tabuleiro
    Dim resposta() As String
    Dim exibirResposta As Boolean

    ' entrada de dados
    T = gerar(2, 0)
    ReDim resposta(T.numPalavras)
    exibirResposta = True
       
    ' saida
    If (exibir(T, 0, 3, exibirResposta) = False) Then
        MsgBox "Nao foi possivel gerar o tabuleiro!"
    Else
    	' vbCrLf = CHR$(13) & CHR$(10)
        MsgBox "Palavras: " & T.numPalavras _
        		& CHR$(13) & CHR$(10) _
                & "Inseridas: " & T.inseridos
                
        ' exibir posicao das palavras no tabuleiro
        resposta = resumo(T)
        For i = 0 To T.numPalavras - 1
            If (exibirResposta = True) Then
                inserir(2 + i, 1, resposta(i))
            Else
                inserir(2 + i, 1, CELULAVAZIA)
            End If
        Next i
    End If
    
End Sub

