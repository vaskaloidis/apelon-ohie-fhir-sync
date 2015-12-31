<?php global $logged_in, $apelon_user, $url; ?>
<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Apelon Terminology Asset Management</a>
        </div>
        <center>
            <div class="navbar-collapse collapse" id="navbar-main">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="<?= $url ?>">Home</a>
                    </li>
                    <li><a href="#rmap">Resource Map Sync</a>
                    </li>
                    <li><a href="#ihris">iHRIS Sync</a>
                    </li>
                    <li><a href="<?= $ihris_site_url ?>">iHRIS Site</a>
                    </li>
                    <li><a href="<?= $rmap_site_url ?>">Resource Map Site</a>
                    </li>
                </ul>
                <form action ="login.php" method="POST" class="navbar-form navbar-right" role="search">
                    <input type="hidden" name="login" value="login" />
                    <?php if($logged_in) { ?>
                        <div class="form-group">
                            <input type="text" class="form-control" name="username" placeholder="Username">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="password" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-default">Sign In</button>
                    <?php } else { ?>
                        <div>Hello, <b><?= $apelon_user ?></b></div>
                    <?php } ?>
                </form>
            </div>
        </center>
    </div>
</div>