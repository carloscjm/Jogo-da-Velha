<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->model('produto_model', 'modelproduto');
    }

    public function index()
    {
        $this->load->view('home');
    }

    public function sessionDificuldade()
    {
        $nivel = $this->input->post('nivel');
        if (!isset($_SESSION)) { //Verificar se a sessão não já está aberta.
            session_start();
        }
        $_SESSION['nivel'] = $nivel;
        echo json_encode($_SESSION['nivel']);
    }
}
