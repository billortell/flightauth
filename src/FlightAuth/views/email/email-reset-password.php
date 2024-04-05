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
                Yep, you know the drill
            </p>
            <!-- opening paragraph -->
            <p style="margin:0;letter-spacing:-0.01em; color:#050505; font-family:'DM Sans',Arial,Verdana,sans-serif;font-size:14px;line-height:18px;">
                You have requested to reset your password.  If this wasn't you, please let us know asap.
                Otherwise, please <u>click the link below</u> to reset your password.
            </p>

            <p>&nbsp;</p>

            <p style="margin:0;letter-spacing:-0.01em; color:white; font-family:'DM Sans',Arial,Verdana,sans-serif;">
                <a href="<?php echo $verification_link;?>" target="_blank" style="background: transparent;
                    border: 1px solid <?php echo $primary;?>; font-weight:400;
                    color: <?php echo $primary;?>;  text-decoration: none; padding: 10px 45px; border-radius: 4px; display:inline-block; mso-padding-alt:0;text-underline-color:#ffffff">

                    <!--[if mso]>
                    <i style="letter-spacing: 25px;mso-font-width:-100%;mso-text-raise:20pt">&nbsp;</i>
                    <![endif]-->
                    <span style="mso-text-raise:10pt;font-weight:bold;">
                        Verify your account
                    </span>
                    <!--[if mso]>
                    <i style="letter-spacing: 25px;mso-font-width:-100%">&nbsp;</i>
                    <![endif]-->

                </a>
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

