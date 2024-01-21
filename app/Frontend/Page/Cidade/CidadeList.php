<?php

#region Imports
use KaiokenFramework\Page\Page;
use KaiokenFramework\Page\Action;

use KaiokenFramework\Components\Form\Form;
use KaiokenFramework\Components\Form\Text;
use KaiokenFramework\Components\Container\VBox;
use KaiokenFramework\Components\Datagrid\Datagrid;
use KaiokenFramework\Components\Datagrid\DatagridColumn;
use KaiokenFramework\Components\Wrapper\BootstrapDatagridWrapper;
use KaiokenFramework\Components\Wrapper\BootstrapFormWrapper;
use KaiokenFramework\Components\Wrapper\KaiokenDatagridWrapper;
use KaiokenFramework\Components\Wrapper\KaiokenFormWrapper;

use KaiokenFramework\Database\Configuration;
use KaiokenFramework\Database\Transaction;

use KaiokenFramework\Traits\DeleteTrait;
use KaiokenFramework\Traits\ReloadTrait;
#endregion

/**
 * Lista de cidades
*/
class CidadeList extends Page
{
    #region Objetos

    private $form;
    private $datagrid;
    private $loaded;
    private $activeRecord;
    private $filters;
    #endregion

    #region Traits
    use DeleteTrait;
    use ReloadTrait { onReload as onReloadTrait; } 
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

    #region criarPagina
    private function criarPagina()
    {
        // Define o Active Record
        $this->activeRecord = 'Cidade';

        #region Criar Formulario
        $this->form = new KaiokenFormWrapper(new Form('form_busca_cidades'));
        $this->form->setTitle('Cidades');
        
        $descricao = new Text('Descricao');
        $descricao->maxlength = "100";

        $this->form->addField('Descrição', $descricao, '90%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array(new CidadeForm, 'onEdit')));
        #endregion

        #region Criar DataGrid

        // instancia a Datagrid
        $this->datagrid = new KaiokenDatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $codigo     = new DatagridColumn('IdCidade',     'Código',   'left', '100px');
        $descricao  = new DatagridColumn('Descricao',   'Descricao', 'left', '300px');
        $codigoIBGE = new DatagridColumn('CodigoIBGE',   'Cod. IBGE', 'left', '100px');
        $estado     = new DatagridColumn('nome_estado', 'Estado',     'left', '300px');
        
        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($descricao);
        $this->datagrid->addColumn($codigoIBGE);
        $this->datagrid->addColumn($estado);

        $this->datagrid->addAction( 'Editar',  new Action([new CidadeForm, 'onEdit']),   'IdCidade', 'fa fa-pencil');
        $this->datagrid->addAction( 'Excluir', new Action([$this, 'onDelete']), 'IdCidade', 'fa fa-trash light');

        // monta a página através de uma tabela
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);

        #endregion
        
        parent::add($box);
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
