<div class="container">
    <?php foreach($users as $user): ?>
        <br>

        <!-- Print this user's name -->
        <?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?>

        <!-- Since follow connections are not deleted, use the 'created' and 'dropped' dates to flag status -->
        <?php if ($user['created'] > $user['dropped']): ?>
            <!-- covers both followed and re-followed cases -->
            <a href='/posts/unfollow/<?php echo $user['user_id']; ?>'>Unfollow</a>
            Followed since: <?php echo Time::display($user['created'], '', $usertimezone); ?>
        <?php elseif ($user['created'] < $user['dropped']): ?>
            <!-- dropped must be > 0 - must have been unfollowed -->
            <a href='/posts/refollow/<?php echo $user['user_id']; ?>'>Re-Follow</a>
            Not Followed since: <?php echo Time::display($user['dropped'], '', $usertimezone); ?>
        <?php else: ?>
            <!-- Otherwise, show the follow link - 'created' must be NULL -->
            <a href='/posts/follow/<?php echo $user['user_id']; ?>'>Follow</a>
        <?php endif; ?>

        <br>

    <?php endforeach; ?>

</div> <!-- /container -->
