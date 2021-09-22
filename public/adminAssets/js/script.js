$(document).ready(function() {
  var w = $(window).width();

  if (w < 400) {
      alert();
      // $(".regular").slick({
      //     lazyLoad: 'ondemand',
      //     dots: true,
      //     infinite: false,
      //     slidesToShow: 2,
      //     slidesToScroll: 2
      // });
  } else {
      // $(".regular").slick({
      //     lazyLoad: 'ondemand',
      //     dots: true,
      //     infinite: false,
      //     slidesToShow: 4,
      //     slidesToScroll: 4
      // });
  }


  if (w < 400) {
      alert();
      // $(".client-list-1").slick({
      //     lazyLoad: 'ondemand',
      //     dots: true,
      //     infinite: false,
      //     slidesToShow: 3,
      //     slidesToScroll: 3
      // });
  } else {
      // $(".client-list-1").slick({
      //     lazyLoad: 'ondemand',
      //     dots: true,
      //     infinite: false,
      //     slidesToShow: 6,
      //     slidesToScroll: 6
      // });
  }


});


$(document).ready(function() {
  // $("#testimonial-slider").owlCarousel({
  //     items: 2,
  //     itemsDesktop: [1000, 2],
  //     itemsDesktopSmall: [979, 2],
  //     itemsTablet: [768, 1],
  //     pagination: false,
  //     navigation: true,
  //     navigationText: ["", ""],
  //     autoPlay: true
  // });
});


$(document).ready(function() {
  //$('#nav-head').scrollToFixed();
  var today = new Date();
   $('select').SumoSelect({search: true});
   $("body").on('focus',"#date_of_joining", function(){
    $(this).datepicker({
    format: "yyyy/mm/dd",
    autoclose:true,
    monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    });
    });
    $("body").on('focus',"#dob", function(){
      $(this).datepicker({
      format: "yyyy/mm/dd",
      autoclose:true,
      endDate: "today",
      maxDate: today,
      monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      }).on('changeDate', function (ev) {
        $(this).datepicker('hide');
    });
      });
})

$(document).on("click",".clickviewmore",function(){
  $(this).closest("td").find(".viewmore").show();
 $(this).closest("td").find(".viewLess").hide();
});
$(document).on("click",".clickviewless",function(){
$(this).closest("td").find(".viewLess").show();
$(this).closest("td").find(".viewmore").hide();
});





//custom js for submiting form
function ajaxCallMultipartForm(url, data) {
  var response = [];
  $.ajax({
      type: "POST",
      enctype: 'multipart/form-data',
      url: url,
      data: data,
      processData: false,
      contentType: false,
      async: true,
      headers: { 'Authorization':$('#authToken').val()},
      cache: false,
      timeout: 600000,  
      success: function(data) {
          if(data.success)
          {
            
            toastr.success(data.message);
            window.setTimeout(function() {
              window.location = $('.eschoolForm').attr('rel');
          }, 3000);
            
          }else{
            hendleError(data);
          }
      },
      error: function(e) {
          console.log("ERROR : ", e);
          hendleError(e.responseJSON);
      }
  });
  return response;
}

function hendleError(data) {

  $('.errormess').remove();
  if (Object.prototype.toString.call(data.error) == '[object String]' && data.error) {
    toastr.error(data.error);
  } else {
      console.log(data.error);
      $.each(data.error, function(key, value) {
          // value= value.replace("_"," ");
          if(key == 'image.0'){
              //alert('yes image->'+key);
              object = $('#attachment');
              object.addClass('errorRed');
              object.after("<span class='errormess' style='width:100%'>Atleast one attachment is required </span>")
          }
          if ($('select[name=' + key + ']').length){
              object = $('select[name=' + key + ']');
          }else if ($('textarea[name=' + key + ']').length){
              object = $('textarea[name=' + key + ']');
          }else{
              object = $('input[name=' + key + ']');

          }
          object.addClass('errorRed');
          object.after("<span class='errormess' style='width:100%'>" + value + " </span>")
      });
  }
}
$('body').on('click', '.submitButton', function() {

  event.preventDefault();
  // Get form
  var form = $('.eschoolForm')[0];
  // Create an FormData object
  var data = new FormData(form);
  // var otherdata=getRequestdata();
  // $.each(otherdata,function(index,value) {
  // data.append(index,value);
  // });
  url = $(form).attr('action');
  response = ajaxCallMultipartForm(url, data);
});
$('body').on('change', '.errorRed', function() {
  $(this).next('.errormess').remove();
  $(this).removeClass('errorRed');
});




$('body').on('click','.gettingform',function(){
    var url   =   $(this).attr('rel');
    var setin = $("#"+$(this).attr('data'));
    requestData={ "school_id":$('#school_id').val(),
                  "role":$('#user_role').val()};
    getHtml(url,requestData, setin);
});

function getHtml(url,requestData,setin){
    $.ajax({
      type: "POST",
      url: url,
      data: requestData,
      headers: { 'Authorization':$('#authToken').val()},
      async: false,
      success: function(response) {
        $(setin).html(response);
      }
  });
}

// $(".toggle-password").click(function() {
//   $(this).toggleClass("fa-eye fa-eye-slash");
//   var input = $($(this).attr("toggle"));
//   if (input.attr("type") == "password") {
//     input.attr("type", "text");
//   } else {
//     input.attr("type", "password");
//   }
// });

$("#show__hide_password").click(function(){
  var obj= $("#password");
  if($(this).hasClass("fa-eye")){
    $(this).removeClass("fa-eye");
    $(this).addClass("fa-eye-slash");
    $(obj).attr('type','text');
  }else
  {
    $(this).removeClass("fa-eye-slash");
    $(this).addClass("fa-eye");
    $(obj).attr('type','password');
  }
});
toastr.options.timeOut = 3000; // 1.5s

$(function(){
  var dtToday = new Date();

  var month = dtToday.getMonth() + 1;
  var day = dtToday.getDate();
  var year = dtToday.getFullYear();

  if(month < 10)
      month = '0' + month.toString();
  if(day < 10)
      day = '0' + day.toString();

  var maxDate = year + '-' + month + '-' + day;    
  $('#dob').attr('max', maxDate);
});

$(function() {
    var requestData={
      "school_id":$('#school_id').val(),
      "role":$('#user_role').val(),
      "take":'5',
      };
    var url ="/api/v2/get-notice";
    getData(url,requestData);
});


function getData(url,requestData,handleData){
    $.ajax({
      type: "POST",
      enctype: 'multipart/form-data',
      url: url,
      data: JSON.stringify(requestData),
      processData: false,
      contentType:'application/json; charset=utf-8',
      dataType: 'json',
      headers: { 'Authorization':$('#authToken').val()},
      async: true,
      cache: false,
      timeout: 600000,
      success: function(response) {
        if(response.success){
          var NoticeHtml=makeNoticeHtml(response.data);
          $('#noticeBoard').html(NoticeHtml);
        }
        
      }
  });
}
function makeNoticeHtml(data){
    var html='';
    for(var i=0;i<data.length;i++){
        var newDate=formatDate(data[i].date);
        html +='<div class="box"><p class="text-right small">'+newDate+'</p><h3>'+data[i].title+' </h3><p>'+data[i].message+'</p><p class="text-right mb-0 mt-1 name"><i class="now-ui-icons users_circle-08"></i> <b>'+data[i].type+'</b> </p></div>';
    }
    return html;
}
function formatDate(date) {
  const monthNames = ["January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"
   ];
  var d = new Date(date),
      month = '' + monthNames[d.getMonth()],
      day = '' + d.getDate(),
      year = d.getFullYear();

  if (month.length < 2) month = '0' + month;
  if (day.length < 2) day = '0' + day;

  return [day,month,year ].join(' ');
}


// toastr.success('Success messages');

// // for errors - red box
// toastr.error('errors messages');

// // for warning - orange box
// toastr.warning('warning messages');

// // for info - blue box
// toastr.info('info messages')
