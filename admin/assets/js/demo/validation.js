$(function () {
	var rules = {
		    	rules: {
					username: {
						minlength: 2,
						required: true
					},
					ime: {
						minlength: 2,
						required: true
					},
					prezime: {
						minlength: 2,
						required: true
					},
					email: {
						required: true,
						email: true
					},
					password: {
						required: false,
						minlength: 6,
					},					
					subject: {
						minlength: 2,
						required: true
					},
					message: {
						minlength: 2,
						required: true
					},
					ipmas: {
						required: true,
						ipcheck: true
					},
					datacentar: {
						required: true
					},
					pw: {
						required: true
					},
					root: {
						required: true
					},
					ssh2port: {
						required: true
					},
					validateSelect: {
						required: true
					},
					validateCheckbox: {
						required: true,
						minlength: 2	
					},
					validateRadio: {
						required: true
					}
				}
		    };
		
	    var validationObj = $.extend (rules, Application.validationRules);
	    
		$('#validation-form').validate(validationObj);
		$('#klijentadd-validate').validate(validationObj);
		
});