<?php 
	$tReturnCode = $_REQUEST['ReturnCode'];
	$tErrorMsg = $_REQUEST['ErrorMessage'];
?>
<HTML>
<HEAD><TITLE>农行网上支付平台-商户接口范例-支付请求</TITLE></HEAD>
<BODY BGCOLOR='#FFFFFF' TEXT='#000000' LINK='#0000FF' VLINK='#0000FF' ALINK='#FF0000'>
<CENTER>支付请求<br>
<?php 
	echo 'ReturnCode   = ['.$tReturnCode.']<br>';
	echo 'ErrorMessage = ['.$tErrorMsg.']<br>';
?>
<a href='MerchantPaymentIE.html'>回商户首页</a></CENTER>
</BODY></HTML>
