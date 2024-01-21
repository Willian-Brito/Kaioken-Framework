IF ( NOT EXISTS(SELECT Mnu.IdMenu 
                  FROM Menu AS Mnu
			     WHERE Mnu.Descricao = 'Exportar XML') )
THEN

    SET @IdMenu = ( SELECT Mnu.IdMenu 
                      FROM Menu AS Mnu 
                     WHERE Mnu.Descricao = 'Exportar XML (NFe)') ;	

    UPDATE Menu 
       SET Descricao = 'Exportar XML'
     WHERE IdMenu = @IdMenu;

END IF;