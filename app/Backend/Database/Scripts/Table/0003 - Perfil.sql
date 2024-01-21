
IF ( NOT EXISTS( SELECT table_name 
                   FROM information_schema.tables 
                  WHERE table_name = 'Perfil') )
THEN

    CREATE TABLE Perfil (
        IdPerfil         INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
		Descricao        VARCHAR(100)  NOT NULL,
		UsuarioAlteracao VARCHAR(20)   NOT NULL,
		DataAlteracao    DATETIME      NOT NULL,

        -- Unique
        CONSTRAINT `Un_Perfil_Descricao` 
            UNIQUE (Descricao)
    );

END  IF;