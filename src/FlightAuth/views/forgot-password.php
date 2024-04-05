<?php
/***
 * @var $title string
 * @var $content string
 * @var $rel_uri string
 */
?>
<div class="text-center mb-5 ">
    <h1 class="fw-bolder">Reset Password</h1>
</div>
<div class="justify-content-center text-center">
    <p class="mb-4">
        If your email is in our system, you'll receive a verification link to reset your password.
    </p>
    <form class="form-floating" action="<?php echo $rel_uri;?>/forgot-password" method="post" accept-charset="utf-8">
        <div class="form-floating mb-3">
            <input type="email" class="form-control "  name="email" id="floatingInputEmail" placeholder="Email" value="">
            <label for="floatingInputEmail">Email</label>
        </div>
        <div class="d-grid">
            <button type="submit" id="submitButton" class="btn btn-primary btn-lg">Reset Password</button>
        </div>
        <input type="hidden" name="action" value="admin.forgotPassword">

        <!-- used to keep the gremlins away -->
        <input type="hidden" name="csrf_token" value="<?= Flight::session()->getValue('csrf_token') ?>"/>
    </form>

</div>

