<?php
/***
 * @var $title string
 * @var $content string
 * @var $rel_uri string
 */
?>
<div class="text-center mb-5 ">
    <h1 >Change Password</h1>
</div>
<div class="justify-content-center text-center">
    <form class="form-floating" action="<?php echo $rel_uri;?>/change-password" method="post" accept-charset="utf-8">

        <div class="form-floating mb-3">
            <input type="password" class="form-control"  name="old_password" id="floatingInputPassword" placeholder="Old Password" value="">
            <label for="floatingInputPassword"><strong>Current</strong> Password</label>
        </div>

        <div class="form-floating mb-3">
            <input type="password" class="form-control"  name="new_password" id="floatingInputPassword" placeholder="New Password" value="">
            <label for="floatingInputPassword"><strong>New</strong> Password</label>
        </div>

        <!-- Submit Button-->
        <div class="d-grid">
            <button type="submit" id="submitButton" class="btn btn-primary btn-lg">Change Password</button>
        </div>

        <input type="hidden" name="action" value="admin.changePassword">

        <!-- used to keep the gremlins away -->
        <input type="hidden" name="csrf_token" value="<?= Flight::session()->getValue('csrf_token') ?>"/>
    </form>
</div>
