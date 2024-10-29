jQuery(function() {
    jQuery("#view").dialog({
         modal: !0,
         autoOpen: !1,
         width: 700,
         draggable: !1,
     });
     jQuery(document).on('click', '#view_data', function( ) {
         var id = jQuery(this).data('id');
         jQuery("#view").dialog('open');
         jQuery.ajax({
             url: demo_js_ob.ajaxurl,
             type: 'POST',
             data: {
                 action: 'mwb_abdn_cart_viewing_cart_from_quick_view',
                 cart_id: id,
                 nonce: demo_js_ob.nonce
             },
             success: function(data) {
                 jQuery("#view").dialog('open');
                 jQuery("#show_table").html(data)
             }
         })
     })
     jQuery(document).on( 'click', '.acfw-overview__keywords-card.mwb-card-support',  function() {
         window.location = jQuery(this).find("a").attr("href");
         return !1
     })
 })