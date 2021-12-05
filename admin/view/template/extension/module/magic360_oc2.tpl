<?php

if (isset($_GET['magic'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

$old_header = $header;
$header = str_replace('</head>',$style.$script.'</head>',$header);
if ($old_header == $header) {
    $header = str_replace('<div id="container">',$style.$script.'<div id="container">',$header);
}

echo $header; ?>

<?php echo $column_left; ?>

<div id="content">
<form action="<?php echo $action; ?>" method="post" id="form-magic360" name="form-magic360" class="form-horizontal">
    <div class="page-header">
	<div class="container-fluid">
	    <div class="pull-right">
		<button type="submit" form="form-magic360" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
		<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
	    </div>
	    <h1><?php echo $heading_title; ?></h1>
	    <ul class="breadcrumb">
	      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
	      <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
	      <?php } ?>
	    </ul>
	</div>
    </div>

<div class="container-fluid">

<?php 

if ($refresh_modifications) { 
   echo  '<iframe style="display:none;" src="'.HTTPS_SERVER.'index.php?route=extension/modification/refresh&'.$token_url.'" width="0" height="0"></iframe>';
}

if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    
<?php 
if (preg_match("/components\/com_(ayelshop|aceshop|mijoshop)\/opencart\//ims",DIR_APPLICATION)) {
    print $style;
}
?>
<div class="content">

  <?php if ($trial) echo $trial_bage;  ?>
  
  <ul class="nav nav-tabs" <?php if (count($blocks) < 2) echo ' style="display:none;"'?>>
	<li class="active" >
	    <a href="#tab-general" data-toggle="tab">General</a>
	</li>
	
	<?php foreach ($blocks as $block_id => $block_name) { 
	if ($block_id == 'default') continue;
	?>
	<li>
	    <a href="#tab-<?php echo $block_id; ?>" data-toggle="tab"><?php echo $block_name; ?></a>
	</li>
	<?php } ?>
	
    </ul>
    
    <div class="tab-content">
    
	<div class="tab-pane  active" id="tab-general">
  
	    <div class="panel panel-default">
		<div class="panel-heading">
		    <h3 class="panel-title"><i class="fa fa-pencil"></i>Module status</h3>
		</div>
	    
		<div class="panel-body">
		
		    <div class="form-group">
			<label class="col-sm-2 control-label" for="magic360settingStatus">Enable/Disable module</label>
			<div class="col-sm-10">
			  <select name="magic360_status" class="form-control">
				<option value="1" <?php echo (($magic360_status) ? 'selected' : ''); ?> ><?php echo $text_enabled; ?></option>
				<option value="0" <?php echo ((!$magic360_status) ? 'selected' : ''); ?> ><?php echo $text_disabled; ?></option>
			  </select>
			  <input type="hidden" name="magic360_status_was" value="<?php echo $magic360_status; ?>">
			</div>
		    </div>
		    
		</div>
	    </div>
	    
	    <?php echo $groupsHTML['general']; ?>
	    
	    <div class="panel panel-default">
		    <div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i>Other</h3>
		    </div>
		    
		    <div class="panel-body">
				<div class="form-group">
				<label class="col-sm-2 control-label" for="magic360settingChache-path">Path to cache folder</label>
				<div class="col-sm-10">
						<input class="form-control" type="text" size="60" value="<?php echo $path_to_cache; ?>" disabled="disabled" />
						<p class="help-block">Relative for site base path</p>
				</div>
				</div>
				<div class="form-group">
				<label class="col-sm-2 control-label" for="magic360settingTotal-items">Total items</label>
				<div class="col-sm-10">
						<input class="form-control" type="text" size="60" value="<?php echo $total_items; ?>" disabled="disabled" />
				</div>
				</div>
				<div class="form-group">
				<label class="col-sm-2 control-label" for="magic360settingUnuser-items">Unused items</label>
				<div class="col-sm-10">
						<input class="form-control" type="text" size="60" value="<?php echo $unused_items; ?>" disabled="disabled" />
				</div>
				</div>
				<div class="form-group">
				<label class="col-sm-2 control-label" for="magic360settingEmpte-chache">Empty cache</label>
				<div class="col-sm-10">
						<select class="form-control" name="what-clear">
						<option value="unused_items" selected="selected">Delete unused items</option>
						<option value="all_items">Delete all items</option>
						</select>&nbsp;
						<input type="hidden" id="clear_cache" name="clear_cache" value="0" />
					</div>
				</div>
				<div class="form-group">
				<div class="col-sm-2"></div>
					<div class=" col-sm-10">
						<a class="btn btn-primary" onclick="$('#clear_cache').val(1);$(this).closest('form').submit();" class="button"><span><?php echo $button_clear; ?></span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	
	
	<?php 
        foreach ($blocks as $block_id => $block_name) { 

            if ($block_id == 'default') continue;

            if ($block_id == 'module') { ?>
	            <div class="tab-pane panel panel-default" id="tab-module">
	            <div class="panel-heading" style="padding: 12px 15px 12px">
	                <h3 class="panel-title" style="line-height: 36px;"><i class="fa fa-pencil"></i>Slideshow boxes</h3>
	                <div class="pull-right">
	                    <a href="<?php echo $new_module; ?>" target="blank" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="Add slideshow box" aria-describedby="tooltip57507"><i class="fa fa-plus-circle"></i> Add slideshow box</a>
	                </div>
	            </div>
	                
	                <div class="table-responsive">
	                    <table class="table table-bordered table-hover">
	                        <thead>
	                            <tr>
	                            <td class="text-left">Name</td>
	                            <td class="text-left">Status</td>
	                            <td class="text-right">Action</td>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        
	                            <?php if ($slideshow_modules) {
	                                foreach ($slideshow_modules as $extension) { ?>
	                                    <tr>
	                                        <td><b><?php echo $extension['name']; ?></b></td>
	                                        <td><?php echo $extension['status']; ?></td>
	                                        <td class="text-right">
	                                            <a href="<?php echo $extension['edit']; ?>" target="blank" data-toggle="tooltip" title="Edit" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
	                                            <a href="<?php echo $extension['delete']; ?>" data-toggle="tooltip" title="Delete" class="btn btn-warning"><i class="fa fa-trash-o"></i></a>
	                                        </td>
	                                    </tr>
	                                <?php } ?>
	                            <?php } ?>
	                        </tbody>
	                    </table>
	                </div>
	            </div>
	        <?php } else { 
            echo $groupsHTML[$block_id];
        	}
        } ?>
	  
	</div>

      </div>
    </form>
  </div>
</div>
</div>
<?php echo $footer; ?>

 

