<?php
namespace App\Service;

class Paging
{
    private $totalPages;
    private $nbElements;
    private $baseRoute;

    public function __construct($baseRoute, $nbElements){
        $this->baseRoute  = $baseRoute;
        $this->nbElements = $nbElements;
        $this->totalPages = ceil($this->nbElements/getenv("PER_PAGE"));
    }
    /**
     * récupère la page courante 
     * 
     * @return int - le numéro de la page courante
     */
    public function getPage(){
        return intval($_GET['page'] ?? 1);
    }

    /**
     * récupère le nombre d'éléments paginés 
     * 
     * @return int - le nombre d'éléments paginés
     */
    public function getNbElements(){
        return $this->nbElements;
    }

    /**
     * vérifie si la page actuelle est la dernière affichable
     * 
     * @return bool - true si la page courante est la dernière page, false sinon
     */
    public function isLastPage(){
        return $this->getPage() == $this->totalPages;
    }

    /**
     * génère la ligne SQL permettant à une requète de limiter le nombre de résultats en fonction de la page courante
     * 
     * @return string - la ligne SQL sous la forme "LIMIT X OFFSET Y" où X est le nombre d'éléments à afficher et Y le nombre d'éléments à ignorer
     * ex : LIMIT 5 OFFSET 10 garde 5 résultats en omettant les 10 premiers (donc du 11eme au 15eme élément)
     */
    public function paginateSQL(){
        $offset = ($this->getPage()-1) * getenv("PER_PAGE");
        return "LIMIT ".getenv("PER_PAGE")." OFFSET ".$offset;
    }

    /**
     * génère et affiche le HTML de la pagination à destination d'une vue
     * 
     * @param string $baseRoute - l'url à intégrer à chaque lien généré par le paginateur
     * @param int $nbElements - le nombre d'éléments total affichable (sera divisé par le nombre de pages généré)
     * 
     * @return void
     */
    public function getHTML(){
         
        $currentPage = $this->getPage();
        
        $html = "<section class='pagination'><ul>";
                
            if($this->totalPages > 1 && $currentPage > 1){
                $html.= "<li class='pagination-arrow'>".
                        "<a href='".$this->baseRoute."&page=1'>".
                            "<i class='fas fa-step-backward'></i>".
                        "</a>".
                        "<a href='".$this->baseRoute."&page=".($currentPage-1)."'>".
                                "<i class='fas fa-chevron-left'></i>".
                        "</a>".
                    "</li>";
                       
            }
            
            for($page = 1; $page <= $this->totalPages; $page++){
                $html.= "<li ". ($currentPage == $page ? "class='active'" : "") . ">".
                        "<a href=". $this->baseRoute. "&page=". $page. ">".
                            $page.
                        "</a>".
                    "</li>";
                        
            }
                
            if($this->totalPages > 1 && $currentPage < $this->totalPages){
                $html.= "<li class='pagination-arrow'>".
                        "<a href='". $this->baseRoute. "&page=".($currentPage+1). "'>".
                            "<i class='fas fa-chevron-right'></i>".
                        "</a>".
                        "<a href='". $this->baseRoute. "&page=". $this->totalPages. "'>".
                            "<i class='fas fa-step-forward'></i>".
                        "</a>".
                    "</li>";
            }

        $html.= "</ul></section>";

        return $html;
    }
}