<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
    <div class="container">
        <h1>Welcome to <?php echo APP_NAME; ?></h1>
        <p>This app demonstrates the use of a framework for developing interactive web apps as part of Harvard Extension
           Schools Dynamic Web Applications (DWA E-15) class. Any similarity between this app and some commercial
           application is purely coincidental.  View <strong>'Features'</strong> to <strong>Learn More</strong> about
           the features required for Project P2.
        </p>
        <?php if(!$user): ?>
            <p><a class="btn btn-primary btn-lg" href="/users/signup">Sign Up &raquo;</a></p>
        <?php else: ?>
            <p><a class="btn btn-primary btn-lg" href="/index/features">Learn More &raquo;</a></p>
        <?php endif; ?>
    </div>
</div> <!-- /container - jumbotron -->

<div class="container">
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h2>Store Your Nuts</h2>
            <p>
                Like any good squirrel, your should store your nuts of wisdom. You can save all your favorite quotes,
                activities and events right here. You can even share what you had for breakfast.  Just sign-up and
                start posting. (I mean, start storing; winter's coming.)
            </p>
            <!-- <p><a class="btn btn-default" href="#">View details &raquo;</a></p> -->
        </div>
        <div class="col-lg-4">
            <h2>Follow Other Nuts</h2>
            <p>
                And of course you want to be social, so we allow you to follow other nuts and see all the
                important information everyone has to share on the oh-so-social Internets.
            </p>
            <!-- <p><a class="btn btn-default" href="#">View details &raquo;</a></p> -->
        </div>
        <div class="col-lg-4">
            <h2>Show Us Your Nuts</h2>
            <?php if(!$user): ?>
                <p>So come on, what are you waiting for. Come show us your nuts. Just click the button below to signup.
                And thanks for sharing.</p>
                <p><a class="btn btn-primary btn-lg" href="/users/signup">Sign Up &raquo;</a></p>
            <?php else: ?>
                <p>Well, I see you're already logged-in. Great! Now you need to get posting to show us your nuts.
                    Just click the button below and get posting. And thanks for sharing.</p>
                <p><a class="btn btn-primary btn-lg" href="/index/add">Post Nuts &raquo;</a></p>
            <?php endif; ?>
        </div>
    </div>

</div> <!-- /container -->
