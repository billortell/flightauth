<?php
/***
 * @var $title string
 * @var $content string
 * @var $rel_uri string
 */
?>

<div class="text-center mb-5 ">
    <h1>Aw, shucks...</h1>
</div>
<div class="justify-content-center text-center">
    <p class=" mb-4">
        This link is no longer valid.
    </p>
    <p class=" mb-4">
        This can happen for various reasons.  However, if you still
        cannot <a href="<?php echo $rel_uri;?>/login">login</a>
        you can still try to <a href="<?php echo $rel_uri;?>/verification-resend">re-send</a>
        another verification email.
    </p>

</div>
