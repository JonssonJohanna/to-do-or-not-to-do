<?php
require __DIR__ . '/app/autoload.php';
require __DIR__ . '/views/header.php';
require __DIR__ . '/views/navigation.php'; ?>

<div>
    <?= show_error() ?>
    <?= show_message() ?>
</div>

<ul>
    <li>Username: <?= get_user_info($database)['username'] ?></li>
    <li>Email address: <?= get_user_info($database)['email'] ?></li>
    <li>Completed tasks:</li>
</ul>

<div>
    <img src="<?= get_image_url() ?>" alt="">
</div>
<?php

?>

<div>Change your profile picture</div>
<form action="/app/users/avatar/upload.php" method="post" enctype="multipart/form-data">
    <label for="avatar">Upload your avatar image in PNG/JPG format</label>
    <input type="file" name="avatar" id="avatar" accept=".png, .jpg, .jpeg">
    <button type="submit">Upload image</button>
</form>

<div>Change your email address</div>
<form action="/app/users/change_email.php" method="post">
    <label for="email">new email address:</label>
    <input type="email" name="email" id="email">
    <button type="submit">Change email address</button>
</form>

<div>Change your password</div>
<form action="/app/users/change_password.php" method="post">
    <label for="password">current password:</label>
    <input name="password" id="password" type="password">
    <label for="new_password">select your password:</label>
    <input name="new_password" id="new_password" type="password">
    <button type="submit">Change password</button>
</form>

<?php require __DIR__ . '/views/footer.php';
