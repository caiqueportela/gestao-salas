var LOGIN = null;

$( "#buscar" ).click(function() {
    $("#tbody").empty();
    var nome = $("#nome").val();
    var url = "/api/salas/?"
    if (nome.length != 0) {
        url += "nome=" + nome;
    }
    if ($("#disponivel").is(':checked')) {
        url += "&disponivel=1";
    }
    console.log(url);
    $.ajax({
        type    : "GET",
        url     : url,
        headers: {'Authorization': 'Bearer ' + LOGIN},
        dataType: "json",
        contentType: 'application/json',
        success:function(data) {
            var table = $("#table tbody");
            $.each(data, function(idx, elem){
                table.append("<tr><td>"+elem.id+"</td><td>"+elem.nome+"</td>   <td>"+elem.descricao+"</td></tr>");
            });
        }
    });
});


$( document ).ready(function() {
    var login = {
        usuario: "4linux",
        senha: "4linux"
    };
    $.ajax({
        type    : "POST",
        url     : "/api/login",
        dataType: "json",
        contentType: 'application/json',
        data: JSON.stringify(login),
        success:function(msg) {
            LOGIN = msg.access_token;
        }
    });
});