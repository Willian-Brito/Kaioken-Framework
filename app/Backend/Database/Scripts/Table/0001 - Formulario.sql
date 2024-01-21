
IF ( NOT EXISTS( SELECT table_name 
                   FROM information_schema.tables 
                  WHERE table_name = 'Formulario') )
THEN

    CREATE TABLE Formulario (
        IdFormulario        INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
		DescricaoLista      VARCHAR(100)      NULL,
		ArquivoLista       	VARCHAR(100)      NULL,
		DescricaoFormulario	VARCHAR(100)      NULL,
		ArquivoFormulario  	VARCHAR(100)      NULL,
        Classe              VARCHAR(100)      NULL
    );

    -- # INDEX
    CREATE INDEX Idx_Formulario_ArquivoLista ON Formulario (ArquivoLista);
    CREATE INDEX Idx_Formulario_ArquivoFormulario ON Formulario (ArquivoFormulario);

    -- # Mostrar todos INDEX tabela 
    -- SHOW INDEX FROM Formulario;

    -- # Explica sobre a execução no comando
    -- EXPLAIN SELECT * FROM Formulario WHERE ArquivoLista = 'CidadeList';

    -- # Deletar INDEX
    -- DROP INDEX Idx_Formulario_ArquivoLista ON Formulario;

END  IF;