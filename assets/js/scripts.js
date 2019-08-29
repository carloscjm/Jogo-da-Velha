function selecionaCampo(linha_coluna)
{
    var element = document.getElementById(linha_coluna);
    element.onclick = 0;
    var url = 'Jogo/controlador';
    var caractere = localStorage.getItem('caractere');
    var posicoes = linha_coluna.split('_');
    $('#'+linha_coluna).css('color', 'black');

    if(caractere == 'o')
    {
        element.innerHTML = '<i id ="show-circle" class="far fa-circle"></i>';

    }else
        element.innerHTML = '<i id ="show-circle" class="fas fa-times"></i>';

    $.ajax({
        url: url,
        type: "post",
        dataType: "json",
        data: {linha: posicoes[0], coluna:posicoes[1], caractere:caractere},
        success: function(json)
        {
            if(json['view']!=null){

                var element = document.getElementById(json['view']);
                element.onclick = 0;
                $('#'+json['view']).css('color', 'black');
                if(caractere == 'o'){

                    element.innerHTML = '<i id ="show-circle" class="fas fa-times"></i>';
                    
                }else 
                    element.innerHTML = '<i id ="show-circle" class="far fa-circle"></i>';

            }
            if(json['vencedor'] == 2){

                alert('Empate! :|');
                location.reload();

            }else if(json['vencedor'] == 0){

                alert('Você venceu! :)');
                location.reload();

            }else if(json['vencedor'] == 1){

                alert('Voce Perdeu! :(');
                location.reload();

            }
            
        }
    })
}

function caractere(caractere)
{
    $('#'+caractere).css('background-color','green');
    localStorage.setItem('caractere', caractere);
}

function dificuldade(nivel)
{
    var url = 'Home/sessionDificuldade';
    $.ajax({
        url: url,
        type: "post",
        dataType: "json",
        data: {nivel: nivel},
        success: function(json)
        {
           $('#'+json).css('background-color','green');
        }
    })
}