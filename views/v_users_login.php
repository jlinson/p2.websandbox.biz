<div class="container">
    <p>
        &nbsp;
    </p>
    <form class="form-signin" method="POST" action="/users/p_login">
        <h2 class="form-signin-heading">Please Login</h2>
        <div class = "form-signin-msg">
            <?php if(isset($user_msg)) echo $user_msg; ?>
        </div>
        <input type="text" class="form-control" placeholder="Email address" name="email" required autofocus>
        <input type="password" class="form-control" placeholder="Password" name="password" required>
        <label class="checkbox">
            <input type="checkbox" value="remember-me" name="remember_me"> Remember me
        </label>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
    </form>

</div> <!-- /container -->
