

<div class="jumbotron">
    <h1 class="display-4">Account</h1>
    <?php 
        if ($ERROR) {
            echo '  <div class="alert alert-danger" role="alert">
                        <strong>Invalid Username or Password!</strong> 
                    </div>';
        } 
    ?>
    <form style="margin-top: 15px;" method="post" action="<?php echo $ACTION; ?>">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                        <input type="text" class="form-control form-control-alternative" name="username" placeholder="Username">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <input type="password" class="form-control form-control-alternative" name="password" placeholder="Password">
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-primary btn-lg" href="#" role="button" name="submit">Sign In</button>
            </div>
            <div class="col-md-6">
            </div>
        </div>
        <br/>
        <p><i>Don't have an account? <a href="<?php echo $path . 'accounts/create'; ?>">Sign up!</a></i></p>
    </form>
</div>