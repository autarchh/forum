<?php
namespace App\Service;

interface ManagerInterface
{
    public function findAll();

    public function findOneById($id);
}