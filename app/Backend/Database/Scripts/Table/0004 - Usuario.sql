
IF ( NOT EXISTS( SELECT table_name 
                   FROM information_schema.tables 
                  WHERE table_name = 'Usuario') )
THEN

    CREATE TABLE Usuario (
        IdUsuario        INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
		Nome             VARCHAR(100)  NOT NULL,
		Usuario   	     VARCHAR(20)   NOT NULL,
		Senha 	         VARCHAR(200)  NOT NULL,
		Email			 VARCHAR(200)      NULL,
		EhAtivo   	     TINYINT           NULL,
		IdPerfil         INT           NOT NULL,
		FotoPerfil		 VARCHAR(5000)     NULL,
		UsuarioAlteracao VARCHAR(20)   NOT NULL,
		DataAlteracao	 DATETIME      NOT NULL,

		-- Foreign key
		CONSTRAINT `Fk_Usuario_IdPerfil`
		   FOREIGN KEY ( IdPerfil )
		REFERENCES Perfil( IdPerfil ),

		-- Unique
		CONSTRAINT `Un_Usuario_Usuario` 
            UNIQUE (Usuario)
    );

END  IF;