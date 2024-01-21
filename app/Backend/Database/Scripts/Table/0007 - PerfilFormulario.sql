
IF ( NOT EXISTS( SELECT table_name 
                   FROM information_schema.tables 
                  WHERE table_name = 'PerfilFormulario') )
THEN

    CREATE TABLE PerfilFormulario (
        IdPerfilFormulario  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        IdPerfil            INT NOT NULL,
        IdFormulario        INT NOT NULL,

	 	-- Foreign key
		CONSTRAINT `Fk_PerfilFormulario_IdPerfil`
		   FOREIGN KEY ( IdPerfil )
		REFERENCES Perfil( IdPerfil ),

        CONSTRAINT `Fk_PerfilFormulario_IdFormulario`
		   FOREIGN KEY ( IdFormulario )
		REFERENCES Formulario( IdFormulario )
    );

END  IF;