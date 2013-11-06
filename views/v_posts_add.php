<div class="container">
    <p>
        &nbsp;
    </p>
    <form class="form-signin" method="POST" action="/posts/p_add">
        <h2 class="form-signin-heading">Post a New Nut:</h2>
        <div class = "form-signin-msg">
            <?php if(isset($user_msg)) echo $user_msg; ?>
        </div>
            <label for='content'>(Posts can be a maximum of 255 characters - HTML tags are accepted.)</label><br>
        <!--
          -->
        <textarea class="posts" name='content' id='content' placeholder="Enter your post here." maxlength="255" autofocus></textarea>

        <br><br>
        <button class="btn btn-lg btn-primary" type="submit">Save Nut</button>
        <p>
      </p>

    </form>

</div> <!-- /container -->
