<?php
/***
 * @var $title string
 * @var $content string
 * @var $rel_uri string
 */
?>
<div class="text-center mb-5 ">
    <h1>Let's verify your account!</h1>
</div>
<div class="justify-content-center text-center">

    <form class="form-floating" action="<?php echo $rel_uri;?>/verification-resend" method="post" accept-charset="utf-8">
        <div class="form-floating mb-3">
            <input type="email" class="form-control "  name="email" id="floatingInputEmail" placeholder="Email" value="">
            <label for="floatingInputEmail">Email</label>
        </div>
        <!-- Submit Button-->
        <div class="d-grid">
            <button type="submit" id="submitButton" class="btn btn-primary btn-lg">Send Verification Link</button>
        </div>
        <input type="hidden" name="action" value="admin.resendLink">

        <!-- used to keep the gremlins away -->
        <input type="hidden" name="csrf_token" value="<?= Flight::session()->getValue('csrf_token') ?>"/>
    </form>

</div>
