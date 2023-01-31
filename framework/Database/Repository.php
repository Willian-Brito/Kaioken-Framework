<?php

namespace KaiokenFramework\Database;

use Exception;

/**
 * Manipular coleções de objetos.
 * @author Willian Brito (h1s0k4)
 */
final class Repository
{
    #region Propriedades da Classe

    private $activeRecord; // classe manipulada pelo repositório
    #endregion

    #region Construtor

    /**
     * Instancia um Repositório de objetos
     * @param $class = Classe dos Objetos
     */
    function __construct($class)
    {
        $this->activeRecord = $class;
    }
    #endregion

    #region Metodos

    #region load

    /**
     * Carrega um conjunto de objetos (collection) da base de dados
     * @param $criteria = objeto do tipo Criteria
    */
    function load(Criteria $criteria)
    {

        $table = constant($this->activeRecord.'::TABLENAME');
        $sql = "SELECT * FROM " . $table;

        #region Monta Comando SQL
        if ($criteria)
        {
            $expression = $criteria->dump();

            // Obtém as propriedades do critério
            $order = $criteria->getProperty('order');
            $limit = $criteria->getProperty('limit');
            $offset= $criteria->getProperty('offset');

            if ($expression)
                $sql .= ' WHERE ' . $expression;

            if ($order)
                $sql .= ' ORDER BY ' . $order;
            
            if ($limit)
                $sql .= ' LIMIT ' . $limit;
            
            if ($offset)
                $sql .= ' OFFSET ' . $offset;            
        }
        #endregion
        
        #region Executa Comando SQL
        if ($conn = Transaction::get())
        {
            #region Log
            $query = $criteria->getCommand($sql);
            Transaction::log($query);
            #endregion

            #region Comando
            $results = array();

            $cmd = $conn->prepare($sql);
            $cmd = $criteria->prepareBinds($cmd);
            $result = $cmd->execute();            
            
            if ($result)
            {
                // percorre os resultados da consulta, retornando um objeto
                while ($row = $cmd->fetchObject($this->activeRecord))
                {
                    // armazena no array $results;
                    $results[] = $row;
                }
            }
            #endregion

            return $results;
        }
        #endregion

        throw new Exception('Não há transação ativa!!');
    }
    #endregion

    #region delete

    /**
     * Excluir um conjunto de objetos (collection) da base de dados
     * @param $criteria = objeto do tipo Criteria
    */
    function delete(Criteria $criteria)
    {
        $expression = $criteria->dump();
        $sql = "DELETE FROM " . constant($this->activeRecord.'::TABLENAME');
        
        if ($expression)
            $sql .= ' WHERE ' . $expression;        
        
        // obtém transação ativa
        if ($conn = Transaction::get())
        {
            #region Log
            $query = $criteria->getCommand($sql);
            Transaction::log($query);
            #endregion
            
            #region Comando
            $cmd = $conn->prepare($sql);
            $cmd = $criteria->prepareBinds($cmd);

            $result = $cmd->execute();
            #endregion

            return $result;
        }
        
        throw new Exception('Não há transação ativa!!');
    }
    #endregion

    #region count

    /**
     * Retorna a quantidade de objetos da base de dados
     * que satisfazem um determinado critério de seleção.
     * @param $criteria = objeto do tipo Criteria
    */
    function count(Criteria $criteria)
    {
        $expression = $criteria->dump();
        $sql = "SELECT count(*) FROM " . constant($this->activeRecord.'::TABLENAME');

        if ($expression)
            $sql .= ' WHERE ' . $expression;        
        
        // obtém transação ativa
        if ($conn = Transaction::get())
        {
            #region Log
            $query = $criteria->getCommand($sql);
            Transaction::log($query);
            #endregion
            
            #region Comando
            $cmd = $conn->prepare($sql);
            $cmd = $criteria->prepareBinds($cmd);

            $result = $cmd->execute();

            if ($result)
                $row = $cmd->fetch();
            #endregion
            
            return $row[0];
        }
        
        throw new Exception('Não há transação ativa!!');
    }
    #endregion

    #endregion    
}
