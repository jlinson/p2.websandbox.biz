<div class="container">

    <?php foreach($users as $post_user): ?>
        <br>

        <!-- Print this user's name -->
        <?php echo $post_user['first_name']; ?> <?php echo $post_user['last_name']; ?> (<?php echo $post_user['handle']; ?>)

        <!-- Since follow connections are not deleted, use the 'created' and 'dropped' dates to flag status -->
        <?php if ($post_user['created'] > $post_user['dropped']): ?>
            <!-- covers both followed and re-followed cases -->
            <a href='/posts/unfollow/<?php echo $post_user['user_id']; ?>'>Unfollow</a>
            Followed since: <?php echo Time::display($post_user['created'], '', $user->timezone); ?>
        <?php elseif ($post_user['created'] < $post_user['dropped']): ?>
            <!-- dropped must be > 0 - must have been unfollowed -->
            <a href='/posts/refollow/<?php echo $post_user['user_id']; ?>'>Re-Follow</a>
            Not Followed since: <?php echo Time::display($post_user['dropped'], '', $user->timezone); ?>
        <?php else: ?>
            <!-- Otherwise, show the follow link - 'created' must be NULL -->
            <a href='/posts/follow/<?php echo $post_user['user_id']; ?>'>Follow</a>
        <?php endif; ?>

        <br>

    <?php endforeach; ?>

</div> <!-- /container -->
