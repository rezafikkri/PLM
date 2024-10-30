<main class="form-plm w-100 mx-auto mt-5">
    <h1 class="h2 mb-2 fw-bold">Update Profile</h1>
    <p class="mb-4 text-body-secondary">Update your profile information.</p>

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

    <form method="post" action="/users/profile">
        <div class="form-floating mb-3">
            <input type="text" name="username" class="form-control form-control-lg rounded-all" placeholder="Username" value="<?= old('username', $user->getUsername()) ?>" id="username">
            <label for="username">Username</label>
        </div>

        <button class="btn btn-lg btn-primary w-100 py-2 fw-medium" type="submit">Save</button>
        <a href="/" class="text-decoration-none mt-4 d-block btn btn-outline-secondary"><i class="bi bi-arrow-left-short"></i>Back to home</a>
        <p class="mt-3 mb-5">By <a href="https://rezafikkri.github.io" class="text-decoration-none">Reza Sariful Fikri</a></p>
    </form>
</main>
