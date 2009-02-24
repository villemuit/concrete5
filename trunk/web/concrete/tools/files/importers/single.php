<?

defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
if (!$cp->canRead()) {
	die(_("Access Denied."));
}


$valt = Loader::helper('validation/token');
Loader::library("file/importer");

$error = "";

if (isset($_POST['fID'])) {
	// we are replacing a file
	$fr = File::getByID($_REQUEST['fID']);
} else {
	$fr = false;
}

if ($valt->validate('upload')) {
	if (isset($_FILES['Filedata']) && (is_uploaded_file($_FILES['Filedata']['tmp_name']))) {
		$fi = new FileImporter();
		$resp = $fi->import($_FILES['Filedata']['tmp_name'], $_FILES['Filedata']['name'], $fr);
		if (!($resp instanceof FileVersion)) {
			switch($resp) {
				case FileImporter::E_FILE_INVALID_EXTENSION:
					$error = t('Invalid file extension.');
					break;
				case FileImporter::E_FILE_INVALID:
					$error = t('Invalid file.');
					break;
				
			}
		}
	}
} else {
	$error = $valt->getErrorMessage();
}
?>
<html>
<head>
<script language="javascript">
	<? if(strlen($error)) { ?>
		alert('<?=$error?>');
		window.parent.ccm_alResetSingle();
	<? } else { ?>
		highlight = new Array();
		highlight.push(<?=$resp->getFileID()?>);
		window.parent.ccm_alRefresh(highlight);
	<? } ?>
</script>
</head>
<body>
</body>
</html>