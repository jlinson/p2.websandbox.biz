<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
    <div class="container">
        <h1>Features of <?php echo APP_NAME; ?></h1>
        <p>This page outlines the features included (and excluded) in this framework-based micro-blog web app. The
           requirements of DWA E-15 Project 2 can be found <a href="http://dwa15.com/Projects/P2">here</a>.</p>
        <?php if(!$user): ?>
            <p><a class="btn btn-primary btn-lg" href="/users/signup">Sign Up &raquo;</a></p>
        <?php endif; ?>
    </div>
</div> <!-- /container - jumbotron -->

<div class="container">
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h2>Required Features</h2>
            <p><ol>
                <li>Sign up</li>
                <li>Log in</li>
                <li>Log out</li>
                <li>Add posts</li>
                <li>See a list of users</li>
                <li>Follow / Unfollow users</li>
                <li>View posts of followed users</li>
            </ol></p>
            <p><a class="btn btn-default" href="http://dwa15.com/Projects/P2">View details &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h2>+1 Features</h2>
            <p><ol>
                <li>Delete a Post (delete contents in Edit mode)</li>
                <li>Edit a Post (with user feedback messages)</li>
                <li>Display Profile information<br>(added post count via MySQL trigger)</li>
                <li>Re-wrote "Follow/Unfollow" SQL into single select.</li>
                <li>Added Re-follow option (users_users are made inactive instead of being deleted)</li>
                <li>Javascript timezone capture on signup</li>
                <li>Incorporated Twitter Bootstrap</li>
            </ol></p>
            <!-- <p><a class="btn btn-default" href="#">View details &raquo;</a></p> -->
        </div>
        <div class="col-lg-4">
            <h2>Not This Time<br>(Out of Time)</h2>
            <p><ol>
                <li>Upload profile photo</li>
                <li>Edit profile / change timezone</li>
                <li>Reset password w/ email confirm</li>
                <li>Email confirm on signup<br>(or email verification requirement)</li>
                <li>"Like" feature</li>
                <li>Javascript client-side form validation</li>
            </ol></p>
            <!-- <p><a class="btn btn-default" href="#">View details &raquo;</a></p> -->
        </div>
    </div>

</div> <!-- /container -->
