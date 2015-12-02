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

		//Keep the ratings hidden still
		$('#rate-champ').hide();

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

	$('#history_button').click(function() {
    	$.post("../php/recommend.php", {action: "match_history", summoner_name: user.summoner}, function(data) {
			displayChamp(data);
        });
    });

	$('#history_rate_button').click(function() {
    	$.post("../php/recommend.php", {action: "match_history_rate", summoner_name: user.summoner}, function(data) {
			displayChamp(data);
        });
    });

	$('#role_button').click(function() {
		primary = $('input[name=primary]:checked').val();
		secondary = $('input[name=secondary]:checked').val();

    	$.post("../php/recommend.php", {action: "role", primary_role: primary, secondary_role: secondary}, function(data) {
			displayChamp(data);
        });
    });

	$('#random-button').click(function(){
		displayChamp("Azir");
	});

	$('#rate-button').click(function(){
		var champName = $('#rate-button').val();
		var newRating = $('input[name=rating]:checked').val();
		$.post("../php/app.php", {action: "updateRating", champName: champName, newRating: newRating}, function(data) {
			console.log(data);
			displayRating(champName);
        });
	
	});

	function displayChamp(champName) {

		//Display champ name
		$('#champ-name').text(champName);

		//Display champ pictures
		$('#champ-pic').attr('src', 'http://ddragon.leagueoflegends.com/cdn/5.23.1/img/champion/' + champName + '.png');

		//Display champ description
        var key = "7fd3b97d-df0c-4ced-b7d8-810a89c8e874";
        $.ajax({
            url: 'https://global.api.pvp.net/api/lol/static-data/na/v1.2/champion?champData=lore&api_key=7fd3b97d-df0c-4ced-b7d8-810a89c8e874',
            type: 'GET',
            dataType: 'json',
            data: {
            },
            success: function (json) {
            	var id = json.data[champName].lore;
            	document.getElementById("champ-description").innerHTML = id;
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
				console.log("error getting champ description");
            }
        });            

		//Display champ builds
		$.ajax({
			url: '../php/getBuild.php',
			type: 'GET',
			data: {"champName": champName},
			dataType: 'json',
			success: function(data) {
				$('#build-pics').html('');
				$('#build-pics').append('<li>');
				for (var i = 0; i < data.length; i++){
					$('#build-pics').append("<img src=\"" + data[i] + "\">");
				}
				$('#build-pics').append('</li>');
			}
		});

		//Display champ rating system
		$('#rate-champ').show();	
		displayRating(champName);	
		$('#rate-button').attr('value', champName);	
	
	}

	function displayRating(champName) {
			$.post("../php/app.php", {action: "getRating", champName: champName}, function(data) {
			if (data == "0 results") {
				console.log("something's wrong");
			} else {
				console.log("data:" +data);
				$('#champ-rating').text('Rating: ' + data);
			}
        });

	}


	//champion stats
	    var currentDataSet;
    var allWinRates;
    var allPopularity;

    $(".graph1").click(function() {
        console.log($("#win-rate").is(":checked"));

        if($("#win-rate").is(":checked"))
            $.post("php/dataV.php", {
                action: "top-winrate all"
            }, function(data) {
                console.log("ajax");
                allWinRates = data;
                console.log();
                console.log();
                start = parseInt($("#high-rank").val())-1;
                end = parseInt($("#low-rank").val());
                currentDataSet = data.slice(start,end);
                //$("body").append(data);
                //d3 stuff
                $('.chart').html('');
                if($("#bars").is(":checked"))
                    drawBar(data.slice(start,end), 50);
                else {
                    var draw = {
                        name: "flare",
                        children: currentDataSet
                    };

                    drawBubble(draw);
                }
             
            }, "json");
        else
            popularity();
    });
    function popularity() {
        console.log("clicked g2");
        $.post("php/dataV.php", {
            action: "top-popularity"
        }, function(root) {
            console.log("returned");
            $('body').append(root);
            allPopularity = root.children;
            start = parseInt($("#high-rank").val())-1;
            end = parseInt($("#low-rank").val());
            currentDataSet = allPopularity.slice(start,end);
            $('.chart').html('');
            //d3 stuff
            if($("#bars").is(":checked"))
                drawBar(currentDataSet, 0);
            else {
                var draw = {name: "flare", children: currentDataSet};
                drawBubble(draw);
            }
            //EO d3
        }, "json");
    }

    $('.addbar').click(function() {


        var input = $("#champ").val();

        addNode(input);
    });
    $('.addbubble').click(function() {


        var input = $("#champ").val();

        addBubble(input);
    });

    //draw bar graph using d3
    function drawBar(data, yStart) {
        var margin = {
                top: 20,
                right: 20,
                bottom: 30,
                left: 150
            },
            width = 960 - margin.left - margin.right,
            height = 500 - margin.top - margin.bottom;
        var x = d3.scale.ordinal().rangeRoundBands([0, width], .1);
        var y = d3.scale.linear().range([height, 0]);
        var xAxis = d3.svg.axis().scale(x).orient("bottom");
        var yAxis = d3.svg.axis().scale(y).orient("left").tickFormat(function(d) {
            return d + "%";
        });
        var svg = d3.select(".chart").append("svg").attr("width", width + margin.left + margin.right).attr("height", height + margin.top + margin.bottom).append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");
        //d3.tsv("data.tsv", type, function(error, data) {
        //if (error) throw error;
        x.domain(data.map(function(d) {
            return d.name;
        }));
        y.domain([yStart, d3.max(data, function(d) {
            return d.size;
        })]);
        svg.append("g").attr("class", "x axis").attr("transform", "translate(0," + height + ")").call(xAxis);
        svg.append("g").attr("class", "y axis").call(yAxis).append("text").attr("transform", "rotate(-90)").attr("y", -70).attr("dy", ".9em").style("text-anchor", "end").text("Win Rate");
        svg.selectAll(".bar").data(data).enter().append("rect").attr("class", "bar").attr("x", function(d) {
            return x(d.name);
        }).attr("width", x.rangeBand()).attr("y", function(d) {
            return y(d.size);
        }).attr("height", function(d) {
            return height - y(d.size);
        });
        //});
        function type(d) {
            d.size = +d.size;
            return d;
        }
    }

    function addBar(champ) {
        console.log("adding bar");
        console.log(champ);
        if($("#win-rate").is(":checked"))
            arr = allWinRates;
        else
            arr = allPopularity;

        for(var i = 0; i < arr.length; i++) {
            //console.log(arr[i].letter)//.substring(1, allWinRates[i].letter.length-1));
            if(arr[i].name === champ) {
                currentDataSet.push(arr[i]);
                console.log("match");
            }
        }
        //currentDataSet.push(data);
        $('.chart').html('');
        drawBar(currentDataSet, 0);
    }

    function addNode(champ) {
        console.log("adding node");
        console.log(champ);
        /*var arr;
        if($("#win-rate").is(":checked"))
            arr = allWinRates;
        else
            arr = allPopularity;

        //console.log(allWinRates[0]);
        console.log(arr[0]);

        for(var i = 0; i < arr.length; i++) {
            //console.log(arr[i])//.substring(1, allWinRates[i].letter.length-1));
            if(arr[i].name === champ) {
                currentDataSet.push(arr[i]);
                console.log("match");
                break;
            }
        }

        for(var i = 0; i < currentDataSet;i++) 
            console.log(currentDataSet[i]);
        //currentDataSet.push(data);*/
        $('.chart').html('');
        if($("#bars").is(":checked")) {
            console.log("drawing bars");
            addBar(champ, 0);
            //drawBar(currentDataSet, 0);     
        }
        else {
            addBubble(champ);
            //drawBubble(currentDataSet); 
        }
    }

    function addBubble(champ) {
        if($("#win-rate").is(":checked"))
            arr = allWinRates;
        else
            arr = allPopularity;
        
        for(var i = 0; i < arr.length; i++) {
            console.log(arr[i]);
            if(arr[i].name === champ) {
                  currentDataSet.push(arr[i]);
                    console.log("match");
            }
        }


        var draw = {name: "flare", children: currentDataSet};
        $('.chart').html('');
        drawBubble(draw);

    }

    //draw bubble graph using d3
    function drawBubble(root) {
        var diameter = 900,
            format = d3.format(",d"),
            color = d3.scale.category20c();
        var bubble = d3.layout.pack().sort(null).size([diameter, diameter]).padding(1.5);
        var svg = d3.select(".chart").append("svg").attr("width", diameter).attr("height", diameter).attr("class", "bubble");
        //d3.json("flare.json", function(error, root) {
        //  if (error) throw error;
        var node = svg.selectAll(".node").data(bubble.nodes(classes(root)).filter(function(d) {
            return !d.children;
        })).enter().append("g").attr("class", "node").attr("transform", function(d) {
            return "translate(" + d.x + "," + d.y + ")";
        });
        node.append("title").text(function(d) {
            return d.className + ": " + format(d.value);
        });
        node.append("circle").attr("r", function(d) {
            return d.r;
        }).style("fill", function(d) {
            return color(d.packageName);
        });
        node.append("text").attr("dy", ".3em").style("text-anchor", "middle").text(function(d) {
            return d.className.substring(0, d.r / 3);
        });
        //});
        // Returns a flattened hierarchy containing all leaf nodes under the root.
        function classes(root) {
            var classes = [];

            function recurse(name, node) {
                if (node.children) node.children.forEach(function(child) {
                    recurse(node.name, child);
                });
                else classes.push({
                    packageName: name,
                    className: node.name,
                    value: node.size
                });
            }
            recurse(null, root);
            return {
                children: classes
            };
        }
        d3.select(self.frameElement).style("height", diameter + "px");
    }
});
