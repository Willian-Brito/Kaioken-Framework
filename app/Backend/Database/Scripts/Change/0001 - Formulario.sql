-- Change Formulário
IF ( NOT EXISTS( SELECT column_name
                   FROM information_schema.columns 
                  WHERE table_name = 'Formulario'
                    AND column_name = 'classe') )
THEN

    ALTER TABLE Formulario ADD Classe VARCHAR(100) NULL;

    UPDATE Formulario 
       SET Classe = ( SELECT REPLACE(ArquivoFormulario, 'Form', '') )
     WHERE ArquivoFormulario IN ('CidadeForm', 'PerfilForm', 'UsuarioForm', 'ParametroForm', 'EmpresaForm' );

END IF;

IF ( NOT EXISTS(SELECT Frm.IdFormulario 
                  FROM Formulario AS Frm
			           WHERE Frm.DescricaoFormulario = 'Exportar XML') )
THEN

    SET @IdFormulario = ( SELECT Frm.IdFormulario 
                            FROM Formulario AS Frm 
                           WHERE Frm.DescricaoFormulario = 'Exportar XML (NFe)') ;	

    UPDATE Formulario 
       SET DescricaoFormulario = 'Exportar XML',
           ArquivoFormulario = 'ExportarXmlForm',
           ArquivoLista = 'ExportarXmlForm'
     WHERE IdFormulario = @IdFormulario;

END IF;