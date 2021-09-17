



jQuery(document).on("click","#helpemailsend",function() {


    var nameReg    = /^[a-zA-Z ]*$/;
    var numberReg  =  /^[0-9]+$/;
    var emailReg   = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

    var name          = jQuery('#formName').val();   
    var email         = jQuery('#formemail').val();
    var role          = jQuery('#role').val();
    var company       = jQuery('#formcompany').val();
    var formmessage   = jQuery('#formmessage').val();

    
    var inputVal = new Array(name, email, role, company, formmessage);


           if(name == ""){
                jQuery('#formName').after('<span class="errorcustom"> Please enter your name </span>');

                return false;
                
            }
          if(email == ""){
            jQuery('#formemail').after('<span class="errorcustom"> Please enter your email</span>');
            return false;
          }else if(!emailReg.test(email)){            
            jQuery('#formemail').after('<span class="errorcustom"> Please enter a valid email address</span>');
            return false;
         }
         if(role == ""){
                jQuery('#formName').after('<span class="errorcustom"> Please enter your user type</span>');

                return false;
                
            }

        if(company == ""){
                jQuery('#formName').after('<span class="errorcustom"> Please enter your company</span>');

                return false;
                
            }
            if(formmessage == ""){
                jQuery('#formmessage').after('<span class="errorcustom"> Please enter your message</span>');

                return false;
                
            }

       setTimeout(function(){ jQuery(".errorcustom").remove(); },  5000);     

    if(email !='') {


        
    var BForm = jQuery("#helpmailform input").serialize();
   
    var ajaxurl = common_data.ajax_url;
    
    jQuery.ajax({ 
         url: ajaxurl,
         data: {action: 'help_email', data:inputVal },
         method:"POST",  
         success: function(data) {
               // This prints '0', I want this to print whatever name the user inputs in the form.
              

              if(data.success==1){                    
                
                         jQuery('#formmessage').val('');
                         jQuery(".modal-body").prepend('<div class="alert alert-success" role="alert" >'+data.message+'</div>');
                          
                          setTimeout(function(){
                            jQuery(".mailModal").trigger('click'); 
                            jQuery(".alert-success").remove();


                      },  3000);             
  
              }else if(data.success==0){
                  
                         
                         jQuery(".modal-body").prepend('<div class="alert alert-success" role="alert" >'+data.message+'</div>');
                        setTimeout(function(){

                            jQuery(".alert-error").remove();

                         },  3000); 
              }

        }
    });
}

   /*var map = {};



    map["action"]='help_email';
   

    //console.log(jQuery(this).val());

var ajaxurl = custom_data.ajax_url;

    jQuery.ajax({

        type: 'POST',

        url: ajaxurl,

        data: map, 

        success: function(data) { 


            if(data.success == "1"){
                  
                jQuery(".mailModal").trigger('click');
                           
                jQuery("#mailModal .modal-body").text(data.message);
                
            }else if(data.success == "0"){ 
                jQuery(".mailModal").trigger('click');
               
              jQuery("#mailModal .modal-body").text(data.message);
            }

            //setTimeout(function(){jQuery(".alert-error").remove(); },  5000);          
      }

    });*/

 });


/*Company check*/
jQuery(document).on('change','#bcompany',function(e){     

        e.preventDefault();
         //validateForm();        

        var company     = jQuery(this).val();
        var map = {};
        
        map["action"] ='exit_company1';
        map["data"]   = company;      
        
        if(company!='') {                
           
           var ajaxurl = custom_data.ajax_url;

            
            jQuery.ajax({ 
                type: 'POST',

                 url: ajaxurl,

                 data: map,   
                 success: function(data) {
                       // This prints '0', I want this to print whatever name the user inputs in the form.
                      
                      if(data.status==1){
                         jQuery("#bcompany").next("span").after('<br><span class="errorcustom">'+data.message+'</span>');
                          setTimeout(function(){jQuery(".errorcustom").remove(); },  5000);

          
                      }

                    }
            });
        }

});




function onChange(form) {

  var password = document.getElementById('pass1');
  var confirm = document.getElementById('pass2');
  setTimeout(function(){jQuery(".alert-danger").remove(); },  5000);

  if (password.value=='') {
    jQuery( ".pmpro_reset_password-field-pass1" ).before( '<div class="alert alert-danger" role="alert">Please fill passwords</div>' );
    return false;
  }else if (confirm.value=='') {
    
    jQuery( ".pmpro_reset_password-field-pass1" ).before( '<div class="alert alert-danger" role="alert">Please fill confirm passwords</div>');
    return false;
  }else if (confirm.value != password.value) {
    
    jQuery( ".pmpro_reset_password-field-pass1" ).before( '<div class="alert alert-danger" role="alert">Passwords do not match</div>' );
    return false;
  }
}

setTimeout(function(){jQuery(".alert-danger").remove(); },  5000);
