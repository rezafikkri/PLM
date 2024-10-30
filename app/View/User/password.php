<main class="form-plm w-100 mx-auto mt-4">
    <h1 class="h2 mb-2 fw-bold">Update Password</h1>
    <p class="mb-4 text-body-secondary">Make sure to note down your new password, to be careful when you forget the password.</p>

    <div class="position-fixed z-3 alert-fixed">
    <?php if (isset($errors)) :  ?>
        <?php foreach ($errors as $error) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <p class="m-0"><?= $error ?></p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($success)) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <p class="m-0"><?= $success ?></p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    </div>

    <form method="post" action="/users/password">
        <div class="form-floating">
            <input type="password" name="oldPassword" class="form-control form-control-lg" placeholder="Old Password" id="oldPassword">
            <label for="oldPassword">Old Password</label>
        </div>
        <div class="form-floating mb-3">
            <input type="password" name="newPassword" class="form-control form-control-lg" placeholder="New Password" id="newPassword">
            <label for="newPassword">New Password</label>
        </div>

        <button class="btn btn-lg btn-primary w-100 py-2 fw-medium" type="submit">Save</button>
        <a href="/" class="text-decoration-none mt-4 d-block btn btn-outline-secondary"><i class="bi bi-arrow-left-short"></i>Back to home</a>
        <p class="mt-3 mb-5">By <a href="https://rezafikkri.github.io" class="text-decoration-none">Reza Sariful Fikri</a></p>
    </form>
</main>
