# :fire: Kaioken Framework

<div align="center">
  <img src="https://raw.githubusercontent.com/Willian-Brito/Kaioken-Framework/main/app/Frontend/assets/img/prints/Kaioken%20Framework.png" alt="Kaioken Framework" />
</div>

<h2>:point_right: Introdução</h2>
<p>
  <b>Kaioken</b> é um <b>framework</b> full stack, SSR (Server Side Rendering) e open source, focado em produtividade e segurança, muito fácil de usar e foi construído na linguagem PHP na versão 8.2.1.

  Este projeto foi desenvolvido com o intuito de melhorar a produtividade e segurança no desenvolvimento do <b>MS Accounting</b> que será um <b>novo produto</b> da empresa <b>MSystem</b>, onde sou responsável pelo desenvolvimento.

  Neste <b>Framework</b> foi aplicado princípios e recomendações de <b>desenvolvimento seguro</b> e boas práticas de engenharia de software como <b>Padrões de Projetos</b> e <b>Padrões de Arquiteturais</b>.
</p>

<h2>:page_facing_up: Padrões Arquiteturais e Projeto</h2>
<p>
Os padrões de projetos que foram utilizados para a construção deste framework, são baseados no livro <b>"Padrões de Projeto: Soluções reutilizáveis de software orientado a objetos"</b> do <b>GoF</b> e <b>"Padrões de Arquitetura de Aplicações Corporativas"</b> do <b>Martin Fowler</b>.

* <b>Connection.php:</b> Encapsula a conexão com o banco de dados configurado, implementa o padrão <b>Singleton</b>.
	
* <b>Transaction.php:</b> Essa classe permite agrupar várias operações de escrita em uma única transação e garante que as mudanças sejam aplicadas ao banco de dados de forma consistente. Implementa o padrão <b>Unit of Work</b> e <b>Singleton</b>.
	
* <b>Record.php:</b> É uma classe abstrata responsável pelas operações do banco de dados (select, insert, update e delete) e também é um super tipo para toda uma camada de classes da camada Model inspirado no padrão <b>Layer Supertype</b>, as classes filhas deste super tipo são chamadas de <b>ActiveRecord</b>, pois elas implementam comporamentos de regras de negócios e operações de banco de dados por meio de herança.

* <b>Repository.php:</b> Um Repository, é uma camada que atua como um gerenciador de coleções, o objetivo desta classe é executar operações em lote sobre objetos, implementando o padrão <b>Repository</b>.

* <b>Criteria.php:</b> Classe que define um critério de filtros de dados encapsulando as consultas de banco de dados em objetos reutilizáveis, ele armazena os filtros que será utilizado pelo repositório, esta classe implementa o padrão <b>QueryObject</b> que mantém uma maneira mais organizada de definir a consulta e tratamento dos valores passados pelo usuário, além de não precisar conhecer a linguagem SQL que será executada pelo banco.

* <b>Router.php</b>: Aplicando o padrão <b>Front Controller</b> esse componente centraliza o gerenciamento das requisições em um único ponto de entrada e direciona as solicitações para as camadas apropriadas com base na rota ou URL. 

* <b>Page.php</b>: Manipula a navegação e ações de todas as telas da aplicação, processando e direcionando as requisições dos usuários para as telas apropriadas, utilizando o padrão <b>Application Controller</b> visando centralizar o tratamento do ﬂuxo da aplicação.

* <b>Template.php</b>: A idéia básica do Template.php é quando a página é usada para atender uma solicitação, os marcadores estáticos no template padrão da aplicação, são substituídos pelo resultado de algum processamento, como uma consulta em um banco de dados ou algum cálculo feito pelo back-end, o Template.php utiliza o padrão <b>Template View</b> para implementar essa funcionalidade.

* <b>Element.php</b>: Classe responsável por processar os dados do domínio elemento por elemento e transformá-los em código HTML. Utilizando o padrão <b>Transform View</b> ele percorre uma coleção de dados do domínio (por exemplo, uma lista de objetos) e para cada elemento da coleção, aplica a lógica para gerar o código HTML correspondente.

* <b>Components</b>: Implementando o padrão <b>Two-Step View</b> a idéia da camada de componentes é transformar dados do domínio em HTML em duas etapas: primeiro formando algum tipo de página lógica e depois representando essa página lógica em componentes HTML para ser enviado ao navegador. A classe <b>Field.php</b> possui apenas dados e servirá como base para componentes mais específicos (text, label, check box, combo box e etc...), e a parte visual é implementada pelas classes filhas.

* <b>Session.php</b>: Esta Classe é responsável por gerenciar e armazenar as informações de estado relacionadas a uma sessão especíﬁca do usuário, implementando o padrão <b>Server Session State</b>.

* <b>Logs:</b> Nos logs do sistema foi implementado o padrão <b>Strategy</b>, onde tem uma família de algoritmos em classes separadas, que implementa logs do tipo texto, html e xml. 

* <b>Wrappers:</b> O <b>Decorator</b> ou <b>Wrapper</b> é um padrão de projeto de software que permite adicionar um comportamento a um objeto já existente em tempo de execução, ou seja, agrega dinamicamente responsabilidades adicionais a um objeto, este padrão foi utilizado para adicionar aparência aos formulários e datagrids da aplicação. A classe <b>Form</b> e <b>Datagrid</b> contém os dados e as classes <b>KaiokenFormWrapper</b> e <b>KaiokenDatagridWrapper</b> contém toda a lógica de apresentação visual, utilizando este padrão caso queira alterar a aparência de sua aplicação, basta desenvolver uma classe de apresentação visual exemplo <b>BootstrapFormWrapper</b> ou <b>MaterializeFormWrapper</b>.

* <b>Trait:</b>  Traits não são padrões de projetos, porém é um recurso incrível do PHP que é muito utilizado na construção de frameworks, basicamente traits são trechos de código (funcionalidades) que podem ser incorporados em classes. A vantagem é que as funcionalidades ficam em arquivos separados e você utiliza apenas quando precisar dessas funções, isso possibilita que não violamos o princípio <b>DRY (Don´t Repeat Yourself)</b> e acaba facilitando a reutilização de códigos, outra vantagem é que não colocamos esse código em classes utilitárias para não inflar com múltiplas funcionalidades e quebrar o principio <b>SRP (Single Pesponsibility Principle)</b> do <b>SOLID</b>.
</p>

<h2>:open_file_folder: Estrutura</h2>
<p>
<div align="center">
  <img src="https://raw.githubusercontent.com/Willian-Brito/Kaioken-Framework/main/app/Frontend/assets/img/prints/Estrutura%20de%20Diret%C3%B3rios.png" alt="Estrutura de Diretórios do Framework" />
</div>
</p>

<h2>:shield: Segurança</h2>
<p>
Além de padrões de projetos, neste framework também foi utilizado princípios e recomendações de <b>desenvolvimento seguro</b>, que possui como ﬁnalidade introduzir camadas de proteção contra os ataques cibernéticos.

Inicialmente foi aplicado mecanismos de proteções contra <b>SQL Injection, XSS (Cross Site Script), CSRF (Cross Site Request Forgery)</b> e <b>Session Hijacking</b>, porém para implementar a proteção contra o ataque de roubo de sessão (Session Hijacking) da maneira correta, é necessário ao logar e deslogar da aplicação, utilizar a classe <b>Session</b> do framework para <b>regerar o ID</b> da sessão. 

    public function logout()
    {
        // Rotinas de logout

        // Gerar um novo ID para Sessão
        Session::regenerate();
    }

Conforme o framework for evoluindo será adicionado mais mecanismos de segurança. Abaixo vamos deixar algumas configurações do arquivo <b>php.ini</b> para a segurança no gerenciamento de sessões:
</p>


    session.name = KAIOKEN_SESSID
    session.cookie_domain="meusite.com.br"
    session.name="KAIOKEN_SESSNAME"
    session.use_trans_sid = 0
    session.entropy_file = /dev/urandom
    session.entropy_length = 32
    session.cookie_httponly=On
    session.cookie_samesite="Strict"
    session.cookie_secure=On
    session.sid_bits_per_character=6
    session.sid_length=48
    session.use_only_cookies=On
    session.use_strict_mode=On
    session.use_trans_sid=Off
    session.gc_maxlifetime = 14000

<h2>:technologist: Guia de Instalação</h2>
<p>
A seguir, os passos mínimos para instalar e configurar uma aplicação no Kaioken Framework. Este é um tutorial com recomendações genéricas, tanto para Linux quanto para Windows.

<h4>1- Instale o Apache</h4>
Instale e configure o Apache2 (apache2.conf), altere as configurações do Apache para ele ler os <b>.htaccess</b> presentes na estrutura de diretório do Framework, que protegem determinados diretórios do acesso indevido.

<br>
   
    AllowOverride All


<h4>2- Instale o PHP</h4>
Instale e configure o PHP.<br>

* <b>Módulos recomendados:</b> soap, xml, curl, sqlite3, php-sodium.
* <b>Configuração para desenvolvimento:</b> Ajuste as configurações para habilitar log de erros, aumentar o limite de RAM, o tempo de execução, o tempo de sessão, e definir limites de upload, etc.

      error_log = /tmp/php_errors.log
      log_errors = On
      display_errors = On
      memory_limit = 256M
      max_execution_time = 120
      error_reporting = E_ALL
      file_uploads = On
      post_max_size = 100M
      upload_max_filesize = 100M
      session.gc_maxlifetime = 14000

* <b>Configuração para produção:</b> Quando a aplicação entrar em produção, desligue a exibição de erros.

      display_errors = Off
      error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE


<h4>3- Estrutura da Aplicação</h4>
Faça download do <b>Kaioken Framework</b> descompacte o template conforme o seu sistema e renomeie para o nome real de seu projeto:
<br>

* Ubuntu: /var/www/html/
* Windows: C:\wamp64\www\

<h4>4- Banco de dados</h4>
Crie o banco de dados de sua aplicação, usando PostgreSQL, MySQL e SQLite (testados até o momento).

Configure o conector dentro da aplicação:

    app/Backend/Config/kaioken.ini

Cada banco de dados deve ser configurado na pasta <b>app/Backend/Config</b> por um INI. Para usar um conector, você deve ter o driver correto habilitado no <b>php.ini</b>.

Exemplo do significado dos campos:

    host = 'IP do Servidor do Banco de Dados'
    port = 'PORTA do Servidor do Banco de Dados'
    name = 'NOME do Banco de Dados'
    user = 'USUÁRIO do Banco de Dados Usado pelo APP'
    pass = 'SENHA do USUÁRIO do Banco de Dados Usado pelo APP'
    type = 'TIPO do Banco de Dados'

A seguir um exemplo de um conector para <b>SQLite</b>:

    host = ''
    port = ''
    name = 'app/database/exemplo.db'
    user = ''
    pass = ''
    type = 'sqlite'

A seguir um exemplo de um conector para <b>PostgreSQL</b>:

    host = '192.168.1.102'
    port = ''
    name = 'exemplo'
    user = 'postgres'
    pass = 'postgres'
    type = 'pgsql'

A seguir um exemplo de um conector para <b>MySQL</b> e <b>MariaDB</b>:

    host = 'localhost'
    name = 'kaioken'
    user = 'kaioken'
    pass = 'kaioken123#'
    port = '3306'
    type = 'mysql'

Para alterar a senha do usuário do banco de dados basta alterar o campo <b>pass</b> no arquivo <b>kaioken.ini</b> e no arquivo de gerar a estrutura do banco de dados do app que está neste caminho: <b>app/Backend/Database/Begin/0000 - ConfiguracaoInicial.sql</b>.

<div align="center">
  <img src="https://raw.githubusercontent.com/Willian-Brito/Kaioken-Framework/main/app/Frontend/assets/img/prints/config_senha.png" alt="Configuração da senha do usuário do banco de dados do app" />
</div>

Depois de realizar a configuração, vamos <b>criptografar o arquivo</b> para mantermos a segurança dos dados de conexão com o banco de dados. Para criptografar, basta entrar na pasta <b>framework/Security/</b> e executar o comando a seguir:

<b>Nota:</b> Para realizar esses comandos não devemos alterar o caminho e nome do arquivo <b>kaioken.ini</b>. Após criptografar pela primeira vez, será gerado o arquivo <b>chave.key</b> na pasta <b>framework/Security/</b>, esse arquivo se trata de uma chave simétrica gerada pelo Kaioken Framework, guarde este aquivo com segurança, pois se trata de um arquivo muito sensível.

    php encrypt.php

Para descriptografar basta executar o comando:

    php decrypt.php

<h4>5- Configurar Estrutura da Aplicação</h4>
Primeiramente vamos iniciar gerando o <b>script</b> de estrutura do banco de dados, para gerar basta ir na pasta <b>app/Backend/Database</b> e executar o seguinte comando:

    php Migrations.php

Será gerado um arquivo chamado <b>Migrations.sql</b>, agora basta abrir este arquivo e executar no banco de dados. Geralmente no banco MySQL ou MariaDB o login padrão é <b>root</b> e senha <b>mysql</b> 
<br><br>
<b>Nota:</b> Esse arquivo de estrutura foi testado somente no banco <b>MariaDB versão 11.2.2</b>

Agora é só executar a aplicação e logar com usuário <b>kaioken</b> com a senha <b>kaioken123</b>.

<div align="center">
  <img src="https://raw.githubusercontent.com/Willian-Brito/Kaioken-Framework/main/app/Frontend/assets/img/prints/login_kaioken.png" alt="Pagina de login do app" />
</div>

<h4>6- Classe Modelo</h4>
Crie classes que representam as tabelas do banco de dados.
Agora é o momento de criar as classes do modelo da aplicação. Uma classe modelo é filha de <b>Record</b>. Esta classe do <b>framework</b> fornece métodos básicos de persistência como <b>save()</b>, <b>delete()</b> e <b>load()</b> que manipulam um objeto na base de dados.
<br><br>

    app/model/User.php

<b>TABLENAME</b> define o nome da tabela que a classe de modelo irá manipular.

    <?php

    use KaiokenFramework\Database\Record;

    class User extends Record
    {
        const TABLENAME = 'User';

        public function getNameUsersByPerfil()
        { 
            $nameUsers = "";

            // Criando objeto Criteria (Query Object)
            $criteria = new Criteria;   
            
            // Adicionando filtro para buscar no banco de dados
            $criteria->add("IdPerfil", "=", $this->IdPerfil);

            // Criando um repositório para buscar uma coleção de registros
            $repository = new Repository('User');

            // Buscando
            $users = $repository->load($criteria);

            foreach($users as $user)
            {
                $nameUsers .= "[{$user->Username}] ";
            }

            return $nameUsers;
        }
    }

<h4>7- Classe Page</h4>
Criando páginas para formulários, listagens, e outros.
Agora é o momento de criar as páginas da aplicação. Para tal, podem ser usados componentes, templates, ou outras técnicas. <br><br>

As páginas controladoras de aplicação são salvas no diretório <b>app/Frontend/Page</b>. A classe deve conter o nome da classe de <b>Active Record</b> que irá manipular os dados do formulário.

    <?php

    #region Imports
    use KaiokenFramework\Page\Page;
    use KaiokenFramework\Page\Action;
    use KaiokenFramework\Components\Form\Form;
    use KaiokenFramework\Components\Form\Text;
    use KaiokenFramework\Components\Container\VBox;
    use KaiokenFramework\Components\Wrapper\KaiokenFormWrapper;
    use KaiokenFramework\Components\Dialog\Message;
    use KaiokenFramework\Components\Form\Combo;
    use KaiokenFramework\Database\Transaction;
    use KaiokenFramework\Traits\EditTrait;
    use KaiokenFramework\Traits\SaveTrait;
    #endregion

    /**
    * Tela de Cadastro de cidades
    */
    class CidadeForm extends Page 
    {
        #region Objetos

        private $form;
        private $loaded;
        #endregion 

        #region Traits

        use EditTrait;
        use SaveTrait { onSave as onSaveTrait; }
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

        #region onSave
        public function onSave()
        {
            try
            {
                $this->validarCampos();
                $this->onSaveTrait();
            }
            catch(Exception $ex)
            {
                new Message('error', $ex->getMessage());
            }
        }
        #endregion

        #region onEdit
        /**
        * Carrega registro para edição
        */
        public function onEdit($param)
        {
            try 
            {
                $IdTabela = "Id{$this->activeRecord}";

                if (isset($param[$IdTabela]))
                {
                    $id = $param[$IdTabela];

                    Transaction::open(); 

                    $cidade = Cidade::find($id);

                    if ($cidade) 
                        $this->form->setData($cidade);                

                    Transaction::close(); 
                }
            }
            catch (Exception $e)
            {
                Transaction::rollback();
                new Message('error', $e->getMessage());
            }
        }
        #endregion

        #region show

        /**
        * exibe a página
        */
        public function show()
        {
            parent::show();
        }
        #endregion

        #region createPage
        private function createPage()
        {
            // Active Record
            $this->activeRecord = 'Cidade';
            
            #region Criar Formulario

            // instancia um formulário
            $this->form = new KaiokenFormWrapper(new Form('form_cidades'));
            $this->form->setTitle('Cadastro de Cidades');
                
            // cria os campos do formulário
            $codigo    = new Text('IdCidade');
            $codigo->maxlength = "15";

            $descricao = new Text('Descricao');
            $descricao->id = "txtDescricao";
            $descricao->maxlength = "100";

            $estado    = new Combo('IdEstado');
            $estado->id = "cbEstado";

            Transaction::open();

            $estados = Estado::all();
            $items = array();

            foreach ($estados as $obj_estado)
            {
                $items[$obj_estado->IdEstado] = $obj_estado->Descricao;
            }
            
            Transaction::close();
            
            $estado->addItems($items);

            $this->form->addField('Código', $codigo, '15%');
            $this->form->addField('Descrição', $descricao, '70%');
            $this->form->addField('Código IBGE', $codigoIBGE, '20%');
            $this->form->addField('Estado', $estado, '70%');
            
            $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
            $this->form->addAction('Cancelar', new Action(array($this, 'onEdit')));
            #endregion
            
            // monta a página através de uma tabela
            $box = new VBox;
            $box->style = 'display:block';
            $box->add($this->form);

            parent::add($box);
        }
        #endregion       

        #endregion
    }
</p>

<h2>:star: Creditos</h2>
<p>
 Quero agradecer a empresa <b>MSystem</b> pela oportunidade de desenvolver o <b>MS Accounting</b> e dar os créditos ao professor <b>Pablo Dall'Oglio</b> pelo excelente curso <b>(PHP - Programando com Orientação a Objetos e Design Patterns)</b> onde muitas funcionalidades deste framework foi inspirada nos ensinamentos adquiridos neste excelente curso e também agradecer ao professor <b>Alcyon Junior</b> pelo fantástico curso <b>Desenvolvimento Seguro Avançado,</b> onde me deu vários insights para aplicar <b>técnicas de desenvolvimento seguro</b>.
</p>
