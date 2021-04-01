<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="configurator/js/owl.carousel.min.js"></script>
<script src="https://dimsemenov.com/plugins/magnific-popup/dist/jquery.magnific-popup.min.js?v=1.1.0"></script>
<!--<script src="js/minibar.min.js"></script>-->
<script src="configurator/js/main.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.2.6/jquery.inputmask.bundle.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $( ".accordion li ul li a" ).removeAttr( "class" );
</script>
<link rel="stylesheet" href="https://cdn.saas-support.com/widget/cbk.css">
<!--<script type="text/javascript" src="https://cdn.saas-support.com/widget/cbk.js?wcb_code=a82740ef67c539733c07113e930e620c" charset="UTF-8" async></script>-->
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(58632361, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        var gets = (function() {
            var a = window.location.search;
            var b = new Object();
            a = a.substring(1).split("&");
            for (var i = 0; i < a.length; i++) {
                c = a[i].split("=");
                b[c[0]] = c[1];
            }
            return b;
        })();
        console.log(gets);
        for (var prop in gets) {
            //alert(prop + "/" + gets[prop]);
            if(prop !== 'cat'){
                SetPropertyFromURL(prop, gets[prop]);
            }
        
        }
    });

    function SetPropertyFromURL(prop, value){
        let a = value.substr(0, value.indexOf('('));
        var exist_prop = $("table").is("#"+prop);
        var exist_value = $("input[type=radio]").is("#"+a);
        if(exist_prop){
            //alert("Найдена: " + prop);
            if(exist_value){
                //alert("Найдена: " + value);
                let select_num = value.split('(').pop().split(')')[0];
                let part_id = value.substr(0, value.indexOf('(')); 
                $( "#" + prop ).find("#" + part_id).prop("checked", true);
                $( "#" + prop ).find('.quantity option[value=' + select_num + ']').attr('selected','selected');
                var image = $( "#" + prop ).find("#" + part_id).attr("data");
                var type = $( "#" + prop ).find("#" + part_id).attr("name");
                setProductImage(image, type);
            }
        }
        calculatePlusMinusFromCheckedForultipleBlocksOnLoad();
        calculatePlusMinusFromChecked();
        generateConfigForMultipleBlocksOnLoad();
        generateConfig();
    }

    function configLinkToEmail(){
        var generatedLink = "";
        var link = window.location.href;
        $.each($("input[type=radio]:checked"), function () {
            generatedLink += "&" + $(this).attr("name") + "=" + $(this).attr("id") + "(" + $(this).parent('.product-block').find('.quantity :selected').val() + ")";
        });
        var result = link.split('&')[0] + generatedLink;
        $("#sborkaLink").attr("value", result.split('#').join(''));
    }

    /*function generateConfigToLink(){
        var generatedLink = "";
        var link = window.location.href;
        $.each($("input[type=radio]:checked"), function () {
            generatedLink += "&" + $(this).attr("name") + "=" + $(this).attr("id");
        });
        var result = link.split('&')[0] + generatedLink;
        navigator.clipboard.writeText(result.split('#').join(''))
            .then(() => {
                alert("Ссылка на сборку скопирована");
            })
            .catch(err => {
                console.log('Невозможно скопировать ссылку на сборку', err);
            });
    }*/
    
    function generateConfigToLink(){
        var generatedLink = "";
        var link = window.location.href;
        $.each($("input[type=radio]:checked"), function () {
            generatedLink += "&" + $(this).attr("name") + "=" + $(this).attr("id") + "(" + $(this).parent('.product-block').find('.quantity :selected').val() + ")";
        });
        var result = link.split('&')[0] + generatedLink;
        navigator.clipboard.writeText(result.split('#').join(''))
            .then(() => {
                alert("Ссылка на сборку скопирована");
            })
            .catch(err => {
                console.log('Невозможно скопировать ссылку на сборку', err);
            });
    }

    function setProductImage(image, type){
        var location = window.location.origin;
        if (image != "") {
            if (type === "korpus_dlya_pk") {
                $("." + type + " img").attr("src", "configurator/images/products/" + image);
                $("." + type + " .zoom").attr("data-magnify-src", location + "/configurator/images/products/" + image);
                $(".config-img-outer img").attr("src", "configurator/images/products/" + image);
            } else {
                $("." + type + " img").attr("src", "configurator/images/products/" + image);
                $("." + type + " .zoom").attr("data-magnify-src", location + "/configurator/images/products/" + image);
            }
        } else {
            $("." + type + " img").attr("src", "configurator/images/products/noimage.png");
        }
    }
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/58632361" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<script>
    window.replainSettings = { id: '5d3deab7-ae79-4c26-9b03-75199b831260' };
    (function(u){var s=document.createElement('script');s.type='text/javascript';s.async=true;s.src=u;
        var x=document.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);
    })('https://widget.replain.cc/dist/client.js');
</script>