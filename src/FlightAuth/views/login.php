<?php
/***
 * @var $title string
 * @var $content string
 * @var $rel_uri string
 */
?>
<div class="text-center mb-5 ">
    <h1 class="fw-bolder">Login</h1>
    <p class=" mb-4">
        Welcome back!
    </p>
</div>
<div class="justify-content-center text-center">

    <form class="form-floating" novalidate action="<?php echo $rel_uri;?>/login" method="post" accept-charset="utf-8">

        <div class="form-floating mb-3">
            <input type="email" class="form-control " name="email" id="floatingInputEmail" placeholder="Email" value="">
            <label for="floatingInputEmail">Email</label>
        </div>

        <div class="form-floating mb-3">
            <input type="password" class="form-control"  name="password" id="floatingInputPassword" placeholder="Password" value="">
            <label for="floatingInputPassword">Password</label>
        </div>

        <!-- Submit Button-->
        <div class="d-grid">
            <button type="submit" id="submitButton" class="btn btn-primary btn-lg">Login</button>
        </div>

        <p class=" mt-4">
            <a href="<?php echo $rel_uri;?>/forgot-password">Forgot password</a>
            <br>
            <a href="<?php echo $rel_uri;?>/register">Register for Access</a>
        </p>


        <input type="hidden" name="action" value="admin.login">

        <!-- used to keep the gremlins away -->
        <input type="hidden" name="csrf_token" value="<?= Flight::session()->getValue('csrf_token') ?>"/>
    </form>

</div>
