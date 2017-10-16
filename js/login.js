$(document).ready(function() {
	$('#frmLogin').submit(function(){
		var username = $('#txtUsuario').val();
		var password = $('#txtPass').val();
		$("#msgbox").html(' <i class="fa fa-spinner fa-pulse"></i>');

		var ajaxReq = "jupiter/api.php";
		$.post(ajaxReq, {action:"login", eUser:username, ePass:password, rand:Math.random()},
		function(data){
			if((data.rpta == '2') || (data.rpta == 2)){
				$("#msgbox").html(' Correcto').fadeTo(900,1);
				document.location = 'preload.php';
			} else {
				$("#msgbox").html(' ¡Error Login!').fadeTo(900,1);
			}
		});

		return false;
	});

});