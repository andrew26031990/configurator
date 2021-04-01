var $zoom;

$("#fileName").change(function () {
  var file = this.files[0];
  var fileType = file.type;
  var match = ["application/pdf", "application/msword", "application/vnd.ms-office", "image/jpeg", "image/png", "image/jpg"];
  if (!(fileType == match[0] || fileType == match[1] || fileType == match[2] || fileType == match[3] || fileType == match[4] || fileType == match[5])) {
    alert("Sorry, only PDF, DOC, JPG, JPEG, & PNG files are allowed to upload.");
    $("#fileName").val("");
    return !1;
  }
});

var selected_price = 0;
var sel_price = new Map();
var sel_bd_id = new Map();
var sel_quantity = new Map();
//var sel_quantity_id = new Map();

$("ul.list li").on("click", function () {
  var id = $(this).closest("table").attr("id");
  $("#" + id + " ul.list li").removeClass("active");
  $(this).addClass("active");
});

$(".helpBtnClick").on("click", function() {
    $('body div[title="Чат с оператором"]').click()
});

$(".refresh").on("click", function (e) {
  e.preventDefault();
  var id = $(this).closest("table").attr("id");
  if (id === "korpus_dlya_pk") {
    $("#" + id + ' input[type="radio"]').prop("checked", !1);
    $(".config-img-outer img").attr("src", "images/config.png");
    $(".korpus_dlya_pk .img-holder > img").attr("src", "images/product.png");
  } else {
    $("#" + id + ' input[type="radio"]').prop("checked", !1);
  }
  resetCustomBlock(id);
  sel_bd_id.delete(id);
  sel_price.delete(id);
  generateConfig();
  console.log(sel_bd_id);
  console.log(sel_price);
  resetCustomBlock(id);
});

$("html select").on("change", function (e) {
  e.preventDefault();
  var id = $(this).closest("table").attr("id");
  var bd_id = $(this).closest("input").attr("id");
  var quantity = $(this).children("option:selected").val();
  collectQuantityFromEachSelectedInput();  
});

$(document).on("change", "input[type=radio]", function () {
  var id = $(this).closest("table").attr("id");
  var bd_id = $(this).attr("id");
  var quantity = $(this).parent('div').find('select option:selected').text();
  selected_price = $(this).val();
  sel_price.set(id, selected_price);
  sel_bd_id.set(id, bd_id);
  collectQuantityFromEachSelectedInput();
  generateConfig();
  calculatePlusMinusFromChecked();
});

$(".list a").click(function () {

  var component = $(this).attr("data-type");
  var filter = $(this).text();
  var filter_id = $(this).attr("filter-id");
  var data_id = $(this).attr("data-id");

  $.ajax({
    url: "/modules/filter.php",
    type: "POST",
    data: { filter: filter, component: component, filterId: filter_id, dataId: data_id },
    beforeSend: function () {
      //$("body .preloader2").css("display", "block");
    },
    error: function (xhr, status, error) {
      alert(xhr.responseText);
      alert(error);
    },
    success: function (response) {
      var raznica = 0;
      var quantity = '';
      var qtity = '';
      console.log(sel_quantity);
      var checked = "";
      var selected = "";
      var myObj = $.parseJSON(response);
      $("#" + component + " .inner_2 .main-list").remove();
      $("#" + component + " .inner_2").append('<ul class="main-list"></ul>');
      if (sel_price.has(component)) {
        if(component !== 'operativnaya_pamyatj' && component !== 'ozu' && component !== 'monitor' && component !== 'operativnaya_pamyatj' && component !== 'jestkie_diski_HDD' && component !== 'jestkiy_disk'){
          selected = "disabled";
        }
        for (var i = 0; i < myObj.length; i++) {
          if (parseInt(myObj[i].price) - parseInt(sel_price.get(component)) < 0) {
            raznica = parseInt(myObj[i].price) - parseInt(sel_price.get(component));
          } else if (parseInt(myObj[i].price) - parseInt(sel_price.get(component)) > 0) {
            raznica = "+" + parseInt(myObj[i].price) - parseInt(sel_price.get(component));
          } else if (parseInt(myObj[i].price) - parseInt(sel_price.get(component)) === 0) {
            raznica = "";
          }

          if (myObj[i].id == sel_bd_id.get(component)) {
            checked = "checked";
          } else {
            checked = "";
          }
          var opt = genereteSel(myObj[i].id);
          var tr_str =
              `<li class="all asus">
                                <div class="product-block">
                                    <input type="radio" data="` +
              myObj[i].image +
              `" id="` +
              myObj[i].id +
              `" name="` +
              component +
              `" value="` +
              myObj[i].price +
              `"` +
              checked +
              `>
                                    <label for="` +
              myObj[i].id +
              `" data-name="` +
              myObj[i].name +
              `">
                                        <span></span><select title="Выберите количество" class="select quantity" method="post" name="`+component+`" `+ selected +` >`+ opt +`
                                                        
                                                    </select>
                                        ` +
              myObj[i].name + 
              `<a href="`+myObj[i].description+`" style="z-index: 99999;width: 20px;margin-left:5px;" target="_BLANK"><img src="configurator/images/info.svg" /></a>
                                    </label>
                                </div>
                                <div class="price-block">
                                    ` +
              format2(raznica, "UZS") +
              `</div>
                            </li>`;
                            
                            
          $("#" + component + " .inner_2 > ul.main-list").append(tr_str);
          calculatePlusMinusFromChecked();
          $("body .preloader2").css("display", "none");
          //$(".preloader").css("display", "none");
          //setQuantities();
          //generateConfig();
          //setQuantityOnEachSelect();
        }
      } else {
        for (var i = 0; i < myObj.length; i++) {
          if (sel_bd_id.has(component)) {
            checked = "checked";
          } else {
            checked = "";
          }
          var tr_str =
              `<li class="all asus">
                                <div class="product-block">
                                    <input type="radio" data="` +
              myObj[i].image +
              `" id="` +
              myObj[i].id +
              `" name="` +
              component +
              `" value="` +
              myObj[i].price +
              `"` +
              checked +
              `>
                                    <label for="` +
              myObj[i].id +
              `" data-name="` +
              myObj[i].name +
              `">
                                        <span></span><select title="Выберите количество" class="select quantity" method="post" name="materinskaya_plata">
                                                        <option value="1" selected>1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                    </select>
                                        ` +
              myObj[i].name +
              `<a href="`+myObj[i].description+`" style="z-index: 99999;width: 20px;margin-left:5px;" target="_BLANK"><img src="configurator/images/info.svg" /></a>
                                    </label>
                                </div>
                                <div class="price-block">
                                    ` +
              format2(raznica, "UZS") +
              `</div>
                            </li>`;
          $("#" + component + " .inner_2 > ul.main-list").append(tr_str);
          calculatePlusMinusFromChecked();
          //$(".preloader").css("display", "none");
          //generateConfig();
          //setQuantities();
        }
        resetCustomBlock(component);
      }
    },
  });
});

function genereteSel(idOfSel){
    let count = sel_quantity.get(idOfSel);
    let opt = '';
    for(i=1; i<7;i++){
        if(count == i){
            opt+='<option value="'+i+'" selected>'+i+'</option>';
        }else{
            opt+='<option value="'+i+'">'+i+'</option>';
        }
    }
    return opt;
}

$(document).on("click", "input[type=radio]", function () {
  var image = $(this).attr("data");
  var type = $(this).attr("name");
  var location = window.location.origin;
  if (image != "") {
    if (type === "korpus_dlya_pk") {
      $("." + type + " img").attr("src", "configurator/images/products/" + image);
      //$("." + type + " .zoom").attr("data-magnify-src", location + "/configurator/images/products/" + image);
      $(".config-img-outer img").attr("src", "configurator/images/products/" + image);
    } else {
      $("." + type + " img").attr("src", "configurator/images/products/" + image);
      //$("." + type + " .zoom").attr("data-magnify-src", location + "/configurator/images/products/" + image);
    }
  } else {
    $("." + type + " img").attr("src", "configurator/images/products/noimage.png");
  }
});

$('ul.inner li a[href^="#"]').click(function () {
  var target = $(this).attr("href");
  $("html, body").animate({ scrollTop: $(target).offset().top }, 2000);
  return !1;
});

$("#modal_2 button[type=submit]").click(function (e) {
  e.preventDefault();
  var name = $("#modal_2 #name").val();
  var phone = $("#modal_2 #phone").val();
  var email = $("#modal_2 #email").val();
  var sborka = $("#modal_2 #sborka").val();
  var config = $(".result").html();
  var amount = $(".configuration .total-title").html();
  var sborkaLink = generateConfigToShare();
  var countRadiosChecked = $(":radio:checked").length;
  var countRadiosUnchecked = $('.scrollbar-inner').length;
  if (name === "" || phone === "" || email === "") {
    alert("Не все поля заполнены");
  } else {
    if(countRadiosChecked >= Math.floor(countRadiosUnchecked/4)){
      $.ajax({
        url: "../../modules/mail.php",
        type: "POST",
        data: { name: name, phone: phone, config: config, amount: amount, email: email, sborka: sborka, sborkaLink: sborkaLink },
        beforeSend: function() {
            
        },
        success: function (response) {
          alert(response);
          recordOrderToDB(name, email, phone, sborka, amount);
          //$("#modal_2 #name").val("");
          //$("#modal_2 #phone").val("");
          //$("#modal_2 #email").val("");
          $("#modal_2 button[class='mfp-close']").click();
        },
      });
    }else{
      alert("Количество выбранных элементов " + countRadiosChecked + " недостаточно для заказа. Необходимо выбрать минимум 4 компонента для сборки");
    }
  }
});



function recordOrderToDB(name, email, phone, sborka, amount){
  $.ajax({
    url: "../modules/recordOrderToDB.php",
    type: "POST",
    data: { name: name, phone: phone, amount: amount, email: email, sborka: sborka },
    success: function (response) {
      console.log(response);
    },
  });
}


$(document).ready(function ($) {
    
    $('.mobile-only').css('z-index', 999);
 

  $('#phone').inputmask("+(999) 99 999-99-99");
  $('#email').inputmask("email");
  /*Andrew script*/
  generateConfigForMultipleBlocksOnLoad()
  docReadyAllBlock();
  generateConfiguration();

  $(".list").each(function () {
    $(this).find("li:first").addClass("active");
  });

  $("body #hellopreloader").fadeOut("slow", function () {});

  $("input[type=radio]").removeAttr("checked");

  collectQuantityFromEachSelectedInput();

  $.each($(".menuElems li > a"), function () {
    var elem = $(this);
    $.ajax({
      url: "../modules/strtr.php",
      type: "POST",
      data: { strtr: $(this).text() },
      success: function (response) {
        elem.attr("href", "#" + response);
      },
    });
  });

  /*end*/

  /* Аккордеон */
  $(".toggle").click(function (e) {
    e.preventDefault();
    var $this = $(this);
    if ($this.next().hasClass("show")) {
      $this.next().removeClass("show");
      $this.next().slideUp(300);
    } else {
      $this.parent().parent().find("li .inner").removeClass("show");
      $this.parent().parent().find("li .inner").slideUp(300);
      $this.next().toggleClass("show");
      $this.next().slideToggle(300);
    }
  });

  /*$('.zoom').magnify({
    'speed': 100,
  });*/

  // Owl Carousel
  var owl = $(".carousel--first");
  var owl_2 = $('.carousel--second');
  var owl_3 = $('.carousel--inner');

  owl.owlCarousel({
    margin: 0,
    loop: true,
    dots: false,
    items: 3
  });

  owl_2.owlCarousel({
    margin: 0,
    loop: false,
    dots: false,
    items: 1,
    mouseDrag: false,
    touchDrag: false,
    pullDrag: false,
    center: true,
  });


  owl_3.owlCarousel({
    margin: 0,
    loop: true,
    dots: false,
    items: 3,
  });

  owl.on('changed.owl.carousel', function (property) {
    var current = property.item.index;
    var data = $(property.target).find(".owl-item .slide").eq(current).data('items');
    $(this).find(".owl-item .slide").removeClass('slide-active')
    $(property.target).find(".owl-item .slide").eq(current).addClass('slide-active');
    owl_2.trigger('to.owl.carousel', [data, 300]);

  })


  $('.carousel--first').on('click', '.slide', function () {
    if ($(this).hasClass('slide-active')) {
      return false;
    }

    else {
      $(this).parents(".owl-stage").find(".owl-item .slide").removeClass('slide-active');
      $(this).addClass('slide-active');
      var data = $(this).data('items');
      owl_2.trigger('to.owl.carousel', [data, 300]);
    }
  })



  /* ===========================
              HEADER UP-DOWN
  =========================== */


  var body = document.querySelector('body');
  var scrollUp = "scroll-up";
  var scrollDown = "scroll-down";
  var lastScroll = 150;


  window.addEventListener("scroll", function () {
    var currentScroll = window.pageYOffset;
    if (currentScroll == 0) {
      body.classList.remove(scrollUp);
      return;
    }

    if (currentScroll > lastScroll && !body.classList.contains(scrollDown)) {
      // down
      body.classList.remove(scrollUp);
      body.classList.add(scrollDown);
    } else if (
        currentScroll < lastScroll &&
        body.classList.contains(scrollDown)
    ) {
      // up
      body.classList.remove(scrollDown);
      body.classList.add(scrollUp);
    }
    lastScroll = currentScroll;
  });
});

/*$(window).on('load', function(){
  $("body .preloader2").css("display", "none");
});*/

window.addEventListener( 'load', function( event ) {
  $("body .preloader2").css("display", "none");
  setTimeout(function(){ 
      $('#__replain_widget').css('right', '60px');
  $('#__replain_widget').css('bottom', '0px');
  }, 3000);
});

//Victors code
$(window).on("scroll load", function () {
  /* ===========================
      STICKY HEADER
  =========================== */

  if ($(this).scrollTop() > 220) {
    $(".header--mobile").addClass("fixed");
  } else {
    $(".header--mobile").removeClass("fixed");
    addClasss()
  }
  addClasss();


  if($(this).scrollTop() > 100) {
    $('aside').addClass('fixed')
  }

  else {
    $('aside').removeClass('fixed');
  }

  /* ===========================
      STICKY HEADER
 =========================== */
  var offset_top = $("header.sticky").attr("data-offset");
  if (($(this).scrollTop() > offset_top)) {
    $("header.sticky").addClass("fixed");
  } else {
    $("header.sticky").removeClass("fixed");
  }

});


$('a[href*="#"]')
    // Remove links that don't actually link to anything
    .not('[href="#"]')
    .not('[href="#modal"]')
    .click(function (event) {
      // On-page links
      if (
          location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
          &&
          location.hostname == this.hostname
      ) {
        // Figure out element to scroll to
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        // Does a scroll target exist?
        if (target.length) {
          // Only prevent default if animation is actually gonna happen
          event.preventDefault();

          $('.carousel--inner a').removeClass('active');
          $(this).addClass('active');

          $('html, body').animate({
            scrollTop: target.offset().top + 50
          }, 1000, function () {
            // Callback after animation
            // Must change focus!
            var $target = $(target);
            $target.focus();
            if ($target.is(":focus")) { // Checking if the target was focused
              return false;
            } else {
              $target.attr('tabindex', '-1'); // Adding tabindex for elements not focusable
              $target.focus(); // Set focus again
            };
          });
        }
      }
    });


function addClasss() {
  var scrollPosition = $(window).scrollTop();

  $('.scrollbar-inner').each(function () {
    var offsetTop = $(this).offset().top;

    if (scrollPosition >= offsetTop) {
      var attribut = $(this).attr('id');
      $('.carousel--inner a').removeClass('active');
      $('.carousel--inner').find('a[href="#' + attribut +'"]').addClass('active');
    }
  })
}



$('.bottom-section__button').on('click', function(e) {
  e.preventDefault();
  if($('.bottom-section__inner').hasClass('active')) {
    $('.bottom-section__inner').removeClass('active');
  }

  else {
    $('.bottom-section__inner').addClass('active');
  }
});


$('.popup').magnificPopup({
  type: 'inline'
});
//Victors code

function collectQuantityFromEachSelectedInput(){
    sel_quantity.clear();
    $.each($("input[type=radio]:checked"), function () {
        var bd_id = $(this).attr('id');
        var id = $(this).closest("table").attr("id");
        var quantity = $(this).parent('div').find('select option:selected').text();
        sel_quantity.set(bd_id, quantity);
    });
    console.log(sel_quantity);
}

function calculatePlusMinusFromChecked() {
  $.each($("input[type=radio]:checked"), function () {
    var id = $(this).closest("table").attr("id");
    var bd_id = $(this).attr("id");
    selected_price = $(this).val();
    sel_price.set(id, selected_price);
    sel_bd_id.set(id, bd_id);
    $.each($('input[name="' + id + '"]'), function (index) {
      var raznica = parseInt($(this).val()) - parseInt(selected_price);
      if (raznica !== 0) {
        if (raznica > 0) {
          $("#" + id + " .price-block")
              .eq(index)
              .html(format2(raznica, "UZS"));
        } else {
          $("#" + id + " .price-block")
              .eq(index)
              .html(format2(raznica, "UZS"));
        }
      } else {
        $("#" + id + " .price-block")
            .eq(index)
            .html("");
      }
    });
  });
}

function printDiv() {
  var divContents = document.getElementsByClassName("center-block")[0].innerHTML;
  var a = window.open("", "", "height=500, width=500");
  a.document.write('<html><head><link rel="stylesheet" href="css/style.css"></head>');
  a.document.write("<head>");
  a.document.write('<link rel="stylesheet" href="css/style.css" type="text/css" media="print">');
  a.document.write("</head>");
  a.document.write('<body style="background-color:#fff;"> <h1>Div contents are <br>');
  a.document.write(divContents);
  a.document.write("</body></html>");
  a.document.close();
  a.print();
}

function setQuantities() {
    
  $.each($("input[type=radio]:checked"), function () {
      
    var id = $(this).closest("table").attr("id");
    var bd_id = $(this).attr("id");
    //alert(bd_id + ' ' + id);
    console.log(sel_quantity);
    if(sel_quantity.has(id)){
        //alert(sel_quantity.get(id));
    }
  });
}

function docReadyAllBlock() {
  $(".price-block").each(function (index) {
    var val = $(this).text();
    $(this).text(format2(val, "UZS"));
  });
}

function resetCustomBlock(block) {
  $("#" + block + " .price-block").each(function (index) {
    var val = $(this).parent().find("input").val();
    $(this).text(format2(val, "UZS"));
  });
}

function copyStringToClipboard(str) {
  var el = document.createElement("textarea");
  el.value = str;
  el.setAttribute("readonly", "");
  el.style = { position: "absolute", left: "-9999px" };
  document.body.appendChild(el);
  el.select();
  document.execCommand("copy");
  document.body.removeChild(el);
}

function format2(n, currency) {
  if (n > 0) {
    return (
        "+" +
        parseFloat(n)
            .toFixed(0)
            .replace(/(\d+)(?=(\d{3})\.?)/, "$1.")
            .replace(/(\d+)(?=(\d{3})\.?)/, "$1.")
            .replace(/(\d+)(?=(\d{3})\.?)/, "$1.") +
        " " +
        currency
    );
  } else {
    return (
        parseFloat(n)
            .toFixed(0)
            .replace(/(\d+)(?=(\d{3})\.?)/, "$1.")
            .replace(/(\d+)(?=(\d{3})\.?)/, "$1.")
            .replace(/(\d+)(?=(\d{3})\.?)/, "$1.") +
        " " +
        currency
    );
  }
}

function format(n, currency) {
  if (n > 0) {
    return (
        parseFloat(n)
            .toFixed(0)
            .replace(/(\d+)(?=(\d{3})\.?)/, "$1.")
            .replace(/(\d+)(?=(\d{3})\.?)/, "$1.")
            .replace(/(\d+)(?=(\d{3})\.?)/, "$1.") +
        " " +
        currency
    );
  } else {
    return (
        parseFloat(n)
            .toFixed(0)
            .replace(/(\d+)(?=(\d{3})\.?)/, "$1.")
            .replace(/(\d+)(?=(\d{3})\.?)/, "$1.")
            .replace(/(\d+)(?=(\d{3})\.?)/, "$1.") +
        " " +
        currency
    );
  }
}



function generateConfiguration(){
  var numBlocks = $('.scrollbar-inner').length;
  var Blocks = $('.scrollbar-inner');
  for(var i=0;i<2;i++){ //numBlocks
    var radioItemsEachBlockLength = $('.scrollbar-inner table:eq(' + i + ') input:radio').length;
    //randNum = Math.floor(Math.random() * radioItemsEachBlockLength);

    //alert(randNum);

    if($('.scrollbar-inner table:eq(' + i + ')').hasClass('countable')){
      $('.scrollbar-inner table:eq(' + i + ') input:radio:eq(0)').prop('checked', true);
    }
  }
  setImageOnLoad();
  generateConfigForMultipleBlocksOnLoad();
  calculatePlusMinusFromCheckedForultipleBlocksOnLoad();
}


function setImageOnLoad(){
  $.each($(".countable input:checked"), function () {
    var image = $(this).attr("data");
    var type = $(this).attr("name");
    if (image != "") {
      if (type === "korpus_dlya_pk") {
        $("." + type + " img").attr("src", "configurator/images/products/" + image);
        $(".config-img-outer img").attr("src", "configurator/images/products/" + image);
      } else {
        $("." + type + " img").attr("src", "configurator/images/products/" + image);
      }
    } else {
      $("." + type + " img").attr("src", "configurator/images/products/noimage.png");
    }
  });
}



function generateConfigForMultipleBlocksOnLoad() {
  var a = [];
  var b = [];
  var sum = 0;
  var str = "";
  $.each($(".countable input[type=radio]:checked"), function () {
    a.push($(this).val());
  });

  sum = 0;
  if (a.length > 0) {
    sum = parseInt(eval(a.join("+")));
  }
  $(".configuration-inner .total-title").html(format(sum, "UZS"));
  $(".bottom-section__left").html(format(sum, "UZS"));
  
  $.each($(".countable input:checked"), function () {
    var idVal = $(this).attr("id");
    var name = $(this).attr("name");
    var type = $("#" + name + " h2").text();
    str = "<p>" + type + "</p><span>\n</span><li>" + $("label[for='" + idVal + "']").attr("data-name") + "</li>";
    b.push(str);
  });
  $(".result").html(b);
  generateConfigToYaShare();
}



function calculatePlusMinusFromCheckedForultipleBlocksOnLoad() {
  $.each($(".countable input[type=radio]:checked"), function () {
    var id = $(this).closest("table").attr("id");
    var bd_id = $(this).attr("id");
    selected_price = $(this).val();
    sel_price.set(id, selected_price);
    sel_bd_id.set(id, bd_id);
    $.each($('input[name="'+ id +'"]'), function (index) {
      var raznica = parseInt($(this).val()) - parseInt(selected_price);
      if (raznica !== 0) {
        if (raznica > 0) {
          $("#" + id + " .price-block")
              .eq(index)
              .html(format2(raznica, "UZS"));
        } else {
          $("#" + id + " .price-block")
              .eq(index)
              .html(format2(raznica, "UZS"));
        }
      }
      else {
        $("#" + id + " .price-block")
            .eq(index)
            .html("");
      }
    });
  });
}

$(document).on("change", "body .quantity", function () {
    //var quatity = $(this).parent().find('table').attr('id');
    collectQuantityFromEachSelectedInput();
    generateConfig();
});

function generateConfig() {
  var a = [];
  var b = [];
  var sum = 0;
  var str = "";
  var kolichestvo = [];
  var quantity = 0;
  $.each($("input[type=radio]:checked"), function () {
    quantity = $(this).parent('div').find('select option:selected').text();
    a.push($(this).val() * quantity);
    //console.log(a);
  });
  sum = 0;
  if (a.length > 0) {
    sum = parseInt(eval(a.join("+")));
  }
  $(".configuration-inner .total-title").html(format(sum, "UZS"));
  $(".bottom-section__left").html(format(sum, "UZS"));

  $.each($("input[type=radio]:checked"), function () {
    var idVal = $(this).attr("id");
    var name = $(this).attr("name");
    var type = $("#" + name + " h2").text();
    quantity = $(this).parent('div').find('select option:selected').text();
    str = "<p>" + type + "</p><span>\n</span><li>"+quantity+" x " + $("label[for='" + idVal + "']").attr("data-name") + "</li>";
    b.push(str);
  });
  $(".result").html(b);
  generateConfigToYaShare();
}

$('.androidShareBtn').click(function(e) {
  var sborka = generateConfigToShare();
  if (navigator.share) {
    navigator.share({
      title: 'Конфигурация',
      url: sborka,
    })
        .then(() => console.log('Successful share'))
        .catch((error) => console.log('Error sharing' + error));
  }
});

function generateConfigToShare(){
  var generatedLink = "";
  var link = window.location.href;
  $.each($("input[type=radio]:checked"), function () {
    //alert($(this).closest('select').find(':selected').val());
    generatedLink += "&" + $(this).attr("name") + "=" + $(this).attr("id") + "(" + $(this).parent('.product-block').find('.quantity :selected').val() + ")";
  });
  //alert(generatedLink);
  var result = link.split('&')[0] + generatedLink;
  return result.split('#').join('');
}

function generateConfigToYaShare(){
  var generatedLink = "";
  var link = window.location.href;
  $.each($("input[type=radio]:checked"), function () {
    //alert($(this).closest().find('select').filter(':selected').val());
    generatedLink += "&" + $(this).attr("name") + "=" + $(this).attr("id") + "(" + $(this).parent('.product-block').find('.quantity :selected').val() + ")";
  });
  var result = link.split('&')[0] + generatedLink;
  $('.ya-share2').attr('data-url', result.split('#').join(''));
}


$('.info').click(function(e) {
  e.preventDefault();
  if($(this).attr('href').toLowerCase().includes('http')){
      window.open($(this).attr('href'), '_blank', 'width=600,height=600');
  }
});




