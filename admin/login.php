<?php require('inc/setting.php'); ?>

<?php
$user = new User();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require('inc/head.php'); ?>
</head>

<body>
    <main class="d-flex w-100">
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
                    <div class="d-table-cell align-middle">

                        <div class="text-center mt-4">
                            <h1 class="h2">Welcome back!</h1>
                            <p class="lead">
                                Sign in to your account to continue
                            </p>

                            <?php



                            if (isset($_POST['btnregister'])) {
                                if ($_POST['password1'] == $_POST['password2']) {
                                    $register = $user->register($_POST);
                                    echo $register;
                                } else {
                                    ?>
                                    <div class="alert alert-danger" role="alert">
                                        <h5 style="text-align: center;"><b>Password mismatched.</b> Please check!</h5>
                                    </div>
                                    <?php
                                }
                            }


                            if (isset($_POST['btnlogin'])) {
                                if ($_POST['useremail'] != "") {
                                    if ($_POST['password'] != "") {
                                        $login = $user->login($_POST);
                                    } else {
                                        ?>
                                        <div class="alert alert-danger" role="alert">
                                            <h5 style="text-align: center;"><b>Password is Required</b></h5>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="alert alert-danger" role="alert">
                                        <h5 style="text-align: center;"><b>Username is Required</b></h5>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="m-sm-3">
                                    <form action="" method="post">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input class="form-control form-control-lg" type="email"
                                                placeholder="Email Address" name="useremail" required value="<?php if (isset($_POST['useremail'])) {
                                                    echo $_POST['useremail'];
                                                } ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <input class="form-control form-control-lg" type="password" name="password"
                                                placeholder="Enter your password" required />
                                        </div>
                                        <div>
                                            <div class="form-check align-items-center">
                                                <input id="customControlInline" type="checkbox" class="form-check-input"
                                                    value="remember-me" name="remember-me" checked>
                                                <label class="form-check-label text-small"
                                                    for="customControlInline">Remember me</label>
                                            </div>
                                        </div>
                                        <div class="d-grid gap-2 mt-3">
                                            <button class="btn btn-lg btn-primary" name="btnlogin">Sign in</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-3">
                            Don't have an account? <a href="pages-sign-up.html">Sign up</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="js/app.js"></script>

</body>

</html>