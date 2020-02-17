<!-- Start of Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand title" href="<?php echo $path . 'home'; ?>">Cardinal Volunteer</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-primary" aria-controls="navbar-primary" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar-primary">
            <div class="navbar-collapse-header">
                <div class="row">
                    <div class="col-6 collapse-brand">
                    <a class="navbar-brand title" style="color: black" href="#">Cardinal Volunteer</a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar-primary" aria-controls="navbar-primary" aria-expanded="false" aria-label="Toggle navigation">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
            <ul class="navbar-nav ml-lg-auto">
             <li class="nav-item">
                    <a class="nav-link" href="<?php echo $path . 'home'; ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $path . 'events'; ?>">Events</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" id="navbar-primary_dropdown_1" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php if (isset($_SESSION["username"])) {
                                echo '<span style="color: black;">' . $_SESSION["username"] . '</span>';
                        } else {
                            echo "Account";
                        } ?>
                    </a>
                    <?php
                                if (isset($_SESSION["uuid"])) {
                                    echo '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbar-primary_dropdown_1">
                                    <a class="dropdown-item" href="' . $path . 'accounts/dashboard">Dashboard</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="' . $path . 'accounts/leave">Sign Out</a>
                                  
                                </div>';
                                } else {
                                    echo '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbar-primary_dropdown_1">
                                    <a class="dropdown-item" href="' . $path . 'accounts/access">Access Account</a>
                                    <a class="dropdown-item" href="'.  $path . 'accounts/create">Create Account</a>
                                </div>';
                                }



                        ?>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End of Navigation -->
<?php 

