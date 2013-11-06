<div class="container" id="list">
    <p>
        <a href="#edit">Edit Window</a>
    </p>
    <div class="jumbotron">
        <div class="container">
            <h2>Posts for <?php echo ($user->first_name . " " . $user->last_name . "  (" . $user->handle . ")"); ?></h2>
            <p>
                Posts created to date: <?php echo $user->posts_count; ?> <br>
                Last post created: <?php echo $user->last_post; ?>
            </p>
        </div>
    </div> <!-- /container - jumbotron -->

    <?php if (empty($posts)): ?>
        <h2>No posts found.</h2>
        <p>To add posts, go to <a href="/posts/users">Post Nuts</a>.</p>

    <?php else: ?>
        <table>
            <thead><tr>
                <th>Post</th>
                <th>&nbsp;</th>
                <th>Created</th>
                <th>&nbsp;</th>
                <th>Modified</th>
                <th>&nbsp;</th>
                <th></th>
                <th></th>
            </tr></thead>
        <?php foreach($posts as $post): ?>
            <tr>
                <td><textarea readonly><?php echo $post['content']; ?></textarea></td>
                <td>&nbsp;</td>
                <td><time datetime="<?php echo Time::display($post['created'], 'Y-m-d G:i', $user->timezone); ?>">
                        <?php echo Time::display($post['created'], '', $user->timezone); ?></time></td>
                <td>&nbsp;</td>
                <td><?php echo Time::display($post['modified'], '', $user->timezone); ?></td>
                <td>&nbsp;</td>
                <td><a class="btn btn-primary btn-sm" href="/posts/edit/<?php echo $post['post_id']; ?>">Edit &raquo;</a></td>
                <td hidden><?php echo $post['post_id']; ?></td>
            </tr>
            <?php if ($post['post_id'] == $content_id) $content = $post['content']; ?>
        <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div> <!-- /container -->
<div class="container" id="edit">
    <p>
        &nbsp;
    </p>
    <form class="form-signin" method="POST" action="/posts/p_edit">
        <h2 class="form-signin-heading">Edit Your Nut:</h2>
        <div class = "form-signin-msg">
            <?php if(isset($user_msg)) echo $user_msg; ?>
        </div>
            <label for='content'>(Posts can be a maximum of 255 characters - HTML tags are accepted.)</label><br>
        <input type='hidden' name='orig_content' value="<?php echo $content; ?>">
        <input type='hidden' name='content_id' value="<?php echo $content_id; ?>">
        <input type='hidden' name='delete_flg' value="<?php echo $delete_flg; ?>">
        <textarea class="posts" name='content' id='content' placeholder="Enter your post here." maxlength="255" autofocus><?php echo $content; ?></textarea>

        <br><br>
        <?php if ($delete_flg): ?>
            <button class="btn btn-lg btn-primary" type="submit">Delete Post</button>
        <?php else: ?>
            <button class="btn btn-lg btn-primary" type="submit">Save Changes</button>
        <?php endif; ?>
        <p>
      </p>

    </form>
    <p>
        <a href="#list">Back to Top</a>
    </p>
</div> <!-- /container -->
