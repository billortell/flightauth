<?php
/***
 * @var $title string
 * @var $content string
 * @var $rel_uri string
 */
?>
<div class="text-center mb-5 ">
    <h1 class="fw-bold ">Success!</h1>
</div>
<div class="justify-content-center text-center">
    <p class=" mb-4">
        And, we've taken the liberty of signing you in automagically. <br>
        Let's get to the goods <a href="<?php echo empty($rel_uri)?"/":$rel_uri;?>">here</a>.
    </p>
</div>

