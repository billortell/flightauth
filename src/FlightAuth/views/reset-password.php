<?php
/***
 * @var $title string
 * @var $content string
 * @var $rel_uri string
 */
?>
<div class="text-center mb-5 ">
    <h1 class="fw-bolder">Reset Password</h1>
    <p class=" mb-4">
        Now, let's reset that password.
    </p>
</div>
<div class="justify-content-center text-center">
    <form class="form-floating" action="<?php echo $rel_uri;?>/reset-password-form" method="post" accept-charset="utf-8">

        <div class="form-floating mb-3">
            <input type="password" class="form-control"  name="password" id="floatingInputPassword" placeholder="Password" value="">
            <label for="floatingInputPassword">Password</label>
        </div>

        <!-- Submit Button-->
        <div class="d-grid">
            <button type="submit" id="submitButton" class="btn btn-primary btn-lg">Reset Password</button>
        </div>

        <input type="hidden" name="action" value="admin.resetPassword">

        <!-- for verification purposes -->
        <input type="hidden" name="selector" value="<?php echo $_SESSION['_reset_password']['selector']; ?>">
        <input type="hidden" name="token" value="<?php echo $_SESSION['_reset_password']['token']; ?>">

        <!-- used to keep the gremlins away -->
        <input type="hidden" name="csrf_token" value="<?= Flight::session()->getValue('csrf_token') ?>"/>

    </form>
</div>

