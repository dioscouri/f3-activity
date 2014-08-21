var Activity = {
        trackClicks : function() {
            jQuery('.activity-track').on('click', function(){
                var el = jQuery(this);
                var action = el.attr('data-activity-action');
                var properties = el.attr('data-activity-properties');
                
                if (action) {
                    var form_data = [{
                        name: "action",
                        value: action
                    }, {
                        name: "properties",
                        value: properties
                    }];
                    
                    var request = jQuery.ajax({
                        type: 'post', 
                        url: './activity/track',
                        data: form_data
                    });
                }
            });
        }
};

jQuery(document).ready(function() {
    Activity.trackClicks();
});