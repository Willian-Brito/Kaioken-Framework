

IF ( NOT EXISTS( SELECT table_name 
                   FROM information_schema.tables 
                  WHERE table_name = 'Estado') )
THEN

    CREATE TABLE Estado (
        IdEstado    INT          NOT NULL PRIMARY KEY,
        Sigla       CHAR(2)      NOT NULL,
        Descricao   VARCHAR(100) NOT NULL
    );

    -- # INDEX
    CREATE INDEX Idx_Estado_Descricao ON Estado (Descricao);
    CREATE INDEX Idx_Estado_Sigla ON Estado (Sigla);

END IF;
