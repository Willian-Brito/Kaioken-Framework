<?php

#region Imports

use KaiokenFramework\Components\Container\VBox;
use KaiokenFramework\Components\Datagrid\Datagrid;
use KaiokenFramework\Components\Datagrid\DatagridColumn;
use KaiokenFramework\Components\Form\Form;
use KaiokenFramework\Components\Form\Text;
use KaiokenFramework\Components\Wrapper\KaiokenDatagridWrapper;
use KaiokenFramework\Components\Wrapper\KaiokenFormWrapper;

use KaiokenFramework\Page\Action;
use KaiokenFramework\Page\Page;

use KaiokenFramework\Traits\DeleteTrait;
use KaiokenFramework\Traits\ReloadTrait;
#endregion

class ParametroList extends Page
{
    #region Propriedades da Classe
    private $form;
    private $loaded;
    private $activeRecord;
    private $filters;
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

    #region Traits
    use ReloadTrait { onReload as onReloadTrait; } 
    use DeleteTrait;
    #endregion

    #region Metodos

    #region createPage
    private function createPage()
    {
        // Define o Active Record
        $this->activeRecord = 'Parametro';

        #region Criar Formulario
        $this->form = new KaiokenFormWrapper(new Form('form_busca_parametro'));
        $this->form->setTitle('Parâmetros do Sistema');
        
        $parametro = new Text('Parametro');
        $parametro->maxlength = "100";
        
        $this->form->addField('Parâmetro', $parametro, '90%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array(new ParametroForm, 'onEdit')));
        #endregion

        #region Criar DataGrid

        // instancia a Datagrid
        $this->datagrid = new KaiokenDatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $user = new DatagridColumn('usuario',   'Usuário', 'left', '100px');
        $descricao = new DatagridColumn('Descricao',   'Descrição', 'left', '350px');
        $tipoParametro = new DatagridColumn('tipo_parametro', 'Tipo Parâmetro',     'left', '150px');
        $valor = new DatagridColumn('Valor',   'Valor', 'left', '50px');
        $observacao = new DatagridColumn('Observacao', 'Observação',     'left', '485px');
        
        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($user);
        $this->datagrid->addColumn($descricao);
        $this->datagrid->addColumn($tipoParametro);
        $this->datagrid->addColumn($valor);
        $this->datagrid->addColumn($observacao);

        $this->datagrid->addAction( 'Editar',  new Action([new ParametroForm, 'onEdit']),   'IdParametro', 'fa fa-pencil');
        $this->datagrid->addAction( 'Excluir', new Action([$this, 'onDelete']), 'IdParametro', 'fa fa-trash light');

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
        if ($dados->Parametro)
        {
            // filtra pela descrição do produto
            $this->filters[] = ['Descricao', 'like', "%{$dados->Parametro}%", 'and'];
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