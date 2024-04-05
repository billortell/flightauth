<?php include('email-header.php');?>

<!-- Start Column 1 -->
<table width="100%" align="center" cellspacing="0" cellpadding="0" border="0" class="full-width">
    <tbody>
    <tr>
        <td height="2">&nbsp;</td>
    </tr>
    <tr>
        <td height="22" style="line-height:22px; " ></td>
    </tr>
    <tr >
        <td style="font-size:15px; mso-line-height-rule:exactly; line-height:18px; color:#95a5a6; font-weight:normal; font-family: Open Sans, sans-serif;"
            align="left">

            <!-- main title -->
            <p style="margin-top:0;margin-bottom:16px;letter-spacing:-0.04em; color:#050505; font-family:'DM Sans',Arial,Verdana,sans-serif;font-size:34px;line-height:42px;font-weight:bold;">
                Yay! You've reset your password
            </p>
            <!-- opening paragraph -->
            <p style="margin:0;letter-spacing:-0.01em; color:#050505; font-family:'DM Sans',Arial,Verdana,sans-serif;font-size:14px;line-height:18px;">
                Just a courtesy email to let you know you reset your password successfully.
            </p>

            <p>&nbsp;</p>

            <p style="margin:0;letter-spacing:-0.01em; color:#050505; font-family:'DM Sans',Arial,Verdana,sans-serif;font-size:14px;line-height:18px;">
                If you did not reset your password, please <a href="<?php echo SITE_DOMAIN_HTTP.SITE_REL_URI; ?>/contact-us">let us know</a> immediately or
                please take the time now to <a href="<?php echo SITE_DOMAIN_HTTP.SITE_REL_URI;?>/reset-password">reset your password</a>.
            </p>

            <p>&nbsp;</p>

        </td>
    </tr>
    <tr>
        <td class="center-stack"></td>
    </tr>
    </tbody>
</table>
<!-- End Column 1 -->


<?php include('email-footer.php');?>

