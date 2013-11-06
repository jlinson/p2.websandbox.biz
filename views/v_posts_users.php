<div class="container">
    <h2 class="h2">Select Users to Follow or Unfollow:</h2>
    <table class="table">
        <tr>
            <th>Handle</th>
            <th>User Name</th>
            <th>Select</th>
            <th>Follow Since</th>
            <th>Not Followed Since</th>
        </tr>
        <?php foreach($users as $post_user): ?>
            <tr>
                <td>
                    (<?php echo $post_user['handle']; ?>)
                </td>
                <td>
                    <!-- Print this user's name -->
                    <?php echo $post_user['first_name']; ?> <?php echo $post_user['last_name']; ?>
                </td>
                <td>
                    <!-- Since follow connections are not deleted, use the 'created' and 'dropped' dates to flag status -->
                    <?php if ($post_user['created'] > $post_user['dropped']): ?>
                        <!-- covers both followed and re-followed cases -->
                        <a href='/posts/unfollow/<?php echo $post_user['user_id']; ?>'>Unfollow</a>
                        </td><td>
                        <?php echo Time::display($post_user['created'], '', $user->timezone); ?>
                    <?php elseif ($post_user['created'] < $post_user['dropped']): ?>
                        <!-- dropped must be > 0 - must have been unfollowed -->
                        <a href='/posts/refollow/<?php echo $post_user['user_id']; ?>'>Re-Follow</a>
                        </td><td>
                        <?php echo Time::display($post_user['dropped'], '', $user->timezone); ?>
                    <?php else: ?>
                        <!-- Otherwise, show the follow link - 'created' must be NULL -->
                        <a href='/posts/follow/<?php echo $post_user['user_id']; ?>'>Follow</a>
                        </td><td>
                    <?php endif; ?>
                </td>
            </tr>
        <br>

    <?php endforeach; ?>

    </table>

</div> <!-- /container -->
