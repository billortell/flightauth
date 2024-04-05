<?php
/***
 * @var $title string
 * @var $content string
 * @var $rel_uri string
 * @var $auth object
 */
?>
<div class="text-center mb-5 ">
    <h1 class="fw-bolder">
        Verification needed
    </h1>
</div>
<div class="justify-content-center text-center">
    <?php if ( $auth->isAdmin() ) : ?>
    <p>
        Have user verify their email address. If they are unable to verify it, they will not be able to login.
    </p>
    <?php else : ?>
    <p>
        Please help us out by verifying your email now.
    </p>
    <?php endif; ?>
</div>
