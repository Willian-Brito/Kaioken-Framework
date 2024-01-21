
DROP PROCEDURE IF EXISTS stp_InserirParametros;

CREATE PROCEDURE stp_InserirParametros()
BEGIN

  -- Definindo variáveis
  DECLARE ExisteMaisLinhas INT DEFAULT 0;
  DECLARE IdUsuarioCursor  INT DEFAULT 0;

  -- Definição do cursor
  DECLARE crParametro CURSOR FOR 
   SELECT IdUsuario 
     FROM Usuario;

  -- Definição da variável de controle de looping do cursor
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET ExisteMaisLinhas = 1;

  -- Abertura do cursor
  OPEN crParametro;

  -- Looping de execução do cursor
  CursorLoop: LOOP
  FETCH crParametro INTO IdUsuarioCursor;

  -- Controle de existir mais registros na tabela
  IF ExisteMaisLinhas = 1 THEN
    LEAVE CursorLoop;
  END IF;


  -- # Parametros do Sistema
  IF ( NOT EXISTS(SELECT Prt.IdParametro
                    FROM Parametro AS Prt
                   WHERE Prt.Descricao = 'Utiliza email obrigatorio no cadastro de usuarios'
                     AND Prt.IdUsuario = IdUsuarioCursor) )
  THEN

        INSERT INTO Parametro (IdUsuario, Descricao, Valor, IdTipoParametro, Observacao, UsuarioAlteracao, DataAlteracao)
            VALUES ( IdUsuarioCursor,
                     'Utiliza email obrigatorio no cadastro de usuarios',
                     'sim',
                     3, -- Texto
                     'Se sim, será obrigatório informar o email no cadastro de Usuários.',
                     'suporte',
                     NOW()
                    );
  END IF;


  -- Retorna para a primeira linha do loop
  END LOOP CursorLoop;

  -- Fechando do cursor
  CLOSE crParametro;

END;

CALL stp_InserirParametros();