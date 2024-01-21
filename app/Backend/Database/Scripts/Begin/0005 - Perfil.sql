
-- Formulário Perfil
IF ( NOT EXISTS(SELECT Frm.IdFormulario 
                  FROM Formulario AS Frm
			     WHERE Frm.DescricaoFormulario = 'Cadastro de Perfil') )
THEN

    -- Obtem as informacoes do usuario
    SET @IdusuarioSuporte = ( SELECT Usr.IdUsuario FROM Usuario AS Usr WHERE Usuario = 'kaioken') ;		
    SET @IdPerfilAtual = ( SELECT Usr.IdPerfil FROM Usuario AS Usr WHERE Usuario = 'kaioken') ;			

    -- Inserindo o formulario
    INSERT INTO Formulario (DescricaoLista, ArquivoLista, DescricaoFormulario, ArquivoFormulario, Classe) 
         VALUES ('Lista de Perfis', 'PerfilList', 'Cadastro de Perfil', 'PerfilForm', 'Perfil');

    -- Pegando ultimo Id Inserido do Formulario
    SET @IdFormularioAtual = (SELECT Frm.IdFormulario
                                FROM Formulario AS Frm
                            ORDER BY Frm.IdFormulario DESC
                               LIMIT 1);

    -- Obtem o menu pai
    SET @IdSubMenuPai = (SELECT Mnu.IdMenu
                           FROM Menu AS Mnu
                          WHERE Descricao = 'Configurações'
                            AND EhPrincipal = 1);	

    -- Obtem o menu filho
    SET @IdSubMenuUsado = (SELECT Mnu.IdMenu
                             FROM Menu AS Mnu 
                            WHERE Descricao = 'Perfil'
                              AND IdMenuPai = @IdSubMenuPai);


    -- Inserindo  o novo menu
    CALL stp_MenuInsert ( @IdMenu, 'Perfil', @IdSubMenuPai, 0, 0, @IdFormularioAtual ); 

    -- Inserindo o formulario ao perfil
    INSERT INTO PerfilFormulario (IdPerfil, IdFormulario) VALUES (@IdPerfilAtual, @IdFormularioAtual); 

END IF;
