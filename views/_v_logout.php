<form class="navbar-form navbar-right" method="POST" action="/users/logout">
    <div class="form-group">
        <a href="/users/profile"><?php echo "Welcome:&nbsp" . $user->first_name; ?></a></li>
    </div>
    <div class="form-group">
        &nbsp;&nbsp;
    </div>
    <button type="submit" class="btn btn-success">Log out</button>
</form>
