<div class="container">
    <p>
        &nbsp;
    </p>
    <form class="form-signin" method="POST" action="/posts/p_add">
        <h2 class="form-signin-heading">Post a New Nut:</h2>
        <div class = "form-signin-msg">
            <?php if(isset($user_msg)) echo $user_msg; ?>
        </div>
        <label for='content'>New Post:</label><br>
        <!--
        <input type="text" class="form-control" placeholder="Email address" name="email" required autofocus>
        <input type="password" class="form-control" placeholder="Password" name="password" required>
        -->
        <textarea name='content' id='content'></textarea>

        <br><br>
        <input type='submit' value='New post'>

        <button class="btn btn-lg btn-primary" type="submit">Save Nut</button>
        <!--
        <p><a class="btn btn-primary btn-lg" href="/users/signup">Sign Up &raquo;</a></p>
        -->
    </form>

</div> <!-- /container -->
