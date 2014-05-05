<div class="well">

<form id="settings-form" role="form" method="post" class="form-horizontal clearfix">

    <div class="clearfix">
        <button type="submit" class="btn btn-primary pull-right">Save Changes</button>
    </div>
    
    <hr/>

    <div class="row">
        <div class="col-md-3 col-sm-4">
            <ul class="nav nav-pills nav-stacked">
                <li class="active">
                    <a href="#tab-general" data-toggle="tab"> General Settings </a>
                </li>            
                <li>
                    <a href="#tab-pusher" data-toggle="tab"> Real Time Settings </a>
                </li>
                <?php if (!empty($this->event)) { foreach ((array) $this->event->getArgument('tabs') as $key => $title ) { ?>
                <li>
                    <a href="#tab-<?php echo $key; ?>" data-toggle="tab"> <?php echo $title; ?> </a>
                </li>
                <?php } } ?>                
            </ul>
        </div>

        <div class="col-md-9 col-sm-8">

            <div class="tab-content stacked-content">
            
                <div class="tab-pane fade in active" id="tab-general">
                
                    <?php echo $this->renderLayout('Activity/Admin/Views::settings/general.php'); ?>

                </div>
            
                <div class="tab-pane fade in" id="tab-pusher">
                    <?php 
                    if (class_exists('Pusher')) 
                    {
                        echo $this->renderLayout('Activity/Admin/Views::settings/pusher.php');
                    } 
                    else 
                    {
                        ?>
                        <div class="alert alert-info">
                            To enable Real Time, install Pusher via composer.
                            <pre> "pusher/pusher-php-server" : "dev-master" </pre>
                        </div>
                        <?php 
                    }
                    ?>
                </div>

                <?php if (!empty($this->event)) { foreach ((array) $this->event->getArgument('content') as $key => $content ) { ?>
                <div class="tab-pane fade in" id="tab-<?php echo $key; ?>">
                    <?php echo $content; ?>
                </div>
                <?php } } ?>

            </div>

        </div>
    </div>

</form>

</div>