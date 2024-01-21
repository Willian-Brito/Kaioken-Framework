
IF ( NOT EXISTS( SELECT table_name 
                   FROM information_schema.tables 
                  WHERE table_name = 'Cidade') )
THEN

    CREATE TABLE Cidade (
        IdCidade         INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
        CodigoIBGE       VARCHAR(10)      NULL,
        Descricao        VARCHAR(100) NOT NULL,
        IdEstado         INT              NULL,
        UsuarioAlteracao VARCHAR(20)  NOT NULL,
        DataAlteracao	 DATETIME     NOT NULL,

        -- Foreign Key
        CONSTRAINT `Fk_Cidade_IdEstado` 
           FOREIGN KEY (IdEstado) 
        REFERENCES Estado (IdEstado)
    );

    -- INDEX
    CREATE INDEX Idx_Cidade_Descricao ON Cidade (Descricao);

END IF;