
IF ( NOT EXISTS( SELECT table_name 
                   FROM information_schema.tables 
                  WHERE table_name = 'Menu') )
THEN

    CREATE TABLE Menu (
        IdMenu       INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
		Descricao    VARCHAR(100)  NOT NULL,
		IdMenuPai    INT               NULL,
        EhPrincipal  TINYINT           NULL,
		Sequencia    INT           NOT NULL,
		IdFormulario INT               NULL,

        -- Foreign key
        CONSTRAINT `Fk_Menu_IdMenuPai` 
		   FOREIGN KEY ( IdMenuPai )
        REFERENCES Menu( IdMenu ),

		CONSTRAINT `Fk_Menu_IdFormulario`
		   FOREIGN KEY ( IdFormulario )
		REFERENCES Formulario( IdFormulario )
    );

    -- # INDEX
    CREATE INDEX Idx_Menu_Descricao ON Menu (Descricao);

END  IF;