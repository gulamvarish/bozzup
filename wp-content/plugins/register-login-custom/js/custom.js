
setTimeout(function(){jQuery(".alert").remove(); },  5000); 


//--------------------------Email Validation---------------------------//


jQuery(document).on("focusout",".email",function(e) {
    var emailAddress=jQuery(this).val(); 
    var pattern = /^([a-z\d!#jQuery%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#jQuery%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?jQuery/i;
    if(!pattern.test(emailAddress)){
        jQuery(".msgg, .alert.alert-danger").fadeIn();
        jQuery(".msgg .alert.alert-danger strong").text("Please enter a valid e-mail address!");
        setTimeout(function(){ jQuery(".msgg").fadeOut(); }, 10000);
        jQuery(this).val("");
        return false;
    }
});


jQuery(document).on("blur",".getfieldvalue[title='email']",function() {



   var map = {};



    map["action"]='checkemail';

    map["email"] = jQuery(this).val();

    jQuery(".loader").fadeIn();

    jQuery(".bdoverlay").fadeIn();

    //console.log(jQuery(this).val());



    jQuery.ajax({

        type: 'POST',

        url: "https://wholesalevinylfencing.net/wp-admin/admin-ajax.php",

        data: map, 

        success: function(data) {

            console.log(data);



            if(data.success == "1"){



                jQuery('.alert').fadeIn().text(data.email+" Email alredy exits");

                jQuery('.email').val("");



                 setTimeout(function(){ jQuery(".alert").fadeOut(); }, 6000);

            }

            else{



            jQuery(".loader").fadeOut();

            jQuery(".bdoverlay").fadeOut();





            }



            jQuery(".loader").fadeOut();

            jQuery(".bdoverlay").fadeOut();

      

      }

    });

 });




 
jQuery(document).on('click','#register',function(e){     

        e.preventDefault();
         //validateForm(); 

    var nameReg    = /^[a-zA-Z ]*$/;
    var numberReg  =  /^[0-9]+$/;
    var emailReg   = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

    var username   = jQuery('#username').val();
    var fname      = jQuery('#fname').val();
    var lname      = jQuery('#lname').val();
    var email      = jQuery('#email').val();
    var emailexit  = jQuery('#email').attr('emailexit');
    var company    = jQuery('#company').val();
    var password   = jQuery('#password').val();
    var cpassword   = jQuery('#cpassword').val();
    var inputVal = new Array(username, fname, lname, email, company, password, cpassword);

 setTimeout(function(){jQuery('.errorcustom').remove(); },  3000); 
        if(email == ""){
            jQuery('#email').after('<span class="errorcustom"> Please enter your email address </span>');
            return false;
        }else if(!emailReg.test(email)){
            alert(email);
            jQuery('#email').after('<span class="errorcustom"> Please enter a valid email address</span>');
            return false;
        }else if(emailexit=='0'){

        

        var inputMessage = new Array("username", "first name", "email address", "company name", "password", "confirm Password");

         jQuery('.errorcustom').remove();
           setTimeout(function(){jQuery('.errorcustom').remove(); },  3000);  

           

            if(inputVal[0] == ""){
                jQuery('#username').after('<span class="errorcustom"> Please enter your ' + inputMessage[0] + '</span>');

                return false;
                
            }

             if(inputVal[1] == ""){
                jQuery('#fname').after('<span class="errorcustom"> Please enter your ' + inputMessage[1] + '</span>');

                return false;
                
            }else if(!nameReg.test(fname)){

                
                

                jQuery('#fname').after('<span class="errorcustom"> Please enter only characters</span>');

                return false;
            } 

            if(!nameReg.test(lname)){
                

                jQuery('#lname').after('<span class="errorcustom"> Please enter only characters</span>');

                return false;
            }       


            if(inputVal[4] == ""){
                jQuery('#company').after('<span class="errorcustom"> Please enter your ' + inputMessage[4] + '</span>');
                return false;
            } 
          
            if(inputVal[5] == ""){
                jQuery('#password').after('<span class="errorcustom"> Please enter your ' + inputMessage[5] + '</span>');
               return false;
            }else if(inputVal[5].length < 8){
                jQuery('#password').after('<span class="errorcustom"> Password length must be atleast 8 characters</span>');
               return false;
            }

             if(inputVal[6] == ""){
                jQuery('#cpassword').after('<span class="errorcustom"> Please enter your ' + inputMessage[6] + '</span>');
               return false;
            }
            if(inputVal[5] != inputVal[6]) {
              jQuery('#password').after('<span class="errorcustom"> Passwords are not same</span>');
               return false;
            }
        }//end of if

        
if(email !='' && emailexit !='') {

    if(company!='') {
                
           var ajaxurl = common_data.ajax_url;
            
            jQuery.ajax({ 
                 url: ajaxurl,
                 data: {action: 'exit_company', data:company },
                 method:"POST",  
                 success: function(data) {
                       // This prints '0', I want this to print whatever name the user inputs in the form.
                      
                      if(data.status==1){   

                             //jQuery(this).parent("tr").remove();   
                             //document.location.href="/customers"; 
                         jQuery("#company").after('<span class="errorcustom">'+data.message+'</span>');
                          setTimeout(function(){jQuery(".errorcustom").remove(); },  5000);
                          return false;
          
                      }else if(data.status==0){
                        //document.location.href="/customers";
                          
                          

                          var BForm = jQuery("#newusercreate input").serialize();
           
                            var ajaxurl = common_data.ajax_url;
                            
                            jQuery.ajax({ 
                                 url: ajaxurl,
                                 data: {action: 'createnewuser', data:inputVal },
                                 method:"POST",  
                                 success: function(data) {
                                       // This prints '0', I want this to print whatever name the user inputs in the form.
                                      
                                      if(data.status==1){                    
                                        
                                                  document.location.href="/customers"; 
                                                 /*jQuery("#message").append('<div class="alert alert-success" role="alert" >'+data.message+'</div>');*/
                                                  setTimeout(function(){jQuery(".alert-success").remove(); },  5000);             
                          
                                      }else if(data.status==0){
                                          
                                                   document.location.href="/customers"; 
                                                 /*jQuery("#message").append('<div class="alert alert-success" role="alert" >'+data.message+'</div>');*/
                                                  setTimeout(function(){jQuery(".alert-error").remove(); },  5000); 
                                      }

                                }
                            });
                          
                      } //end of after success else if

                }
            }); //end of ajax company success else if
        }else{

            var BForm = jQuery("#newusercreate input").serialize();
           
                            var ajaxurl = common_data.ajax_url;
                            
                            jQuery.ajax({ 
                                 url: ajaxurl,
                                 data: {action: 'createnewuser', data:inputVal },
                                 method:"POST",  
                                 success: function(data) {
                                       // This prints '0', I want this to print whatever name the user inputs in the form.
                                      
                                      if(data.status==1){                    
                                        
                                                  document.location.href="/customers"; 
                                                 /*jQuery("#message").append('<div class="alert alert-success" role="alert" >'+data.message+'</div>');*/
                                                  setTimeout(function(){jQuery(".alert-success").remove(); },  5000);             
                          
                                      }else if(data.status==0){
                                          
                                                   document.location.href="/customers"; 
                                                 /*jQuery("#message").append('<div class="alert alert-success" role="alert" >'+data.message+'</div>');*/
                                                  setTimeout(function(){jQuery(".alert-error").remove(); },  5000); 
                                      }

                                }
                            });
            
        }
        
            
        
}

});

/*Update*/
jQuery(document).on('click','#updateuser',function(e){     

        e.preventDefault();
         //validateForm(); 
         

    var nameReg    = /^[a-zA-Z ]*$/;
    var numberReg  =  /^[0-9]+$/;
    var emailReg   = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

    var username   = jQuery('#username').val();
    var fname      = jQuery('#fname').val();
    var lname      = jQuery('#lname').val();
    var email      = jQuery('#email').val();
    var company    = jQuery('#company').val();
    var password    = jQuery('#password').val();
    var cpassword   = jQuery('#cpassword').val();
    var oldpassword = jQuery('#oldpassword').val();
    var userid      = jQuery('#userid').val();

    var inputVal = new Array(username, fname, lname, email, company, password, cpassword, oldpassword, userid);

    var inputMessage = new Array("username", "first name", "email address", "company name", "password", "confirm Password");

     jQuery('.errorcustom').remove();
       setTimeout(function(){jQuery('.errorcustom').remove(); },  5000);  


        if(inputVal[0] == ""){
            jQuery('#username').after('<span class="errorcustom"> Please enter your ' + inputMessage[0] + '</span>');

            return false;
            
        }

         if(inputVal[1] == ""){
            jQuery('#fname').after('<span class="errorcustom"> Please enter your ' + inputMessage[1] + '</span>');

            return false;
            
        }else if(!nameReg.test(fname)){
            

            jQuery('#fname').after('<span class="errorcustom"> Please enter only characters</span>');

            return false;
        } 

        if(!nameReg.test(lname)){
            

            jQuery('#lname').after('<span class="errorcustom"> Please enter only characters</span>');

            return false;
        }
        

      
        if(inputVal[3] == ""){
            jQuery('#email').after('<span class="errorcustom"> Please enter your ' + inputMessage[2] + '</span>');
            return false;
        } 
        else if(!emailReg.test(email)){
            jQuery('#email').after('<span class="errorcustom"> Please enter a valid email address</span>');
            return false;
        }

        if(inputVal[4] == ""){
            jQuery('#company').after('<span class="errorcustom"> Please enter your ' + inputMessage[3] + '</span>');
            return false;
        }  
       

        if(inputVal[5] != ""){
            if(inputVal[5].length < 8){
            jQuery('#password').after('<span class="errorcustom"> Password length must be atleast 8 characters</span>');
           return false;
        }
            if(inputVal[5] != inputVal[6]) {
              jQuery('#password').after('<span class="errorcustom"> Passwords are not same</span>');
               return false;
            }
        }
         
        
if(username!=''  && fname !='' && email !='' && company !='' && oldpassword !='') {
        
   var ajaxurl = common_data.ajax_url;
    
    jQuery.ajax({ 
         url: ajaxurl,
         data: {action: 'update_user_data', data:inputVal },
         method:"POST",  
         success: function(data) {
               // This prints '0', I want this to print whatever name the user inputs in the form.
              
              if(data.status==1){                    
                 
                   document.location.href="/edit-user/?id="+userid; 
                         /*jQuery("#message").append('<div class="alert alert-success" role="alert" >'+data.message+'</div>');*/
                          setTimeout(function(){jQuery(".alert-success").remove(); },  5000);               
  
              }else if(data.status==0){
                  
                           document.location.href="/edit-user/?id="+userid; 
                         /*jQuery("#message").append('<div class="alert alert-success" role="alert" >'+data.message+'</div>');*/
                          setTimeout(function(){jQuery(".alert-error").remove(); },  5000); 
              }

        }
    });
}

});

/*Update*/
jQuery(document).on('click','#deleteuser',function(e){     

        e.preventDefault();
         //validateForm();

         

      var userid        = jQuery(this).attr('userid'); 
      var currentuserid = jQuery(this).attr('currentuserid');    
        
        if(userid!='') {
                
           var ajaxurl = common_data.ajax_url;
            
            jQuery.ajax({ 
                 url: ajaxurl,
                 data: {action: 'delete_user', data:userid, currentuserid:currentuserid },
                 method:"POST",  
                 success: function(data) {
                       // This prints '0', I want this to print whatever name the user inputs in the form.
                      
                      if(data.status==1){   

                             //jQuery(this).parent("tr").remove();   
                             document.location.href="/customers"; 
                         /*jQuery("#message").append('<div class="alert alert-success" role="alert" >'+data.message+'</div>');*/
                          setTimeout(function(){jQuery(".alert-success").remove(); },  5000);

          
                      }else if(data.status==0){
                        document.location.href="/customers";
                          
                          /*jQuery("#message").append('<div class="alert alert-danger" role="alert" >'+data.message+'</div>');
                         */ setTimeout(function(){jQuery(".alert-error").remove(); },  5000);
                      }

                }
            });
        }

});


/*Company check*/
jQuery(document).on('change','#company',function(e){     

        e.preventDefault();
         //validateForm();

         

      var company     = jQuery(this).val();

     

      
        
        if(company!='') {
                
           var ajaxurl = common_data.ajax_url;
            
            jQuery.ajax({ 
                 url: ajaxurl,
                 data: {action: 'exit_company', data:company },
                 method:"POST",  
                 success: function(data) {
                       // This prints '0', I want this to print whatever name the user inputs in the form.
                      
                      if(data.status==1){   

                             //jQuery(this).parent("tr").remove();   
                             //document.location.href="/customers"; 
                         jQuery("#company").after('<span class="errorcustom">'+data.message+'</span>');
                          setTimeout(function(){jQuery(".errorcustom").remove(); },  5000);

          
                      }else if(data.status==0){
                        //document.location.href="/customers";
                          
                          jQuery("#company").after('<span class="errorcustom">'+data.message+'</span>');
                          setTimeout(function(){jQuery(".errorcustom").remove(); },  5000);
                      }

                }
            });
        }

});


/*Company check*/
jQuery(document).on('change','#email',function(e){     

        e.preventDefault();
         //validateForm();         

      var email     = jQuery(this).val();         
        if(email!='') {
                
           var ajaxurl = common_data.ajax_url;
            
            jQuery.ajax({ 
                 url: ajaxurl,
                 data: {action: 'exit_email', data:email },
                 method:"POST",  
                 success: function(data) {
                       // This prints '0', I want this to print whatever name the user inputs in the form.
                     
                      if(data.status==1){   
                       
                             //jQuery(this).parent("tr").remove();   
                             //document.location.href="/customers"; 
                        
                          jQuery("#email").attr('emailexit', '1');  
                          jQuery("#email").after('<span class="errorcustom">'+data.message+'</span>');
                          
                          jQuery("#username").val(data.username).attr('readonly', 'readonly');
                          jQuery("#fname").val(data.fname).attr('readonly', 'readonly');
                          jQuery("#lname").val(data.lname).attr('readonly', 'readonly');
                          jQuery("#company").attr('placeholder', data.company).attr('readonly', 'readonly');
                          jQuery("#password, #cpassword").attr('readonly', 'readonly');


                          setTimeout(function(){jQuery(".errorcustom").remove(); },  5000);

          
                      }else if(data.status==0){

                         jQuery("#username").val(data.username).removeAttr('readonly');
                          jQuery("#fname").val(data.fname).removeAttr('readonly');
                          jQuery("#lname").val(data.lname).removeAttr('readonly');
                          jQuery("#company").attr('placeholder', 'Company Name').removeAttr('readonly');
                          jQuery("#password, #cpassword").removeAttr('readonly');
                        //document.location.href="/customers";
                          jQuery("#email").attr('emailexit', '0');   
                          jQuery("#email").after('<span class="errorcustom">'+data.message+'</span>');
                          setTimeout(function(){jQuery(".errorcustom").remove(); },  5000);
                      }

                }
            });
        }

});



/*resend*/
jQuery(document).on('click','.resend',function(e){     

        e.preventDefault();
         //validateForm();         

      var id     = jQuery(this).attr('id');     

        if(id!='') {
                
           var ajaxurl = common_data.ajax_url;
            
            jQuery.ajax({ 
                 url: ajaxurl,
                 data: {action: 'resend_username_password', id:id },
                 method:"POST",  
                 success: function(data) {
                       // This prints '0', I want this to print whatever name the user inputs in the form.
                     console.log(data);
                      if(data.status==1){
                          jQuery("#message").prepend('<span class="alert alert-success">'+data.message+'</span>');
                          setTimeout(function(){jQuery(".alert-success").remove(); },  5000);
                      }else if(data.status==0){                         
                          jQuery("#message").prepend('<span class="alert alert-error">'+data.message+'</span>');
                          setTimeout(function(){jQuery(".alert-error").remove(); },  5000);
                      }

                }
            });
        }

});



/*Add Supplier*/

jQuery(document).on('click','#registersupplier',function(e){     

        e.preventDefault();
         //validateForm(); 

    var nameReg    = /^[a-zA-Z ]*$/;
    var numberReg  =  /^[0-9]+$/;
    var emailReg   = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

    var username   = jQuery('#username').val();
    var fname      = jQuery('#fname').val();
    var lname      = jQuery('#lname').val();
    var email      = jQuery('#email').val();
    var company    = jQuery('#company').val();
    var password   = jQuery('#password').val();
    var cpassword  = jQuery('#cpassword').val();
    var supplier   = jQuery('#supplier').val();

    var inputVal = new Array(username, fname, lname, email, company, password, cpassword, supplier);

    var inputMessage = new Array("username", "first name", "email address", "company name", "password", "confirm Password");

     jQuery('.errorcustom').remove();
       setTimeout(function(){jQuery('.errorcustom').remove(); },  5000);  


        if(inputVal[0] == ""){
            jQuery('#username').after('<span class="errorcustom"> Please enter your ' + inputMessage[0] + '</span>');

            return false;
            
        }

         if(inputVal[1] == ""){
            jQuery('#fname').after('<span class="errorcustom"> Please enter your ' + inputMessage[1] + '</span>');

            return false;
            
        }else if(!nameReg.test(fname)){
            

            jQuery('#fname').after('<span class="errorcustom"> Please enter only characters</span>');

            return false;
        } 

        if(!nameReg.test(lname)){
            

            jQuery('#lname').after('<span class="errorcustom"> Please enter only characters</span>');

            return false;
        } 
        

      
        if(inputVal[3] == ""){
            jQuery('#email').after('<span class="errorcustom"> Please enter your ' + inputMessage[3] + '</span>');
            return false;
        } 
        else if(!emailReg.test(email)){
            jQuery('#email').after('<span class="errorcustom"> Please enter a valid email address</span>');
            return false;
        }

        if(inputVal[4] == ""){
            jQuery('#company').after('<span class="errorcustom"> Please enter your ' + inputMessage[4] + '</span>');
            return false;
        } 
      
        if(inputVal[5] == ""){
            jQuery('#password').after('<span class="errorcustom"> Please enter your ' + inputMessage[5] + '</span>');
           return false;
        }else if(inputVal[5].length < 8){
            jQuery('#password').after('<span class="errorcustom"> Password length must be atleast 8 characters</span>');
           return false;
        }

         if(inputVal[6] == ""){
            jQuery('#cpassword').after('<span class="errorcustom"> Please enter your ' + inputMessage[6] + '</span>');
           return false;
        }
        if(inputVal[5] != inputVal[6]) {
          jQuery('#password').after('<span class="errorcustom"> Passwords are not same</span>');
           return false;
        }

         
        
if(username!=''  && fname !='' && email !='' && company !='' && password !='') {
        
  
   
    var ajaxurl = common_data.ajax_url;
    
    jQuery.ajax({ 
         url: ajaxurl,
         data: {action: 'createnewsupplier', data:inputVal },
         method:"POST",  
         success: function(data) {
               // This prints '0', I want this to print whatever name the user inputs in the form.
              
              if(data.status==1){                    
                
                          document.location.href="/customers"; 
                         /*jQuery("#message").append('<div class="alert alert-success" role="alert" >'+data.message+'</div>');*/
                          setTimeout(function(){jQuery(".alert-success").remove(); },  5000);             
  
              }else if(data.status==0){
                  
                           document.location.href="/customers"; 
                         /*jQuery("#message").append('<div class="alert alert-success" role="alert" >'+data.message+'</div>');*/
                          setTimeout(function(){jQuery(".alert-error").remove(); },  5000); 
              }

        }
    });
}

});

jQuery(document).ready(function() {
    var ajaxurl =  jQuery('#user').attr('url');

    jQuery('#user').DataTable( {


        ajax: {
            url: common_data.ajax_url + '?action=user_datatables'
        },
        "columns": [       

        { "data": "username" },
        { "data": "name" },
        { "data": "email" },
        { "data": "company" },
        { "data": "addeddate" },

        ],"columnDefs": [ {
            "targets": 5,
            "data": "id",
            "render": function ( data, type, row, meta ) {
                /*return '<a class="fa fa-edit" href="/edit-user?id='+row.id+'"></a><a id="deleteuser" userid="'+row.id+'" currentuserid="'+row.currentuserid+'" href="javascript:void(0)" class="fa fa-trash" href="'+row.id+'"></a>';*/
                return '<a class="fa fa-envelope resend" href="javascript:void(0)" id="'+row.id+'" title="Resend Username and Password"></a>';
            }
        } ]
        
       
    } );
} );


