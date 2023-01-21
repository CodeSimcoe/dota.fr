<?php
	require('mysql_connect.php');
	
	if (isset($_GET['player'])) {
		$player = mysql_real_escape_string(substr($_GET['player'], 0, 25));
		
		echo '<table class="simple">
			<tr><td></td><td colspan="4">&nbsp;</td><td></td></tr>
			<tr><td></td><td colspan="4">Top 25 alli&eacute;s en ladder Normal</td><td></td></tr>
			<tr><td></td><td colspan="4">&nbsp;</td><td></td></tr>
			<tr>
				<td width="25"></td>
				<th>#</th>
				<th>Joueur</th>
				<th>Nbr parties</th>
				<th>Victoires/D&eacute;faites</th>
				<td width="25"></td>
			</tr>
			<tr><td></td><td class="line" colspan="4"></td><td></td></tr>';

			$sql = "SELECT
						PlayerName,
						COUNT(*) AS 'Total',
						SUM(Wins) AS 'Wins',
						SUM(Loses) AS 'Loses'
					FROM (
						SELECT 
							GameId,
							PlayerName,
							CASE resultat 
								WHEN 'win' THEN 1
								ELSE 0
							END AS 'Wins',
							CASE resultat 
								WHEN 'lose' THEN 1
								ELSE 0
							END AS 'Loses'
						FROM (
							SELECT id GameId, p1 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p2 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p3 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p4 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p5 PlayerName FROM lg_laddergames
						) AS GamesGlobal
						INNER JOIN
							lg_ladderfollow
						ON game_id = GameId AND player = '".$player."'
						WHERE
							GameId IN (
								SELECT
									game_id
								FROM (
									SELECT
										game_id,
										resultat,
										status,
										winner,
										CASE p1 
											WHEN '".$player."' THEN 'se'
											ELSE 
												CASE p2
													WHEN '".$player."' THEN 'se'
													ELSE 
														CASE p3
															WHEN '".$player."' THEN 'se'
															ELSE 
																CASE p4
																	WHEN '".$player."' THEN 'se'
																	ELSE 
																		CASE p5
																			WHEN '".$player."' THEN 'se'
																			ELSE 'sc' 
																		END
																END
														END
												END
										END team
									FROM
										lg_ladderfollow
									INNER JOIN
										lg_laddergames
									ON 
										lg_laddergames.id = game_id
									WHERE
										player = '".$player."'
									AND	resultat IN ('win', 'lose')
									AND	status = 'closed'
									AND	winner != 'none'
									AND	xp != 0
								) AS Games	
								WHERE
									team = 'se'
							)
						UNION
						SELECT 
							GameId,
							PlayerName,
							CASE resultat 
								WHEN 'win' THEN 1
								ELSE 0
							END AS 'Wins',
							CASE resultat 
								WHEN 'lose' THEN 1
								ELSE 0
							END AS 'Loses'
						FROM (
							SELECT id GameId, p6 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p7 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p8 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p9 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p10 PlayerName FROM lg_laddergames
						) AS GamesGlobal
						INNER JOIN
							lg_ladderfollow
						ON game_id = GameId AND player = '".$player."'
						WHERE
							GameId IN (
								SELECT
									game_id
								FROM (
									SELECT
										game_id,
										resultat,
										status,
										winner,
										CASE p1 
											WHEN '".$player."' THEN 'se'
											ELSE 
												CASE p2
													WHEN '".$player."' THEN 'se'
													ELSE 
														CASE p3
															WHEN '".$player."' THEN 'se'
															ELSE 
																CASE p4
																	WHEN '".$player."' THEN 'se'
																	ELSE 
																		CASE p5
																			WHEN '".$player."' THEN 'se'
																			ELSE 'sc' 
																		END
																END
														END
												END
										END team
									FROM
										lg_ladderfollow
									INNER JOIN
										lg_laddergames
									ON 
										lg_laddergames.id = game_id
									WHERE
										player = '".$player."'
									AND	resultat IN ('win', 'lose')
									AND	status = 'closed'
									AND	winner != 'none'
									AND	xp != 0
								) AS Games	
								WHERE
									team = 'sc'
							)
					) AS PlayedWith
					GROUP BY
						PlayerName
					ORDER BY
						COUNT(*) DESC, PlayerName
					LIMIT 26";
			$t = mysql_query($sql) or die(mysql_error());
			$l = mysql_fetch_object($t);
			$i = 0;
			while ($l = mysql_fetch_object($t)) {
				$i++;
				$cell = (($i%2 == 0) ? ' class="alternate"' : '');
				echo '<tr>
						<td></td>
						<td'.$cell.'><center><i>'.$i.'.</i></center></td>
						<td'.$cell.'><center><a href="?f=player_profile&player='.$l->PlayerName.'">'.$l->PlayerName.'</a></center></td>
						<td'.$cell.'><center>'.$l->Total.'</center></td>
						<td'.$cell.'><center><span class="win">'.$l->Wins.'</span> / <span class="lose">'.$l->Loses.'</span></center></td>
						<td></td>
					</tr>';
			}
			
			
		echo '<tr><td></td><td colspan="4">&nbsp;</td><td></td></tr>';
		echo '<tr><td></td><td colspan="4">&nbsp;</td><td></td></tr>';
		echo '<tr><td></td><td colspan="4">&nbsp;</td><td></td></tr>';
		echo '<tr><td></td><td colspan="4">Top 25 adversaires en ladder Normal</td><td></td></tr>
			<tr><td></td><td colspan="4">&nbsp;</td><td></td></tr>
			<tr>
				<td width="25"></td>
				<th>#</th>
				<th>Joueur</th>
				<th>Nbr parties</th>
				<th>Victoires/D&eacute;faites</th>
				<td width="25"></td>
			</tr>';
		echo '<tr><td></td><td class="line" colspan="4"></td><td></td></tr>';
		
			$sql = "SELECT
						PlayerName,
						COUNT(*) AS 'Total',
						SUM(Wins) AS 'Wins',
						SUM(Loses) AS 'Loses'
					FROM (
						SELECT 
							GameId,
							PlayerName,
							CASE resultat 
								WHEN 'win' THEN 1
								ELSE 0
							END AS 'Wins',
							CASE resultat 
								WHEN 'lose' THEN 1
								ELSE 0
							END AS 'Loses'
						FROM (
							SELECT id GameId, p1 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p2 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p3 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p4 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p5 PlayerName FROM lg_laddergames
						) AS GamesGlobal
						INNER JOIN
							lg_ladderfollow
						ON game_id = GameId AND player = '".$player."'
						WHERE
							GameId IN (
								SELECT
									game_id
								FROM (
									SELECT
										game_id,
										resultat,
										status,
										winner,
										CASE p1 
											WHEN '".$player."' THEN 'se'
											ELSE 
												CASE p2
													WHEN '".$player."' THEN 'se'
													ELSE 
														CASE p3
															WHEN '".$player."' THEN 'se'
															ELSE 
																CASE p4
																	WHEN '".$player."' THEN 'se'
																	ELSE 
																		CASE p5
																			WHEN '".$player."' THEN 'se'
																			ELSE 'sc' 
																		END
																END
														END
												END
										END team
									FROM
										lg_ladderfollow
									INNER JOIN
										lg_laddergames
									ON 
										lg_laddergames.id = game_id
									WHERE
										player = '".$player."'
									AND	resultat IN ('win', 'lose')
									AND	status = 'closed'
									AND	winner != 'none'
									AND	xp != 0
								) AS Games	
								WHERE
									team = 'sc'
							)
						UNION
						SELECT 
							GameId,
							PlayerName,
							CASE resultat 
								WHEN 'win' THEN 1
								ELSE 0
							END AS 'Wins',
							CASE resultat 
								WHEN 'lose' THEN 1
								ELSE 0
							END AS 'Loses'
						FROM (
							SELECT id GameId, p6 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p7 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p8 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p9 PlayerName FROM lg_laddergames
							UNION
							SELECT id GameId, p10 PlayerName FROM lg_laddergames
						) AS GamesGlobal
						INNER JOIN
							lg_ladderfollow
						ON game_id = GameId AND player = '".$player."'
						WHERE
							GameId IN (
								SELECT
									game_id
								FROM (
									SELECT
										game_id,
										resultat,
										status,
										winner,
										CASE p1 
											WHEN '".$player."' THEN 'se'
											ELSE 
												CASE p2
													WHEN '".$player."' THEN 'se'
													ELSE 
														CASE p3
															WHEN '".$player."' THEN 'se'
															ELSE 
																CASE p4
																	WHEN '".$player."' THEN 'se'
																	ELSE 
																		CASE p5
																			WHEN '".$player."' THEN 'se'
																			ELSE 'sc' 
																		END
																END
														END
												END
										END team
									FROM
										lg_ladderfollow
									INNER JOIN
										lg_laddergames
									ON 
										lg_laddergames.id = game_id
									WHERE
										player = '".$player."'
									AND	resultat IN ('win', 'lose')
									AND	status = 'closed'
									AND	winner != 'none'
									AND	xp != 0
								) AS Games	
								WHERE
									team = 'se'
							)
					) AS PlayedWith
					GROUP BY
						PlayerName
					ORDER BY
						COUNT(*) DESC, PlayerName
					LIMIT 26";
			$t = mysql_query($sql) or die(mysql_error());
			$l = mysql_fetch_object($t);
			$i = 0;
			while ($l = mysql_fetch_object($t)) {
				$i++;
				$cell = (($i%2 == 0) ? ' class="alternate"' : '');
				echo '<tr>
						<td></td>
						<td'.$cell.'><center><i>'.$i.'.</i></center></td>
						<td'.$cell.'><center><a href="?f=player_profile&player='.$l->PlayerName.'">'.$l->PlayerName.'</a></center></td>
						<td'.$cell.'><center>'.$l->Total.'</center></td>
						<td'.$cell.'><center><span class="win">'.$l->Wins.'</span> / <span class="lose">'.$l->Loses.'</span></center></td>
						<td></td>
					</tr>';
			}
			
			
		}
	?>
	</table>