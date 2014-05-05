<div class="row">
    <div class="col-md-12">

        <div class="row">
            <div class="col-md-2">

                <h3>Pusher</h3>

            </div>
            <!-- /.col-md-2 -->

            <div class="col-md-10">

                <div class="form-group">
                    <label>Enabled?</label>
                    <select name="pusher[enabled]" class="form-control">
                        <option value="0" <?php if ($flash->old('pusher.enabled') == '0') { echo "selected='selected'"; } ?>>No</option>
                        <option value="1" <?php if ($flash->old('pusher.enabled') == '1') { echo "selected='selected'"; } ?>>Yes</option>
                    </select>
                
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label>App ID</label>
                    <input type="text" name="pusher[app_id]" placeholder="App ID" value="<?php echo $flash->old('pusher.app_id'); ?>" class="form-control" />
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label>App Secret</label>
                    <input type="text" name="pusher[app_secret]" placeholder="App Secret" value="<?php echo $flash->old('pusher.app_secret'); ?>" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Channel</label>
                    <input type="text" name="pusher[app_secret]" placeholder="App Secret" value="<?php echo $flash->old('pusher.channel'); ?>" class="form-control" />
                </div>
                <!-- /.form-group -->

            </div>
            <!-- /.col-md-10 -->

        </div>
        <!-- /.row -->      

    </div>
</div>
