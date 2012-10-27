var usr = undefined, fuck = undefined, unfuck = undefined, resources = undefined;


$('body').ready(function(){ 
	var body_parser = Handlebars.compile($("#body-template").html()) 
	var fuck_parser = Handlebars.compile($("#full-fuck-template").html()) 
	var unfuck_parser = Handlebars.compile($("#unfuck-template").html()) 
	var resource_parser = Handlebars.compile($("#resource-template").html()) 

	var body = {
		location: "Berlin, Germany",
		image_url: "http://placekitten.com/400/400",
		description_full: "'Fuck' is an English word that is almost universally considered vulgar. In its literal meaning, it refers to the act of sexual intercourse. It is also used as a profanity, either to denote disdain or as an intensifier.The origin of the word is obscure. It is usually considered to be first attested to around 1475, but it may be considerably older. In modern usage, 'fuck' and its derivatives (such as 'fucker' and 'fucking') can be used in the position of a noun, a verb, an adjective or an adverb. There are many common phrases which make use of the word, as well as a number of compounds incorporating it, such as 'motherfucker'.",
		unfuck_url: "fuck/3/unfuck",
		watching: true
	}

	var fuck = {
	};

	var unfuck = {
		id: 1,
		description: "The word's use is considered obscene in social contexts, but may be common in informal and domestic situations. It is unclear whether the word has always been considered vulgar, and if not, when it first came to be used to describe (often in an extremely angry, hostile or belligerent manner) unpleasant circumstances or people in an intentionally offensive way, such as in the term motherfucker, one of its more common usages in some parts of the English-speaking world. In the modern English-speaking world, the word fuck is often considered highly offensive. Most English-speaking countries censor it on television and radio. A study of the attitudes of the British public found that fuck was considered the third most severe profanity and its derivative motherfucker second. Cunt was considered the most severe (Hargrave, 2000). Some have argued that the prolific usage of the word fuck has de-vulgarized it, an example of the 'dysphemism treadmill'. Despite its offensive nature, the word is common in popular usage.",
		involved: true,
		owned: false,
		creator: {
			name: "Bruce Wayne",
			profile_url: "http://www.google.com/?q=Bruce+Wayne",
			profile_picture: "http://placekitten.com/50/50"	
		}
	}

	var resource1 = {
		id: 2,
		amount: 100,
		description: "Rocks",
		has_pledger: true, 
		pledger: "Steve Stones",
		from: "20/12/2013",
		to: "26/12/2012",
		required: true
	}, resource2 = {
		id: 3,
		amount: 10,
		description: "Hours",
		has_pledger: true,
		pledger: "Timely Tim",
		required: false
	}, resource3 = {
		id: 3,
		amount: 100,
		description: "Planks of Wood",
		has_pledger: false,
		required: true,
		from: "20/12/2013",
		to: "26/12/2012",
	};

	$("#body-content").html(body_parser(body));
	//$("#unfucks").html(unfuck_parser(unfuck));
	//$("#resources_holder_1").html(resource_parser(resource1) + resource_parser(resource2) + resource_parser(resource3));

});

/*$('body').ready(function(){ 
	var featured = Handlebars.compile($("#featured-fucks-template").html()), 
		base = Handlebars.compile($("#base-fucks-template").html()); 
	var fucks = { 
		featured_fucks: 
			[ {
				image_url: "http://placekitten.com/300/200", 
				title: "We need help!", 
				description_short: "We are from a small village in Germany, and our school is about to close because we don't have enough kids! We need people in our village...", 
				url: "fuck/1",
				unfuck_url: "fuck/1/unfuck", 
			}, {
				image_url: "http://placekitten.com/300/200", 
				title: "I hope the templates work.", 
				description_short: "I'm trying to get the handlebars templates to work. Hopefully they will, because otherwise I'll go on a murder spree and kill this kitten...", 
				url: "fuck/2",
				unfuck_url: "fuck/2/unfuck", 
			}, {
				image_url: "http://placekitten.com/300/200", 
				title: "I have no idea of what I'm doing!", 
				description_short: "Just filling some stuff so I can have some text here, you know what it's like. Just some text to pretend we have some content and all...", 
				url: "fuck/3",
				unfuck_url: "fuck/3/unfuck",
			} ],
		other_fucks:
			[{
				id: "4",
				approved: false,
				unfuck_amount: "3",
				title: "I'm a smaller fuck",
				url: "fuck/4",
				unfuck_url: "fuck/4/unfuck",
			}, {
				id: "5",
				approved: false,
				unfuck_amount: "1",
				title: "Small, but interesting fuck",
				url: "fuck/5",
				unfuck_url: "fuck/4/unfuck",
			}, {
				id: "6",
				approved: true,
				unfuck_amount: "5",
				title: "This fuck has been approved",
				url: "fuck/6",
				unfuck_url: "fuck/6/unfuck",
			}, {
				id: "7",
				approved: false,
				unfuck_amount: "10",
				title: "This fuck has lots of unfucks",
				url: "fuck/7",
				unfuck_url: "fuck/7/unfuck",
			}

		]
	};

	$("#body-wrap").html(featured(fucks) + "<br />" + base(fucks));


	$( "#from" ).datepicker({
		defaultDate: "+1d",
		changeMonth: true,
		numberOfMonths: 1,
		onSelect: function( selectedDate ) {
			$( "#to" ).datepicker( "option", "minDate", selectedDate );
		}
	});
	$( "#to" ).datepicker({
		defaultDate: "+1d",
		changeMonth: true,
		numberOfMonths: 1,
		onSelect: function( selectedDate ) {
			$( "#from" ).datepicker( "option", "maxDate", selectedDate );
		}
	});

});*/


function init(){
	new AjaxUpload('imageUpload', {
		action: $('form#upload-fuck-image').attr('action'),
		name: 'image',
		onSubmit: function(file, extension) {
			$('#uploaded-image-holder').addClass('loading-upload');
		},
		onComplete: function(file, response) {
			thumb.load(function(){
				$('#uploaded-image-holder').removeClass('loading-upload');
				thumb.unbind();
			});
			thumb.attr('src', response);
		}
	});
}

function showUploadedItem (source) {  
    var img  = document.createElement("img");  
    	img.src = source;
    	img.class = "uploaded-image";
    $('#uploaded-image-holder *').each(function(){
    	$('#' . this).fadeOut(100);
    });

    $('#uploaded-image-holder').appendChild(img);
}  

function user(id, first, email, location){
	this.Id = id;
	this.first = first;
	this.email = email;
	this.locale = location;
}

function fuck(id, title, description, short_description, owned, watched, location, image_url, unfucks, accepted, featured){
	this.Id = id;
	this.title = title;
	this.description = description;
	this.short_description = short_description;
	this.owned = owned;
	this.watched = watched;
	this.locale = location;
	this.image_url = image_url;
	this.unfucks_number = unfucks; 
	this.accepted = accepted; 
	this.featured = featured;

	this.toggle_watch = toggle_watch;

	this.pledge = pledge_unfuck;
	this.do_pledge = do_pledge_unfuck;

	this.display_fuck = display_fuck;
}

function unfuck(id, description, user, chosen, location, resources){
	this.Id = id;
	this.description = description;
	this.user = user;
	this.chosen = chosen;
	this.locale = location;
	this.resources = resources;

	this.create = create_unfuck;
	this.do_create = do_create_unfuck; 
	
	this.accept_unfuck = accept_unfuck;
	this.display_unfuck = display_unfuck;
}

function resources(id, unit, quantity, available_from, available_to, location, pledger_first, pledger_last){
	this.Id = id;
	this.unit = unit;
	this.quantity = quantity;
	this.available_from = available_from;
	this.available_to = available_to;
	this.locale = location;
	this.pledger_id;

	this.display_resource = display_resource;

}

function toggle_watch(watch){
	if(this.watch == watch){
		return;
	}

	if(post('../fuck/' + this.Id + '/watch/' + watch)){
		this.watch = watch;
	}else{
		$('#server-error').alert();
	}
}

function display_resource(){
	return "<div class=''>" + this.quantity + " x " + this.unit + " (offered by " + this.pledger_first + " " + this.pledger_last + ")</div><div class='validity' alt='Available from " + this.available_from + " to " + this.available_from + "'></div>";  
}



function post(url){

}

function load_fuck_form(){
	$('#body-wrap-frontpage').hide();
	//set_logo_location(user.locale);

	$('#body-wrap-create').removeClass('unfuck-create-form');
	
	if($('#body-wrap-create').hasClass('fuck-create-form') == false)
		$('#body-wrap-create').addClass('fuck-create-form');
	
}

function set_logo_default(){
	$('#locale').fadeOut(
		100,
		function(){
			$('#unfuck').animate(
				{'marginTop':'0px'},
				100,
				function (){
					$('#the_world').fadeIn(100);
				}
			)
		}
	);
}

function set_logo_location(location){
	$('#the_world').fadeOut(
		100,
		function(){
			$('#unfuck').animate(
				{'marginTop':'10px'},
				100,
				function (){
					$('#locale').text(location);
					$('#locale').fadeIn(100);
				}
			)
		}
	);
}


