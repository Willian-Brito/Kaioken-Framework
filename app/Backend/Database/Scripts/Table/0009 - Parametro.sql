
IF ( NOT EXISTS( SELECT table_name 
                   FROM information_schema.tables 
                  WHERE table_name = 'Parametro') )
THEN

    CREATE TABLE Parametro (
        
        IdParametro       INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
        IdUsuario         INT           NOT NULL,
		Descricao         VARCHAR(200)  NOT NULL,
        Valor             VARCHAR(50)   NOT NULL,
        IdTipoParametro   INT           NOT NULL,
        Observacao        VARCHAR(1000) NOT NULL,
        UsuarioAlteracao  VARCHAR(20)   NOT NULL,
        DataAlteracao	  DATETIME      NOT NULL,

        -- Foreign key
		CONSTRAINT `Fk_Parametro_IdTipoParametro`
		   FOREIGN KEY ( IdTipoParametro )
		REFERENCES TipoParametro( IdTipoParametro ),

        CONSTRAINT `Fk_Parametro_IdUsuario`
		   FOREIGN KEY ( IdUsuario )
		REFERENCES Usuario( IdUsuario )
    );

    -- # INDEX
    CREATE INDEX Idx_Parametro_Descricao ON Parametro (Descricao);
    CREATE INDEX Idx_Parametro_Valor ON Parametro (Valor);

END IF;