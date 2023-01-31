<?php

namespace KaiokenFramework\Components\Datagrid;

use KaiokenFramework\Page\Action;

/**
 * Representa a paginação de uma datagrid
 * @author Willian Brito (h1s0k4)
 */
class PageNavigation
{
    #region Propriedades da Classe

    private $action;
    private $pageSize;
    private $currentPage;
    private $totalRecords;
    #endregion
    
    #region Construtor
    public function __construct()
    {
        $this->pageSize = 10;
    }
    #endregion
    
    #region Metodos

    #region setAction

    function setAction(Action $action)
    {
        $this->action = $action;
    }
    #endregion
    
    #region setPageSize

    function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }
    #endregion
    
    #region setCurrentPage

    function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }
    #endregion
    
    #region setTotalRecords

    function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
    }
    #endregion
    
    #region show

    function show()
    {
        $pages = ceil($this->totalRecords / $this->pageSize);
        
        echo '<ul class="pagination">';
        for ($n=1; $n <= $pages; $n++)
        {
            $offset = ($n -1) * $this->pageSize;
            
            $action = $this->action;
            $action->setParameter('offset', $offset);
            $action->setParameter('page',   $n);
            
            $url = $action->serialize();
            $class = ($this->currentPage == $n) ? 'active' : '';
            
            echo "<li class='{$class}'>";
            echo "<a href='$url'>{$n}</a>&nbsp;&nbsp;";
            echo '</li>';
            
        }
        echo '</ul>';
    }
    #endregion

    #endregion
}