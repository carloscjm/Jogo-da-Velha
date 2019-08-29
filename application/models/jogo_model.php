<?php
class jogo_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertJogada($linha, $coluna, $acao = 2, $caractere)
    {
        if ($acao == 0) {
            $jogador = 'Player';
        } else {
            $jogador = 'Maquina';
        }

        $this->db->set('data', 'NOW()', false);
        $this->db->set('jogo', $_SESSION['nomeJogo']);
        $this->db->set('linha', $linha);
        $this->db->set('coluna', $coluna);
        $this->db->set('caractere', $caractere);
        $this->db->insert('log');
    }

    public function insertVencedor($linha, $coluna, $resultado, $caractere)
    {
        $this->db->set('data', 'NOW()', false);
        $this->db->set('jogo', $_SESSION['nomeJogo']);
        $this->db->set('linha', $linha);
        $this->db->set('coluna', $coluna);
        $this->db->set('caractere', $caractere);
        $this->db->set('vencedor', $resultado);
        $this->db->insert('log');

    }
}
