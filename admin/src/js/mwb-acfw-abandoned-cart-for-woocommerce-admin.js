(function($) {
     'use strict';
     $(document).ready(function() {
         const MDCText = mdc.textField.MDCTextField;
         const textField = [].map.call(document.querySelectorAll('.mdc-text-field'), function(el) {
             return new MDCText(el)
         });
         const MDCRipple = mdc.ripple.MDCRipple;
         const buttonRipple = [].map.call(document.querySelectorAll('.mdc-button'), function(el) {
             return new MDCRipple(el)
         });
         const MDCSwitch = mdc.switchControl.MDCSwitch;
         const switchControl = [].map.call(document.querySelectorAll('.mdc-switch'), function(el) {
             return new MDCSwitch(el)
         });
         $('.mwb-password-hidden').click(function() {
             if ($('.mwb-form__password').attr('type') == 'text') {
                 $('.mwb-form__password').attr('type', 'password')
             } else {
                 $('.mwb-form__password').attr('type', 'text')
             }
         })
     });
     $(window).load(function() {
         if ($(document).find('.mwb-defaut-multiselect').length > 0) {
             $(document).find('.mwb-defaut-multiselect').select2()
         }
     })
 })(jQuery);
 jQuery(document).ready(function(a) {
     acfw_admin_param.tab && a.ajax({
         type: "GET",
         url: acfw_admin_param.ajaxurl,
         data: {
             action: "mwb_acfw_get_graph_data"
         },
         success: function(a) {
             for (var e = a, t = JSON.parse(e), r = [], o = [], n = 0; n < t.length; n++) r[n] = t[n].MONTHNAME, o[n] = t[n].count;
             var b = document.getElementById("myChart").getContext("2d");
             new Chart(b, {
                 type: "line",
                 data: {
                     labels: r,
                     datasets: [{
                         fill: !0,
                         label: "Monthly Data",
                         data: o,
                         backgroundColor: ["rgba(54, 162, 235, 1)", "rgba(255, 206, 86, 1)", "rgba(75, 192, 192, 1)", "rgba(153, 102, 255, 1)", "rgba(255, 159, 64, 1)"],
                         borderColor: ["rgba(33, 145, 81, 0.5)", "rgba(33, 145, 81, 0.2)", "rgba(33, 145, 81, 0.2)", "rgba(33, 145, 81, 0.2)", "rgba(33, 145, 81, 0.2)", "rgba(33, 145, 81, 0.2)", "rgba(33, 145, 81, 0.2)"],
                         borderWidth: 1
                     }]
                 },
                 options: {
                     scales: {
                         scales: {
                             yAxes: [{
                                 beginAtZero: !1
                             }],
                             beginAtZero: !0,
                             xAxes: [{
                                 autoskip: !0,
                                 maxTicketsLimit: 50
                             }]
                         }
                     },
                     legend: {
                         display: !0,
                         position: "top",
                         labels: {
                             fontColor: "rgb(255,255,255)",
                             fontSize: 16
                         }
                     }
                 }
             })
         }
     })
 });