<div class="container">
    <p>
    &nbsp;
    </p>
    <form class="form-signin" method="POST" action="/users/p_signup">
        
        <h2 class="form-signin-heading">Please Sign-up</h2>
        <div class = "form-signin-msg">
            <?php if(isset($user_msg)) echo $user_msg; ?>
        </div>
        <input type="text" class="form-control" placeholder="First name" name="first_name" required autofocus>
        <input type="text" class="form-control" placeholder="Last name" name="last_name" required>
        <input type="text" class="form-control" placeholder="Handle / User ID" name="handle" required>
        <input type="text" class="form-control" placeholder="Email address" name="email" required>
        <input type="password" class="form-control" placeholder="Password" name="password" required>
        <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirm" required>
        <label class="checkbox">
            <input type="checkbox" name="remember_me" value="TRUE"> Remember me
        </label>
        <input type='hidden' name='timezone'>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign-up</button>
        <br>
        <h4 class="form-signin-msg">Required input:</h4>
        <ul>
            <li><strong>Email address</strong> - must be unique; you cannot signup twice with the same email address.</li>
            <li><strong>Handle or User ID</strong> - must be a min. 6 characters and a max. 25 characters; used to publicly identify your posts.</li>
            <li><strong>Password</strong> - must be a min. 6 characters and a max. 25 characters.</li>
        </ul>

    </form>
</div> <!-- /container -->
