<!DOCTYPE html>
<html lang="pt-br">

    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/')?>bootstrap/css/bootstrap.min.css">
        <link href="<?php echo base_url('assets/')?>fontawesome-free-5.0.1/css/fontawesome-all.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/')?>css/style.css">
        <script src="<?php echo base_url('assets/')?>js/jquery-3.3.1.min.js"></script>
        <script src="<?php echo base_url('assets/')?>js/scripts.js"></script>
    </head>
    <div class="container">
    <div class="row">
        <div class="col">
            <div class="align-center">
                <span>Dificuldade:</span>
                <button id="1" type="button" class="btn btn-primary margin" onclick='dificuldade(this.id);'>Fácil</i></button>
                <button id="2" type="button" class="btn btn-primary margin" onclick='dificuldade(this.id);'>Médio</i></button>
                <button id="3" type="button" class="btn btn-primary margin" onclick='dificuldade(this.id);'>Difícil</i></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="align-center">
                <span>Caractere:</span>
                <button id="o" type="button" class="btn btn-primary margin" onclick='caractere(this.id);'><i class="far fa-circle"></i></button>
                <button id="x" type="button" class="btn btn-primary margin" onclick='caractere(this.id);'><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="align-center">
                <a href='/jogodavelha/jogo'><button type="button" class="btn btn-primary play" >Play</button><a>
            </div>
        </div>
    </div>
</html>