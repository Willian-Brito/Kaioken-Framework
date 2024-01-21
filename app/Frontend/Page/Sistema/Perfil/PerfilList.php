<?php

#region Imports

use KaiokenFramework\Components\Base\JScript;
use KaiokenFramework\Components\Container\HBox; 
use KaiokenFramework\Page\Page;
use KaiokenFramework\Page\Action;

use KaiokenFramework\Components\Form\Form;
use KaiokenFramework\Components\Form\Text;
use KaiokenFramework\Components\Container\VBox;
use KaiokenFramework\Components\Datagrid\Datagrid;
use KaiokenFramework\Components\Datagrid\DatagridColumn;
use KaiokenFramework\Components\Dialog\Message;
use KaiokenFramework\Components\Dialog\Question;
use KaiokenFramework\Components\Form\CheckButton;
use KaiokenFramework\Components\Wrapper\KaiokenDatagridWrapper;
use KaiokenFramework\Components\Wrapper\KaiokenFormWrapper;
use KaiokenFramework\Database\Transaction;
use KaiokenFramework\Traits\DeleteTrait;
use KaiokenFramework\Traits\ReloadTrait;
#endregion

/**
 * Lista de Perfis
*/
class PerfilList extends Page
{
    #region Objetos

    private $form;
    private $datagrid;
    private $loaded;
    private $activeRecord;
    private $filters;
    #endregion

    #region Traits
    use ReloadTrait { onReload as onReloadTrait; } 
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

    #region onReload
    /**
     * Carrega os dados
    */
    public function onReload()
    {
        // obtém os dados do formulário de buscas
        $dados = $this->form->getData();
        
        // verifica se o usuário preencheu o formulário
        if ($dados->Descricao)
        {
            // filtra pela descrição do produto
            $this->filters[] = ['Descricao', 'like', "%{$dados->Descricao}%", 'and'];
        }
        
        $this->onReloadTrait();
        $this->loaded = true;
    }
    #endregion

    #region createPage
    private function createPage()
    {
        // Define o Active Record
        $this->activeRecord = 'Perfil'; 
        $this->filters[] = ['IdPerfil', '!=', Usuario::getIdUsuarioSuporte(), 'AND'];

        #region Criar Formulario
        $this->form = new KaiokenFormWrapper(new Form('form_busca_Perfil'));
        $this->form->setTitle('Perfil');
        
        $descricao = new Text('Descricao');
        $descricao->maxlength = "100";

        $this->form->addField('Descrição', $descricao, '90%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array(new PerfilForm, 'onEdit')));
        #endregion

        #region Criar DataGrid 

        // instancia a Datagrid
        $this->datagrid = new KaiokenDatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $codigo     = new DatagridColumn('IdPerfil',     'Código',   'left', '100px');
        $descricao  = new DatagridColumn('Descricao',   'Perfil', 'left', '300px');
        $usuario    = new DatagridColumn('nome_usuarios', 'Usuarios',     'left', '300px');
        
        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($descricao);
        $this->datagrid->addColumn($usuario);

        $this->datagrid->addAction( 'Editar',  new Action([new PerfilForm, 'onEdit']),   'IdPerfil', 'fa fa-pencil');
        $this->datagrid->addAction( 'Excluir', new Action([$this, 'onDelete']), 'IdPerfil', 'fa fa-trash light');

        // monta a página através de uma tabela
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);
        #endregion
        
        parent::add($box);
    }
    #endregion

    #region onDelete
    /**
     * Pergunta sobre a exclusão de registro
     */
    function onDelete($param)
    {
        $id = $param['key']; // obtém o parâmetro $i

        $actionYes = new Action(array($this, 'Delete'));
        $actionYes->setParameter('key', $id);

        $actionNo = new Action(array($this, 'fecharMsg'));
        
        new Question('Deseja realmente excluir o registro?', $actionYes, $actionNo);
    }
    #endregion

    #region Delete
    /**
     * Exclui um registro
    */
    function Delete($param)
    {
        try
        {            
            Transaction::open();

            $IdPerfil = $param['key'];
            $perfil = new Perfil($IdPerfil);      

            if($IdPerfil == Perfil::getIdPerfilSuporte())
                throw new Exception("Não é possível excluir o perfil 'suporte'");

            $perfil->deletarTodosFormularios();
            $perfil->delete();

            Transaction::close();

            $this->onReload(); 

            new Message('success', "Registro excluído com sucesso");
            $this->redirect($param['class']);
        }
        catch (Exception $e)
        {
            Transaction::rollback();
            new Message('error', $e->getMessage());
        }
    }
    #endregion

    #region fecharMsg
    function fecharMsg()
    {
        JScript::run("$('.question').hide();");
    }
    #endregion

    #region show

    /**
     * exibe a página
     */
    public function show()
    {
        // se a listagem ainda não foi carregada
        if (!$this->loaded)
        {
            $this->onReload();
        }
        parent::show();
    }
    #endregion

    #region redirect
    function redirect($class) 
    {
        $link = "index.php?class=$class";
        JScript::redirect($link, 1000);
    }
    #endregion

    #endregion
}
