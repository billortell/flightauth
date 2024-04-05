<?php
/***
 * @var $title string
 * @var $content string
 * @var $rel_uri string
 */
?>
<div class="text-center mb-5 ">
    <h1 class="fw-bolder">Change Email</h1>
</div>
<div class="justify-content-center text-center">
    <form class="form-floating" action="<?php echo $rel_uri;?>/change-email" method="post" accept-charset="utf-8">
        <div class="form-floating mb-3">
            <input type="text" class="form-control"  name="email" placeholder="Email" value="">
            <label for="floatingInputPassword">Enter new email</label>
        </div>
        <div class="d-grid">
            <button type="submit" id="submitButton" class="btn btn-primary btn-lg">Change Email</button>
        </div>
        <input type="hidden" name="action" value="admin.changeEmail">

        <!-- used to keep the gremlins away -->
        <input type="hidden" name="csrf_token" value="<?= Flight::session()->getValue('csrf_token') ?>"/>
    </form>
</div>
