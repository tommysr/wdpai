<?php

require_once "AppController.php";

class AuthController extends AppController
{
  public function __construct()
  {
    parent::__construct();
  }

  public function login() {
    if (!$this -> isPost()) {
      return $this-> render('login', ['title' => 'Sign in']);
    }

    // TODO: Handle sign in
  }

  public function register() {
    if (!$this -> isPost()) {
      return $this->render('register', ['title' => 'Sign up']);
    }

    // TODO: Handle register
  }

}