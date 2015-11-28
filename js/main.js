$(document).ready(function(){

	var User = function(username, password, summoner) {
		this.username = username;
		this.password = password;
		this.summoner = summoner;
	}

	var user = User('Not logged in', '');

    $('.app').hide();
    $("#login").click(function(){ 
    	console.log("login clicked");
        var username = $("#loginUsername").val();
        var password = $("#loginPassword").val();
        $.post("../php/app.php", {action: "login", username: username, password: password}, function(data)  {
        	console.log("logging in");
	        if (data == "True") {

				//get summoner name for the corresponding user
				$.post("../php/app.php", {action: "getSummoner", username: username}, function(data){	
					summoner = data;
					showApp(username, password, summoner);
				});
	        }
	        else if(data == "False"){
				$('#login-msg').text('Invalid username or password');
	        }
    	});

    });

    $("#signup").click(function() {
    	var username = $("#signUsername").val();
        var password = $("#signPassword").val();
		var summoner = $("#signSummoner").val();

        $.post("../php/app.php", {action: "signup", username: username, password: password, summoner: summoner}, function(data) {
			if (data == "User already exists") {
				$('#signup-msg').text('Username already exists');
			} else if (data == "Success") {
				showApp(username, password, summoner);
			}
        });

    });

	//Shows the app once the user signs up or logs in.
	function showApp(username, password, summoner) {	
	    user = new User(username, password, summoner);
		$('.loginsignup').hide();
		$('.app').show();

		//Display the username and summoner name
		$('#login-name').text(user.username);
		$('#summoner-name').text(user.summoner);

		//Display the user's favorite champions
		displayFavs(username);
	}

	function displayFavs(username) {
        $.post("../php/app.php", {action: "getFavs", username: username}, function(data) {
			if (data == "No favorites") {
				//Do nothing
			} else {
				$('#no-fav-champs').hide();
				$('#fav-champ-list').append(data);

				//Bind the delete champ button for each champ
				$('.deleteChamp').click(function() {
					var champ = $(this).attr('value');
					console.log(champ);
					$.post("../php/app.php", {action: "deleteChamp", username: user.username, champ: champ}, function(data) {
					if(data === "fail"){
						//do nothing
					}
					else
						$('#' + champ).remove()
					});
				});
			}
        });
	}

    $('#favChamp').click(function() {
    	var fav = $('#inputFav').val();
    	$.post("../php/app.php", {action: "favChamp", username: user.username, favoriteChamp: fav}, function(data) {
    		$('#inputFav').val('');
        	if(data === "exists"){
        		//do nothing
        	}
        	else {
				//reload the favorites
				$('#fav-champ-list').empty();
				displayFavs(user.username);
			}
        });
    });

});
