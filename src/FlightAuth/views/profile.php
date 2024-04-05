<?php
/***
 * @var $title string
 * @var $content string
 * @var $rel_uri string
 */
?>
<div class="text-center mb-5">
    <h1 class="fw-bolder">Profile</h1>
</div>
<div class="justify-content-center left">

    <form class="form-floating" action="<?php echo $rel_uri;?>/profile/<?php echo $profile_user_id;?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">

        <div class="row justify-content-md-start ">
            <div class="col-md-3 text-center ">
                <div class="mb-2">
<!--                    <img class='circle-image-large' src="--><?php //echo Flight::get_gravatar($email, 125, 'identicon'); ?><!--" alt="" />-->
                    <img class='circle-image-large' src="<?php echo $user_profile->pic;?>" alt="" />
                </div>
                <div>
                    <p ><a href="javascript:void(0);" class="upload-image">Update profile image</a></p>
                    <div class="input-group mb-3 visually-hidden">
                        <input class="form-control profile_pic" type="file" name="profile_pic" id="formFile" />
                        <!--            <label for="formFile" class="form-label">Feature currently unavailable...</label>-->
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control "  name="profile_firstname" id="profile_firstname" placeholder="First name" value="<?php echo $user_profile->firstname;?>" aria-describedby="profile_firstname">
                    <label for="profile_firstname">First name</label>
                </div>
                <div class="form-floating mb-3 ">
                    <input type="text" class="form-control "  name="profile_lastname" id="profile_lastname" placeholder="Last name" value="<?php echo $user_profile->lastname;?>" aria-describedby="profile_lastname">
                    <label for="profile_lastname">Last name</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="phone" class="form-control "  name="profile_phone" id="profile_phone" placeholder="Contact number" value="<?php echo $user_profile->phone;?>" aria-describedby="profile_phone">
                    <label for="profile_phone">Contact Number</label>
                </div>
            </div>
        </div>

        <!-- Submit Button-->
        <div class="d-grid">
            <input type="hidden" name='profile_user_id' value="<?php echo $profile_user_id;?>">
            <button type="submit" id="submitButton" class="btn btn-primary btn-lg">Update</button>
        </div>

    </form>

</div>

