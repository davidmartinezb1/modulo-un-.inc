<?php 
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
$url=$protocol.$_SERVER['HTTP_HOST'].'/'.drupal_get_path("module","tedaMas").'/assets/files/terminos-y-condiciones-te-damos-mas.pdf';
//$url='//docs.google.com/gview?url='.$url.'&amp;embedded=true';
?>


<main>
    <div id="visualizador" class="visualizador" style="display: block;">
        <iframe width="1180" height="600" frameborder="0" src="<?php print $url ?>"></iframe>
    </div>
</main>
