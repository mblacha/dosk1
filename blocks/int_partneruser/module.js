$(function() {
	
	//set initial state.    
    if($('#id_profile_field_peselbrak').is(":checked")) {
    	$('#id_profile_field_pesel').prop('disabled', true);
    }
    
    if($('#id_profile_field_nrswiadectwabrak').is(":checked")) {
    	$('#id_profile_field_nrswiadectwa').prop('disabled', true);
    }
    
    if($('#id_profile_field_katswiadectwabrak').is(":checked")) {
    	$('#id_profile_field_cert_C').prop('disabled', true);
    	$('#id_profile_field_cert_C1').prop('disabled', true);
    	$('#id_profile_field_cert_CE').prop('disabled', true);
    	$('#id_profile_field_cert_C1E').prop('disabled', true);
    	$('#id_profile_field_cert_D').prop('disabled', true);
    	$('#id_profile_field_cert_D1').prop('disabled', true);
    	$('#id_profile_field_cert_DE').prop('disabled', true);
    	$('#id_profile_field_cert_D1E').prop('disabled', true);
    }


    $('#id_profile_field_peselbrak').change(function() {
        if($(this).is(":checked")) {
            //var returnVal = confirm("Are you sure?");
            //$(this).attr("checked", returnVal);
        	$('#id_profile_field_pesel').prop('disabled', true);
        } else {
        	$('#id_profile_field_pesel').prop('disabled', false);
        }                
    });
    
    $('#id_profile_field_nrswiadectwabrak').change(function() {
        if($(this).is(":checked")) {            
        	$('#id_profile_field_nrswiadectwa').prop('disabled', true);
        } else {
        	$('#id_profile_field_nrswiadectwa').prop('disabled', false);
        }                
    });
    
    $('#id_profile_field_katswiadectwabrak').change(function() {
        if($(this).is(":checked")) {            
        	$('#id_profile_field_cert_C').prop('disabled', true);
        	$('#id_profile_field_cert_C1').prop('disabled', true);
        	$('#id_profile_field_cert_CE').prop('disabled', true);
        	$('#id_profile_field_cert_C1E').prop('disabled', true);
        	$('#id_profile_field_cert_D').prop('disabled', true);
        	$('#id_profile_field_cert_D1').prop('disabled', true);
        	$('#id_profile_field_cert_DE').prop('disabled', true);
        	$('#id_profile_field_cert_D1E').prop('disabled', true);
        } else {
        	$('#id_profile_field_cert_C').prop('disabled', false);
        	$('#id_profile_field_cert_C1').prop('disabled', false);
        	$('#id_profile_field_cert_CE').prop('disabled', false);
        	$('#id_profile_field_cert_C1E').prop('disabled', false);
        	$('#id_profile_field_cert_D').prop('disabled', false);
        	$('#id_profile_field_cert_D1').prop('disabled', false);
        	$('#id_profile_field_cert_DE').prop('disabled', false);
        	$('#id_profile_field_cert_D1E').prop('disabled', false);
        }                
    });  
    
    $(".postcode").mask("99-999");
    $(".calendar").mask("99-99-9999");       
    $(".street").mask("ul.?*********************************************",{placeholder:" "});
    $(".pkk").mask("99999999999999999999999999");

	
	$('input, text').placeholder();   
	
	$( ".calendar" ).datepicker($.datepicker.regional['pl']);

	
	$("#edit_user").validate({
		
		rules: {
			firstname: {
				 required: true,				
			},			
			lastname: {
				 required: true,				
			},			
			profile_field_pesel: {
				peselcheck: true,		
			},			
			profile_field_dataur: {
				datecheck: true,		
			},
			
		},
		
		messages: {
			profile_field_pesel: {
				peselcheck: "Niepoprawny numer pesel"
			},
			profile_field_dataur: {
				datecheck: "Niepoprawny format daty"
			},						
		}

	});
	
	$.validator.addMethod("datecheck", function(value) {
		return /^(0[1-9]|[12][0-9]|3[01])[-](0[1-9]|1[012])[-](19|20)\d\d$/.test(value);	   
	});
	
	$.validator.addMethod("peselcheck", function(value) {		
		var valid = /^[0-9]{11}$/.test(value);
	
		if(valid){	
			var wagi = [9,7,3,1,9,7,3,1,9,7];
			var suma = 0;
			    
			for(var i=0;i<wagi.length;i++) {
				suma+=(parseInt(value.substring(i,i+1),10) * wagi[i]);
			}
			suma=suma % 10;
					
			return (suma===parseInt(value.substring(10,11),10));
		} else {
			return false;
		}
	});

	
	
});
