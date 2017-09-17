<?php global $wpo_wcpdf; ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>
	<?php
	// check if method exists to prevent fatal errors! (backwards compatibility)
	if (method_exists($wpo_wcpdf, 'get_template_name')) {
		echo $wpo_wcpdf->get_template_name($wpo_wcpdf->export->template_type);
	} else {
		switch ($wpo_wcpdf->export->template_type) {
			case 'invoice':
				_e( 'Invoice', 'wpo_wcpdf' );
				break;
			case 'packing-slip':
				_e( 'Packing Slip', 'wpo_wcpdf' );
				break;
			case 'proforma':
				_e( 'Proforma Invoice', 'wpo_wcpdf_pro' );
				break;
			case 'credit-note':
				_e( 'Credit Note', 'wpo_wcpdf_pro' );
				break;
			default:
				echo $wpo_wcpdf->export->template_type;
				break;
		}
	}
	?>
	</title>
	<style type="text/css"><?php $wpo_wcpdf->template_styles(); ?></style>
	<style type="text/css"><?php do_action( 'wpo_wcpdf_custom_styles', $wpo_wcpdf->export->template_type ); ?></style>
</head>
<body class="<?php echo $wpo_wcpdf->export->template_type; ?>">
<?php echo $wpo_wcpdf->export->output_body; ?>
</body>
</html>