<?php include_once('layout.php')?>
<?php layout_begin() ?>
<div id="settings_container" class="d-flex justify-content-center align-items-center row">
    <div class="col-12 col-lg-7 card p-4 shadow-lg">
        <h1 class="card-title text-center text-muted">Settings</h1>
        <hr>
        <div class="">
            <div class="form-group mb-3">
                <label for="exampleInputEmail1" class="form-label">IP address</label>
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" disabled>
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>

            <div class="form-group mb-3">
                <label for="exampleInputEmail1" class="form-label">Username</label>
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>

            <div class="d-flex justify-content-end">
                <div class="ml-auto btn btn-primary">UPDATE</div>
            </div>

        </div>
    </div>
</div>
<?php layout_end() ?>