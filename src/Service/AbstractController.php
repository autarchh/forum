<?php
namespace App\Service;

abstract class AbstractController implements ControllerInterface
{
    protected function render($view, $data = null){
        return [
            "view" => $view, 
            "data" => $data
        ];
    }

    protected function isGranted($role){
        return Session::isRoleUser($role);
    }

    protected function redirectTo($route){
        return Router::redirect($route);
    }

    protected function addFlash($type, $message){
        Session::setMessage($type, $message);
    }

    protected function logUser($user){
        Session::setUser($user);
    }

    protected function logoutUser(){
        Session::removeUser();
    }
    
}