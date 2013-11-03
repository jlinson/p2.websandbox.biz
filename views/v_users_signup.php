<div class="container">
    <p>
    &nbsp;
    </p>
    <form class="form-signin" method="POST" action="/users/p_signup">
        <h2 class="form-signin-heading"><?php if(isset($user_msg)) echo $user_msg; ?></h2>
        <input type="text" class="form-control" placeholder="First name" name="first_name" required autofocus>
        <input type="text" class="form-control" placeholder="Last name" name="last_name" required>
        <input type="text" class="form-control" placeholder="Email address" name="email" required>
        <input type="password" class="form-control" placeholder="Password" name="password" required>
        <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirm" required>
        <label class="checkbox">
            <input type="checkbox" name="remember_me" value="TRUE"> Remember me
        </label>
        <input type='hidden' name='timezone'>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign-up</button>
    </form>

</div> <!-- /container -->
