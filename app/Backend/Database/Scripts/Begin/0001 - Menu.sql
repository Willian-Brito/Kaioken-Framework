
-- # Criando os Menus
IF ( NOT EXISTS( SELECT Mnu.IdMenu 
                   FROM Menu AS Mnu
                  WHERE Mnu.Descricao = 'Dashboard') )
THEN

    -- # Dashboard
    INSERT INTO Menu (Descricao, IdMenuPai, EhPrincipal, Sequencia, IdFormulario) 
         VALUES ('Dashboard', NULL, 1, 1, NULL);

    -- # Cadastros
    INSERT INTO Menu (Descricao, IdMenuPai, EhPrincipal, Sequencia, IdFormulario) 
         VALUES ('Cadastros', NULL, 1, 2, NULL);

    -- # Consultas
    INSERT INTO Menu (Descricao, IdMenuPai, EhPrincipal, Sequencia, IdFormulario) 
         VALUES ('Consultas', NULL, 1, 3, NULL);

    -- # Relatórios
    INSERT INTO Menu (Descricao, IdMenuPai, EhPrincipal, Sequencia, IdFormulario) 
         VALUES ('Relatórios', NULL, 1, 4, NULL);

    -- # Configurações
    INSERT INTO Menu (Descricao, IdMenuPai, EhPrincipal, Sequencia, IdFormulario) 
         VALUES ('Configurações', NULL, 1, 5, NULL);

END  IF;