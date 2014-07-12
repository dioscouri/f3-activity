<div class="row">
    <div class="col-md-12">

        <div class="row">
            <div class="col-md-2">


            </div>
            <!-- /.col-md-2 -->

            <div class="col-md-10">

                <div class="form-group">
                    <label>Track activity?</label>
                    <select name="tracking[enabled]" class="form-control">
                        <option value="0" <?php if ($flash->old('tracking.enabled') == '0') { echo "selected='selected'"; } ?>>No</option>
                        <option value="1" <?php if ($flash->old('tracking.enabled') == '1') { echo "selected='selected'"; } ?>>Yes</option>
                    </select>
                
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label>Excluded IP Addresses</label>
                    <input type="text" name="excluded[ips]" placeholder="Excluded IP Addresses" value="<?php echo implode( ",", (array) $flash->old('excluded.ips') ); ?>" class="form-control ui-select2-tags" data-tags="[]" />
                </div>
                <!-- /.form-group -->

            </div>
            <!-- /.col-md-10 -->

        </div>
        <!-- /.row -->

    </div>
</div>
