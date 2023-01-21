<script type="text/javascript">

/*
$(document).ready(function () {
	$("#password2").bind("onBlur", function() {
		alert("triggered");
		if ($("#password").val() != $("#password2").val()) {
			$("#passbox").html("Erreur");
		}
	});
});
*/

function verifPseudo(pseudo) {
	if (pseudo != '') {
		$.get('ajax/checknick.php',
			{
				pseudo: escape(pseudo),
				anticache: new Date().getTime()
			},
			function(data) {
				if (data == '1') {
					$("#pseudobox").html('<?php echo '<span class="lose"><b>'.Lang::INVALID_USERNAME.'</b></span>'; ?>');
				} else if (data == '2') {
					$("#pseudobox").html('<?php echo '<span class="win"><b>'.Lang::VALID_USERNAME.'</b></span>'; ?>');
				}
			}
		);
	}
}

function verifPass() {
	if ($("#pass1").val() != $("#pass2").val()) {
		$("#passbox").html('<?php echo '<span class="lose"><b>'.Lang::INVALID_PASSWORD.'</b></span>'; ?>');
	} else {
		$("#passbox").empty();
	}
}

function verifMail() {
	if ($("#mail").val() != $("#mail2").val()) {
		$("#mailbox").html('<?php echo '<span class="lose"><b>'.Lang::INVALID_EMAIL.'</b></span>'; ?>');
	} else {
		$("#mailbox").empty();
	}
}

function validate() {
	if ($("#rules").attr("checked")) {
		$("#regform").submit();
	} else {
		$("#exclam").fadeIn();
	}
}
</script>

<?php
	ArghPanel::begin_tag(Lang::REGISTERATION);
?>
<form action="?f=register_validate" method="POST" id="regform">
<table class="simple">
	<colgroup>
		<col width="30%" />
		<col width="35%" />
		<col width="35%" />
	</colgroup>
	<tr>
		<td><img src="img/icons/user.png" alt="" /> <span class="bigger"><span class="red">*</span> <?php echo Lang::USERNAME; ?></span></td>
		<td><input type="text" size="25" name="username" maxlength="25" value="<?php echo $_POST['username']; ?>" onKeyUp="javascript:verifPseudo(this.value);" /></td>
		<td><span id="pseudobox"></span></td>
	</tr>
		
	<tr>
		<td colspan="3"><span class="info"><font size="1"><?php echo Lang::REG_USERNAME_DESCR; ?></font></span><br /><br /></td>
	</tr>
	
	<tr>
		<td><img src="img/icons/key.png" alt="" /> <span class="bigger"><span class="red">*</span> <?php echo Lang::PASSWORD; ?></span></td>
		<td colspan="2"><input type="password" size="25" id="pass1" name="password" maxlength="25" value="<?php echo $_POST['password']; ?>" /></td>
	</tr>
	
	<tr>
		<td colspan="3"><span class="info"><font size="1"><?php echo Lang::REG_PASSWORD_DESCR; ?></font></span><br /><br /></td>
	</tr>
	
	<tr>
		<td><span class="bigger"><span class="red">*</span> <?php echo Lang::REPEAT_PASSWORD; ?></span></td>
		<td><input type="password" size="25" id="pass2" name="password2" maxlength="25" value="<?php echo $_POST['password2']; ?>" onBlur="javascript:verifPass();" /></td>
		<td><span id="passbox"></span></td>
	</tr>
	
	<tr>
		<td colspan="3"><span class="info"><font size="1"><?php echo Lang::REG_REPEAT_PASSWORD_DESCR; ?></font></span><br /><br /></td>
	</tr>
	
	<tr>
		<td><img src="img/icons/user_green.png" alt="" /> <span class="bigger"><?php echo Lang::BNET_ACCOUNT; ?></span></td>
		<td colspan="2"><input type="text" size="25" name="bnet" maxlength="25" value="<?php echo $_POST['bnet']; ?>"></td>
	</tr>
	
	<tr>
		<td colspan="3"><span class="info"><font size="1"><?php echo Lang::REG_BNET_DESCR; ?></font></span><br /><br /></td>
	</tr>
	
	<tr>
		<td><img src="img/icons/user_red.png" alt="" /> <span class="bigger"><span class="red">*</span> <?php echo Lang::GARENA_ACCOUNT; ?></span></td>
		<td colspan="2"><input type="text" size="25" name="ggc" maxlength="50" value="<?php echo $_POST['ggc']; ?>"></td>
	</tr>
	
	<tr>
		<td colspan="3"><span class="info"><font size="1"><?php echo Lang::REG_GARENA_DESCR; ?></font><br /><br /></td>
	</tr>
	
	<tr>
		<td><img src="img/icons/email.png" alt="" /> <span class="bigger"><span class="red">*</span> <?php echo Lang::EMAIL; ?></span></td>
		<td colspan="2"><input type="text" size="25" id="mail" name="mail" maxlength="60" value="<?php echo $_POST['mail']; ?>"></td>
	</tr>
	
	<tr>
		<td colspan="3"><span class="info"><font size="1"><?php echo Lang::REG_EMAIL_DESCR; ?></font></span><br /><br /></td>
	</tr>
	
	<tr>
		<td><span class="bigger"><span class="red">*</span> <?php echo Lang::REPEAT_EMAIL; ?></span></td>
		<td><input type="text" size="25" id="mail2" name="mail2" maxlength="60" value="<?php echo $_POST['mail2']; ?>" onBlur="javascript:verifMail();" /></td>
		<td><span id="mailbox"></span></td>
	</tr>
	
	<tr>
		<td colspan="3"><span class="info"><font size="1"><?php echo Lang::REG_REPEAT_EMAIL_DESCR; ?></font></span><br /><br /></td>
	</tr>
	
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3"><span class="red">*</span><?php echo Lang::REG_MANDATORY_FIELD; ?><br />&nbsp;</td></tr>

	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	
	<tr>
		<td colspan="3">
			<img src="img/icons/exclamation.png" alt="" style="display: none;" id="exclam" />&nbsp;<input type="checkbox" name="rules" id="rules" <?php if (isset($_POST['correct'])) echo 'checked'; ?>/> <?php echo Lang::REG_RULES_READ; ?> <a href="?f=league_rules"><?php echo Lang::LEAGUE_RULES; ?></a> - <a href="?f=ladder_rules"><?php echo Lang::LADDER_RULES; ?></a>
		</td>
	</tr>
	
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	
	<tr>
		<td colspan="3" align="center"><input type="button" value="<?php echo Lang::VALIDATE; ?>" style="width: 200px;" onClick="javascript:validate();" /></td>
	</tr>
<!--
	<tr>
		<td colspan="2"><span class="info"><font size="1"><i><br />Conformément à la loi n°78-17 du 6 janvier 1978, modifiée par la loi n°2004-801 du 6 août 2004, relative à l’informatique, aux fichiers et aux libertés, vous disposez d’un droit d’accès, de rectification, de modification et de suppression des données vous concernant. Si vous souhaitez exercer ce droit et obtenir communication des informations vous concernant, vous pourrez à tout moment modifier ces informations via votre espace membre ou en contactant l'administrateur par <a href="mailto:arghcontact@gmail.com">email</a>.</i></font></span></td>
	</tr>
-->

</table>
</form>

<?php
	ArghPanel::end_tag();
?>