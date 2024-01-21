

$(document).ready(function() {
    
    uploadFile();
});

//#region checkBoxSelected
// function checkBoxSelected() {

//     if ($('#chkEhAtivo').val() == "0") {
//         $('#chkEhAtivo').prop('value', '1')
//     } else {
//         $('#chkEhAtivo').prop('value', '0')
//     }
// }
//#endregion

//#region Senha
let duracao = 300;

//#region mostrarSenha
function mostrarSenha() {
    $('#credenciais').show(duracao)
}
//#endregion

//#region ocultarSenha
function ocultarSenha() {
    $('#credenciais').hide(duracao)
}
//#endregion

//#endregion

//#region ocultarPerfil
function ocultarPerfil() {
    $('#gpPerfil').hide(duracao);
}
//#endregion

//#region uploadFile
function uploadFile() {

    const inputFile = document.querySelector("#picture-input");
    const avatarImg = document.querySelector("#avatar-img");

    inputFile.addEventListener("change", function(e) {

        const inputTarget = e.target;
        const file = inputTarget.files[0];
        
        if (file) 
        {
            const reader = new FileReader();

            reader.addEventListener("load", function (e) {

                const readerTarget = e.target;
                const src = readerTarget.result;

                avatarImg.src = src;
            });

            reader.readAsDataURL(file);
        }
    });
}
//#endregion

//#region setImg
function setImg(path) {
    $('#avatar-img').attr('src', path);
}
//#endregion

//#region setFotoPerfil
function setFotoPerfil(path) {
    $('.foto-perfil img').attr('src', path);
}
//#endregion

//#region setUsuario
function setUsuario(nome) {
    $('#idUser').text(nome);
}
//#endregion

//#region setPerfil 
function setPerfil(descricao) {
    $('#idPerfil').text(descricao);
}
//#endregion

//#region removerFoto
function removerFoto() {

    let ehStatusNovo = $('input[name="PageStatus"]').attr('value') == "1";
        
    if(ehStatusNovo) {

        $('#picture-input').val("");
        setImg('app/Frontend/assets/img/icon/avatar.svg');

        return;
    }

    AjaxRequest();
}
//#endregion

//#region AjaxRequest
function AjaxRequest() {

    $status = $('input[name="PageStatus"]').attr('value');
    
    $.ajax({
        url: '?class=UsuarioForm&method=onRemove',
        type: 'POST',
        dataType: 'html',
        data: { 'PageStatus' : $status },
        success: function success(response) {
            
            setImg('app/Frontend/assets/img/icon/avatar.svg');
        },
        error: function error(response){
            Msg("error", response)
        },
    })
}
//#endregion