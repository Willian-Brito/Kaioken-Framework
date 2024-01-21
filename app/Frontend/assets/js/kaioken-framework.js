/*

        ##################################
        #                                #
        #   Company: Msystem             #
        #   Developer: h1s0k4            #
        #   Project: Kaioken Framework   #
        #   Version: 1.0.0               #
        #                                #
        ##################################

*/



$(document).ready(function() {

    InitializeFunctions();
});

//#region InitializeFunctions
function InitializeFunctions() {
    
    // KaiokenSave();
    MostrarMenuLateral();
    FecharMenuLateral();
    TransformarMenuLateral();
    AtivarSelecaoMenu();
    CollapseSubMenu();
    CarregarConfiguracao();
    AjustarAlturaDataGrid();
    CloseAlert();  
    TrocarTema();  
    MostrarMenuUsuario();
}   
//#endregion

//#region Objetos

const MenuLateral = document.querySelector("aside");
const BtnMenu = document.querySelector("#btn-menu");
const BtnFechar = document.querySelector("#btn-fechar");
const BtnLogo = document.querySelector("#BtnLogo");
const Tema = document.querySelector(".trocar-tema");
const Body = document.body;

const nav = document.getElementById('nav-bar')
const TagsH3 = document.querySelectorAll('.barra-lateral h3')
const container = document.getElementById('container')
const sidebar = document.querySelector(".barra-lateral");
const User = document.querySelector(".perfil");

let darkTheme = "dark-theme";
//#endregion

//#region ClicarNoMenu
function ClicarNoMenu(IdMenu) {
    
    $(`#${IdMenu}`).trigger('click');
}
//#endregion

//#region ClicarMenuDashboard
// function ClicarMenuDashboard()
// {
//     $('#Dashboard').trigger('click');
//     window.location.href = "index.php?class=DashboardList";
// }
//#endregion

//#region MostrarMenuLateral
function MostrarMenuLateral() {

    BtnMenu.addEventListener('click', () => {
        MenuLateral.style.display = 'block';
    })
}
//#endregion

//#region MostrarMenuUsuario
function MostrarMenuUsuario() {

    User.addEventListener('click', () => {

        let dropdown = document.querySelector(".dropdown-user");
        
        if(dropdown.style.display == 'none')
        {
            dropdown.style.display = 'flex'   
        }
        else
        {
            dropdown.style.display = 'none' 
        }
    })
}
//#endregion

//#region FecharMenuLateral
function FecharMenuLateral() {
    BtnFechar.addEventListener('click', () => {
        MenuLateral.style.display = 'none';
    })
}
//#endregion

//#region TrocarTema
function TrocarTema() {

    
    Tema.addEventListener('click', () => {
        Body.classList.toggle(darkTheme);

        if(Body.classList.contains(darkTheme)) {
            localStorage.setItem("Theme", darkTheme);
            // BtnLogo.src = "Frontend/assets/img/logo-branco-2.png";
            BtnLogo.src = "app/Frontend/assets/img/logo-menu.png";
        } else {
            localStorage.setItem("Theme", "light-theme");
            BtnLogo.src = "app/Frontend/assets/img/logo-menu1.png";
        }

        Tema.querySelector('span:nth-child(1)').classList.toggle('ativo');
        Tema.querySelector('span:nth-child(2)').classList.toggle('ativo');
    })
}
//#endregion

//#region CarregarConfiguracao
function CarregarConfiguracao() {

    let theme = localStorage.getItem("Theme")
    let menu = localStorage.getItem("Menu")
    let selecaoMenu = localStorage.getItem("SelecaoMenu")

    if(!menu)
        localStorage.setItem("Menu", "open")

    if(selecaoMenu) 
        ClicarNoMenu(selecaoMenu);

    if (theme && theme === darkTheme) {
        Body.classList.toggle(darkTheme);
        Tema.querySelector('span:nth-child(1)').classList.toggle('ativo');
        Tema.querySelector('span:nth-child(2)').classList.toggle('ativo');
        // BtnLogo.src = "Frontend/assets/img/logo-branco-2.png";
        BtnLogo.src = "app/Frontend/assets/img/logo-menu.png";
    }

    if (menu && menu === "close") {

        nav.classList.toggle('active')
        container.classList.toggle('active')
        sidebar.classList.toggle('close')

        TagsH3.forEach(h3 =>
        {
            h3.classList.toggle('active')
        })
    }
}
//#endregion

//#region Efeito no Menu
function EfeitoScrollMenu()
{

    window.addEventListener("scroll", function () {
        const header = document.querySelector("header");
        const imagem = document.querySelector("header img");

        if(window.scrollY > 0)
            imagem.src = "app/Frontend/assets/img/MSystem.png";
        else
            imagem.src = "app/Frontend/assets/img/MSystem1.png";

        header.classList.toggle("sticky", window.scrollY > 0);
    });
}
//#endregion

//#region TransformarMenuLateral
function TransformarMenuLateral() {

    const toggle = document.getElementById('BtnLogo')

    const ExisteTodosElementos = toggle && nav && TagsH3 && container

    if(ExisteTodosElementos) {

        toggle.addEventListener('click', ()=>
        {
            nav.classList.toggle('active')
            container.classList.toggle('active')
            sidebar.classList.toggle('close')

            TagsH3.forEach(h3 =>
            {
                h3.classList.toggle('active')
            })

            if (MenuEstaFechado()) {
                localStorage.setItem("Menu", "close");
                TagEmBreveFechado();
            } else {
                localStorage.setItem("Menu", "open");
                TagEmBreveAberto();
            }
        })

        MostrarTagEmBreve();
    }

}

function MostrarTagEmBreve() {

    EstaFechado = localStorage.getItem('Menu') === "close"

    if (EstaFechado) {
        TagEmBreveFechado();
    } else {
        TagEmBreveAberto();
    }
}

function TagEmBreveFechado() {

    $('.em-breve').each(function() {
        this.style = "height: 10px;"     
        this.innerHTML = "";
    })
}

function TagEmBreveAberto() {
    
    $('.em-breve').each(function() {
        $(this).css("height", "");  
        this.innerHTML = "Em Breve";
    })
}

function MenuEstaFechado() {

    if (nav.classList.contains("active") && container.classList.contains("active") && sidebar.classList.contains("close"))
        return true
    else
        return false
}
//#endregion

//#region AtivarSelecaoMenu
function AtivarSelecaoMenu() {

    const OpcaoMenu = document.querySelectorAll('.nav-links > li')
    const OpcaoMenuIcon = document.querySelectorAll('.nav-links > li .icon-link')
    let menu = localStorage.getItem("Menu")

    function AplicarSelecao() {

        if(OpcaoMenu) {

            OpcaoMenu.forEach(link => link.classList.remove('selecionado'))
            let TemSubmenu = this.children[0].classList.contains("icon-link")

            if(!TemSubmenu && menu === "open") {
                OpcaoMenuIcon.forEach(icon => icon.parentElement.classList.remove("show-submenu"))
            }

            this.classList.add('selecionado')
            localStorage.setItem("SelecaoMenu", $(this).attr('id'));
        }
    }

    OpcaoMenu.forEach(link => link.addEventListener('click', AplicarSelecao))
}
//#endregion

//#region CollapseSubMenu
function CollapseSubMenu() {

    let arrow = document.querySelectorAll(".arrow");
    let iconLink = document.querySelectorAll(".icon-link");

    iconLink.forEach(item => item.addEventListener('click', (e)=> {

        let menu = localStorage.getItem("Menu");

        if(menu === "open")
        {
            let li = e.target.parentElement.parentElement.parentElement;

            li.classList.toggle("show-submenu");
            li.style.css = "z-index: 99;";
        }

    }));

    arrow.forEach(item => item.addEventListener('click', (e)=> {

        let menu = localStorage.getItem("Menu");

        if(menu === "open")
        {
            let li = e.target.parentElement.parentElement;

            li.classList.toggle("show-submenu");
            li.style.css = "z-index: 99;";
        }

    }));
}
//#endregion

//#region AjustarAlturaDataGrid
function AjustarAlturaDataGrid() {

    var elementTbody = $('[data_auto_height="true"]');

    for (var i = 0; i < elementTbody.length; i++) {

        var heightNew = 0;
        var idGrid = elementTbody[i].parentElement.id;

        let _diffFooter = $('footer').css('visibility') === "collapse" ? 35 : 50;
        heightNew = ($('footer').offset().top - $(elementTbody[i]).offset().top - _diffFooter) - GetInt($(elementTbody[i]).attr('data_padding_bottom'));

        if (heightNew > GetInt($(elementTbody[i]).attr('data_min_height'))) {
            $(elementTbody[i]).css("height", heightNew);
        } else {
            $(elementTbody[i]).css("height", $(elementTbody[i]).attr('data_min_height'));
        }
    }
}
//#endregion

//#region GetInt
function GetInt(sValue) {

    var _Out = 0;

    sValue = $.trim(sValue);

    if (sValue != "") {
        if (typeof sValue != "string") {
            sValue = getStr(sValue);
        }

        sValue = sValue.replace(".", "").replace(",", ".").replace(/[a-z|\s]/gi, "");
        _Out = isNaN(parseInt(sValue, 10)) ? 0 : parseInt(sValue, 10);
    }

    return _Out;
}
// #endregion

// #region CloseAlert
function CloseAlert() {
    
    $(".close-alert").click(function(){
        $(this).parent().remove();
    });
}
// #endregion

//#region Msg
function Msg(tipo, mensagem, idFoco) {

	let button = $("<button/>").addClass('close-alert').append("x");
	let i = $("<i/>").addClass('material-icons').append("error_outline");
	
    let content = $("<div />").addClass(`toast material-alert ${tipo}`)
						      .append(button)
						      .append(i)
						      .append(mensagem);

	$('body').append(content);

    CloseAlert();

    setTimeout(function() {
        $('.close-alert').trigger('click');
        $(idFoco).focus();
	    $(idFoco).select();        
    }, 3000);
}
//#endregion

//#region ativarCheckBox
function ativarCheckBox(IdFormulario)
{
    $(`input[value="${IdFormulario}"]`).prop('checked', true)
}
//#endregion

//#region checkBoxSelected
function checkBoxSelected(input) {
    console.log(input)
    var id = $(input).attr('id')

    if ($(`#${id}`).val() == "0") {
        $(`#${id}`).prop('value', '1')
    } else {
        $(`#${id}`).prop('value', '0')
    }
}
//#endregion

//#region html
function html(id, value) {

    if (existsElement(id)) {
        
        if (value != undefined) {
            return $(`#${id}`).html(value);
        } else {
            return $(`#${id}`).html();
        }
    }
}
//#endregion

//#region existsElement
function existsElement(id) {

    var exist = $(`#${id}`).length == 1;
    return exist;
}
//#endregion

//#region setDataForm
function setDataForm(Id, value)
{
    $(`#${Id}`).prop('value', value)
}
//#endregion

//#region disabled
function disabled(Id)
{
    $(`#${Id}`).attr('disabled', 'disabled')
}
//#endregion

//#region setPageStatus
function setPageStatus(value) {
    $('input[name="PageStatus"]').prop('value', value)
}
//#endregion

//#region generateTokenCSRF
function generateTokenCSRF(token) {
    $('#TokenCsrf').val(token)
}
//#endregion

//#region downloadFile
/**
 * Download a file
 */
function downloadFile(file, basename)
{
    window.open('../../../../../KaiokenFramework/Util/download.php?file='+file+'&basename='+basename, '_blank');
}
//#endregion

//#region AddSelecaoMenu
function AddSelecaoMenu() {
    console.log('AddSelecaoMenu')
    $('.barra-lateral ul li').removeClass("selecionado");
    $('#Dashboard').addClass("selecionado");    
}
//#endregion

//#region KaiokenSave
function KaiokenSave() {

    let btnSalvar = $("#btnSalvar:not(.no-framework)");

    btnSalvar.click(function (e) {
            
        e.preventDefault();
        
        let form = $('form');
        let form_ajax = $('#ajax_load');

        $.ajax({
            url: form.attr("action"),
            data: form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function success(response) { console.log(response); form_ajax.html(response) },
            error: function error(response){ Msg("error", response) },
        })
    })
}
//#endregion