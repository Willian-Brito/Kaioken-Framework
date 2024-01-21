
$(document).ready(function() {

    btnSalvar();
    btnCancelar();
});

//#region btnSalvar
function btnSalvar() {

    $('#btnSalvar').click(function(e){

        e.preventDefault();          
        
        var FormulariosSelecionados = [];

        $('input[type="checkbox"]').each(function() {
            
            if ($(this).prop("checked") == true) {
                
                FormulariosSelecionados.push($(this).val());
            }
        });

        if (ValidarCampos(FormulariosSelecionados)) {

            AjaxRequest(FormulariosSelecionados)
        }
    });
}
//#endregion

//#region btnCancelar
function btnCancelar() {

    $('button[name="cancelar"]').click(function(e){

        e.preventDefault();          
        
        $('#txtDescricao').val('');
    });

}
//#endregion

//#region ValidarCampos
function ValidarCampos(FormulariosSelecionados) {

    const error = "error";

    if( $.trim($('#txtCodigo').val()) == "")
    {
        Msg(error, "Preencha o campo Código!", "#txtCodigo");
        return false;
    }

    if( $.trim($('#txtDescricao').val()) == "")
    {
        Msg(error, "Preencha o campo Descrição!", "#txtDescricao");
        return false;
    }

    if(FormulariosSelecionados.length == 0) {
        Msg(error, "Selecione ao menos um Formulário!");
        return false;
    }

    return true;
}
//#endregion

//#region AjaxRequest
function AjaxRequest(FormulariosSelecionados) {

    let txtDescricao = $('#txtDescricao').val()
    let txtCodigo = $('#txtCodigo').val()
    let txtTokenCsrf = $('#TokenCsrf').val()

    $.ajax({
        url: '?class=PerfilForm&method=onSave',
        type: 'POST',
        dataType: 'html',
        data: {
            'IdPerfil'  : txtCodigo,
            'Descricao' : txtDescricao,
            'Csrf_Token_Form' : txtTokenCsrf,
            'Formularios': FormulariosSelecionados
        },
        success: function success(response) {
            
            $("#result").html(response);
        },
        error: function error(response){
            Msg("error", response)
        },
    })
}
//#endregion

