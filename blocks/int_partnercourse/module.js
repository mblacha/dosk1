
$(function() {
	
	$("#id_enrolfrom").datepicker({
		dateFormat: 'dd-mm-yy',

		onSelect: function(selected) {
			$("#id_enrolto").datepicker("option","minDate", selected)
		},

	  });

	  $("#id_enrolto").datepicker({
		  dateFormat: 'dd-mm-yy',

	      onSelect: function(selected) {
	         $("#id_enrolfrom").datepicker("option","maxDate", selected)
	      }

	  }); 

	  $('input, text').placeholder(); 
	
	$("#enrol_partnercourse").validate({
		rules: {
			enrolfrom: {
				 required: true,
				 datecheck: true
			},
			enrolto: {
				 required: true,
				 datecheck: true
			},
			user: {
				selectcheck: true
			},
			course: {
				selectcheck: true
			},

		},		
		errorPlacement: function(error, element) {
			
			if ((element.attr("name") == "enrolfrom") || (element.attr("name") == "enrolto")){
			  error.insertAfter("#errordata");				  
			} else {
			  error.insertAfter(element);
			}	
			
		},
		messages: {
			enrolfrom: {
				required: "Pole wymagane.",
				datecheck: "Niepoprawny format daty"
			},
			enrolto: {
				required: "Pole wymagane",
				datecheck: "Niepoprawny format daty"
			},			
		}
	});
	
	$.validator.addMethod("datecheck", function(value) {
	   return /^(0[1-9]|[12][0-9]|3[01])[-](0[1-9]|1[012])[-](19|20)\d\d$/.test(value);	   
	});


    $.validator.addMethod('selectcheck', function (value) {
        return (value != '0');
    }, "Pole wymagane");

	
});


