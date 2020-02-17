

<div class="jumbotron">
    <h1 class="display-4">Dashboard</h1>
    <?php 
        if ($ERROR) {
            echo '  <div class="alert alert-danger" role="alert">
                        <strong>Invalid Username or Password!</strong> 
                    </div>';
        } 
    ?>
    <form style="margin-top: 15px;" method="post" action="">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control form-control-alternative" id="first_name" name="first_name" value="<?php echo $user["first_name"]; ?>" placeholder="Username">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control form-control-alternative" id="last_name "name="last_name" value="<?php echo $user["last_name"]; ?>" placeholder="Username">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control form-control-alternative" id="username" name="username" disabled="true" value="<?php echo $user["username"]; ?>" placeholder="Username">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control form-control-alternative" id="email" name="email" value="<?php echo $user["email"]; ?>" placeholder="Email">
                    </div>
                </div>
            </div>
        <!-- Password Change -->
        <hr/>
        <div style="background: white; padding: 10px; border-radius: 10px;">
        <h1 class="display-4">Password Update</h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group <?php  if ($PASSWORD_ERROR == true) echo 'has-danger'; ?>">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control form-control-alternative" id="current_password" name="current_password" placeholder="Current Password">
                        <?php
                                if ($INVALID_CURRENT_PASSSWORD == true) {
                                    echo '<div class="help-block">Invalid Current Password!</div>';
                                }
                                    ?>
                        </div>
                    </div>
                </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group <?php  if ($PASSWORD_ERROR == true) echo 'has-danger'; ?>">
                        <label for="password">New Password</label>
                        <input type="password" class="form-control form-control-alternative" id="password" name="password" placeholder="New Password">
                        <?php
                                if ($INVALID_NEW_PASSSWORD == true) {
                                        echo '<div class="help-block">Confirm Password and Password did not match</div>';
                                    }
                                ?>
                        </div>
                    </div>
                </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group <?php  if ($PASSWORD_ERROR == true) echo 'has-danger'; ?>">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" class="form-control form-control-alternative" id="confirm_password" name="confirm_password"  placeholder="Confirm New Password">
                        </div>
                    </div>
                </div>
        </div>
        <hr/>
          <!-- Password Change -->
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-primary btn-lg" href="#" role="button" name="submit">UPDATE</button>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </form>
</div>