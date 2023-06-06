
<html>
<body style="background-color: #f6f6f6; color: #333; font-family: 'Trebuchet MS',Tahoma,serif; padding: 30px;">
<center>
    <div style="max-width: 700px; min-width: 500px; margin: 0 auto; border: solid 1px #e5e2d3;background-color: #ffffff; background-image: url('http://ukietech.com/images/mailer/background_stripe.jpg');">
        <table width="100%" cellspacing="0" cellpadding="0" style="border-top: 7px solid #ed1f24; font-size: 13px;">
            <tr>
                <td style="padding: 15px 15px 0 15px;" width="74" align="center" valign="top">
                    <a href="http://ukietech.com">
                        <img width="61px" height="50px" src="http://www.ukietech.com/images/mailer/ukietech.png"/>
                    </a>
                </td>

                <td>
                    <h2 style="font-size: 14px;font-weight: bold;text-transform: uppercase;padding: 9px 0 0 20px;margin: 0;color: #222222;">Ukietech subscription</h2>
                </td>

                <td width="100" align="right" style="padding: 9px 20px 0 5px;background: url('http://www.ukietech.com//images/mailer/dotter-divider.png') no-repeat 0 55%;">
                    <span style="font-size: 14px;font-weight: bold;text-transform: uppercase;color: #ed1f24;"><?=date('F',time())?></span><span style="font-size: 14px;font-weight: bold;text-transform: uppercase;color: #222222;"> <?=date('d',time())?></span>
                </td>

            </tr>
        </table>

        <div style="background: url('http://ukietech.com/images/mailer/background_note_trans.png')">
            <div style="padding: 10px 15px 40px 125px; text-align: left; min-height: 100px;">

                <h3 style="font-size: 14px;font-weight: bold;text-transform: uppercase;color: #ed1c24;margin: 0 0 15px 0;">lATEST NEWS</h3>

                <? foreach($items as $item) : ?>

                    <? if($item['section'] == 'news') : ?>
                        <? $link = "news/post/{$item['alias']}/"; ?>

                        <div style="overflow: hidden;margin-bottom: 25px;">
                            <div style="width: 70px;max-height: 70px;border: 1px solid #cfcfcf;float: left;">
                                <a href="http://ukietech.com/<?= $link; ?>">
                                    <img src="http://ukietech.com/<?=isset($images[$item['id']]) ? $images[$item['id']]['src'] : ''?>" alt="" height="70" width="70">
                                </a>
                            </div>

                            <div style="margin-left: 100px;">
                                <h4 style="margin: 0"><a href="http://ukietech.com/<?= $link; ?>" style="text-decoration:none;color: #17718b;font-size: 14px;font-family: Georgia, 'Century Schoolbook L', Serif;font-weight: normal;"><?= $item['name']; ?></a></h4>
                                <div style="color: #c4c4c4;font-size: 12px;margin: 0;font-family: Georgia, 'Century Schoolbook L', Serif;font-weight: normal;"><?= $item['date']; ?></div>
                                <div style="font-size: 12px;font-family: Arial, 'Nimbus Sans L', Helvetica, sans-serif;line-height: 1.5em;">
                                    <?=$item['text2']?>
                                </div>
                            </div>
                        </div>
                    <? endif;?>

                <? endforeach; ?>

                <h3 style="font-size: 16px;font-weight: bold;text-transform: uppercase;color: #ed1c24;margin: 0 0 15px 0;">BLOG POSTS</h3>


                <? foreach($items as $item) : ?>

                    <? if($item['section'] == 'blog') : ?>
                        <? $link = "blog/show/{$item['id']}/"; ?>

                        <div style="overflow: hidden;">

                            <table style="width: 100%">
                                <tr>
                                    <td style="min-width: 40%" valign="top">

                                        <table>
                                            <tr>
                                                <td colspan="2">
                                                    <h4 style="margin: 0;"><a style="text-decoration:none;color: #17718b;font-size: 14px;font-family: Georgia, 'Century Schoolbook L', Serif;font-weight: normal;" href="#"><?=$item['name'];?></a></h4>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #c4c4c4;font-size: 12px;font-family: Georgia, 'Century Schoolbook L', Serif;font-weight: normal;">Management - <?=$item['date']?></td>
<!--                                                <td align="right" style="color: #c4c4c4;font-size: 12px;margin: 0;font-family: Georgia, 'Century Schoolbook L', Serif;font-weight: normal;text-align: right">Management</td>-->
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="font-size: 12px;font-family: Arial, 'Nimbus Sans L', Helvetica, sans-serif;line-height: 1.5em;margin-top: 10px;text-align: justify;">
                                                    <?=$item['text1'];?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>


                                    <? if(isset($images[$item['id']])) : ?>
                                        <td style="" valign="top" align="center">
                                                <a href="http://ukietech.com/<?= $link; ?>">
                                                    <img style="border: 1px solid #cfcfcf" src="http://ukietech.com/<?=isset($images[$item['id']]) ? $images[$item['id']] : ''?>" alt="">
                                                </a>
                                        </td>
                                    <? endif;?>


                                </tr>
                            </table>
                        </div>

                    <? endif;?>

                <? endforeach; ?>

                <!--                    --><?//=isset($message) ? $message : '' ?>
            </div>

            <div style="position: relative; padding: 30px 5px 5px 119px;background: url('http://ukietech.com/images/mailer/contact_us.png') no-repeat 4% 0;">

                <table width="100%" cellspasing="0" cellpadding="4" style="font-size: 13px;">
                    <tr>
                        <td valign="top">
                            <img style="vertical-align: top;" src="http://ukietech.com/images/mailer/icon-mail.png" />
                            <a href="mailto:mail@ukietech.com" style="display: inline-block; line-height: 22px;text-decoration: none;color: #231f20;">
                                mail@ukietech.com
                            </a>
                        </td>
                        <td valign="top">
                            <img style="vertical-align: top;" src="http://ukietech.com/images/mailer/icon-link.png" />
                            <a href="http://www.ukietech.com/" style="display: inline-block; line-height: 22px;text-decoration: none;color: #231f20;">
                                www.ukietech.com
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <img style="vertical-align: top;" src="http://ukietech.com/images/mailer/icon-phone.png" />
										<span style="display: inline-block; line-height: 22px;text-decoration: none;color: #231f20;">
											 772 . 988 . 9967
										</span>
                        </td>
                        <td valign="top">
                            <img style="vertical-align: top;" src="http://ukietech.com/images/mailer/icon-address.png" />
										<span style="display: inline-block; line-height: 22px;">
											646 N Bosworth Suite A. Chicago, IL 60642
										</span>
                        </td>
                    </tr>
                </table>
            </div>

        </div>


        <div style="padding: 0 0 0 125px;">
            <table width="100%" cellpadding="0" cellspacing="0" >
                <tr>
                    <td>
                        <div>
                            <a href="http://www.facebook.com/pages/Chicago-IL/Ukietech-Corp/118539015812" style="margin-right: 4px"><img src="http://ukietech.com/images/mailer/soc-facebook.png" alt="facebook"></a>
                            <a href="http://twitter.com/ukietech" style="margin-right: 4px"><img src="http://ukietech.com/images/mailer/soc-twitter.png" alt="twitter"></a>
                            <a href="http://www.linkedin.com/company/ukitech-arttoolbox" style="margin-right: 4px"><img src="http://ukietech.com/images/mailer/soc-in.png" alt="LinkedIn"></a>
                            <a href="http://www.yelp.com/biz/ukietech-chicago" style="margin-right: 4px"><img src="http://ukietech.com/images/mailer/soc-yelp.png" alt="yelp"></a>
                        </div>
                        <div>
                        </div>
                    </td>
                    <td align="right">
                        <img style="vertical-align: top;" src="http://www.ukietech.com/images/mailer/ukietech-footer.png" />
                    </td>
                </tr>
            </table>
        </div>

    </div>
</center>
</body>
</html>