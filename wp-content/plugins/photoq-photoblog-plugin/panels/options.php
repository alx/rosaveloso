<div class="wrap">
	<form method="post" action="options-general.php?page=whoismanu-photoq.php">
		
		<h2><?php _e('PhotoQ Options', 'PhotoQ'); ?></h2>
		
		<p class="submit top-savebtn">
			<input type="submit" name="info_update" class="button-primary"
				value="<?php _e('Save Changes', 'PhotoQ') ?>" />
		</p>
			
		
				
			<div id="poststuff">
			
			<div  class="postbox ">
			<h3 class="postbox-handle"><span><?php _e('Image sizes', 'PhotoQ') ?></span></h3>
			<div class="inside">
			
			
			<?php $this->_oc->render('imageSizes');?>
			
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
			<tr valign="top">
					<th scope="row">
						<label for="newImageSizeName"><?php _e('Name of new image size', 'PhotoQ'); ?>:</label>
					</th>
					<td>
			<input type="text" name="newImageSizeName" id="newImageSizeName"
					size="20" maxlength="20" value="" />
			<input type="submit" class="button-secondary"
					name="addImageSize"
					value="<?php _e('Add Image Size', 'PhotoQ') ?> &raquo;" />
			
					</td>
			</tr>
			</table>
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
			<tr valign="top">
				<th scope="row"><?php _e('Hide \'original\' folder', 'PhotoQ'); ?>:</th>
				<td>
				<?php 
					$this->_oc->render('originalFolder');
					
					$folderName = get_option('wimpq_originalFolder');
					$folderName = $folderName ? $folderName : 'original';
					echo '<br/>('.__('Current name', 'PhotoQ').': '.$folderName.')';
				?></td>
			</tr>
			</table>
			</div>
			</div>
			
			<div  class="postbox ">
			<h3 class="postbox-handle"><span><?php _e('Views', 'PhotoQ') ?></span></h3>
			<div class="inside">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
				<tr valign="top">
					<th>the_content</th>
					<td></td>
				</tr>
				<?php $this->_oc->render('contentView');?>
			</table>	
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
				<tr valign="top">
					<th>the_excerpt</th>
					<td></td>
				</tr>
				<?php $this->_oc->render('excerptView');?>
			</table>
			</div>
			</div>
			
			
			<div  class="postbox closed">
			<h3 class="postbox-handle"><span><?php _e('Exif', 'PhotoQ') ?></span></h3>
			<div class="inside">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
				
				<?php 
				
				$exifDisplayOptions = array(
					'exifDisplay' => __('Exif Formatting Options:', 'PhotoQ')
				); 
				$this->_oc->showOptionArray($exifDisplayOptions);
				
				?>
				
				<tr valign="top">
					<th scope="row"><?php _e('Choose Exif Tags', 'PhotoQ'); ?>:
						<br/><br/><span class="setting-description"><?php _e('You can select/deselect EXIF tags via drag-and-drop between the two lists.<br/>Within the list of selected tags you can also change the order via drag-and-drop.', 'PhotoQ') ?></span>
					</th>
					<td>
						<?php 
							if(!get_option( "wimpq_exif_tags" )) 
								_e('No tags yet. PhotoQ will learn exif tags from uploaded photos. Upload a photo first, then come back and choose your exif tags here.', 'PhotoQ');
							else
								$this->_oc->render('exifTags');
						?>
					</td>
				</tr>
			</table>
			</div>
			</div>
			
			
			<div  class="postbox closed">
			<h3 class="postbox-handle"><span><?php _e('Watermarking', 'PhotoQ') ?></span></h3>
			<div class="inside">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
			<tr valign="top">
					<th scope="row"><?php _e('Watermark Image', 'PhotoQ') ?>:</th>
					<td>
					<?php $this->showCurrentWatermark(); ?>
			
			<input style="vertical-align: top;" type="submit" class="button-secondary"
					name="showWatermarkUploadPanel"
					value="<?php _e('Change Watermark', 'PhotoQ') ?> &raquo;" />
					</td>
			</tr>
			
			<?php $this->_oc->render('watermarkOptions');?>
			
			</table>
			</div>
			</div>
			
			
			
			<div  class="postbox closed">
			<h3 class="postbox-handle"><span><?php _e('Meta Fields', 'PhotoQ') ?></span></h3>
			<div class="inside">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
				<?php 
				
				$metaFieldOptions = array(
					'fieldAddPosted' => __('Upon Add:', 'PhotoQ'),
					'fieldDeletePosted' => __('Upon Delete:', 'PhotoQ'),
					'fieldRenamePosted' => __('Upon Rename:', 'PhotoQ')
				); 
				
				
				$this->_oc->showOptionArray($metaFieldOptions);
				
				
				?>
				
				<tr valign="top">
					<th><?php _e('Defined Fields:', 'PhotoQ'); ?></th>
					<td>
						<table width="200" cellspacing="2" cellpadding="5"
							class="meta_fields noborder">

							<?php
								$this->showMetaFields();				
							?>
				
						</table>
					</td>
				</tr>	
			</table>
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
				<tr valign="top">
					<th scope="row">
						<label for="newFieldName"><?php _e('Name of new field', 'PhotoQ'); ?>:</label>
					</th>
					<td>
						<input type="text" name="newFieldName" id="newFieldName"
								size="20" maxlength="20" value="" />
						<input type="submit" class="button-secondary"
								name="addField"
								value="<?php _e('Add Meta Field', 'PhotoQ') ?> &raquo;" />
					</td>
				</tr>
			</table>
			</div>
			</div>
		
			<div  class="postbox closed">
			<h3 class="postbox-handle"><span><?php _e('Further Options', 'PhotoQ') ?></span></h3>
			<div class="inside">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
				
				<?php 
				
				$furtherOptions = array(
					'imgdir' => __('Image Directory:', 'PhotoQ'),
					'imagemagickPath' => __('ImageMagick Path:', 'PhotoQ'),
					'cronJobs' => __('Automatic Posting:', 'PhotoQ'),
					'qPostStatus' => __('PhotoQ Post Status:', 'PhotoQ'),
					'qPostDefaultCat' => __('PhotoQ Default Category:', 'PhotoQ'),
					'qPostAuthor' => __('PhotoQ Default Author:', 'PhotoQ'),
					'specialCaps' => __('Roles/Capabilities:', 'PhotoQ'),
					'foldCats' => __('Fold Categories:', 'PhotoQ'),
					'showThumbs' => __('Admin Thumbs:', 'PhotoQ'),
					'autoTitles' => __('Auto Titles:', 'PhotoQ'),
					'enableFtpUploads' => __('FTP Upload:', 'PhotoQ'),
					'postMulti' => __('Second Post Button:', 'PhotoQ'),
					'deleteImgs' => __('Deleting Posts:', 'PhotoQ'),
					'enableBatchUploads' => __('Batch Uploads:', 'PhotoQ')
				); 
				
				
				$this->_oc->showOptionArray($furtherOptions);
				
				
				?>
				
			</table>
			</div>
			</div>
			
			<div  class="postbox closed">
			<h3 class="postbox-handle"><span><?php _e('Maintenance', 'PhotoQ') ?></span></h3>
			<div class="inside">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
				
				<?php if(false): //eventually move this out completely?>
				<tr valign="top">
					<th scope="row"><?php _e('PhotoQ < 1.5.2:','PhotoQ') ?></th>
					<td><label for="oldImgDir">Old Image Directory: </label>
					<input type="text" name="oldImgDir" id="oldImgDir"
					size="20" maxlength="20" value="" />
					<input style="vertical-align: top;" type="submit" class="button-secondary"
					name="showMoveImgDirPanel"
					value="<?php _e('Move ImgDir to wp-content', 'PhotoQ') ?> &raquo;" /></td>
				</tr>
				<?php endif; ?>
				
				<tr valign="top">
					<th scope="row"><?php _e('Upgrade:','PhotoQ') ?></th>
					<td><input style="vertical-align: top;" type="submit" class="button-secondary"
					name="showUpgradePanel"
					value="<?php _e('Upgrade from PhotoQ < 1.5', 'PhotoQ') ?> &raquo;" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Rebuild Published:','PhotoQ') ?></th>
					<td><input style="vertical-align: top;" type="submit" class="button-secondary"
					name="rebuildAll"
					value="<?php _e('Rebuild All Published Photos', 'PhotoQ') ?>" 
					onclick="return confirm(
						'<?php _e('Are you sure? This will rebuild all published photos recreating all the thumbs. It might thus take a while.', 'PhotoQ'); ?>');"/>
					</td>
				</tr>
				
			</table>
			</div>
			</div>
		
		
		<?php 
			if ( function_exists('wp_nonce_field') )
					wp_nonce_field('photoq-updateOptions','photoqUpdateOptionsNonce');
		?>
		
		<p class="submit">
			<input type="submit" name="info_update" class="button-primary"
				value="<?php _e('Save Changes', 'PhotoQ') ?>" />
		</p>
		</div>
	</form>
</div> 