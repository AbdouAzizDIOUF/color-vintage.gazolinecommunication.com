"use strict";
let $ = jQuery;
var slick_items_parameters;
var defaultSomme1 = 0;
var defaultSomme2 = 0;
jQuery(document).ready(function() {

    $('.obflink').click(function(){
        var t = $(this);
        var link = atob(t.data('o'));
        window.open(link);
    })
    $('#calendar-formation2').multiDatesPicker();
	if($('.calendar-formation').length) {
		/* GET date of all sessions on the current formation instead of this array */
		var availableDates = ["2022-04-26","2022-04-27","2022-04-28","2022-04-29","2022-04-30","2022-04-31","2022-08-01","2022-04-05","2022-04-06","2022-04-04","2022-04-08","2022-04-09","2022-04-10","2022-04-11","2022-04-19","2022-04-20","2022-04-21","2022-04-22","2022-04-23","2022-04-24","2022-04-25","2022-09-20","2022-09-21","2022-09-22","2022-09-23","2022-09-24","2022-09-25","2022-09-26","2022-09-27","2022-09-28","2022-09-29","2022-09-30","2022-10-01","2022-10-02","2022-10-03","2022-08-16","2022-08-17","2022-08-18","2022-08-19","2022-08-20","2022-08-21","2022-08-22","2022-08-23","2022-08-24","2022-08-25","2022-08-26","2022-08-27","2022-08-28","2022-08-29","2022-08-30","2022-08-31","2022-09-01","2022-09-02","2022-09-03","2022-09-04","2022-09-05","2022-10-18","2022-10-19","2022-10-20","2022-10-21","2022-10-22","2022-10-23","2022-10-24"]; 
		function unavailable(date) {
	        var ymd = date.getFullYear() + "-" + ("0"+(date.getMonth()+1)).slice(-2) + "-" + ("0"+date.getDate()).slice(-2);
	        
	        if ($.inArray(ymd, availableDates) < 0 ) {
	            return [false,"disabled","Booked Out"];
	        } else {
	            return [true, "enabled", "Available"];
		    }
	    }
		$('.calendar-formation').datepicker({
		        inline: true,
		        firstDay: 1,
		        showOtherMonths: true,
		        closeText: 'Fermer',
		        prevText: 'Précédent',
		        nextText: 'Suivant',
		        currentText: 'Aujourd\'hui',
		        monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
		        monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
		        dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
		        dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
		        dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
		        weekHeader: 'Sem.',
		        dateFormat: 'yy-mm-dd',
		        beforeShowDay: unavailable,
                selectMultiple: true,
		    });
    }




	if($('#calendar').length) {

	    var calendar = new tui.Calendar(document.getElementById('calendar'), {
	        defaultView: 'week',
	        taskView: false,
	        disableClick: true,
	        disableDblClick: true,
	        isReadOnly: true,
	        useDetailPopup: true,
	        calendars: [
	            {
	              id: '1',
	              name: 'Formations',
	              color: '#ffffff',
	            },
	        ],
	        month: {
	            daynames: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
	            startDayOfWeek: 1,
	            hourStart: 8,
	            hourEnd: 21,
	            workweek: true
	        },
	        week: {
	            daynames: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
	            startDayOfWeek: 1,
	            hourStart: 8,
	            hourEnd: 21,
	            workweek: true
	        },
	        template: {
	       		monthDayname: function(dayname) {
	       		  return '<span class="calendar-week-dayname-name">' + dayname.label + '</span>';
	       		},
		        timegridDisplayPrimayTime: function(time) {
		            return time.hour + 'h';
		        },
		        timegridDisplayTime: function(time) {
		            return time.hour + 'h';
		        },
		        popupDetailDate: function(isAllDay, start, end) {
		          var isSameDate = moment(start).isSame(end);
		          var endFormat = (isSameDate ? '' : 'YYYY.MM.DD ') + 'hh:mm';
		          if (isAllDay) {
		            return moment(start).format('YYYY.MM.DD') + (isSameDate ? '' : ' - ' + moment(end).format('YYYY.MM.DD'));
		          }
		          return (start.getHours() + 'h' + start.getMinutes() + ' - ' + end.getHours() + 'h' + end.getMinutes());
		        },
		        popupDetailBody: function(schedule) {
					return 'Lien : ' + schedule.body;
		        },
		        timegridDisplayPrimaryTime: function(time) {
		            var hour = time.hour;
		            // var meridiem = hour >= 12 ? 'pm' : 'am';

		            // if (hour > 12) {
		            //   hour = hour - 12;
		            // }

		            return hour + ' ' + meridiem;
		            },
		        timegridDisplayTime: function(time) {
		            return getPadStart(time.hour) + ':' + getPadStart(time.hour);
		        },
		        timegridCurrentTime: function(timezone) {
		            var templates = [];

		            if (!timezone) {
		                return '';
		            }

		            if (timezone.dateDifference !== 0) {
		                templates.push('[' + timezone.dateDifferenceSign + timezone.dateDifference + ']<br>');
		            }

		            templates.push(moment(timezone.hourmarker.toUTCString()).format('HH:mm'));
		            return templates.join('');
		        },
		        dayGridTitle: function(viewName) {
	            	var title = '';
					switch(viewName) {
						case 'milestone':
						  title = '<span class="tui-full-calendar-left-content">Etapes</span>';
						  break;
						case 'task':
						  title = '<span class="tui-full-calendar-left-content">Tâches</span>';
						  break;
						case 'allday':
						  title = '<span class="tui-full-calendar-left-content">Journée entière</span>';
						  break;
					}
					return title;
	            }
	        },
	    });

	    $('.move-today').click(function(event) {
	        calendar.today();
	        // $('#renderRange').text(calendar.getDate());
	    });

	    $('.move-prev').click(function(event) {
	        calendar.prev();
	        // $('#renderRange').text(calendar.getDate());
	    });
	    $('.move-next').click(function(event) {
	        calendar.next();
	        // $('#renderRange').text(calendar.getDate());
	    });

	    $.ajax({
	        url: '/ajax/calendar',
	        type: 'POST',
	        dataType: 'json',
	    })
	    .done(function(data) {
	        calendar.createSchedules(data);
	    })
	    .fail(function() {
	    })
	    .always(function() {
	    });
	}

    // Offre Title webform
    $('#edit-offre').val($('#offre-title').text());

    // Slick Slider

    slick_items_parameters = {
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: !1,
        autoplaySpeed: 2e3,
        dots: !0,
        arrows: !0,
        responsive: [{
            breakpoint: 768,
            settings: {
                arrows: !1,
            }
        }]
    };


    var pathname = window.location.pathname;
    var search = window.location.search.split('=')['1'];
    if (pathname=='/panier'){
        history.pushState({}, null, pathname);
    }else if (pathname=='/nos-kits-professionnels' && search){
        let usernam = localStorage.getItem("Drupal.visitor.name");
        let email = localStorage.getItem("Drupal.visitor.mail");
        if (usernam != undefined && usernam != '' && email != undefined && email != '') {

        }else {
            addPanier(search);
            var dialog = document.getElementById('window');
            dialog.show();
        }


        history.pushState({}, null, pathname);
    }else if (pathname=='/delete' && search){
        history.pushState({}, null, "/panier");
    }else if (pathname=='/node' && search){
        history.pushState({}, null, "/");
    }else if (pathname=='/node'){
        history.pushState({}, null, "/");
        /*$("#confirmMail").css("dispal", "block");
        $( "#dialog-message" ).dialog({
            modal: true,
            buttons: {
                Ok: function() {
                    $( this ).dialog( "close" );
                }
            }
        });*/
    }





	$('a.nav-item').click(function (e) {
	  e.preventDefault()
	  $(this).tab('show')
	})


	$('input[type="range"]').change(function () {
	    var val = ($(this).val() - $(this).attr('min')) / ($(this).attr('max') - $(this).attr('min'));

	    $(this).css('background-image',
	                '-webkit-gradient(linear, left top, right top, '
	                + 'color-stop(' + val + ', #94A14E), '
	                + 'color-stop(' + val + ', #C5C5C5)'
	                + ')'
	                );
	});

    var e = $("#burger-menu");
    e.click(function() {
        e.toggleClass("open"), $(".overlay-menu, .navbarheader").toggleClass("open");
        $('body').toggleClass('menu-open');
    });
    window.addEventListener("scroll", function() {
        ! function() {
            if (1150 < $(window).width()) {
                var e = window.pageYOffset,
                    s = $(".navbar-fixed-top");
                230 <= e ? s.addClass("is-scroll") : s.removeClass("is-scroll")
            }
        }()
    }),

		0 < $(".slider-home").length && $(".slider-home").slick({
        dots: false,
        arrows: !0,
        infinite: !0,
        speed: 300,
        slidesToShow: 1,
        slidesToScroll: 1,
        responsive: [{
            breakpoint: 1370,
            settings: {
                arrows: !1
            }
        }]
    }), 0 < $(".slide-accordion").slick({
        dots: false,
        arrows: !0,
        infinite: !0,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 3,
        responsive: [{
            breakpoint: 1370,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: !1
            }
        }]
    }), 0 < $(".slide-actus").slick({
        dots: false,
        arrows: !0,
        infinite: !0,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 3,
        responsive: [{
            breakpoint: 1370,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: !1
            }
        }]
    }), AOS.init({
        duration: 1e3,
        once: !0,
        disable: "mobile"
    })



			if( $('.slider-produits').length > 0){
			$('.slider-produits').slick({
			dots: true,arrows: false,infinite: false,speed: 300,slidesToShow:1,slidesToScroll: 2,appendArrows: $('.slider-produits'),
			responsive: [
			{breakpoint: 9999,settings: {slidesToShow: 1,slidesToScroll: 1}},
			{breakpoint: 992,settings: {slidesToShow: 1}},
			{breakpoint: 520,settings: {slidesToShow: 1,slidesToScroll: 1}}
			]
			});
			};

			if( $('#reassurance .items').length > 0){
			$('#reassurance .items').slick({
			dots: false,arrows: true,infinite: false,speed: 300,slidesToShow:1,slidesToScroll: 2,appendArrows: $(''),
			responsive: [
			{breakpoint: 9999,settings: 'unslick'},
			{breakpoint: 992,settings: {slidesToShow: 1}},
			{breakpoint: 520,settings: {slidesToShow: 1,slidesToScroll: 1}}
			]
			});
			};

			if( $('#histoire .time-line').length > 0){
			$('#histoire .time-line').slick({
			dots: false,arrows: true,infinite: false,speed: 300,slidesToShow:1,slidesToScroll: 2,appendArrows: $('#histoire'),
			responsive: [
			{breakpoint: 9999,settings:  {slidesToShow:4}},
			{breakpoint: 992,settings: {slidesToShow: 1}},
			{breakpoint: 520,settings: {slidesToShow: 1,slidesToScroll: 1}}
			]
			});
			};
});




/* CUSTOM SELECT  */




var x, i, j, l, ll, selElmnt, a, b, c;
/* Look for any elements with the class "custom-select": */
x = document.getElementsByClassName("custom-select");
l = x.length;
for (i = 0; i < l; i++) {
  selElmnt = x[i].getElementsByTagName("select")[0];
  ll = selElmnt.length;
  /* For each element, create a new DIV that will act as the selected item: */
  a = document.createElement("DIV");
  a.setAttribute("class", "select-selected");
  a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
  x[i].appendChild(a);
  /* For each element, create a new DIV that will contain the option list: */
  b = document.createElement("DIV");
  b.setAttribute("class", "select-items select-hide");
  for (j = 1; j < ll; j++) {
    /* For each option in the original select element,
    create a new DIV that will act as an option item: */
    c = document.createElement("DIV");
    c.innerHTML = selElmnt.options[j].innerHTML;
    c.addEventListener("click", function(e) {
        /* When an item is clicked, update the original select box,
        and the selected item: */
        var y, i, k, s, h, sl, yl;
        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
        sl = s.length;
        h = this.parentNode.previousSibling;
        for (i = 0; i < sl; i++) {
          if (s.options[i].innerHTML == this.innerHTML) {
            s.selectedIndex = i;
            h.innerHTML = this.innerHTML;
            y = this.parentNode.getElementsByClassName("same-as-selected");
            yl = y.length;
            for (k = 0; k < yl; k++) {
              y[k].removeAttribute("class");
            }
            this.setAttribute("class", "same-as-selected");
            break;
          }
        }
        h.click();
    });
    b.appendChild(c);
  }
  x[i].appendChild(b);
  a.addEventListener("click", function(e) {
    /* When the select box is clicked, close any other select boxes,
    and open/close the current select box: */
    e.stopPropagation();
    closeAllSelect(this);
    this.nextSibling.classList.toggle("select-hide");
    this.classList.toggle("select-arrow-active");
  });
}

function closeAllSelect(elmnt) {
  /* A function that will close all select boxes in the document,
  except the current select box: */
  var x, y, i, xl, yl, arrNo = [];
  x = document.getElementsByClassName("select-items");
  y = document.getElementsByClassName("select-selected");
  xl = x.length;
  yl = y.length;
  for (i = 0; i < yl; i++) {
    if (elmnt == y[i]) {
      arrNo.push(i)
    } else {
      y[i].classList.remove("select-arrow-active");
    }
  }
  for (i = 0; i < xl; i++) {
    if (arrNo.indexOf(i)) {
      x[i].classList.add("select-hide");
    }
  }
}

/* If the user clicks anywhere outside the select box,
then close all select boxes: */
document.addEventListener("click", closeAllSelect);

/* CUSTOM FORM RANGE DATE */



function searchKitsByCategories(id) {
    var categories = $('input[name="scales"]:checked').serialize();
    $.ajax({
        url: '/kits/search',
        type: 'POST',
        dataType: 'json',
        data: {search: categories},
    }).done(function (data) {
        console.log(data);
        var html = '';
        $.each(data.kits, function(index, val)
        {
            html += '<div class="col-md-4 hovereffect margin-bottom-60" data-aos="fade" data-aos-delay="300">';
                html += '<div>';
                    html += '<img class="block text-center margin-auto border-color-bottom" src="'+val.image+'" alt="'+val.title+'">';
                    html += '<div class="flex img-hover">';
                    html += '<a href="'+val.link+'" class="oeil"></a>';
                    if (data.logged!=null && data.logged!=undefined && data.logged!=''){
                        html += '<a style="cursor: pointer" onclick="addPanierPopupInfo('+val.id+')" class="panier"></a>';
                    }else {
                        html += '<a style="cursor: pointer" onclick="addPanier('+val.id+')" class="panier"></a>';
                    }
                    html += '</div>';
                html += '</div>';
                html += '<span class="lora size20 bold-font uppercase lineheight30 block margin-top-30 margin-bottom-20 text-left">'+val.title+'</span>';
                html += '<p class=" block  lineheight15 block padding-bottom-30">'+ val.body.substr(0, 150) +' ...</p>';
                html += '<span class="date size20 light-font">'+val.prix+'</span>';
            html += '</div>';
        });
        $('#kits').html(html);
    }).fail(function() {

    }).always(function() {

    });
};

function deleteKitOfCart(id){
    $.ajax({
        url: '/delete',
        type: 'POST',
        dataType: 'json',
        data: {id: id},
    }).done(function (data) {
        location.reload();
    }).fail(function() {

    }).always(function() {

    });
}


function addNombreKitCart(id) {
    let nombre = $('#'+id).val();
    nombre = parseInt(nombre)
    //alert(nombre);
    if (nombre<=0){
        $('#'+id).val(1);
    }else
    {
        $.ajax({
            url: '/updateNumberOfKitToCart',
            type: 'POST',
            dataType: 'json',
            data: {id: id, nombre: nombre},
        }).done(function (data) {
            var htmlRecap = '';
            $.each(data.kits, function(index, kit)
            {
                htmlRecap+='<div class="flex space-between  margin-bottom-10">';
                htmlRecap+='<div>'+kit.title+'<strong> x <span>'+kit.nombre+'</span></strong></div>';
                htmlRecap+='<div>'+kit.prixTotal+' €</div>';
                htmlRecap+='</div>';
            });
            $('#recapPanier').html(htmlRecap);
            $('#sommeTotal').text(data.sommes+',00 €');
            $('#amount').val(data.somme+',00 €');
            $('#sommeKit').val(data.sommesKits)
            location.reload();
            if (parseInt(data.sommesKits)>=150){
                $('#gratuiteLivraison').val("gratuiteLivraison")
            }else {
                $('#gratuiteLivraison').val("ngratuiteLivraison")
            }
        }).fail(function() {

        }).always(function() {
        });
    }
}


function addNombreKitCart2(id) {
    let nombre = $('#'+id).val();
    nombre = parseInt(nombre)
    //alert(nombre);
    if (nombre<=0){
        $('#'+id).val(1);
    }else {
        nombre = nombre+1;
        $.ajax({
            url: '/updateNumberOfKitToCart',
            type: 'POST',
            dataType: 'json',
            data: {id: id, nombre: nombre},
        }).done(function (data) {
            var htmlRecap = '';
            $.each(data.kits, function(index, kit)
            {
                htmlRecap+='<div class="flex space-between  margin-bottom-10">';
                htmlRecap+='<div>'+kit.title+'<strong> x <span>'+kit.nombre+'</span></strong></div>';
                htmlRecap+='<div>'+kit.prixTotal+' €</div>';
                htmlRecap+='</div>';
            });
            $('#recapPanier').html(htmlRecap);
            $('#sommeTotal').text(data.sommes+',00 €');
            $('#amount').val(data.somme+',00 €');
            $('#'+id).val(nombre);
            $('#sommeKit').val(data.sommesKits)
            location.reload();
            if (parseInt(data.sommesKits)>=150){
                $('#gratuiteLivraison').val("gratuiteLivraison")
            }else {
                $('#gratuiteLivraison').val("ngratuiteLivraison")
            }
        }).fail(function() {

        }).always(function() {
        });
    }
}

function substractionNombreKitCart(id) {
    let nombre = $('#'+id).val();
    nombre = parseInt(nombre)
    if (nombre==1){
        $('#'+id).val(1);
    }else {
        nombre = nombre-1;
        $.ajax({
            url: '/updateNumberOfKitToCart',
            type: 'POST',
            dataType: 'json',
            data: {id: id, nombre: nombre},
        }).done(function (data) {
            console.log(data);
            var htmlRecap = '';
            $.each(data.kits, function(index, kit)
            {
                htmlRecap+='<div class="flex space-between  margin-bottom-10">';
                htmlRecap+='<div>'+kit.title+'<strong> x <span>'+kit.nombre+'</span></strong></div>';
                htmlRecap+='<div>'+kit.prixTotal+' €</div>';
                htmlRecap+='</div>';
            });
            $('#recapPanier').html(htmlRecap);
            $('#sommeTotal').text(data.sommes+',00 €');
            $('#amount').val(data.somme+',00 €');
            $('#'+id).val(nombre);
            $('#sommeKit').val(data.sommesKits)
            location.reload();
            if (parseInt(data.sommesKits)>=150){
                $('#gratuiteLivraison').val("gratuiteLivraison")
            }else {
                $('#gratuiteLivraison').val("ngratuiteLivraison")
            }
        }).fail(function() {

        }).always(function() {
        });
    }
}


function addPanier(idKit){
  console.log(idKit);
    $.ajax({
        url: '/panier',
        type: 'GET',
        dataType: 'json',
        data: {add: idKit},
    }).done(function (data) {
        console.log(data.nbKitsPanier);
        $('#nbkitsPanier').text(data.nbKitsPanier);

    }).fail(function() {

    }).always(function() {

    });
}

$( function() {
    jQuery.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            if (regexp.constructor != RegExp)
                regexp = new RegExp(regexp);
            else if (regexp.global)
                regexp.lastIndex = 0;
            return this.optional(element) || regexp.test(value);
        }/*,"Numero de telephone non valide"*/
    );

    jQuery.validator.addMethod("laxEmail", function(value, element) {

        // allow any non-whitespace characters as the host part

        return this.optional( element ) || /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@(?:\S{1,63})$/.test( value );

    }, 'Please enter a valid email address.');


    $('#user-register-form').validate({
        errorClass: "error1 fail-alert1",
        validClass: "valid success-alert",
        rules: {
            name: {
                required: true,
            },
            "field_prenom[0][value]":{
                required: true,
            },
            "field_nom[0][value]":{
                required: true,
            },
            mail: {
                required: true,
                mail: true,
                "regex": /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
            },
        },
        messages: {
            name: {
                required: "Ce champ est obligatoire",
            },
            "field_prenom[0][value]":{
                required: "Le prenom est obligatoire"
            },
            "field_nom[0][value]":{
                required: "Le nom est obligatoire"
            },
            mail: {
                required: "Ce champ est obligatoire",
                mail: "Veuillez vérifier votre adresse email",
                regex: "Veuillez vérifier votre adresse email"
            },

        }
    });
    
    $( "#tabs" ).tabs();
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }/*, "Value must not equal arg."*/);



    $("#validateInfoLivraison").validate({
        errorClass: "error fail-alert",
        validClass: "valid success-alert",
        rules: {
            name: {
                required: true,
            },
            lastname : {
                required: true,
            },
            email: {
                required: true,
                "regex": /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/
            },
            phone: {
                "required": true,
                "regex": /^[0-9-+]+$/
                /*"regex": /^\+(?:[0-9] ?){6,14}[0-9]$/*/
            },

            adress : {
                required: true,
            },
            postal : {
                required: true,
                minlength: 4,
            },
            ville : {
                required: true,
                minlength: 3,
            },
            nom_other : {
                required: $(function () {
                    $("#other_adress").change(function () {
                        if ($(this).is(':checked')){
                            return true;
                        }else {
                            return false
                        }
                    })
                    /* $("#other_adress").is(':checked') ? true : false,*/
                })
            },
            prenom_other : {
                required: $(function () {
                    $("#other_adress").change(function () {
                        if ($(this).is(':checked')){
                            return true;
                        }else {
                            return false
                        }
                    })
                   /* $("#other_adress").is(':checked') ? true : false,*/
                })
            },
            adress_other : {
                required: $(function () {
                    $("#other_adress").change(function () {
                        if ($(this).is(':checked')){
                            return true;
                        }else {
                            return false
                        }
                    })
                    /* $("#other_adress").is(':checked') ? true : false,*/
                })
            },
            postal_other : {
                required: $(function () {
                    $("#other_adress").change(function () {
                        if ($(this).is(':checked')){
                            return true;
                        }else {
                            return false
                        }
                    })
                    /* $("#other_adress").is(':checked') ? true : false,*/
                })
            },
            ville_other : {
                required: $(function () {
                    $("#other_adress").change(function () {
                        if ($(this).is(':checked')){
                            return true;
                        }else {
                            return false
                        }
                    })
                    /* $("#other_adress").is(':checked') ? true : false,*/
                })
            },
        },
        messages: {
            name: {
                required: "Ce champ est obligatoire",
            },
            phone: {
                required: "Ce champ est obligatoire",
            },
            lastname: {
                required: "Ce champ est obligatoire",
            },
            email: {
                required: "Ce champ est obligatoire",
            },
            adress: {
                required: "Ce champ est obligatoire",
            },
            postal: {
                required: "Ce champ est obligatoire",
            },
            ville: {
                required: "Ce champ est obligatoire",
            },
            nom_other: {
                required: "Ce champ est obligatoire",
            },
            prenom_other: {
                required: "Ce champ est obligatoire",
            },
            adress_other: {
                required: "Ce champ est obligatoire",
            },
            postal_other: {
                required: "Ce champ est obligatoire",
            },
            ville_other: {
                required: "Ce champ est obligatoire",
            },

        }
    });

} );


$("#contacterNousFORM").validate({
    errorClass: "error fail-alert",
    validClass: "valid success-alert",
    rules: {
        name: {
            required: true,
        },
        lastname : {
            required: true,
        },
        email: {
            required: true,
            "regex": /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/
        },
        phone: {
            "required": true,
            "regex": /^[0-9-+]+$/
            /*"regex": /^\+(?:[0-9] ?){6,14}[0-9]$/*/
        },

        Objet : {
            required: true,
        },
        Message : {
            required: true,
            minlength: 10,
        },
        flexCheckDefaultContact : {
            required: true,
        },
    },
    messages: {
        name: {
            required: "Ce champ est obligatoire",
        },
        lastname: {
            required: "Ce champ est obligatoire",
        },
        email: {
            required: "Ce champ est obligatoire",
            email: "Mail n'est pas valide"
        },
        Objet: {
            required: "Ce champ est obligatoire",
        },
        Message: {
            required: "Ce champ est obligatoire",
            minlength: "Au moins 10 caractères"
        },
        flexCheckDefaultContact: {
            required: "Merci de cocher la case",
        },

    }
});



$("#other_adress").change(function () {
    if ($(this).is(':checked')){
        $("#otherAdress").css("display", "block");
        $("#other").val("other");
    }else {
        $("#otherAdress").css("display", "none");
        $("#other").val("");
    }
})


$("#paiementCarte").change(function () {
    if ($(this).is(':checked')){
        $(this).prop("checked", true);
        $("#paiementPaypal").prop("checked", false);
        $("#paiementParPaypal").css("display", "none");
        $("#paiementParCarte").css("display", "block");

    }
})


$("#paiementPaypal").change(function () {
    if ($(this).is(':checked')){
        $(this).prop("checked", true);
        $("#paiementCarte").prop("checked", false);
        $("#paiementParCarte").css("display", "none");
        $("#paiementParPaypal").css("display", "block");
    }
})



$("#collapsePanier").click(function (){
    $(this).addClass("active");
    $("#choixLivraison").removeClass("active");
    $("#collapseInfoPerso").removeClass("active");
    $("#collapsePaiement").removeClass("active");

    $("#collapse1").css("display", "block");
    $("#collapse2_1").css("display", "none");
    $("#collapse2").css("display", "none");
    $("#collapse3").css("display", "none");
    $("#pcE1").css("display", "block");
    $("#pcE2_1").css("display", "none");
    $("#pcE2").css("display", "none");
    $("#pcE3").css("display", "none");
});

$("#choixLivraison").click(function (){

    $(this).addClass("active");
    $("#collapsePanier").removeClass("active");
    $("#collapseInfoPerso").removeClass("active");
    $("#collapsePaiement").removeClass("active");

    $("#collapse2_1").css("display", "block");
    $("#collapse1").css("display", "none");
    $("#collapse2").css("display", "none");
    $("#collapse3").css("display", "none");
    $("#pcE1").css("display", "none");
    $("#pcE2_1").css("display", "block");
    $("#pcE2").css("display", "none");
    $("#pcE3").css("display", "none");
});

$("#collapseInfoPerso").click(function (){
    if ($("#uniquementFormation").val()=="uniquementFormation"){
        $(this).addClass("active");
        $("#choixLivraison").removeClass("active");
        $("#collapsePanier").removeClass("active");
        $("#collapsePaiement").removeClass("active");

        $("#collapse1").css("display", "none");
        $("#collapse2_1").css("display", "none");
        $("#collapse2").css("display", "block");
        $("#collapse3").css("display", "none");

        $("#pcE1").css("display", "none");
        $("#pcE2_1").css("display", "none");
        $("#pcE2").css("display", "block");
        $("#pcE3").css("display", "none");
    }else if ($("#flexRadioDefault1").is(':checked')==true || $("#flexRadioDefault2").is(':checked') ==true || $("#flexRadioDefault3").is(':checked')==true ){
        $(this).addClass("active");
        $("#choixLivraison").removeClass("active");
        $("#collapsePanier").removeClass("active");
        $("#collapsePaiement").removeClass("active");

        $("#collapse1").css("display", "none");
        $("#collapse2_1").css("display", "none");
        $("#collapse2").css("display", "block");
        $("#collapse3").css("display", "none");

        $("#pcE1").css("display", "none");
        $("#pcE2_1").css("display", "none");
        $("#pcE2").css("display", "block");
        $("#pcE3").css("display", "none");
    }else {
        $("#alertChoixLivraison").css({"display":"block", "color": "red", "text-align": "center"});
    }

});


$("#collapsePaiement").click(function (){
    if ($("#uniquementFormation").val()=="uniquementFormation" && $("#validateInfoLivraison").valid()){
        $(this).addClass("active");
        $("#choixLivraison").removeClass("active");
        $("#collapseInfoPerso").removeClass("active");
        $("#collapseInfoPerso").removeClass("active");

        $("#collapse1").css("display", "none");
        $("#collapse2_1").css("display", "none");
        $("#collapse2").css("display", "none");
        $("#collapse3").css("display", "block");

        $("#pcE1").css("display", "none");
        $("#pcE2_1").css("display", "none");
        $("#pcE2").css("display", "none");
        $("#pcE3").css("display", "block");
    }else if ($("#validateInfoLivraison").valid() && ($("#flexRadioDefault1").is(':checked')==true || $("#flexRadioDefault2").is(':checked') ==true || $("#flexRadioDefault3").is(':checked')==true) ) {
        $(this).addClass("active");
        $("#choixLivraison").removeClass("active");
        $("#collapseInfoPerso").removeClass("active");
        $("#collapseInfoPerso").removeClass("active");

        $("#collapse1").css("display", "none");
        $("#collapse2_1").css("display", "none");
        $("#collapse2").css("display", "none");
        $("#collapse3").css("display", "block");

        $("#pcE1").css("display", "none");
        $("#pcE2_1").css("display", "none");
        $("#pcE2").css("display", "none");
        $("#pcE3").css("display", "block");
    }
});


$("#pcE1").click(function () {
    if ($("#uniquementFormation").val()=="uniquementFormation"){
        $("#collapseInfoPerso").click();
    }else {
        $("#choixLivraison").click();
    }

})


$("#pcE2_1").click(function () {
    if ($("#uniquementFormation").val()=="uniquementFormation"){
        $("#alertChoixLivraison").css("display", "none");
        $("#collapseInfoPerso").attr("id", "collapseInfoPerso");
        $("#collapseInfoPerso").click();
    }else if ($("#flexRadioDefault1").is(':checked')==true || $("#flexRadioDefault2").is(':checked') ==true || $("#flexRadioDefault3").is(':checked')==true ){
        $("#alertChoixLivraison").css("display", "none");
        $("#collapseInfoPerso").attr("id", "collapseInfoPerso");
        $("#collapseInfoPerso").click();
    }else {
        $("#alertChoixLivraison").css({"display":"block", "color": "red", "text-align": "center"});
    }

})

$("#pcE2").click(function () {
    if ($("#validateInfoLivraison").valid()) {
        $('#name1').val($('#name').val());
        $('#lastname1').val($('#lastname').val());
        $('#email1').val($('#email').val());
        $('#phone1').val($('#phone').val());
        $('#adress1').val($('#adress').val());
        $('#adress_plus1').val($('#adress_plus').val());
        $('#postal1').val($('#postal').val());
        $('#ville1').val($('#ville').val());

        $('#name2').val($('#name').val());
        $('#lastname2').val($('#lastname').val());
        $('#email2').val($('#email').val());
        $('#phone2').val($('#phone').val());
        $('#adress2').val($('#adress').val());
        $('#adress_plus2').val($('#adress_plus').val());
        $('#postal2').val($('#postal').val());
        $('#ville2').val($('#ville').val());

        if ($("#other").val()=='other'){
            $('#nom_other2').val($('#nom_other').val());
            $('#prenom_other2').val($('#prenom_other').val());
            $('#societe_other2').val($('#societe_other').val());
            $('#adress_other2').val($('#adress_other').val());
            $('#adress_plus_other2').val($('#adress_plus_other').val());
            $('#postal_other2').val($('#postal_other').val());
            $('#ville_other2').val($('#ville_other').val());

            $('#nom_other1').val($('#nom_other').val());
            $('#prenom_other1').val($('#prenom_other').val());
            $('#societe_other1').val($('#societe_other').val());
            $('#adress_other1').val($('#adress_other').val());
            $('#adress_plus_other1').val($('#adress_plus_other').val());
            $('#postal_other1').val($('#postal_other').val());
            $('#ville_other1').val($('#ville_other').val());
        }

        $("#collapsePaiement").click();
    }
   /* $("#collapsePaiement").click();*/
})


function bonjour(title) {
    console.log(title);
    //let links = $('[name='+title+']').val()
}

function download(title){
    console.log(title);
   // let links = $('[name='+title+']').val();
    $('[name='+title+']').each(function(  ) {
       console.log( $( this ).val() );
       let url = $( this ).val();
       if (!url.includes("https://")) {
           url = "https://color-vintage.gazolinecommunication.com"+url;
       }
       console.log(url);
        $.ajax({
            url: url,
            method: 'GET',
            xhrFields: {
                responseType: 'blob'
            },
            success: function (data) {
                var a = document.createElement('a');
                var url = window.URL.createObjectURL(data);
                a.href = url;
                a.download = 'myfile.pdf';
                document.body.append(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            }
        });
   });
}

$( function() {
    let dateFormationAArray = [];
    let dateFormationMArray = [];
    let dateFormationDArray = [];

    let dateFinFormationAArray = [];
    let dateFinFormationMArray = [];
    let dateFinFormationDArray = [];


    // Debut formation
    $('.dateFormationA').each(function(  ) {
        dateFormationAArray.push($(this).val());
    });

    $('.dateFormationM').each(function(  ) {
        dateFormationMArray.push($(this).val());
    });
    $('.dateFormationD').each(function(  ) {
        dateFormationDArray.push($(this).val());
    });
    // Fin Debut formation


    $('.dateFormationFinA').each(function(  ) {
        dateFinFormationAArray.push($(this).val());
    });

    $('.dateFormationFinM').each(function(  ) {
        dateFinFormationMArray.push($(this).val());
    });
    $('.dateFormationFinD').each(function(  ) {
        dateFinFormationDArray.push($(this).val());
    });

    var dateCalendar = [];


    let sessionTab = [];
    for (let i = 0; i < dateFormationAArray.length; i++) {
        sessionTab.push({
            'anneeDeb': dateFormationAArray[i], 'moisDeb': dateFormationMArray[i]-1, 'jourDeb': dateFormationDArray[i],
            'anneeFin': dateFinFormationAArray[i], 'moisFin': dateFinFormationMArray[i]-1,'jourFin':dateFinFormationDArray[i],
            'range': Math.abs(dateFinFormationDArray[i]-dateFormationDArray[i])
        });
    }

    let nbDayByMounth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    for (let i = 0; i < sessionTab.length; i++)
    {
        let sessionOne = sessionTab[i];
        let date = new Date(sessionOne.anneeDeb, sessionOne.moisDeb);
        if (sessionOne.moisDeb == sessionOne.moisFin)
        {
            let currentDay = sessionOne.jourDeb;
            console.log(sessionOne.jourDeb);
            console.log(sessionOne.jourFin);
            for (let j = sessionOne.jourDeb; j <= sessionOne.jourFin; j++) {
                dateCalendar.push(date.setDate(j));
            }
        }else {
            console.log("bonjour");
            let nbJoursMoisDeb = nbDayByMounth[sessionOne.moisDeb-1];
            let nbJoursMoisFin = nbDayByMounth[sessionOne.moisFin-1];
            for (let j = sessionOne.jourDeb; j <= nbJoursMoisDeb; j++) {
                let date = new Date(sessionOne.anneeDeb, sessionOne.moisDeb, sessionOne.jourDeb);
                dateCalendar.push(date.setDate(j));
            }

            for (let j = 1; j <= sessionOne.jourFin; j++) {
                let date = new Date(sessionOne.anneeFin, sessionOne.moisFin, sessionOne.jourFin);

                dateCalendar.push(date.setDate(j));
            }
            console.log("Fin bonjour");
        }
    }


    var options = {}

    options.dayNamesMin = ['D', 'L', 'M', 'M', 'J', 'V', 'S'];
    options.monthNames = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
    //options.timeOnlyTitle = 'Heure';
    //options.timeText = '';
    //options.hourText = 'Heure';
    //options.minuteText = 'Minute';
   // options.nextText = 'Suivant';
    //options.prevText = 'Précédent';
    //options.currentText = 'Heure actuelle';
   // options.closeText = 'Valider';
    $('#mdp-demo').multiDatesPicker(options);
    $('#mdp-demo').multiDatesPicker({
        addDates: dateCalendar,
        /*beforeShowDay: $.datepicker.noWeekends,*/
        maxPicks: dateCalendar.length

    });


} );

$('#addProduitPanierpopup').click(function (){
    console.log('bonjour');
    var dialog2 = document.getElementById('window2');
    dialog2.close();
})

function addPanierPopupInfo(id){
    ///nos-kits-professionnels
    let currentUri = $('#currentURI').val();
    let currentUriF = $('#currentURIfORMATION').val();
    let currentUriK = $('#currentURIKIT').val();
    let usernam = localStorage.getItem("Drupal.visitor.name");
    let email = localStorage.getItem("Drupal.visitor.mail");
    if (usernam != undefined && usernam != '' && email != undefined && email != '') {
        dialog4.show();
    }else {
        var dialog2 = document.getElementById('window2');
        dialog2.show();
    }

    let html = '';
    if (currentUri != undefined){
         html = '<a href="/user-login?destination=/'+currentUri+'?addIdProduit='+id+'" class="connexion2">Se connecter</a>'
    }
    if (currentUriF!= undefined){
         html = '<a href="/user-login?destination=/'+currentUriF+'?addIdProduit='+id+'" class="connexion2">Se connecter</a>'
    }
    if (currentUriK != undefined){
         html = '<a href="/user-login?destination='+currentUriK+'?addIdProduit='+id+'" class="connexion2">Se connecter</a>'
    }
    $('#addProduitPanierpopup').html(html);
}

(function() {
    var dialog = document.getElementById('window');
    var dialog2 = document.getElementById('window2');

    $('.show').each(function( ) {
        //dateFormationDArray.push($(this).val());
        if ($(this).click(function() {
            dialog.show();
        }));
    });



    $('#exit').each(function( ) {
        //dateFormationDArray.push($(this).val());
        if ($(this).click(function() {
            dialog.close();
        }));
    });

    $('.fermer').each(function( ) {
        //dateFormationDArray.push($(this).val());
        if ($(this).click(function() {
            dialog.close();
        }));
    });

    $('.fermer').each(function( ) {
        //dateFormationDArray.push($(this).val());
        if ($(this).click(function() {
            dialog2.close();
        }));
    });

    $('.exit2').each(function( ) {
        //dateFormationDArray.push($(this).val());
        if ($(this).click(function() {
            dialog2.close();
        }));
    });


})();


// $('#paypalpayer').click(function() {
//     console.log("paypal");
//     $("#paypalform").css("display", "block");
//     $("#cbform").css("display", "none");
// });


// $('#cbpayer').click(function() {
//     $("#paypalform").css("display", "none");
//     $("#cbform").css("display", "block");
// });


 $('#flexRadioDefault1').click(function() {
     $("#alertChoixLivraison").css("display","none");
     $(choixLivraisonValue1).val("Click And Collect");
     $(choixLivraisonValue2).val("Click And Collect");
     $('#signatureColissimo').css("display", "block");
     $('#messSignature').text("Click And Collect");
     $('#prixSignature').text("0 €");


     if ($('#gratuiteLivraison').val() == "gratuiteLivraison"){

     }
     if ($('#gratuiteLivraison').val() != "gratuiteLivraison" || $('#gratuiteLivraison').val()==undefined){
         if (defaultSomme1==1){
             defaultSomme1 = 0;
             defaultSomme2 = 0;
             let sommevalue = parseInt($('#amount').val())-9;
             $('#sommeTotal').text(sommevalue+',00 €');
             $('#amount').val(sommevalue);
             $('#amount2').val(sommevalue);
         }

         if (defaultSomme2==1){
             defaultSomme2 = 0;
             defaultSomme1 = 0;
             let sommevalue = parseInt($('#amount').val())-8;
             $('#sommeTotal').text(sommevalue+',00 €');
             $('#amount').val(sommevalue);
             $('#amount2').val(sommevalue);
         }
     }
 });

$('#flexRadioDefault2').click(function() {
    $("#alertChoixLivraison").css("display","none");
    if ($(this).is(':checked')){
        $("#choixLivraisonValue1").val("Colissimo Sans Signature");
        $("#choixLivraisonValue2").val("Colissimo Sans Signature");
        $('#signatureColissimo').css("display", "block");
        $('#messSignature').text("Colissimo sans signature");


        if ($('#gratuiteLivraison').val() != "gratuiteLivraison" || $('#gratuiteLivraison').val()==undefined)
        {
            $('#prixSignature').text("8 €");
            if (defaultSomme1==0){
                defaultSomme2 = 1;
                defaultSomme1 = 0;
                let sommevalue = $('#amount').val();
                $('#sommeTotal').text(somme(sommevalue, 8)+',00 €');
                $('#amount').val(somme(sommevalue, 8));
                $('#amount2').val(somme(sommevalue, 8));
            }else {
                defaultSomme2 = 1;
                let sommevalue = parseInt($('#amount').val())-9;
                $('#sommeTotal').text(somme(sommevalue, 8)+',00 €');
                $('#amount').val(somme(sommevalue, 8));
                $('#amount2').val(somme(sommevalue, 8));
            }
        }else {
            $('#prixSignature').text("0 €");
        }


    }else {

    }


});

$('#flexRadioDefault3').click(function() {
    $("#alertChoixLivraison").css("display","none");
    $("#choixLivraisonValue1").val("Colissimo Avec Signature");
    $("#choixLivraisonValue2").val("Colissimo Avec Signature");
    $('#signatureColissimo').css("display", "block");
    $('#messSignature').text("Colissimo avec signature");
    $('#prixSignature').text("9 €");
    //alert(somme(data.somme, 9))
    if ($('#gratuiteLivraison').val() != "gratuiteLivraison" || $('#gratuiteLivraison').val()==undefined)
    {
        $('#prixSignature').text("9 €");
        if (defaultSomme2==0){
            defaultSomme1 = 1;
            let sommevalue = $('#amount').val();
            $('#sommeTotal').text(somme(sommevalue, 9)+',00 €');
            $('#amount').val(somme(sommevalue, 9));
            $('#amount2').val(somme(sommevalue, 9));
        }else {
            defaultSomme1 = 1;
            defaultSomme2 = 0;
            let sommevalue = parseInt($('#amount').val())-8;
            $('#sommeTotal').text(somme(sommevalue, 9)+',00 €');
            $('#amount').val(somme(sommevalue, 9)+',00 €');
            $('#amount2').val(somme(sommevalue, 9));
        }
    }else {
        $('#prixSignature').text("0 €");
    }


});

function somme(val1, val2){
    return parseInt(val1)+parseInt(val2);
}

// $('#colissimo').click(function() {
//     $("#colissimoLivraison").css("display", "block");
//     $("#clickANDcollectLivraison").css("display", "none");
//     $(this).css("background-color","#ca8723" );
//     $(clickANDcollect).css("background-color","#000" );

//     $(choixLivraisonValue1).val("Colissimo");
//     $(choixLivraisonValue2).val("Colissomo");

// });




$('#radio-1').on(function() {
    if ($(this).is(':checked')){
        $(choixLivraisonValue1).val("Colissimo Avec Signature");
        $(choixLivraisonValue2).val("Colissomo Avec Signature");
    }

    $(choixLivraisonValue1).val("Colissimo");
    $(choixLivraisonValue2).val("Colissomo");

});

$('#radio-2').on(function() {
    if ($(this).is(':checked')){
        $(choixLivraisonValue1).val("Colissimo Avec Signature");
        $(choixLivraisonValue2).val("Colissomo Avec Signature");
    }

    $(choixLivraisonValue1).val("Colissimo");
    $(choixLivraisonValue2).val("Colissomo");

});

/*$( function() {
    $( "input" ).checkboxradio();
    $( "fieldset" ).controlgroup();
} );*/


$('#ensavoirplusButton').click(function() {
    $("#plusDefault").css("display", "none");
    $("#ensavoirplus").css("display", "block");
    $(this).css("display", "none");
    $("#ensavoirMoinButton").css("display", "block");
});


$('#ensavoirMoinButton').click(function() {
    $("#plusDefault").css("display", "block");
    $("#ensavoirplusButton").css("display", "block");
    $(this).css("display", "none");
    $("#ensavoirplus").css("display", "none");
});

var pathname = window.location.pathname;

if (pathname=='/user-login'){
    $('#edit-mail').keyup(function () {
        if ($('#edit-mail').val() != undefined && $('#edit-mail').val()!= '') {
            var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            $('#alertmail').remove();
            if(!regex.test($('#edit-mail').val())) {
                $('#edit-mail--description').append("<p style='color: red' id='alertmail' classe='alertmail'>Veuillez saisir un mail valide ! ex: email@email.Com</p>");
            }else {
                $('#alertmail').remove();
                $.ajax({
                    url: '/user-checkMail',
                    type: 'POST',
                    dataType: 'json',
                    data: {mail: $('#edit-mail').val()},
                }).done(function (data) {
                    $('#alertmail').remove();
                    $('#edit-mail-error').remove();
                    if (data == true) {
                        $('#edit-mail-error').remove();
                        $('.fail-alert1').css("display", "none !important");
                        $('#edit-mail--description').append("<p style='color: red' id='alertmail' classe='alertmail'>Attention, email deja utilisé</p>");
                        $('#edit-submit--2').css("display", "none");
                    } else {
                        $('#edit-mail-error').remove();
                        $('.fail-alert1').css("display", "none !important");
                        /* $('.alertmail').remove();*/
                        $('#alertmail').remove();
                        $('#edit-submit--2').css("display", "block");
                    }
                }).fail(function () {

                }).always(function () {
                });
            }
        }
    })
}


$('#edit-name--2').keyup(function () {
    if ($('#edit-name--2').val() != undefined && $('#edit-name--2').val()!= '') {
        $.ajax({
            url: '/user-checkUserName',
            type: 'POST',
            dataType: 'json',
            data: {username: $('#edit-name--2').val()},
        }).done(function (data) {
            $('#alertusername').remove();
            $('#edit-mail-error').remove();
            if (data == true) {
                $('#edit-mail-error').remove();
                $('#edit-submit--2').css("display", "none");
                $('#edit-name--2--description').append("<p style='color: red' id='alertusername' class='alertusername'>Attention, username déja utilisé</p>");
            } else {
                $('#edit-mail-error').remove();
                $('#alertusername').remove();
                $('#edit-submit--2').css("display", "block");
            }
        }).fail(function () {

        }).always(function () {
        });
    }
});



function  generateFacture(id){
    $.ajax({
        url: '/facture/'+id,
        type: 'GET',
        dataType: 'json',
    }).done(function (data) {
        console.log(data);
    }).fail(function () {

    }).always(function () {
    });
}

$(function() {
    let usernam = localStorage.getItem("Drupal.visitor.name");
    let email = localStorage.getItem("Drupal.visitor.mail");
    let dialog4 = document.getElementById('window3');
    if (usernam != undefined && usernam != '' && email != undefined && email != '') {
        dialog4.show();
    }

    let dialog5 = document.getElementById('window3');
    if ($('#exit3').click(function() {
        $('#window3').css("display","none");
        localStorage.removeItem("Drupal.visitor.name");
        localStorage.removeItem("Drupal.visitor.mail");
    }));

})


function displayByCategoryFormation(idCatFormation){
   // alert("bonjour");
}

