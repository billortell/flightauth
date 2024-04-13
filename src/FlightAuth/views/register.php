<?php
/***
 * @var $title string
 * @var $content string
 * @var $rel_uri string
 * @var $auth string
 */
?>
<div class="text-center mb-5">
    <h1>Register</h1>
</div>
<div class="justify-content-center left">

    <form class="form-floating" action="<?php echo $rel_uri;?>/register" method="post" accept-charset="utf-8">
        <div class="form-floating mb-3">
            <input type="text" class="form-control "  name="username" id="floatingInputUsername" placeholder="Username" value="" aria-describedby="username_help">
            <label for="floatingInputUsername">Username</label>
            <div id="username_help" class="form-text">Required to be unique - be creative ;)</div>
        </div>
        <div class="form-floating mb-3">
            <input type="email" class="form-control "  name="email" id="floatingInputEmail" placeholder="Email" value="" aria-describedby="email_help">
            <label for="floatingInputEmail">Email</label>
            <div id="email_help" class="form-text">If you've registered before, please don't register again (<a href="/forgot-password">recover password</a> instead).</div>
        </div>
        <div class="form-floating mb-3">
            <input type="password" class="form-control"  name="password" id="floatingInputPassword" placeholder="Password" value="" aria-describedby="password_help">
            <label for="floatingInputPassword">Password</label>
            <div id="password_help" class="form-text">Minimum 8 characters (don't use 123456)</div>
        </div>
        <?php if ( $auth->isAdmin() ) : ?>
        <div class="form-check mb-3 text-center">
            <input type="radio" class="btn-check" name="require_email_verification" id="success-outlined" autocomplete="off" checked value="1">
            <label class="btn btn-outline-success rounded rounded-pill" for="success-outlined">Require </label>

            or

            <input type="radio" class="btn-check" name="require_email_verification" id="danger-outlined" autocomplete="off" value="0">
            <label class="btn btn-outline-danger rounded rounded-pill" for="danger-outlined">Skip </label>

            Email Verification
        </div>
        <?php endif; ?>

        <!-- Submit Button-->
        <div class="d-grid">
            <button type="submit" id="submitButton" class="btn btn-primary btn-lg">Create Account</button>
        </div>

        <div class="text-center">
            <p class=" mt-4 mb-4">
                Already have an account? <a href="<?php echo $rel_uri;?>/login">Login</a>
            </p>
            <p class="small text-muted">
                By creating an account, you agree to our <a href="<?php echo $rel_uri;?>/terms">Terms & Conditions</a> &
                <a href="<?php echo $rel_uri;?>/privacy">Privacy Policy</a>. <br>
                You also agree to receiving occasional emails about our product and services on your registered email address.
            </p>
        </div>
        <input type="hidden" name="action" value="admin.createUser">

        <!-- used to keep the gremlins away -->
        <input type="hidden" name="csrf_token" value="<?= Flight::session()->getValue('csrf_token') ?>"/>

    </form>

</div>
