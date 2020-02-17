

<div class="jumbotron">
                    <h1 class="display-4">Create Account</h1>
                    
                    <form style="margin-top: 15px;" method="post" action="">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group <?php  if ($MISSING_USERNAME == true) echo 'has-danger'; ?>">
                                    <input type="text" class="form-control form-control-alternative" id="username" value="<?php echo $USERNAME; ?>" name="username" placeholder="Username">
                                    <?php
                                            if ($INVALID_USERNAME == true) {
                                                echo '<div class="help-block">Username Already Taken</div>';
                                            }
                                    ?>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group <?php  if ($MISSING_FIRST_NAME == true) echo 'has-danger'; ?>">
                                    <input type="text" class="form-control form-control-alternative" value="<?php echo $FIRST_NAME; ?>" name="first_name" placeholder="First Name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group <?php  if ($MISSING_LAST_NAME == true) echo 'has-danger'; ?>">
                                    <input type="text" class="form-control form-control-alternative" value="<?php echo $LAST_NAME; ?>" name="last_name" placeholder="Last Name">
                                   
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group <?php  if ($MISSING_EMAIL == true) echo 'has-danger'; ?>">
                                    <input type="email" class="form-control form-control-alternative" value="<?php echo $EMAIL; ?>" name="email" placeholder="Email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group <?php  if ($MISSING_PASSWORD == true) echo 'has-danger'; ?>">
                                    <input type="password" class="form-control form-control-alternative" value="<?php echo $PASSWORD; ?>" name="password" placeholder="Password">
                                    <?php
                                            if ($INVALID_PASSWORD == true) {
                                                echo '<div class="help-block">Password Invalid (8-50 characters)</div>';
                                            }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group <?php  if ($MISSING_CONFIRM_PASSWORD == true) echo 'has-danger'; ?>">
                                    <input type="password" class="form-control form-control-alternative" value="<?php echo $CONFIRM_PASSWORD; ?>" id="exampleFormControlInput1" name="confirm_password" placeholder="Confirm Password">
                                    <?php
                                            if ($INVALID_CONFIRM_PASSWORD == true) {
                                                echo '<div class="help-block">Confirm Password and Password did not match</div>';
                                            }
                                    ?>
                                </div>
                            
                            </div>
                        </div>
                        <div class="alert alert-default" role="alert">
                            <strong>Notice!</strong> All entries need at least 4 characters! Passwords needs at least 8.
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-primary btn-lg" href="#" role="button" name="submit">Create Account</button>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>
                    </form>
                </div>