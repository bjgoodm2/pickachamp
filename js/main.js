$(document).ready(function(){

	var user;

	function User(username, password) {
		this.username = username;
		this.password = password;
	}
    $('.app').hide();
    $("#login").click(function(){ 
    	console.log("login clicked");
        var username = $("#loginUsername").val();
        var password = $("#loginPassword").val();
        console.log(username);
        console.log(password);
        $.post("../php/app.php", {action: "login", username: username, password: password}, function(data)  {
        	console.log("dick");
        	//alert(data);
	        if (data === "True") {
	        	$('.loginsignup').hide();
	        	$('.app').show();

	        	user = new User(username, password);
	        }
	        else if(data == "False"){
	        	console.log("gg");
	        }

    	});

    });

    $("#signup").click(function() {
    	var username = $("#signUsername").val();
        var password = $("#signPassword").val();

        $.post("../php/app.php", {action: "signup", username: username, password: password}, function(data) {
        	$('.loginsignup').hide();
	        $('.app').show();

	        user = new User(username, password);
        });

    });

    $('#favChamp').click(function() {
    	var fav = $('#inputFav').val();
    	//alert(fav);
    	$.post("../php/app.php", {action: "favChamp", username: user.username, favoriteChamp: fav}, function(data) {
    		//alert(data);
    		$('#inputFav').val('');
        	if(data === "exists"){
        		//do nothing
        	}
        	else
        		$('.app').append("<p id="+fav+">"+fav+"</p>")
        });
    });

    $('#deleteChamp').click(function() {
    	var champ = $('#inputDelete').val();
    	//alert(fav);
    	$.post("../php/app.php", {action: "deleteChamp", username: user.username, champ: champ}, function(data) {
    		//alert(data);
    		$('#inputDelete').val('');
        	if(data === "fail"){
        		//do nothing
        	}
        	else
        		$('#' + champ).remove()
        });
    });
});
