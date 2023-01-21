<?php
	ArghSession::exit_if_not_logged();
	ArghPanel::begin_tag();
	
	if (/*false && */ArghSession::is_gold()) {
		$query = "SELECT gold_expire FROM lg_users WHERE username = '".ArghSession::get_username()."'";
		$result = mysql_query($query);
		$expiration = mysql_fetch_row($result);
		$expiration = $expiration[0];
		if ($expiration == 0) {
			echo '<center>Vous disposez d\'un compte <span class="vip">gold</span> illimité.</center>';
		} else {
			echo '<center>Vous disposez déjà d\'un compte <span class="vip">gold</span>. Expiration le <i>'.date(Lang::DATE_FORMAT_DAY, $expiration).'</i></center>';
		}
	} else {
?>
	<p>
		Nous vous proposons de souscrire à un compte <span class="vip"><b>gold</b></span> pour profiter pleinement de votre plateforme.<br />
		La souscription à un compte gold vous offrira de nombreux avantages : parties illimités, friendlist pour avoir plus de chances d'être dans la même équipe que vos amis, statistiques détaillées, changement de son nom d'utilisateur... consultez le tableau comparatif pour un aperçu des avantages.<br /><br />
		Bien que cela soit évident, la souscription à un compte "gold" ne rend personne au dessus du règlement. Il est à respecter par quiconque.<br />
		Le code allopass obtenu est strictement personnel et ne doit en aucun cas être donné sous peine de clôture immédiate du compte.
	</p>
	<br />
	<table class="listing">
		<colgroup>
			<col width="40%" />
			<col width="3%" />
			<col width="27%" />
			<col width="3%" />
			<col width="27%" />
		</colgroup>
		<thead>
			<tr>
				<th>Fonctionnalité</th>
				<th></th>
				<th>Compte normal</th>
				<th></th>
				<th>Compte <span class="vip">Gold</span></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Nombre de parties</td>
				<td><img src="img/icons/delete.png" alt="" /></td>
				<td><span class="lose">Limitées</span></td>
				<td><img src="img/icons/accept.png" alt="" /></td>
				<td><span class="win">Illimitées</span></td>
			</tr>
			<tr class="alternate">
				<td>Changement du username</td>
				<td><img src="img/icons/delete.png" alt="" /></td>
				<td><span class="lose">Impossible</span></td>
				<td><img src="img/icons/accept.png" alt="" /></td>
				<td><span class="win">Possible</span></td>
			</tr>
			<tr>
				<td>Choix de la bannière</td>
				<td><img src="img/icons/delete.png" alt="" /></td>
				<td><span class="lose">1 bannière</span></td>
				<td><img src="img/icons/accept.png" alt="" /></td>
				<td><span class="win">Toutes les bannières</span></td>
			</tr>
			<tr class="alternate">
				<td>Publicité</td>
				<td><img src="img/icons/delete.png" alt="" /></td>
				<td><span class="lose">Présence de pubs</span></td>
				<td><img src="img/icons/accept.png" alt="" /></td>
				<td><span class="win">Pas de pub, sauf campagne exceptionnelle</span></td>
			</tr>
			<tr>
				<td>Notifications</td>
				<td><img src="img/icons/delete.png" alt="" /></td>
				<td><span class="lose">Aucune notification</span></td>
				<td><img src="img/icons/accept.png" alt="" /></td>
				<td><span class="win">Accès au système de notifications</span></td>
			</tr>
			<tr class="alternate">
				<td>Statistiques</td>
				<td><img src="img/icons/delete.png" alt="" /></td>
				<td><span class="lose">Pas de statistiques avancées</span></td>
				<td><img src="img/icons/accept.png" alt="" /></td>
				<td><span class="win">Accès à toutes les statistiques</span></td>
			</tr>
			<tr>
				<td>Refresh</td>
				<td><img src="img/icons/delete.png" alt="" /></td>
				<td><span class="lose">Refresh lent</span></td>
				<td><img src="img/icons/accept.png" alt="" /></td>
				<td><span class="win">Refresh instantané et automatique</span></td>
			</tr>
			<tr class="alternate">
				<td>Listings</td>
				<td><img src="img/icons/delete.png" alt="" /></td>
				<td><span class="lose">Listings courts</span></td>
				<td><img src="img/icons/accept.png" alt="" /></td>
				<td><span class="win">Listings longs</span></td>
			</tr>
			<tr>
				<td>Friendlist</td>
				<td><img src="img/icons/delete.png" alt="" /></td>
				<td><span class="lose">Non disponible</span></td>
				<td><img src="img/icons/accept.png" alt="" /></td>
				<td><span class="win">Disponible</span></td>
			</tr>
			
		</tbody>
	</table>
<?php
	ArghPanel::end_tag();
	ArghPanel::begin_tag();
?>
	<center>
		<strong>Par AlloPass (1 allopass / mois)</strong><br /><br />
		<table border="0" width="436" height="411" style="border: 1px solid #E5E3FF;" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2" width="436">
					<table width="436" border="0" cellpadding="0" cellspacing="0">
						<tr height="27">
							<td width="127" align="left" bgcolor="#D0D0FD">
								<a href="http://www.allopass.com/" target="_blank"><img src="http://payment.allopass.com/imgweb/common/access/logo.gif" width="127" height="27" border="0" alt="Allopass"></a>
							</td>
							<td width="309" align="right" bgcolor="#D0D0FD">
								<font style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 10px; color: #000084; font-style : none; font-weight: bold; text-decoration: none;">Solution de micro paiement sécurisé<br>Secure micro payment solution</font>
							</td>
						</tr>
						<tr height="30">
							<td colspan="2" width="436" align="center" valign="middle" bgcolor="#F1F0FF">
								<font style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #000084; font-style : none; font-weight: bold; text-decoration: none;">Pour acheter ce contenu, insérez le code obtenu en cliquant sur le drapeau de votre pays</font>
								<br>
								<font style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #5E5E90; font-style : none; font-weight: bold; text-decoration: none;">To buy this content, insert your access code obtained by clicking on your country flag</font>
							</td>
						</tr>
						<tr height="2"><td colspan="2" width="436" bgcolor="#E5E3FF"></td></tr>
					</table>
				</td>
			</tr>
			<tr height="347">
				<td width="284">
					<iframe name="APsleft"  width="284" height="347" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" src="http://payment.allopass.com/acte/scripts/iframe/left.apu?ids=176058&amp;idd=509718&amp;lang=fr"></iframe>
				</td>
				<td width="152">
					<iframe name="APsright" width="152" height="347" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" src="http://payment.allopass.com/acte/scripts/iframe/right.apu?ids=176058&amp;idd=509718&amp;lang=fr"></iframe>
				</td>
			</tr>
			<tr height="5"><td colspan="2" bgcolor="#D0D0FD" width="436"></td></tr>
		</table>
	</center>
<?php
	ArghPanel::end_tag();
	ArghPanel::begin_tag();
?>
	<center>
		<!-- CB -->
		<strong>Par carte bleue (1.59&euro; / mois)</strong><br />
		<table border="0" cellpadding="0" cellspacing="0" width="149" height="80">
			<tr>
				<td width="149" height="80">
					<form name="cben" action="https://payment.allopass.com/subscription/subscribe.apu" method="POST" target="DisplaySub">
					<input type="hidden" name="ids" value=" 176058">
						<input type="hidden" name="idd" value="510025">
						<input type="hidden" name="recall" value="1">
						<input type="hidden" name="lang" value="fr">
						<input type="image" src="https://fr.allopass.com/imgweb/script/fr/cb_subscribe.gif" alt="Subscribe " onClick="window.open('','DisplaySub','toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=1,resizable=1,width=600,height=570');" border=0>
					</form>
				</td>
			</tr>
		</table>
		<br /><br />
		<form action="https://payment.allopass.com/subscription/access.apu" method="POST">
			<input type="hidden" name="ids" value="176058"/>
			<input type="hidden" name="idd" value="510025"/>
			<input type="hidden" name="recall" value="1">
			<input type="hidden" name="lang" value="fr"/>
			<table border="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td width="300" height="68" colspan="3">
					<img src="https://fr.allopass.com/imgweb/script/fr/cb_top.gif" alt=""/>
					</td>
				</tr>
				<tr>
					<td width="157" valign="middle" bgcolor="White">
						<font face="Arial,Helvetica" color="Black" size="11" Style="font-size: 12px;">
							<b>Enter your member pass</b>
						</font>
					</td>
					<td width="80" bgcolor="White">
						<input type="text" size="10" maxlength="10" value="Pass" name="code" onFocus="if (this.form.code.value=='Pass') this.form.code.value=''" style="BACKGROUND-COLOR: #E7E7E7; BORDER-BOTTOM: #000080 1px solid; BORDER-LEFT: #000080 1px solid; BORDER-RIGHT: #000080 1px solid; BORDER-TOP: #000080 1px solid; COLOR: #000080; CURSOR: text; FONT-FAMILY: Arial; FONT-SIZE: 10pt; FONT-WEIGHT:bold; LETTER-SPACING: normal; WIDTH:85; TEXT-ALIGN=center;"/>
					</td>
					<td width="58" align="center" bgcolor="White">
						<input type="button" name="APsub" value="" onClick="this.form.submit(); this.form.APsub.disabled=true;" style="border:0px;margin:0px;padding:0px;width:48px; height:18px; background:url('https://fr.allopass.com/img/bt_ok.png');"/>
					</td>
				</tr>
				<tr>
					<td colspan="3" width="300" height="13">
						<img src="https://payment.allopass.com/img/cb_bot.gif" alt=""/>
					</td>
				</tr>
			</table>
		</form>
	</center>
<?php
	}
	ArghPanel::end_tag();
?>