<div class="container">

    <?php if (empty($posts)): ?>
        <h2>No posts found.</h2>
        <p>To view posts, please <a href="/posts/users">select people to follow.</a></p>

    <?php else: ?>

        <?php foreach($posts as $post): ?>

            <article>
                <?php if ($current_uid != $post['post_user_id']): ?>
                    <h2><?php echo $post['first_name']; ?> <?php echo $post['last_name']; ?> (<?php echo $post['handle']; ?>) posted:</h2>
                    <table>
                <?php endif; ?>
                    <tr>
                        <td><textarea readonly><?php echo $post['content']; ?></textarea></td>
                        <td>&nbsp;</td>
                        <td><time datetime="<?php echo Time::display($post['created'],'Y-m-d G:i', $user->timezone); ?>">
                                  <?php echo Time::display($post['created'], '', $user->timezone); ?>
                        </time></td>
                    </tr>
                <?php if ($current_uid != $post['post_user_id']): ?>
                    </table>
                <?php endif; ?>

                <?php $current_uid = $post['post_user_id']; ?>

            </article>

        <?php endforeach; ?>

    <?php endif; ?>
</div> <!-- /container -->