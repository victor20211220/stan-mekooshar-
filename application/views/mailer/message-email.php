Subject: Order is paid.

<?=new View('mailer/email-header')?>
<p>You received new private message:</p>
<table cellspacing="4" cellpadding="4">
        <tr>
                <td><b>From:</b></td>
                <td><?=$senderName ?></td>
        </tr>
        <tr>
                <td><b>Subject:</b></td>
                <td><?=$subject ?></td>
        </tr>
        <tr>
                <td><b>Message:</b></td>
                <td><?=nl2br(Html::chars($message)) ?></td>
        </tr>
</table>
<?=new View('mailer/email-footer')?>