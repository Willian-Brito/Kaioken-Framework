<?php

#region Imports
namespace KaiokenFramework\Database;

use Exception;
use KaiokenFramework\Enum\SqlCommandsEnum;
use KaiokenFramework\Log\LoggerHTML;
use KaiokenFramework\Log\LoggerTXT;
use KaiokenFramework\Log\LoggerXML;
use KaiokenFramework\Util\Util;
#endregion

/**
 * Permite definir um Active Record
 * @author Willian Brito (h1s0k4)
 */
abstract class Record implements IRecord
{
    #region Propriedade da Classe

    protected $data; // array contendo os dados do objeto
    protected $prepared; // array contendo os dados preparados para executar o comando sql
    protected $query;
    #endregion

    #region Construtor

    /**
     * Instancia um Active Record. Se passado o $id, já carrega o objeto
     * @param [$id] = ID do objeto
    */
    public function __construct($id = NULL)
    {
        if ($id)
        {
            // carrega o objeto correspondente
            $object = $this->load($id);

            if ($object)
                $this->fromArray($object->toArray());            
        }
    }
    #endregion
    
    #region Metodos

    #region [*] Magicos

    #region Set

    /**
     * Executado sempre que uma propriedade for atribuída.
    */
    public function __set($prop, $value)
    {
        // verifica se existe método set_<propriedade>
        if (method_exists($this, 'set_'.$prop))
        {
            // executa o método set_<propriedade>
            call_user_func(array($this, 'set_'.$prop), $value);
        }
        else
        {
            if ($value === NULL)
            {
                unset($this->data[$prop]);
            }
            else
            {
                // atribui o valor da propriedade
                $this->data[$prop] = $value;
            }
        }
    }
    #endregion
    
    #region Get

    /**
     * Executado sempre que uma propriedade for requerida
    */
    public function __get($prop)
    {
        // verifica se existe método get_<propriedade>
        if (method_exists($this, 'get_'.$prop))
        {
            // executa o método get_<propriedade>
            return call_user_func(array($this, 'get_'.$prop));
        }
        else
        {
            // retorna o valor da propriedade
            if (isset($this->data[$prop]))
            {
                return $this->data[$prop];
            }
        }
    }
    #endregion
    
    #region Isset

    /**
     * Retorna se a propriedade está definida
    */
    public function __isset($prop)
    {
        return isset($this->data[$prop]);
    }
    #endregion
    
    #region Clone

    /**
     * Limpa o ID para que seja gerado um novo ID para o clone.
    */
    public function __clone()
    {
        $Id = $this->getId();

        unset($this->data[$Id]);
    }
    #endregion

    #endregion

    #region [:] Estáticos

    #region executeFunction
    public static function executeFunction($function, $params)
    {
        if($conn = Transaction::get())
        {
            $paramString = self::prepareParams($params);
            $sql = "SELECT $function($paramString)";

            $cmd = $conn->prepare($sql);

            foreach($params as $key => $value)
            {
                $cmd->bindValue(":$key", $value);
            }

            $cmd->execute();

            return $cmd->fetch();
        }

        throw new Exception('Não há transação ativa');        
    }
    #endregion

    #region executeProcedure
    public static function executeProcedure($procedure, $params)
    {
        if($conn = Transaction::get())
        {
            $paramString = self::prepareParams($params);
            $sql = "CALL $procedure($paramString)";

            $cmd = $conn->prepare($sql);

            foreach($params as $key => $value)
            {
                $cmd->bindValue(":$key", $value);
            }

            $cmd->execute();

            return $cmd->fetchAll();
        }

        throw new Exception('Não há transação ativa');        
    }
    #endregion

    #region prepareParams
    private static function prepareParams($params)
    {
        $out = "";

        if(!empty($params))
        {
            $ultimoValor = end($params);
            $ultimaKey = key($params);
    
            foreach($params as $key => $value)
            {
                if($key == $ultimaKey)
                {
                    $out .= ":$key";
                    break;
                }
                
                $out .= ":$key,";
            }
        }

        return $out;
    }
    #endregion

    #endregion

    #region [+] Publicos

    #region fromArray

    /**
     * Preenche os dados do objeto com um array
    */
    public function fromArray($data)
    {
        $this->data = $data;
    }
    #endregion
    
    #region toArray

    /**
     * Retorna os dados do objeto como array
    */
    public function toArray()
    {
        return $this->data;
    }
    #endregion
    
    #region load

    /*
     * Recupera (retorna) um objeto da base de dados pelo seu ID
     * @param $id = ID do objeto
    */
    public function load($id)
    {
        $sql  = "SELECT * FROM {$this->getEntity()} WHERE {$this->getId()} = :id";
        
        // obtém transação ativa
        if ($conn = Transaction::get())
        {        
            #region Log
            $query = $this->getCmd(SqlCommandsEnum::Select);            
            Transaction::log($query);
            #endregion

            #region Comando
            $cmd = $conn->prepare($sql);
            $cmd->bindValue(":id", $id);
            $result = $cmd->execute();

            if($result)
                $object = $cmd->fetchObject(get_class($this));            

            return $object;
            #endregion
        }
        
        throw new Exception('Não há transação ativa!!');
    }
    #endregion
    
    #region save

    /**
     * Salva o objeto na base de dados
    */
    public function save()
    {
        $cmd = $this->create();        
        
        if ($conn = Transaction::get())
        {
            #region Log
            Transaction::log($this->query);
            #endregion

            #region Comando
            $cmd->execute();
            #endregion

            $newId = $conn->lastInsertId();

            return $newId;
        }

        throw new Exception('Não há transação ativa');
    }    
    #endregion

    #region delete

    /**
     * Exclui um objeto da base de dados através de seu ID.
     * @param $id = ID do objeto
    */
    public function delete($id = null)
    {

        $id = $id ? $id : (int) $this->data[$this->getId()];
        $sql  = "DELETE FROM {$this->getEntity()} WHERE {$this->getId()} = :id";

        if ($conn = Transaction::get())
        {
            #region Log
            $query = $this->getCmd(SqlCommandsEnum::Delete);
            Transaction::log($query);
            #endregion

            #region Comando
            $cmd = $conn->prepare($sql);
            $cmd->bindValue(":id", $id);
            $result = $cmd->execute();
            #endregion

            return $result;
        }

        throw new Exception('Não há transação ativa!!');
    }
    #endregion
 
    #region find

    /**
     * Busca um objeto pelo id
    */
    public static function find($id)
    {
        // Retorna nome da classe filha
        $classname = get_called_class();
        
        $activeRecord = new $classname;
        return $activeRecord->load($id);
    }
    #endregion

    #region all

    /**
     * Retorna todos objetos
    */
    public static function all()
    {
        $classname = get_called_class();
        $rep = new Repository($classname);
        
        return $rep->load(new Criteria);
    }
    #endregion

    #region getEntity

    /**
     * Retorna o nome da entidade (tabela)
    */
    public function getEntity()
    {
        // obtém o nome da classe
        $class = get_class($this);
        
        // retorna a constante de classe TABLENAME
        return constant("{$class}::TABLENAME");
    }
    #endregion
    
    #region getId

    /**
    * Retorna o nome do ID da classe
    */
    public function getId()
    {
        return "Id{$this->getEntity()}";
    }
    #endregion

    #endregion

    #region [-] Privados

    #region create
    private function create()
    {
        $Id = $this->getId();

        if (empty($this->data[$Id]) OR (!$this->load( $this->data[$Id] )))
        {
            $cmd = $this->insert();
        }
        else
        {
            $cmd = $this->update();
        }

        return $cmd;
    }
    #endregion

    #region insert

    /**
     * Monta comando insert
    */
    private function insert()
    {
        $this->prepared = $this->prepareDataInsert();
        $this->query = $this->getCmd(SqlCommandsEnum::Insert);
        $query = "INSERT INTO {$this->getEntity()} ({$this->prepared[0]}) VALUES ({$this->prepared[1]})";

        if($conn = Transaction::get())
        {
            $cmd = $conn->prepare($query);

            for($i = 0; $i < count($this->prepared[2]); $i++)
            {
                $column = $this->prepared[2][$i];
                $value = $this->prepared[3][$i];

                $cmd->bindValue("{$column}", $value);
            }

            return $cmd;
        }
    
    }
    #endregion

    #region update

    /**
     * Monta comando update
    */
    private function update()
    {        
        $this->prepared = $this->prepareDataUpdate();
        $IdTabela = $this->getId();
        $id = (int) $this->data[$IdTabela];

        $query = "UPDATE {$this->getEntity()} SET {$this->prepared[0]}  WHERE {$IdTabela} = :id";
        $this->query = $this->getCmd(SqlCommandsEnum::Update, $id);

        if($conn = Transaction::get())
        {
            #region Atribui Parametro do Comando WHERE
            $cmd = $conn->prepare($query);
            $cmd->bindValue(":id", $id);
            #endregion
    
            #region Atribui Parametro do Comando SET
            for($i = count($this->prepared[1]) -1; $i >= 0; $i--)
            {
                $column = $this->prepared[1][$i];
                $value = $this->prepared[2][$i];

                $cmd->bindValue("{$column}", $value);
                $this->query = str_replace("{$column}","{$value}", $this->query);
            }
            #endregion
            
            return $cmd;
        }
    }
    #endregion
       
    #region getLastId

    /**
     * Retorna o último ID
    */
    public function getLastId()
    {
        // inicia transação
        if ($conn = Transaction::get())
        {
            // instancia instrução de SELECT
            $Id = $this->getId();
            $sql  = "SELECT max({$Id}) FROM {$this->getEntity()}";
            
            // cria log e executa instrução SQL
            Transaction::log($sql);
            $result= $conn->query($sql);

            // retorna os dados do banco
            $row = $result->fetch();
            return $row[0];
        }
        else
        {
            // se não tiver transação, retorna uma exceção
            throw new Exception('Não há transação ativa!!');
        }
    }
    #endregion

    #region PrepareComamands

    #region Insert
    private function prepareDataInsert()
    {
        $strKeys = "";
        $strBinds = "";
        $binds = [];
        $values = [];

        foreach ($this->data as $key => $value)
        {
            $strKeys = "{$strKeys},{$key}";
            $strBinds = "{$strBinds},:{$key}";
            $binds[] = ":{$key}";
            $values[] = $value;
        }

        $strKeys = substr($strKeys, 1);
        $strBinds = substr($strBinds, 1);

        return [$strKeys, $strBinds, $binds, $values];
    }
    #endregion 

    #region Update
    private function prepareDataUpdate()
    {
        $strKeysBinds = "";
        $binds = [];
        $values = [];
        $query = "";

        foreach ($this->data as $key => $value)
        {
            $strKeysBinds = "{$strKeysBinds},{$key}=:{$key}";
            $binds[] = ":{$key}";
            $values[] = $value;
        }

        $strKeysBinds = substr($strKeysBinds, 1);

        return [$strKeysBinds, $binds, $values, $query];
    }
    #endregion

    #endregion

    #region getCmd
    private function getCmd($tipoCmd, $id = null)
    {
        switch ($tipoCmd)
        {
            #region Insert
            case SqlCommandsEnum::Insert: 

                $query = "INSERT INTO {$this->getEntity()} ({$this->prepared[0]})".
                              ' VALUES (' . implode(', ', array_values($this->prepared[3])) . ')';
                return $query;
            #endregion

            #region Update
            case SqlCommandsEnum::Update: 
                $query = "UPDATE {$this->getEntity()} SET {$this->prepared[0]}  WHERE {$this->getId()} = {$id}";
                return $query;
            #endregion

            #region Delete
            case SqlCommandsEnum::Delete: 
                $this->query = "DELETE FROM {$this->getEntity()} WHERE {$this->getId()} = {$this->data[$this->getId()]}";
                return $this->query;
            #endregion

            #region Select
            case SqlCommandsEnum::Select: 
                $query = "SELECT * FROM {$this->getEntity()} WHERE {$this->getId()}=" . (int) $id;
                return $query;
            #endregion
        }
    }
    #endregion

    #endregion

    #endregion   
}