
IF ( NOT EXISTS( SELECT table_name 
                   FROM information_schema.tables 
                  WHERE table_name = 'TipoParametro') )
THEN

    CREATE TABLE TipoParametro (
        
        IdTipoParametro       INT    NOT NULL AUTO_INCREMENT PRIMARY KEY,
		Descricao        VARCHAR(50) NOT NULL,

        -- Unique
        CONSTRAINT `Un_TipoParametro_Descricao` 
            UNIQUE (Descricao)
    );
    
    INSERT INTO TipoParametro (IdTipoParametro, Descricao) VALUES (1, 'Inteiro');
	INSERT INTO TipoParametro (IdTipoParametro, Descricao) VALUES (2, 'Decimal');
    INSERT INTO TipoParametro (IdTipoParametro, Descricao) VALUES (3, 'Texto');
    INSERT INTO TipoParametro (IdTipoParametro, Descricao) VALUES (4, 'Data');

END IF;