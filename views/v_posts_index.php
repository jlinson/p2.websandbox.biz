<div class="container">

    <?php if (empty($posts)): ?>
        <h2>No posts found.</h2>
        <p>To view posts, please <a href="/posts/users">select people to follow.</a></p>

    <?php else: ?>

        <?php foreach($posts as $post): ?>

            <article>

                <h2><?php echo $post['first_name']; ?> <?php echo $post['last_name']; ?> posted:</h2>

                <p><?php echo $post['content']; ?></p>

                <time datetime="<?php echo Time::display($post['created'],'Y-m-d G:i', $usertimezone); ?>">
                    <?php echo Time::display($post['created'], '', $usertimezone); ?>
                </time>

            </article>

        <?php endforeach; ?>

    <?php endif; ?>
</div> <!-- /container -->