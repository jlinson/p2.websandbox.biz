<div class="container">
    <p>
        &nbsp;
    </p>
    <form class="form-signin" method="POST" action="/users/p_login">
        <h2 class="form-signin-heading"><?php if(isset($user_msg)) echo $user_msg; ?></h2>
        <input type="text" class="form-control" placeholder="Email address" required autofocus>
        <input type="password" class="form-control" placeholder="Password" required>
        <label class="checkbox">
            <input type="checkbox" value="remember-me"> Remember me
        </label>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
    </form>

</div> <!-- /container -->
