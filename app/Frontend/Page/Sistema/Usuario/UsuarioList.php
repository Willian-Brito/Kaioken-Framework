<?php

#region Imports

use KaiokenFramework\Components\Container\VBox;
use KaiokenFramework\Components\Datagrid\Datagrid;
use KaiokenFramework\Components\Datagrid\DatagridColumn;
use KaiokenFramework\Components\Dialog\Message;
use KaiokenFramework\Components\Dialog\Question;
use KaiokenFramework\Components\Form\Form;
use KaiokenFramework\Components\Form\Text;
use KaiokenFramework\Components\Wrapper\KaiokenDatagridWrapper;
use KaiokenFramework\Components\Wrapper\KaiokenFormWrapper;

use KaiokenFramework\Database\Transaction;

use KaiokenFramework\Page\Action;
use KaiokenFramework\Page\Page;

use KaiokenFramework\Traits\DeleteTrait;
use KaiokenFramework\Traits\ReloadTrait;
#endregion

/**
 * Lista de Usuários
*/
class UsuarioList extends Page 
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
    use DeleteTrait;
    #endregion

    #region Construtor

    /**
     * Construtor da página
    */
    public function __construct()
    {
        parent::__construct();
        $this->criarPagina();
    }
    #endregion

    #region Metodos

    #region criarPagina
    private function criarPagina()
    {
        // Define o Active Record
        $this->activeRecord = 'Usuario';
        $IdUsuarioSuporte = 1;
        $this->filters[] = ['IdUsuario', '!=', $IdUsuarioSuporte, 'AND'];

        #region Criar Formulario
        $this->form = new KaiokenFormWrapper(new Form('form_busca_Usuario'));
        $this->form->setTitle('Usuario');
        
        $usuario = new Text('Usuario');
        $usuario->maxlength = "100";
        
        $this->form->addField('Usuario', $usuario, '90%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array(new UsuarioForm, 'onNovo')));
        #endregion

        #region Criar DataGrid

        // instancia a Datagrid
        $this->datagrid = new KaiokenDatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $usuario = new DatagridColumn('Nome',   'Nome', 'left', '350px');
        $email = new DatagridColumn('Usuario',   'Usuario', 'left', '200px');
        $perfil = new DatagridColumn('nome_perfil', 'Perfil',     'left', '250px');
        $ehAtivo = new DatagridColumn('eh_ativo', 'Está Ativo',     'left', '100px');
        
        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($usuario);
        $this->datagrid->addColumn($email);
        $this->datagrid->addColumn($perfil);
        $this->datagrid->addColumn($ehAtivo);

        $this->datagrid->addAction( 'Editar',  new Action([new UsuarioForm, 'onEdit']),   'IdUsuario', 'fa fa-pencil');
        $this->datagrid->addAction( 'Excluir', new Action([$this, 'onDelete']), 'IdUsuario', 'fa fa-trash light');
        $this->datagrid->addAction( 'Alterar Senha', new Action([new AlterarSenhaForm, 'onEdit']), 'IdUsuario', 'fa fa-key');

        // monta a página através de uma tabela
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);

        #endregion
        
        parent::add($box);
    }
    #endregion

    #region onReload
    /**
     * Carrega os dados
     */
    public function onReload()
    {
        // obtém os dados do formulário de buscas
        $dados = $this->form->getData();
        
        // verifica se o usuário preencheu o formulário
        if ($dados->Usuario)
        {
            // filtra pela descrição do produto
            $this->filters[] = ['Usuario', 'like', "%{$dados->Usuario}%", 'and'];
        }
        
        $this->onReloadTrait();   
        $this->loaded = true;
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

    #endregion
}