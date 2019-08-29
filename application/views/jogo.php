<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/')?>bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/')?>css/style.css">
        <link href="<?php echo base_url('assets/')?>fontawesome-free-5.0.1/css/fontawesome-all.css" rel="stylesheet" type="text/css">
        <script src="<?php echo base_url('assets/')?>js/jquery-3.3.1.min.js"></script>
        <script src="<?php echo base_url('assets/')?>js/scripts.js"></script>
    </head>
    <div class="container" style='margin-top:20px;'>
        <div class="row">
            <div class='col-sm'>
                <table class="table" style= 'width:20%;'> <?php
                        $div= 'borda-direita borda-inferior';
                        for ($linha=0; $linha<3; $linha++) {?><tr><?php
                            for ($coluna=0; $coluna<3; $coluna++) {
                                if($linha== 2){
                                    $div = 'borda-direita';
                                } else
                                if($coluna==2) {
                                    $div = 'borda-inferior';
                                }else $div= 'borda-direita borda-inferior';
                                if($linha== 2 && $coluna==2){
                                    $div = 'borda';
                                }?>
                                <td class='<?=$div?>'>
                                    <div id='<?=$linha?>_<?=$coluna?>' class='campo' onclick='selecionaCampo(this.id);'>
                                        <?=$table[$linha][$coluna]?>
                                    </div>
                                </td>
                                <?php
                            }?></tr><?php
                        }?>
                    </tr>
                </table>
            </div>
            <div class='col-sm'>
                <h3>Nível de dificuldade: <?=$nivel?></h3>
                <a href='/jogodavelha/'><button id="o" type="button" class="btn btn-primary back-home">Voltar ao início</button></a>
            </div>
        </div>
    </div>
</html>