
$(document).ready(function() {

    const inputs = document.querySelectorAll(".input");  

	inputs.forEach(input => {
		input.addEventListener("focus", addcl);
		input.addEventListener("blur", remcl);
	});
	

	CloseAlert();
});

function addcl(){
	let parent = this.parentNode.parentNode;
	parent.classList.add("focus");
}

function remcl(){
	let parent = this.parentNode.parentNode;
	if(this.value == ""){
		parent.classList.remove("focus");
	}
}

function CloseAlert() {

    $(".close-alert").click(function(e){
        $(this).parent().remove();
        e.preventDefault();
    });
}

function Msg(tipo, mensagem, idFoco) {

	let button = $("<button/>").addClass('close-alert').append("x");
	let i = $("<i/>").addClass('material-icons').append("error_outline");
	
    let content = $("<div />").addClass(`toast material-alert ${tipo}`)
						      .append(button)
						      .append(i)
						      .append(mensagem);

	$('#result').append(content);

	$(idFoco).focus();
	$(idFoco).select();

	CloseAlert();

}
