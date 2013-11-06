<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
    <div class="container">
        <h2>Profile for <?php echo $user->first_name . " " . $user->last_name; ?></h2>
        <aside>Handle:&nbsp;&nbsp; <?php echo $user->handle; ?></aside>
        <p>
            Created:&nbsp;&nbsp; <?php echo Time::display($user->created, '', $user->timezone); ?>
        </p>
    </div>
</div> <!-- /container - jumbotron -->

<div class="container">
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h2>Contact Information</h2>
            <p>
                Email:&nbsp;&nbsp; <?php echo $user->email; ?>
            </p>
            <!-- <p><a class="btn btn-default" href="#">View details &raquo;</a></p> -->
        </div>
        <div class="col-lg-4">
            <h2>Location</h2>
            <p>
                Timezone:&nbsp;&nbsp; <?php echo $user->timezone; ?>
            </p>
            <!-- <p><a class="btn btn-default" href="#">View details &raquo;</a></p> -->
        </div>
        <div class="col-lg-4">
            <h2>Activity</h2>
            <p>
                Posts: &nbsp;&nbsp; <?php echo $user->posts_count; ?><br>
                Last post: &nbsp; <?php echo $user->last_post; ?><br>
            </p><p>
                <a class="btn btn-default" href="/posts/index">View  &raquo;</a>
            </p>
            <br>
            <p>
                Last login:&nbsp;  <?php echo Time::display($user->last_login, '', $user->timezone); ?>
            </p>
        </div>
    </div>
    <label for='content'>Modified:&nbsp;&nbsp; <?php echo Time::display($user->modified, '', $user->timezone); ?></label>
</div> <!-- /container -->