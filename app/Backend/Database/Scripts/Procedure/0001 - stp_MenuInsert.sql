
DROP PROCEDURE IF EXISTS stp_MenuInsert;

CREATE PROCEDURE stp_MenuInsert ( OUT IdMenu       INT,
                                   IN Descricao	   VARCHAR(100),
						                       IN IdMenuPai	   INT,
						                       IN EhPrincipal  TINYINT,
						                       IN Sequencia	   INT,
			                             IN IdFormulario INT ) 
BEGIN
        INSERT INTO Menu (Descricao,	 
                          IdMenuPai,	 
                          EhPrincipal,
                          Sequencia,	 
                          IdFormulario)       	    
                  VALUES (Descricao,	 
                          IdMenuPai,	 
                          EhPrincipal, 
                          Sequencia,	 
                          IdFormulario);

         SELECT Mnu.IdMenu INTO IdMenu
           FROM Menu AS Mnu 
       ORDER BY Mnu.IdMenu DESC
          LIMIT 1;

END; 

/*
CALL stp_MenuInsert ( @IdMenu, 'Cidade', 3, 0, 0, NULL  );
SELECT @IdMenu;

-- TABELAS
select * 
  from INFORMATION_SCHEMA.Tables 
 Where Table_Schema  = 'Msystem'
   and Table_Type = 'Base table'
   and Table_name = 'Usuario'


  -- COLUNAS
select * 
  from INFORMATION_SCHEMA.COLUMNS 
 Where Table_Schema  = 'Msystem'
   and Table_name = 'Usuario'
   and Column_name = 'Senha' 

show procedure status where db = 'Msystem' and name = 'stp_MenuInsert'
*/