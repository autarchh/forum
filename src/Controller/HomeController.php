<?php
namespace App\Controller;

use App\Service\AbstractController;

class HomeController extends AbstractController
{
    public function __construct()
    {

    }
    
    public function index(): array
    {
        return $this->render("home/home.php"); 
    }

}