jQuery(document).ready(function($) {
     function setCookie(cname, cvalue, exdays) {
          var d = new Date();
          d.setTime(d.getTime() + (exdays*24*60*60*1000));
          var expires = "expires="+ d.toUTCString();
          document.cookie = cname + "=" + cvalue + ";" + expires + "; path=/";
     }
     jQuery( 'input#billing_email' ).on( 'change', function() {

          var guest_user_email = jQuery( 'input#billing_email' ).val();
          var pat = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(guest_user_email);
          if ( ! pat) {
               return;
          }
          setCookie( 'mwb_atc_email', guest_user_email, 5 );
          $.ajax({
                    url: mwb_ck_mail_ob.ajaxurl,
                    type: 'POST',
                    data: {
                         action: 'save_mail_checkout',
                         guest_user_email : guest_user_email,
                         nonce : mwb_ck_mail_ob.nonce,
                    },
                    success: function(data) {
                    }
               });
          });
});