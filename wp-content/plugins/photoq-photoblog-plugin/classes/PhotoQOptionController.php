<?php

/**
 * Option controller subclass responsible for hanlding options of the PhotoQ plugin.
 * @author: M. Flury
 * @package: PhotoQ
 *
 */
class PhotoQOptionController extends OptionController
{
	var $ORIGINAL_IDENTIFIER = 'original';
	var $THUMB_IDENTIFIER = 'thumbnail';
	var $MAIN_IDENTIFIER = 'main';
	
	/**
	 * Reference to ErrorStack singleton
	 * @var object PEAR_ErrorStack
	 */
	var $_errStack;
		
	/**
	 * PHP5 type constructor
	 *
	 * @access public
	 */
	function __construct()
	{
		parent::__construct("wimpq_options", new PhotoQRenderOptionVisitor());
		
		//get the PhotoQ error stack for easy access and set it up properly
		$this->_errStack = &PEAR_ErrorStack::singleton('PhotoQ');
		
		//get alternative original identifier if available
		$originalID = get_option( "wimpq_originalFolder" );
		if($originalID)
			$this->ORIGINAL_IDENTIFIER = $originalID;
			
		
		//establish default options
		$this->_defineAndRegisterOptions();
		
		//localize strings in js scripts etc. of option controller
		$this->localizeStrings(array(
				"switchLinkLabel" => __('Switch Sides', 'PhotoQ')
			)
		);
		
	}
	
	
	
	/**
	 * Defines all the plugin options and registers them with the OptionController.
	 *
	 * @access private
	 */
	function _defineAndRegisterOptions()
	{
		
		//define general tests not associated to options but that should be passed
		$this->addTest(new RO_SafeModeOffInputTest());
		$this->addTest(new RO_GDAvailableInputTest());
		$this->addTest(new RO_WordPressVersionInputTest('2.7','2.9'));
		
		//exif related settings
		//first the reorderable list of discovered exif tags
		$exifTags =& new RO_ReorderableList('exifTags');
		if($tags = get_option( "wimpq_exif_tags" )){
			foreach($tags as $key => $value){
				$exifTags->addChild(new PhotoQExifTagOption($key, $value));
			}
		}
		//localize strings
		$exifTags->localizeStrings(array(
				"selectedListLabel" => __('selected', 'PhotoQ'),
				"deselectedListLabel" => __('deselected', 'PhotoQ')
			)
		);
		$this->registerOption($exifTags);
		
		//now the exif display options
		$exifDisplayOptions =& new CompositeOption('exifDisplay');
		$exifDisplayOptions->addChild(
			new TextFieldOption(
				'exifBefore',
				attribute_escape('<ul class="photoQExifInfo">'),
				'',
				'<table class="optionTable"><tr><td>'. __('Before List','PhotoQ'). ': </td><td>',
				sprintf(__('Default is %s','PhotoQ'), '<code>'.attribute_escape('<ul class="photoQExifInfo">').'</code>') .'</td></tr>',
				'30'
			)
		);
		$exifDisplayOptions->addChild(
			new TextFieldOption(
				'exifAfter',
				attribute_escape('</ul>'),
				'',
				'<tr><td>'. __('After List','PhotoQ'). ': </td><td>',
				sprintf(__('Default is %s','PhotoQ'), '<code>'.attribute_escape('</ul>').'</code>') .'</td></tr>',
				'30'
			)
		);
		$exifDisplayOptions->addChild(
			new TextFieldOption(
				'exifElementBetween',
				'',
				'',
				'<tr><td>'. __('Between Elements','PhotoQ'). ': </td><td>',
				'</td></tr>',
				'30'
			)
		);
		$exifDisplayOptions->addChild(
			new TextAreaOption(
				'exifElementFormatting',
				attribute_escape('<li class="photoQExifInfoItem"><span class="photoQExifTag">[key]:</span> <span class="photoQExifValue">[value]</span></li>'),
				'',
				'<tr><td>'. __('Element Formatting','PhotoQ'). ': </td><td>
				<span class="setting-description">'
				.sprintf(__('You can specify the HTML that should be printed for each element here. Two shortags %1$s and %2$s are available. %1$s is replaced with the name of the EXIF tag, %2$s with its value. Here is an example, showing the default value: %3$s', 'PhotoQ'),'[key]','[value]','<code>'.attribute_escape('<li class="photoQExifInfoItem"><span class="photoQExifTag">[key]:</span> <span class="photoQExifValue">[value]</span></li>').'</code>').'
				</span></td></tr><tr><td/><td>',
				'</td></tr></table>',
				2, 75
			)
		);
		$this->registerOption($exifDisplayOptions);
		
		//watermark options
		$watermark =& new CompositeOption('watermarkOptions');
		$watermarkPosition =& new RadioButtonList(
				'watermarkPosition',
				'BL',
				'',
				'<tr valign="top"><th scope="row">'. __('Position','PhotoQ'). ': </th><td>',
				'</td></tr>'
		);
		$valueLabelArray = array(
			'BR' => __('Bottom Right','PhotoQ'),
			'BL' => __('Bottom Left','PhotoQ'),
			'TR' => __('Top Right','PhotoQ'),
			'TL' => __('Top Left','PhotoQ'),
			'C|' => __('Center','PhotoQ'),
			'R|' => __('Right','PhotoQ'),
			'L|' => __('Left','PhotoQ'),
			'T|' => __('Top','PhotoQ'),
			'B|' => __('Bottom','PhotoQ'),
			'*'  => __('Tile','PhotoQ')
		);
		$watermarkPosition->populate($valueLabelArray);
		$watermark->addChild($watermarkPosition);
		
		$watermark->addChild(
			new TextFieldOption(
				'watermarkOpacity',
				'100',
				'',
				'<tr valign="top"><th scope="row">'. __('Opacity','PhotoQ'). ': </th><td>',
				'%</td></tr>',
				'2'
			)
		);
		
		$watermark->addChild(
			new TextFieldOption(
				'watermarkXMargin',
				'20',
				__('left/right','PhotoQ'). ':',
				'<tr valign="top"><th scope="row">'. __('Margins','PhotoQ'). ': </th><td>',
				'px, ',
				'2',
				'2'
			)
		);
		
		$watermark->addChild(
			new TextFieldOption(
				'watermarkYMargin',
				'20',
				__('top/bottom', 'PhotoQ'). ':',
				'',
				'px<br/>('. __('Values smaller than one are interpreted as percentages instead of pixels.','PhotoQ'). ')</td></tr>',
				'2',
				'2'
			)
		);
		
		$this->registerOption($watermark);
		
		//build field checkbox options
		$this->registerOption(
			new CheckBoxOption(
				'fieldAddPosted',
				'0',
				__('Add to already posted as well.','PhotoQ')
			)
		);
		
		$this->registerOption(
			new CheckBoxOption(
				'fieldDeletePosted',
				'0',
				__('Delete from already posted as well.','PhotoQ')
			)
		);
		$this->registerOption(
			new CheckBoxOption(
				'fieldRenamePosted',
				'1',
				__('Rename already posted as well.','PhotoQ')
			)
		);

		

		//build and register further options
		
		//$imgDirOption =& new RO_ChangeTrackingContainer('imgDirOption');
		$imgdir =& new TextFieldOption(
				'imgdir',
				'wp-content',
				'',
				'',
				'<br />'. sprintf(__('Default is %s','PhotoQ'), '<code>wp-content</code>')
		);
		$imgdir->addTest(new DirExistsInputTest('',
			__('Image Directory not found','PhotoQ'). ': '));
		$imgdir->addTest(new FileWritableInputTest('',
			__('Image Directory not writable','PhotoQ'). ': '));
		//$imgDirOption->addChild($imgdir);	
		$this->registerOption($imgdir);
		
		
		$imagemagickPath =& new TextFieldOption(
				'imagemagickPath',
				'',
				sprintf(_c('Absolute path to the ImageMagick convert executable. (e.g. %1$s ). Leave empty if %2$s is in the path.| example programname','PhotoQ'),'<code>/usr/bin/convert</code>','"convert"')
		);
		
		$this->registerOption($imagemagickPath);
		
		
		
		$cronOptions =& new CompositeOption('cronJobs');
		$cronOptions->addChild(
			new TextFieldOption(
				'cronFreq',
				'23',
				__('Cronjob runs every','PhotoQ'). ' ',
				'',
				__('hours','PhotoQ'),
				'3',
				'5'
			)
		);
		$cronOptions->addChild(
			new CheckBoxOption(
				'cronPostMulti',
				'0',
				__('Use settings of second post button for automatic posting.','PhotoQ'),
				'<p>', '</p>'
			)
		);
		$cronOptions->addChild(
			new CheckBoxOption(
				'cronFtpToQueue',
				'0',
				__('When cronjob runs, automatically add FTP uploads to queue.','PhotoQ'),
				'<p>', '</p>'
			)
		);
		$this->registerOption($cronOptions);

		$adminThumbs =& new CompositeOption('showThumbs', '1','','<table>','</table>');
		$adminThumbs->addChild(
			new TextFieldOption(
				'showThumbs-Width',
				'120',
				'',
				'<tr><td>'._c('Thumbs shown in list of published photos are maximum | ends with: px wide','PhotoQ'). '</td><td>',
				_c('px wide| starts with: thumbs ... are','PhotoQ'). ', ',
				'3',
				'3'
			)
		);
		$adminThumbs->addChild(
			new TextFieldOption(
				'showThumbs-Height',
				'60',
				'',
				' ',
				__('px high','PhotoQ'). '. <br/></td></tr>',
				'3',
				'3'
			)
		);
		$adminThumbs->addChild(
			new TextFieldOption(
				'photoQAdminThumbs-Width',
				'200',
				'',
				'<tr><td>'.__('Thumbs shown in PhotoQ edit dialogs are maximum','PhotoQ'). '</td><td>',
				__('px wide','PhotoQ'). ', ',
				'3',
				'3'
			)
		);
		$adminThumbs->addChild(
			new TextFieldOption(
				'photoQAdminThumbs-Height',
				'90',
				'',
				' ',
				__('px high','PhotoQ'). '. <br/></td></tr>',
				'3',
				'3'
			)
		);
		$adminThumbs->addChild(
			new TextFieldOption(
				'editPostThumbs-Width',
				'300',
				'',
				'<tr><td>'.__('Thumbs shown in WordPress post editing dialog are maximum','PhotoQ'). '</td><td>',
				__('px wide','PhotoQ'). ', ',
				'3',
				'3'
			)
		);
		$adminThumbs->addChild(
			new TextFieldOption(
				'editPostThumbs-Height',
				'400',
				'',
				' ',
				__('px high','PhotoQ'). '.</td></tr>',
				'3',
				'3'
			)
		);
		$this->registerOption($adminThumbs);
		
		$autoTitles = new CompositeOption('autoTitles');
		$autoTitles->addChild(
			new TextFieldOption(
				'autoTitleRegex',
				'', __('Custom Filter','PhotoQ'). ':', 
				'', 
				'<br/>
				<span class="setting-description">'. 
				sprintf(__('An auto title is a title that is generated automatically from the filename. By default PhotoQ creates auto titles by removing the suffix from the filename, replacing hyphens and underscores with spaces and by capitalizing the first letter of every word. You can specify an additional custom filter to remove more from the filename above. Perl regular expressions are allowed, parts of filenames that match the regex are removed (regex special chars %s need to be escaped with a backslash). Note that the custom filter is applied first, before any of the default replacements.','PhotoQ'),'<code>. \ + * ? [ ^ ] $ ( ) { } = ! < > | :</code>') 
				. '<br/>'.
				__('Examples: <code>IMG</code> to remove the string "IMG" from anywhere within the filename, <code>^IMG</code> to remove "IMG" from beginning of filename.','PhotoQ').'</span>'
			)
		);
		$autoTitles->addChild(
			new TextFieldOption(
				'autoTitleNoCapsShortWords',
				'2', 
				'<br/><br/>' . __('Do not capitalize words with','PhotoQ'). ' ', 
				'', 
				' ' . __('characters or less,', 'PhotoQ'),
				2,2
			)
		);
		$autoTitles->addChild(
			new TextFieldOption(
				'autoTitleCaps',
				'I', 
				' ' . __('except for the following words','PhotoQ'). ':<br/>', 
				'', 
				'
				<span class="setting-description">'. 
				__('(Separate words with commas)', 'PhotoQ') 
				. '</span><br/><br/>',
				100,200
			)
		);
		$autoTitles->addChild(
			new TextAreaOption(
				'autoTitleNoCaps',
				_c('for, and, nor, but, yet, both, either, neither, the, for, with, from, because, after, when, although, while|english words that are not capitalized', 'PhotoQ'), 
				' ' . __('Do not capitalize any of the following words (Separate words with commas)','PhotoQ'). ':<br/>', 
				'', 
				'',
				2,100
			)
		);
		$this->registerOption($autoTitles);
		
		$enableFtp =& new CheckBoxOption(
			'enableFtpUploads',
			'0',
			__('Allow importing of photos from the following directory on the server','PhotoQ'). ': '
		);
		$enableFtp->addChild(
			new TextFieldOption(
				'ftpDir',
				'',
				'',
				'',
				'<br />'. sprintf(__('Full path (e.g., %s)','PhotoQ'),'<code>'.ABSPATH.'wp-content/ftp</code>')
			)
		);
		$this->registerOption($enableFtp);
		
		$this->registerOption(
			new TextFieldOption(
				'postMulti',
				'999',
				__('Second post button posts ','PhotoQ'),
				'',
				__(' photos at once.','PhotoQ'),
				'3',
				'3'
			)
		);
		
		$this->registerOption(
			new CheckBoxOption(
				'foldCats',
				'0',
				__('Fold away category lists per default.','PhotoQ')
			)
		);
		
		$this->registerOption(
			new CheckBoxOption(
				'deleteImgs',
				'1',
				__('Delete image files from server when deleting post.','PhotoQ')
			)
		);

		$this->registerOption(
			new CheckBoxOption(
				'enableBatchUploads',
				'1',
				__('Enable Batch Uploads.','PhotoQ')
			)
		);

		$statusArray = array("draft", "private", "publish");
		$postStatus = new DropDownList(
				 'qPostStatus',
				 'publish',
				 __('This is the default status of posts posted via PhotoQ.','PhotoQ')
		);
		$postStatus->populate(PhotoQHelper::arrayCombine($statusArray,$statusArray));
		$this->registerOption($postStatus);
		
		$this->registerOption(
			new AuthorDropDownList(
				 'qPostAuthor',
				 '1',
				 __('PhotoQ will fall back to this author if no author can be determined by any other means. This is for example the case if photos are automatically added to the queue through cronjobs.','PhotoQ')
			)
		);
		
		$this->registerOption(
			new CategoryDropDownList(
				 'qPostDefaultCat',
				 '1',
				 __('This is the default category for posts posted via PhotoQ.','PhotoQ')
			)
		);
		
		$roleOptions = new CompositeOption('specialCaps','','','<table><tr>','</tr></table>');
		$roleOptions->addChild(
			new PhotoQRoleOption(
				'editorCaps','editor',
				array('use_primary_photoq_post_button','use_secondary_photoq_post_button','reorder_photoq'),
				__('Editor','PhotoQ'),
				'<td>',
				'</td>'
			)
		);
		$roleOptions->addChild(
			new PhotoQRoleOption(
				'authorCaps','author',
				array('use_primary_photoq_post_button','use_secondary_photoq_post_button','reorder_photoq'),
				__('Author','PhotoQ'),
				'<td>',
				'</td>'
			)
		);
		
		$this->registerOption($roleOptions);
		
		$imageSizes =& new ImageSizeContainer('imageSizes', array());
		
		$imageSizes->addChild(new ImageSizeOption($this->THUMB_IDENTIFIER, '0', '80', '60'));
		$imageSizes->addChild(new ImageSizeOption($this->MAIN_IDENTIFIER, '0'));
		
		$this->registerOption($imageSizes);
		
		
		$originalFolder =& new CompositeOption('originalFolder');
		$originalFolder->addChild(
			new CheckBoxOption(
				'hideOriginals',
				'0',
				__('Hide folder containing original photos. If checked, PhotoQ will attribute a random name to the folder.','PhotoQ'),
				'',
				''
			)
		);
		$this->registerOption($originalFolder);
		
		$contentView =& new PhotoQViewOption(
				'contentView',
				$this->MAIN_IDENTIFIER,
				$this->THUMB_IDENTIFIER
		);
		$contentView->addChild(
			new CheckBoxOption(
				'inlineDescr',
				'1',
				__('Include photo description in post content.','PhotoQ'),
				'<tr><th>'. __('Photo Description','PhotoQ'). ':</th><td>',
				'</td></tr>'
			)
		);
		$contentView->addChild(
			new CheckBoxOption(
				'inlineExif',
				'0',
				__('Include Exif data in post content.','PhotoQ'),
				'<tr><th>'. __('Exif Meta Data','PhotoQ'). ':</th><td>',
				'</td></tr>'
			)
		);
		$this->registerOption($contentView);
		
		
		$excerptView =& new PhotoQViewOption(
				'excerptView',
				$this->MAIN_IDENTIFIER,
				$this->THUMB_IDENTIFIER
		);
		$this->registerOption($excerptView);
		
		//overwrite default options with saved options from database
		$this->load();
				
		//populate lists of image sizes that depend on runtime stuff and cannot be populated before
		$contentView->populate($this->getImageSizeNames(),$this->ORIGINAL_IDENTIFIER == 'original');
		$excerptView->populate($this->getImageSizeNames(),$this->ORIGINAL_IDENTIFIER == 'original');
	
		//check for existence of cache directory
		//convert backslashes (windows) to slashes
		$cleanAbs = str_replace('\\', '/', ABSPATH);
		$this->addTest( new DirExistsInputTest(
			preg_replace('#'.$cleanAbs.'#', '', $this->getCacheDir()), 
			__('Cache Directory not found','PhotoQ'). ': ')
		);
		$this->addTest( new FileWritableInputTest(
			preg_replace('#'.$cleanAbs.'#', '', $this->getCacheDir()), 
			__('Cache Directory not writeable','PhotoQ'). ': ')
		);
	}
	
	/**
	 * initialize stuff that depends on runtime configuration so that 
	 * what is displayed represents the changes from last update.
	 *
	 */
	function initRuntime()
	{
		//$this->load();
		//populate lists of image sizes that depend on runtime stuff and cannot be populated before
		$this->_options['contentView']->unpopulate();
		$this->_options['excerptView']->unpopulate();
		
		//put the available image sizes into the list for content and excerpt
		$this->_options['contentView']->populate($this->getImageSizeNames(),$this->ORIGINAL_IDENTIFIER == 'original');
		$this->_options['excerptView']->populate($this->getImageSizeNames(),$this->ORIGINAL_IDENTIFIER == 'original');
		
		//test for presence of imageMagick
		$imagemagickTest = new PhotoQImageMagickPathCheckInputTest();
		$msg = $imagemagickTest->validate($this->_options['imagemagickPath']);
		$this->_options['imagemagickPath']->setTextAfter('<br/>'. $msg);
	}
	
	function addImageSize($name)
	{
		$imageSizes =& $this->_options['imageSizes'];
		if($name != 'original' && !array_key_exists($name, $imageSizes->getValue())){
			//add corresponding directory to imgdir
			if(PhotoQHelper::createDir($this->getImgDir() . $name)){
				//add to database
				$imageSizes->addChild(new ImageSizeOption($name));
				$this->_store();
			}else
				return new PhotoQErrorMessage(sprintf(__("Could not create image size. The required directory in %s could not be created. Please check your settings and/or PHP Safe Mode.",'PhotoQ'), $this->getImgDir() . $name ));			
		}else
			return new PhotoQErrorMessage(__("Name already taken, please choose another name.",'PhotoQ'));
		return new PhotoQStatusMessage(__("New image size successfully created.",'PhotoQ'));
	}
	
	function removeImageSize($name)
	{
		$imageSizeDir = $this->getImgDir() . $name;
		//remove corresponding dirs from server
		if(!file_exists($imageSizeDir) || PhotoQHelper::recursiveRemoveDir($imageSizeDir)){
			$imageSizes =& $this->_options['imageSizes'];
			//remove from database
			$imageSizes->removeChild($name);
			$this->_store();
		}else
				return new PhotoQErrorMessage(sprintf(__('Could not remove image size. The required directories in %s could not be removed. Please check your settings.','PhotoQ'), $imageSizeDir));
		return new PhotoQStatusMessage(__("Image size successfully removed.",'PhotoQ'));
	}
	
	
	function getQDir(){
		return $this->getImgDir().'qdir/';
	}
	
	/**
	 * Returns the cache directory used by phpThumb. This is now fixed to wp-content/photoQCache.
	 *
	 * @return string	The cache directory.
	 */
	function getCacheDir(){
		return str_replace('\\', '/', ABSPATH) . 'wp-content/photoQCache/';
	}
	
	function getImgDir(){
		//prepend ABSPATH to $imgdir if it is not already there
		$dirPath = str_replace(ABSPATH, '', trim($this->getValue('imgdir')));
		//$dirPath = str_replace(ABSPATH, '', 'wp-content');
		$dir = rtrim(ABSPATH . $dirPath, '/');
		return $dir . '/';
	}
	
	function getFtpDir(){
		//for windows directories (e.g. c:/) we don't want a first slash
		$firstSlash = '/';
		if(preg_match('/^[a-zA-Z]:/', $this->getValue('ftpDir')))
			$firstSlash = '';
		return $firstSlash.trim($this->getValue('ftpDir'), '\\/').'/';
	}
	
	function getMainIdentifier()
	{
		return $this->MAIN_IDENTIFIER;
	}
	
	function getThumbIdentifier()
	{
		return $this->THUMB_IDENTIFIER;
	}
	
	function getOriginalIdentifier()
	{
		return $this->ORIGINAL_IDENTIFIER;
	}
	
	/**
	 * Returns an array containing all image sizes.
	 *
	 * @return array	the names of all registered imageSizes
	 */
	function getImageSizeNames()
	{
		return array_keys($this->getValue('imageSizes'));
	}
	
	/**
	 * Returns an array containing names of image sizes that changed during last update.
	 *
	 * @return array	the names of all changed imageSizes
	 */
	function getChangedImageSizeNames()
	{
		$imageSizes =& $this->_options['imageSizes'];
		return $imageSizes->getChangedImageSizeNames();
	}
	
	
	/**
	 * Returns an array containing names of imagesizes that have a watermark.
	 * @return array
	 */
	function getImageSizeNamesWithWatermark(){
		$imageSizes =& $this->_options['imageSizes'];
		return $imageSizes->getImageSizeNamesWithWatermark();
	}
	
	/**
	 * Goes through all exif tags that changed. Returns two arrays, the first
	 * one containing the names of tags that got added to tagfromexif, the
	 * the second one containing the names of those who got deleted.
	 * @return unknown_type
	 */
	function getAddedDeletedTagsFromExif(){
		$changedTags =& $this->_options['exifTags']->getChildrenWithAttribute();
		$added = array();
		$deleted = array();
		foreach($changedTags as $tag){
			//get the checkbox that determines tagFromExif status
			$checkBox =& $tag->getOptionByName($tag->getName().'-tag');
			if($checkBox->getValue() == 1)
				$added[] = $tag->getName();
			else
				$deleted[] = $tag->getName();			
		}
		return array($added, $deleted);
	}
	
	/*function getAllTagsFromExif(){
		$result = array();
		$allTags =& $this->_options['exifTags']->getChildrenWithAttribute('getValue');
		foreach($allTags as $tag)
			$result[] = $tag->getName();
		
		return $result;
	}*/
	
	
	function getOldValues($containerName)
	{
		$opt =& $this->_options[$containerName];
		return $opt->_oldValues;
	}
	
	/**
	 * Validate options and record any errors occuring
	 */
	function validateOptions(){
		//do the input validation
		$validationErrors = parent::validate();
		if(count($validationErrors)){
			foreach($validationErrors as $valError){
				$this->_errStack->push(PHOTOQ_ERROR_VALIDATION,'error', array(), $valError);
			}
		}
	}
	
	/**
	 * Show array of options as rows of the table
	 * @param $optionArray
	 * @return unknown_type
	 */
	function showOptionArray($optionArray){
		foreach ($optionArray as $optName => $optLabel){
			echo '<tr valign="top">'. PHP_EOL;
			echo '   <th scope="row">'.$optLabel.'</th>'.PHP_EOL.'   <td>';
			$this->render($optName);
			echo '</td>'.PHP_EOL.'</tr>'. PHP_EOL;
		}
	}
	
	
	

	
	
}


/**
 * The PhotoQRenderOptionVisitor:: is responsible for rendering of the options. It 
 * renders every visited option in HTML.
 *
 * @author  M. Flury
 * @package PhotoQ
 */
class PhotoQRenderOptionVisitor extends RenderOptionVisitor
{
	
	
	 
	/**
	 * Method called whenever a
	 * ImageSizeOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object ImageSizeOption &$dropDownList	Reference to visited option.
	 */
	 function visitImageSizeOptionBefore(&$imageSize)
	 {
	 	$deleteLink = '';
	 	if($imageSize->isRemovable()){
	 		$deleteLink = 'options-general.php?page=whoismanu-photoq.php&amp;action=deleteImgSize&amp;entry='.$imageSize->getName();
	 		$deleteLink = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($deleteLink, 'photoq-deleteImgSize' . $imageSize->getName()) : $deleteLink;
	 		$deleteLink = '<a href="'.$deleteLink.'" class="delete" onclick="return confirm(\'Are you sure?\');">Delete</a>';
	 	}
	 	print '<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
	 				<tr valign="top">
	 					<th> ' .$imageSize->getName().'</th>
	 					<td style="text-align:right">'.$deleteLink.'</td>
	 				</tr>';
	 	
	 }
	 
	 /**
	 * Method called whenever a
	 * ImageSizeOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object ImageSizeOption &$imageSize	Reference to visited option.
	 */
	 function visitImageSizeOptionAfter(&$imageSize)
	 {
	 	print "</table>";
	 }
	 
	 
	 function visitPhotoQExifTagOptionBefore(&$option)
	 {
	 	print '<b>'.$option->getExifKey().'</b> ( '.$option->getExifExampleValue().' )<br/>'.PHP_EOL;
	 }
	 
	function visitPhotoQRoleOptionBefore(&$option)
	 {
	 	print $option->getTextBefore();
	 	print $option->getLabel().':'.PHP_EOL;
	 	print '<ul>'.PHP_EOL;
	 	
	 }
	 
	function visitPhotoQRoleOptionAfter(&$option)
	 {
	 	print '</ul>'.PHP_EOL;
	 	print $option->getTextAfter();
	 }
	 	
}




class ImageSizeContainer extends CompositeOption
{
	/**
	 * Check whether we find a value for this option in the array pulled from
	 * the database. If so adopt this value. Pass the array on to all the children
	 * such that they can do the same.
	 *
	 * @param array $storedOptions		Array pulled from database.
	 * @access public
	 */
	function load($storedOptions)
	{
		if(is_array($storedOptions)){
			if(array_key_exists($this->getName(), $storedOptions)){
				$this->setValue($storedOptions[$this->getName()]);
			}
			//register all ImageSizes that can be added/removed on runtime
			foreach ($this->getValue() as $key => $value){
				//only add if not yet there and removable
				if(!$this->getOptionByName($key) && $value) $this->addChild(new ImageSizeOption($key, '1'));
			}
			parent::load($storedOptions);
		}
		
		

	}
	
	/**
	 * Stores own values in addition to selected childrens values in associative 
	 * array that can be stored in Wordpress database.
	 * 
	 * @return array $result		Array of options to store in database.
	 * @access public
	 */
	function store()
	{
		$result = array();
		$result[$this->_name] = $this->getValue();
		$result = array_merge($result, parent::store());
		return $result;
	}
	
	/**
	 * Add an option to the composite. And add its name to the list of names (= value of ImageSizeContainer)
	 * 
	 * @param object ReusableOption &$option  The option to be added to the composite.
	 * @return boolean	True if options could be added (composite), false otherwise.
	 * @access public
	 */
	function addChild(&$option)
	{	
		if(is_a($option, 'ImageSizeOption')){
			$newValue = $this->getValue();
			$newValue[$option->getName()] = $option->isRemovable();
			$this->setValue($newValue);
			return parent::addChild($option);
		}
		return false;
	}
	
	/**
	 * Remove an option from the composite.	
	 * 
	 * @param string $name  The option to be removed from the composite.
	 * @return boolean 		True if existed and removed, False otherwise.
	 * @access public
	 */
	function removeChild($name)
	{	
		$newValue = $this->getValue();
		if($newValue[$name]){ //only remove images sizes that are allowed to be removed
			unset($newValue[$name]);
			$this->setValue($newValue);
			return parent::removeChild($name);
		}
		return false;
	}
	
	/**
	 * Returns an array containing names of imagesizes that changed during
	 * last update.
	 * @return array
	 */
	function getChangedImageSizeNames(){
		return $this->_getImageSizeNamesWithAttribute();
	}
	
	/**
	 * Returns an array containing names of imagesizes that have a watermark.
	 * @return array
	 */
	function getImageSizeNamesWithWatermark(){
		return $this->_getImageSizeNamesWithAttribute('hasWatermark');
	}
	
	/**
	 * Low level function that allows to query image sizes through a callback function.
	 * Names of image sizes whose callback return true are returned in an array.
	 * @param $hasAttributeCallback the callback function to be called.
	 * @return array names of image sizes for which the callback returned true
	 */
	function _getImageSizeNamesWithAttribute($hasAttributeCallback = 'hasChanged'){
		$with = array();
		foreach($this->getChildrenWithAttribute($hasAttributeCallback) as $current)
			$with[] = $current->getName();
			
		return $with;
		
		/*$with = array();
		$numChildren = $this->countChildren();
		for ($i = 0; $i < $numChildren; $i++){
			$current =& $this->getChild($i);
			if(method_exists($current, $hasAttributeCallback)){
				if($current->$hasAttributeCallback())
					$with[] = $current->getName();
			}else
				die('PhotoQOptionController: method callback with name ' . $hasAttributeCallback . 'does not exist');
		}
		return $with;*/
	}

	
}

class ImageSizeOption extends CompositeOption
{
	
	/**
	 * Default width of Image size.
	 *
	 * @access private
	 * @var integer
	 */
	var $_defaultWidth;
	
	/**
	 * Default height of Image size.
	 *
	 * @access private
	 * @var integer
	 */
	var $_defaultHeight;
	


	/**
	 * PHP5 type constructor
	 */
	function __construct($name, $defaultValue = '1', $defaultWidth = '700', $defaultHeight = '525')
	{
		parent::__construct($name, $defaultValue);
		
		$this->_defaultWidth = $defaultWidth;
		$this->_defaultHeight = $defaultHeight;
		
		$this->_buildRadioButtonList();
		
		
		$this->addChild(
			new TextFieldOption(
				$this->_name . '-imgQuality',
				'95',
				'',
				'<tr valign="top"><th scope="row">'.__('Image Quality','PhotoQ').': </th><td>',
				'%</td></tr>',
				'2'
			)
		);
		
		$this->addChild(
			new CheckBoxOption(
				$this->_name . '-watermark',
				'0',
				__('Add watermark to all images of this size.','PhotoQ'),
				'<tr valign="top"><th scope="row">'.__('Watermark','PhotoQ').':</th><td>',
				'</td></tr>'
			)
		);
		
	}
	
	
	
	function _buildRadioButtonList()
	{
		$imgConstr = new RadioButtonList(
				$this->_name . '-imgConstraint',
				'rect'
		);

		$maxDimImg = new RadioButtonOption(
				'rect',
				__('Maximum Dimensions','PhotoQ').': ',
				'<tr valign="top"><th scope="row">',
				'</th>'
		);
		$maxDimImg->addChild(
			new TextFieldOption(
				$this->_name . '-imgWidth',
				$this->_defaultWidth,
				'',
				'<td>',
				__('px wide','PhotoQ').', ',
				'4',
				'5'
			)
		);
		$maxDimImg->addChild(
			new TextFieldOption(
				$this->_name . '-imgHeight',
				$this->_defaultHeight,
				'',
				'',
				__('px high','PhotoQ').' ',
				'4',
				'5'
			)
		);
		$maxDimImg->addChild(
			new CheckBoxOption(
				$this->_name . '-zoomCrop',
				0,
				__('Crop to max. dimension','PhotoQ').'.&nbsp;)',
				'&nbsp;(&nbsp;',
				'</td></tr>'
			)
		);
		$imgConstr->addChild($maxDimImg);


		$smallestSideImg = new RadioButtonOption(
				'side',
				__('Smallest side','PhotoQ').': ',
				'<tr valign="top"><th scope="row">',
				'</th>'
		);
		$smallestSideImg->addChild(
			new TextFieldOption(
				$this->_name . '-imgSide',
				'525',
				'',
				'<td>',
				'px</td></tr>',
				'4',
				'5'
			)
		);
		$imgConstr->addChild($smallestSideImg);

		$fixedWidthImg = new RadioButtonOption(
				'fixed',
				__('Landscape Width','PhotoQ').': ',
				'<tr valign="top"><th scope="row">',
				'</th>'
		);
		$fixedWidthImg->addChild(
			new TextFieldOption(
				$this->_name . '-imgFixed',
				'525',
				'',
				'<td>',
				'px</td></tr>',
				'4',
				'5'
			)
		);
		$imgConstr->addChild($fixedWidthImg);

		$imgConstr->addChild(
			new RadioButtonOption(
				'noResize',
				__('Original Size','PhotoQ').': ',
				'<tr valign="top"><th scope="row">',
				'</th><td>'.__('Keep original image size, don\'t resize','PhotoQ').'.</td></tr>'
			)
		);
		
		
		
		$this->addChild($imgConstr);
	}
	
	
	
	
	/**
	 * Tests whether the ImageSize in question is removable of not.
	 *
	 * @return boolean
	 */
	function isRemovable()
	{
		return $this->getValue();
	}
	
	/**
	 * Returns boolean indicating whether this image size sports a watermark.
	 * @return boolean true if image size has watermark, false otherwise
	 */
	function hasWatermark(){
		$option = &$this->getOptionByName($this->_name.'-watermark');
		return $option->getValue(); 
	}
 	
	
	

}


class PhotoQViewOption extends CompositeOption
{
	
	var $_mainID;
	var $_thumbID;
	
	


	/**
	 * PHP5 type constructor
	 */
	function __construct($name, $mainID, $thumbID)
	{
		parent::__construct($name);
		
		$this->_mainID = $mainID;
		$this->_thumbID = $thumbID;
		
		$this->_buildRadioButtonList();
	}
	
	
	function _buildRadioButtonList()
	{
		$viewType =& new RadioButtonList(
				$this->_name . '-type',
				'single'
		);

		
		$singleImg =& new RadioButtonOption(
				'single',
				__('Single Photo','PhotoQ').': ',
				'<tr valign="top"><th scope="row">',
				'</th>'
		);
		$singleSize =& new DropDownList(
				$this->_name . '-singleSize',
				$this->_mainID,
				'',
				'<td>',
				'</td></tr>'
		);
		$singleImg->addChild($singleSize);
		$viewType->addChild($singleImg);
		
		
		$imgLink =& new RadioButtonOption(
				'imgLink',
				__('Image Link','PhotoQ').': ',
				'<tr valign="top"><th scope="row">',
				'</th>'
		);
		$imgLinkSize =& new DropDownList(
				$this->_name . '-imgLinkSize',
				$this->_thumbID,
				'',
				'<td>',
				__(' linking to ','PhotoQ')
		);		
		$imgLink->addChild($imgLinkSize);
		$imgLinkTargetSize =& new DropDownList(
				$this->_name . '-imgLinkTargetSize',
				$this->_mainID,
				'',
				'',
				''
		);
		$imgLink->addChild($imgLinkTargetSize);
		
		$imgLink->addChild(
			new TextFieldOption(
				$this->_name . '-imgLinkAttributes',
				attribute_escape('rel="lightbox"'),
				', '.__('link having following attributes','PhotoQ').': ',
				'',
				'<br />
				<span class="setting-description">'.__('Allows interaction with JS libraries such as Lightbox and Shutter Reloaded without modifying templates.','PhotoQ').'</span></td></tr>',
				'40'
			)
		);
		
		$viewType->addChild($imgLink);
		
		$this->addChild($viewType);
		
	}
	
	/**
	 * Populate the lists of image sizes with the names of registered image sizes as key, value pair.
	 *
	 * @param array $imgSizeNames
	 * @access public
	 */
	function populate($imgSizeNames, $addOriginal = true)
	{
		//add the original as an option
		if($addOriginal)
			array_push($imgSizeNames,'original');
		
		$singleSize =& $this->getOptionByName($this->_name .'-singleSize');
		$singleSize->populate(PhotoQHelper::arrayCombine($imgSizeNames,$imgSizeNames));
		
		$imgLinkSize =& $this->getOptionByName($this->_name .'-imgLinkSize');
		$imgLinkSize->populate(PhotoQHelper::arrayCombine($imgSizeNames,$imgSizeNames));
		
		$imgLinkTargetSize =& $this->getOptionByName($this->_name .'-imgLinkTargetSize');
		$imgLinkTargetSize->populate(PhotoQHelper::arrayCombine($imgSizeNames,$imgSizeNames));
		
	}
	
	/**
	 * Remove names of registered image sizes as key, value pair.
	 *
	 * @access public
	 */
	function unpopulate()
	{
		$singleSize =& $this->getOptionByName($this->_name .'-singleSize');
		$singleSize->removeChildren();
		$imgLinkSize =& $this->getOptionByName($this->_name .'-imgLinkSize');
		$imgLinkSize->removeChildren();
		$imgLinkTargetSize =& $this->getOptionByName($this->_name .'-imgLinkTargetSize');
		$imgLinkTargetSize->removeChildren();
		
	}
	
	
	
	

}


/**
 * The PhotoQImageMagickPathCheckInputTest:: checks whether 
 * imagemagick path really leads to imagemagick.
 *
 * @author  M.Flury
 * @package PhotoQ
 */
class PhotoQImageMagickPathCheckInputTest extends InputTest
{
	
	/**
	 * Concrete implementation of the validate() method. This methods determines 
	 * whether input validation passes or not.
	 * @param object ReusableOption &$target 	The option to validate.
	 * @return String 	The error message created by this test.
	 * @access public
	 */
	function validate(&$target)
	{	
		$errMsg = '';
		require_once(PHOTOQ_PATH.'lib/phpThumb_1.7.9/phpthumb.class.php');
		// create phpThumb object
		$phpThumb = new phpThumb();
		$phpThumb->config_imagemagick_path = ( $target->getValue() ? $target->getValue() : null );
		//under windows the version check doesn't seem to work so we also check for availability of resize
		if ( !$phpThumb->ImageMagickVersion() && !$phpThumb->ImageMagickSwitchAvailable('resize') ) {
    		$errMsg = __("Note: ImageMagick does not seem to be installed at the location you specified. ImageMagick is optional but might be needed to process bigger photos, plus PhotoQ might run faster if you configure ImageMagick correctly. If you don't care about ImageMagick and are happy with using the GD library you can safely ignore this message.",'PhotoQ');
		}
		return $this->formatErrMsg($errMsg);
	}
	
	
}


class PhotoQExifTagOption extends CompositeOption
{
	var $_exifExampleValue;
		
	function __construct($exifKey, $exifExampleValue)
	{
		parent::__construct($exifKey);
		$this->_exifExampleValue = $exifExampleValue;
			
		$this->addChild(
			new TextFieldOption(
				$exifKey.'-displayName',
				'',
				__('Display Name','PhotoQ').': ',
				'',
				'<br/>',
				'20')
		);
		
		//whether to use it for tagFromExif
		$this->addChild(
			new CheckBoxOption(
				$exifKey.'-tag',
				'0', 
				__('Create post tags from EXIF data','PhotoQ').'', 
				'', 
				''
			)
		);
		
	}
	
	function getExifKey(){
		return $this->getName();
	}
	
	function getExifExampleValue(){
		return $this->_exifExampleValue;
	}

}


class PhotoQRoleOption extends RO_CapabilityCheckBoxList
{

	function __construct($name, $role = 'administrator', $defaultValue = '', $label = '',
				$textBefore = '', $textAfter = '')
	{
		parent::__construct($name, $role, $defaultValue, $label, $textBefore, $textAfter);
		
		$this->addChild(
			new RO_CheckBoxListOption(
				'use_primary_photoq_post_button',
				__('Allowed to use primary post button','PhotoQ'),
				'<li>',
				'</li>'
			)
		);
		$this->addChild(
			new RO_CheckBoxListOption(
				'use_secondary_photoq_post_button',
				__('Allowed to use secondary post button','PhotoQ'),
				'<li>',
				'</li>'
			)
		);
		$this->addChild(
			new RO_CheckBoxListOption(
				'reorder_photoq',
				__('Allowed to reorder queue','PhotoQ'),
				'<li>',
				'</li>'
			)
		);
		
	}

		
}		
		


?>
