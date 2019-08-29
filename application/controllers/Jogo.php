<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Jogo extends CI_Controller
{

    public function __construct($table = [])
    {
        parent::__construct();
        $this->load->model('jogo_model', 'gravaLog');
    }

    public function index()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['nivel'])) {
            $_SESSION['nivel'] = 1;
        }
        $date = date('Y-m-d H:i:s');
        $_SESSION['nomeJogo'] = 'jogo_' . $date;
        $dados['nivel'] = $_SESSION['nivel'];
        $dados['table'] = $tabela['tabela'] = $this->initTable(); //Cria tabela
        $_SESSION['tabela'] = $tabela['tabela']; //Passa a tabela para sessao
        $this->load->view('jogo', $dados);
    }

    public function controlador()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $linha = $this->input->post('linha');
        $coluna = $this->input->post('coluna');
        $caractere = $this->input->post('caractere');
        if ($caractere == '') {
            $caractere = 'x';
        }
        $caractereMaquina = $caractere == 'x' ? 'o' : 'x';
        if ($_SESSION['tabela'][$linha][$coluna] == '_') {

            $_SESSION['tabela'][$linha][$coluna] = $caractere;
            $this->gravaLog->insertJogada($linha, $coluna, 0, $caractere); //Grava o log das jogadas
            if ($this->fimDeJogo()) //verifica se houve um empate
            {
                if ($this->vencedor() == $caractere) {
                    $vencedor = 0;
                    $this->gravaLog->insertVencedor($linha, $coluna, 'Player venceu o jogo.', $caractere);
                } else {
                    $vencedor = 2;
                    $this->gravaLog->insertVencedor($linha, $coluna, 'Empate', $caractereMaquina);
                }
                $posicao = [
                    'view' => null,
                    'vencedor' => $vencedor,
                ];
            } else {
                $posicao = $this->maquina($caractere);
            }
            //Da inicio a jogada da maquina
            echo json_encode($posicao); //Retorna ovalor para a view

        }
    }

    public function maquina($caractere) //Controla ajogada da maquina
    {
        $caractereMaquina = $caractere === 'x' ? 'o' : 'x';
        $posicao = $this->posicao($caractereMaquina); //Chama a funçao que retorna a posiçao que a maquina vai jogar
        switch ($posicao) {
            case 1:$l = 0;
                $c = 0;
                break;
            case 2:$l = 0;
                $c = 1;
                break;
            case 3:$l = 0;
                $c = 2;
                break;
            case 4:$l = 1;
                $c = 0;
                break;
            case 5:$l = 1;
                $c = 1;
                break;
            case 6:$l = 1;
                $c = 2;
                break;
            case 7:$l = 2;
                $c = 0;
                break;
            case 8:$l = 2;
                $c = 1;
                break;
            case 9:$l = 2;
                $c = 2;
                break;
        }
        if ($_SESSION['tabela'][$l][$c] == '_') {
            $_SESSION['tabela'][$l][$c] = $caractereMaquina; //Adiciona ajogada da maquina na sessão
            $this->gravaLog->insertJogada($l, $c, 1, $caractereMaquina); //Grava o log das jogadas

            if ($this->vencedor() == $caractereMaquina) { //Verifica se ha um vencedor
                $vencedor = 1;
                $this->gravaLog->insertVencedor($l, $c, 'Maquina venceu o jogo.', $caractereMaquina);
            } else if ($this->vencedor() == $caractere) {
                $vencedor = 0;
            } else {
                $vencedor = 3;
            }
            $print = [
                'view' => $l . '_' . $c,
                'vencedor' => $vencedor,
            ];
            return $print; //Retorna o valor que irá para a view
        } else {
            return false;
        }

    }

    public function posicao($caractereMaquina) //Retorna a posição em que a maquina vai joga
    {
        $tabela = $_SESSION['tabela'];
        $dificuldade = $_SESSION['nivel'];

        if ($dificuldade == '1') {
            return $this->facil($tabela, $caractereMaquina);
        }

        if ($dificuldade == '2') {
            return $this->medio($tabela, $caractereMaquina);
        }

        if ($dificuldade == '3') {
            return $this->dificil($tabela, $caractereMaquina);
        }
    }

    private function facil($tabela, $caractereMaquina) //Configurações de dificuldade do nivel facil
    {
        $verificaCentro = $this->selecaoAleatoria(0, 3);
        $verificaLaterais = $this->selecaoAleatoria(0, 2);

        $movimento = $this->podePerder($tabela, $caractereMaquina); //verifica se o jogador pode ganhar na proxima jogada
        if ($movimento !== null) {
            return $movimento;
        }

        $movimento = $this->podeGanhar($tabela, $caractereMaquina); //maquina verifica se pode ganhar
        if ($movimento !== null) {
            return $movimento;
        }

        if ($verificaLaterais == 1) {
            $movimento = $this->verificaLaterais($tabela, $caractereMaquina); //Verifica os cantos
            if ($movimento !== null) {
                return $movimento;
            }
        }

        if ($verificaCentro == 1) {
            $movimento = $this->verificaCentro($tabela, $caractereMaquina); //Verifica o centro da matriz
            if ($movimento !== null) {
                return $movimento;
            }
        }

        $movimento = $this->verificaDisponibilidade($tabela, $caractereMaquina); //Verifica qualquer outra posiçao para jogar
        if ($movimento !== null) {
            return $movimento;
        }

    }

    private function medio($tabela, $caractereMaquina) //Configurações de dificuldade do nivel medio
    {

        $verificaDiagonal = $this->selecaoAleatoria(0, 1);

        $movimento = $this->podeGanhar($tabela, $caractereMaquina); //maquina verifica se pode ganhar
        if ($movimento !== null) {
            return $movimento;
        }

        $movimento = $this->podePerder($tabela, $caractereMaquina); //verifica se ojogador podeganhar naproxima jogada
        if ($movimento !== null) {
            return $movimento;
        }

        if ($verificaDiagonal == 0) {
            $movimento = $this->verificaDiagonal($tabela, $caractereMaquina);
            if ($movimento !== null) {
                return $movimento;
            }
        }

        if ($verificaDiagonal == 0) {
            $movimento = $this->verificaLaterais($tabela, $caractereMaquina); //Verifica os cantos
            if ($movimento !== null) {
                return $movimento;
            }
        }

        $movimento = $this->verificaCentro($tabela, $caractereMaquina); //Verifica o centro da matriz
        if ($movimento !== null) {
            return $movimento;
        }

        $movimento = $this->verificaDisponibilidade($tabela, $caractereMaquina); //Verifica qualquer outra posiçao para jogar
        if ($movimento !== null) {
            return $movimento;
        }

    }

    private function dificil($tabela, $caractereMaquina) //Configurações de dificuldade do nivel difici
    {
        $verificaDiagonal = $this->selecaoAleatoria(0, 1);

        $movimento = $this->podeGanhar($tabela, $caractereMaquina); //maquina verifica se pode ganhar
        if ($movimento !== null) {
            return $movimento;
        }

        $movimento = $this->podePerder($tabela, $caractereMaquina); //verifica se ojogador podeganhar naproxima jogada
        if ($movimento !== null) {
            return $movimento;
        }
        $movimento = $this->verificaCentro($tabela, $caractereMaquina); //Verifica o centro da matriz
        if ($movimento !== null) {
            return $movimento;
        }

        if ($verificaDiagonal == 1) {
            $movimento = $this->verificaDiagonal($tabela, $caractereMaquina);
            if ($movimento !== null) {
                return $movimento;
            }
        }

        $movimento = $this->verificaLaterais($tabela, $caractereMaquina); //Verifica os cantos
        if ($movimento !== null) {
            return $movimento;
        }

        $movimento = $this->verificaDisponibilidade($tabela, $caractereMaquina); //Verifica qualquer outra posiçao para jogar
        if ($movimento !== null) {
            return $movimento;
        }

    }

    public function selecaoAleatoria($min, $max)
    {
        return rand($min, $max);

    }

    public function podeGanhar($tabela, $caractereMaquina) //Verifica se a maquina pode ganhar na proxima jogada
    {
        // verifica horizontal
        for ($i = 0; $i < 3; $i++) {
            $count = 0;
            for ($j = 0; $j < 3; $j++) {
                if ($tabela[$i][$j] === $caractereMaquina) {
                    $count++;
                } elseif ($tabela[$i][$j] === '_') {
                    $posicao = $this->seleciona($i, $j);
                } else {
                    $count = 0;
                    break;
                }
            }
            if ($count == 2) {
                return $posicao;
            }
        }

        // verifica vertical
        for ($i = 0; $i < 3; $i++) {
            $count = 0;
            for ($j = 0; $j < 3; $j++) {
                if ($tabela[$j][$i] === $caractereMaquina) {
                    $count++;
                } elseif ($tabela[$j][$i] === '_') {
                    $posicao = $this->seleciona($j, $i);
                } else {
                    $count = 0;
                    break;
                }
            }
            if ($count == 2) {
                return $posicao;
            }
        }

        //primeira diagonal
        $count = 0;
        for ($i = 0; $i < 3; $i++) {
            if ($tabela[$i][$i] === $caractereMaquina) {
                $count++;
            } elseif ($tabela[$i][$i] === '_') {
                $posicao = $this->seleciona($i, $i);
            } else {
                $count = 0;
                break;
            }
        }
        if ($count == 2) {
            return $posicao;
        }

        // segunda diagonal
        $count = 0;
        for ($i = 0; $i < 3; $i++) {
            if ($tabela[$i][2 - $i] === $caractereMaquina) {
                $count++;
            } elseif ($tabela[$i][2 - $i] === '_') {
                $posicao = $this->seleciona($i, 2 - $i);
            } else {
                $count = 0;
                break;
            }
        }

        if ($count == 2) {
            return $posicao;
        }
    }

    private function podePerder($tabela, $caractereMaquina) //Verifica se a maquina pode perder na proxima jogada
    {
        $caractere = $caractereMaquina === 'x' ? 'o' : 'x';
        return $this->podeGanhar($tabela, $caractere);
    }

    private function verificaLaterais($tabela, $caractereMaquina) //Verifica disponibilidade das lateraisda tabela
    {
        $caractereJogador = $caractereMaquina == 'x' ? 'o' : 'x';
        $cantos = [
            $tabela[0][0],
            $tabela[0][2],
            $tabela[2][0],
            $tabela[2][2],
        ];
        $contador = 0;
        foreach ($cantos as $canto) {
            if ($canto === '_') {
                $contador++;
            }
        }

        if ($contador == 4) {
            $numeroCanto = rand(0, 3);
            switch ($numeroCanto) {
                case 0:return 9;
                case 1:return 7;
                case 2:return 3;
                case 3:return 1;
            }
        }

        if ($contador == 3) {
            $numeroCanto = 1;
            $disponivel = [];

            foreach ($cantos as $key => $canto) {
                if ($canto !== '_') {
                    array_push($disponivel, $key);
                }
            }
            $numeroCanto = $disponivel[array_rand($disponivel)];
            switch ($numeroCanto) {
                case 0:return 9;
                case 1:return 7;
                case 2:return 3;
                case 3:return 1;
            }
        }

        if ($contador >= 2 and $tabela[1][1]) {
            $numeroCanto = 1;
            $disponivel = [];

            if ($tabela[0][1] === '_' and ($tabela[0][0] == $caractereJogador or $tabela[0][2] == $caractereJogador)) {
                array_push($disponivel, 2);
            }
            if ($tabela[1][0] === '_' and ($tabela[0][0] == $caractereJogador or $tabela[2][0] == $caractereJogador)) {
                array_push($disponivel, 4);
            }
            if ($tabela[1][2] === '_' and ($tabela[0][2] == $caractereJogador or $tabela[2][2] == $caractereJogador)) {
                array_push($disponivel, 6);
            }
            if ($tabela[2][1] === '_' and ($tabela[2][0] == $caractereJogador or $tabela[2][2] == $caractereJogador)) {
                array_push($disponivel, 8);
            }
            return $disponivel[array_rand($disponivel)];
        }

    }

    private function verificaCentro($tabela, $caractereMaquina) //Verifica a disponibilidade do centro da tabela
    {
        if ($tabela[1][1] === '_') {
            return 5;
        }
    }

    private function verificaDisponibilidade($tabela, $caractereMaquina) //Verifica outras possibilidades de jogada
    {
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                if ($tabela[$i][$j] === '_') {
                    return $this->seleciona($i, $j);
                }
            }
        }
    }

    private function verificaDiagonal($tabela, $caractereMaquina)
    {
        $vertical1 = 0;
        $vertical2 = 0;
        $sorteio1 = array(3, 7);
        $sorteio2 = array(1, 9);

        for ($i = 0; $i < 3; $i++) {
            if ($tabela[$i][$i] != '_') {
                $vertical1++;
            }
        }

        if ($vertical1 < 3) {
            $this->verificaLaterais($tabela, $caractereMaquina);
        } else {
            if ($tabela[0][2] != '_' || $tabela[2][0] != '_') {
                $this->verificaLaterais($tabela, $caractereMaquina);
            } else {
                $key = array_rand($sorteio1, 1);
                return $sorteio1[$key];
            }
        }

        for ($i = 0; $i < 3; $i++) {
            if ($tabela[$i][2 - $i] != "_") {
                $vertical2++;
            }
        }

        if ($vertical2 < 3) {
            $this->verificaLaterais($tabela, $caractereMaquina);
        } else {
            if ($tabela[0][0] != '_' || $tabela[2][2] != '_') {
                $this->verificaLaterais($tabela, $caractereMaquina);
            } else {
                $key = array_rand($sorteio2, 1);
                return $sorteio2[$key];
            }
        }
    }

    private function seleciona($i, $j)
    {
        return $i * 3 + $j % 3 + 1;
    }

    private function initTable()
    {
        $table = [];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $table[$i][$j] = '_';
            }
        }
        return $table;
    }

    public function fimDeJogo() //verifica empate
    {
        $tabela = $_SESSION['tabela'];
        $vencedor = $this->vencedor();
        if ($vencedor !== '_' && $vencedor != null) {
            return true;
        }

        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                if ($tabela[$i][$j] === '_') {
                    return false;
                }
            }
        }
        return true;
    }

    //Funções para verificar se houve um vencedor
    public function vencedor()
    {

        if ($vencedor = $this->horizontal()) {
            return $vencedor;
        }

        if ($vencedor = $this->vertical()) {
            return $vencedor;
        }

        if ($vencedor = $this->diagonal()) {
            return $vencedor;
        }
    }

    private function horizontal()
    {
        $tabela = $_SESSION['tabela'];
        for ($i = 0; $i < 3; $i++) {
            $vencedor = $tabela[$i][0];

            for ($j = 0; $j < 3; $j++) {
                if ($tabela[$i][$j] != $vencedor) {
                    $vencedor = null;
                    break;
                }
            }
            if ($vencedor !== null) {
                break;
            }
        }
        return $vencedor;
    }

    private function vertical()
    {
        $tabela = $_SESSION['tabela'];
        for ($i = 0; $i < 3; $i++) {
            $vencedor = $tabela[0][$i];

            for ($j = 0; $j < 3; $j++) {
                if ($tabela[$j][$i] != $vencedor) {
                    $vencedor = null;
                    break;
                }
            }
            if ($vencedor !== null) {
                break;
            }
        }
        return $vencedor;
    }

    private function diagonal()
    {
        $tabela = $_SESSION['tabela'];
        $vencedor = $tabela[0][0];
        for ($i = 0; $i < 3; $i++) {
            if ($tabela[$i][$i] != $vencedor) {
                $vencedor = null;
                break;
            }
        }

        if ($vencedor === null) {
            $vencedor = $tabela[0][2];
            for ($i = 0; $i < 3; $i++) {
                if ($tabela[$i][2 - $i] != $vencedor) {
                    $vencedor = null;
                    break;
                }
            }
        }

        return $vencedor;
    }

}
