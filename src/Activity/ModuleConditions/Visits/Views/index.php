<div id="ruleset-activity-visits" class="ruleset row">
    
    <div class="col-md-2">
        
        <h3>Visits</h3>
        <p class="help-block">Show/Hide this module based on visit history.</p>
                            
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
        <div class="row">
            <div class="col-md-3">
                <select name="assignment[activity-visits][method]" class="form-control ruleset-switcher">
                    <option value="ignore" <?php if ($flash->old('assignment.activity-visits.method') == "ignore") { echo "selected='selected'"; } ?>>Ignore this condition</option>
                    <option value="include" <?php if ($flash->old('assignment.activity-visits.method') == "include") { echo "selected='selected'"; } ?>>Enabled</option>
                    <?php /* ?><option value="exclude" <?php if ($flash->old('assignment.activity-visits.method') == "exclude") { echo "selected='selected'"; } ?>>Exclude</option> */ ?>
                </select>                
            </div>
            <div class="col-md-9">
            
                <div class="ruleset-options">                
                    <div class="ruleset-enabled <?php if (!in_array($flash->old('assignment.activity-visits.method'), array( "include", "exclude" ) ) ) { echo "hidden"; } ?>">
                        
                        <select name="assignment[activity-visits][has_visited]" class="form-control">
                            <option value="0" <?php if ($flash->old('assignment.activity-visits.has_visited') == "0") { echo "selected='selected'"; } ?>>First-time visitor</option>
                            <option value="1" <?php if ($flash->old('assignment.activity-visits.has_visited') == "1") { echo "selected='selected'"; } ?>>Has visited before</option>
                        </select>
                    </div>                        
                    <div class="text-muted ruleset-disabled <?php if (in_array($flash->old('assignment.activity-visits.method'), array( "include", "exclude" ) ) ) { echo "hidden"; } ?>">
                        This condition is ignored.
                    </div>                                  
                </div>              
                  
            </div>    
        </div>
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->