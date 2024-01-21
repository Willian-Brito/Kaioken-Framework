
IF ( NOT EXISTS( SELECT User 
                   FROM mysql.user 
                  WHERE User = 'kaioken') )
THEN

    -- # Criando o Usuário (kaioken framework)
    CREATE USER 'kaioken'@'localhost' IDENTIFIED BY 'kaioken123#';

    -- # Adicionando permissões ao usuário kaioken framework
     GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE, SHOW VIEW, LOCK TABLES
        ON kaioken.* 
        TO 'kaioken'@'localhost';

    -- # Recarregar privilegios
    FLUSH PRIVILEGES;

      -- # Perfil (Suporte)
    INSERT INTO Perfil (IdPerfil, Descricao, UsuarioAlteracao, DataAlteracao) 
         VALUES (1, 'Suporte', 'suporte', NOW());

    -- # Usuario (Suporte)
    INSERT INTO Usuario (Nome,
		               Usuario,
		               Senha,
                         Email,
		               EhAtivo,
		               IdPerfil,
		               UsuarioAlteracao,
		               DataAlteracao)
                 VALUES ('kaioken',
                         'kaioken',
                        --  '$argon2id$v=19$m=65536,t=4,p=1$Nk94MHFteXZLLnFiOW0ucg$ASDrLhkf7bfUzIzUaEQBoi7eN1KkxDxhEAERsBh741k',
                         '$argon2id$v=19$m=65536,t=4,p=1$S3pRekgwbWFWT0NCeUhFSQ$lGMtbdHEsqREme7fD+fPkA',
                         'KaiokenFramework@gmail.com',
                         1,
                         1,
                         'kaioken',
                         NOW());

     -- # Inserindo Formulario (LoginForm)
     INSERT INTO Formulario (DescricaoLista, ArquivoLista, DescricaoFormulario, ArquivoFormulario) 
          VALUES (NULL, NULL, NULL, 'LoginForm');		

     -- # Vincular Formulario (LoginForm) ao Perfil (suporte)
     SET @IdPerfilSuporte = ( SELECT Pef.IdPerfil FROM Perfil AS Pef WHERE Descricao = 'suporte') ;	
     SET @IdFormularioLogin = ( SELECT Frm.IdFormulario FROM Formulario AS Frm WHERE ArquivoFormulario = 'LoginForm');

     INSERT INTO PerfilFormulario (IdPerfil, IdFormulario) VALUES (@IdPerfilSuporte, @IdFormularioLogin);

    -- # Tabelas para consulta de permissões
    -- SELECT * FROM mysql.db;
    -- SELECT * FROM mysql.tables_priv;
    -- SELECT * FROM mysql.columns_priv;
    -- SELECT * FROM mysql.procs_priv;

    -- # Comandos de Permissões
    -- GRANT <privilegios> ON <nivel de acesso> TO <usuário>
    -- REVOKE <privilegios> ON <nivel de acesso> FROM <usuário>

    -- # Verificar permissões de um usuário
    -- SHOW GRANTS FOR 'nome_do_usuário'@'localhost';

    -- # Remover Usuário
    -- DROP USER 'exemplo'@'localhost';

    -- # Fonte: http://dev.mysql.com/doc/refman/5.1/en/privileges-provided.html

END  IF;