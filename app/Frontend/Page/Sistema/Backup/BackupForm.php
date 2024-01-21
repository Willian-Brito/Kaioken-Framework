<?php

#region Imports

use KaiokenFramework\Backup\IBackup;
use KaiokenFramework\Backup\MySqlBackup;

use KaiokenFramework\Page\Page;
use KaiokenFramework\Page\Action;

use KaiokenFramework\Components\Form\Form;
use KaiokenFramework\Components\Form\Text;
use KaiokenFramework\Components\Container\VBox;
use KaiokenFramework\Components\Wrapper\KaiokenFormWrapper;
use KaiokenFramework\Components\Dialog\Message;
#endregion

/**
 * Tela para Gerar Backup do Sistema
 */
class BackupForm extends Page
{
    #region Objetos

    private $form;
    private $idInput;
    #endregion 

    #region Construtor

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        $this->createPage();
    }
    #endregion

    #region Metodos

    #region export
    public function export()
    {
        try
        {
            $this->gerarBackup(new MySqlBackup());
        }
        catch(Exception $ex)
        {
            new Message('error', $ex->getMessage(), 3000, "#$this->idInput");
        }
    }
    #endregion

    #region createPage
    private function createPage()
    {
        #region Criar Formulario

        // instancia um formulário
        $this->form = new KaiokenFormWrapper(new Form('form_backup'));
        $this->form->setTitle('Gerar Backup'); 
              
        // cria os campos do formulário
        $path = new Text('Caminho');
        $path->id = "txtPath";
        $path->placeholder = "Backend/Tmp/";
        $path->maxlength = "100";

        $this->form->addField('Caminho Interno', $path, '90%');        
        $this->form->addAction('Exportar', new Action(array($this, 'export')));
        #endregion
        
        // monta a página através de uma tabela
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);

        parent::add($box);
    }
    #endregion

    #region getIdInput
    function getIdInput($indice) {

        $field = $this->form->getFields()[$indice];
        return $field->getProperty("id");
    }
    #endregion

    #region gerarBackup
    private function gerarBackup(IBackup $backupDatabase)
    {
        $txtPath = $this->form->getData();
        $backupDatabase->export($txtPath->Caminho);
    }
    #endregion

    #endregion
}
