<h1>Users</h1>
<p>Larger lists = bliss</p>

<div class="card my-3">
    <div class="card-header">
        <li class="list-group-item">
            <div class="row">
                <div class="d-none d-md-block col-md-1">User</div>
                <div class="col-3 col-md-5">Username/Email</div>
                <div class="col-3 col-md-2">Verified</div>
                <div class="col-3 col-md-2">Last login</div>
                <div class="col-3 col-md-2">Registered</div>

            </div>
        </li>
    </div>
    <ul class="list-group list-group-flush">

            <?php /* @var $users array */ ?>
            <?php foreach ( $users as $user ) : ?>
            <?php
                $registered = !empty($user->registered) ? \Carbon\Carbon::createFromTimestamp($user->registered)->toDateTimeString() : $user->registered;
                $last_login = !empty($user->last_login) ? \Carbon\Carbon::createFromTimestamp($user->last_login)->toDateTimeString() : $user->last_login;
            ?>
        <li class="list-group-item">
            <div class="row">
                <div class="d-none d-md-block col-md-1">
                    <?= $user->id ;?>
                    <a href="/user/login-as/<?= $user->id ;?>">
                        <i class="bi bi-person-fill-up"></i>
                    </a>
                </div>
                <div class="col-3 col-md-5"><?= $user->email ;?><br><?= $user->username ;?></div>
                <div class="col-3 col-md-2"><?= $user->verified ;?></div>
                <div class="col-3 col-md-2"><?= $last_login ;?></div>
                <div class="col-3 col-md-2"><?= $registered ;?></div>
            </div>
        </li>
            <?php endforeach; ?>

    </ul>
</div>
