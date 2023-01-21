<?php

		include '/home/www/ligue/classes/CacheManager.php';
		include '/home/www/ligue/classes/BanManager.php';
		include '/home/www/ligue/classes/LadderStates.php';
        include '/home/www/ligue/mysql_connect.php';
        include '/home/www/ligue/ladder_functions.php';

        //Cloture des games Ladder
        $query = "SELECT * FROM lg_laddergames WHERE status = 'playing'";
        $table = mysql_query($query);
        if (mysql_num_rows($table) > 0) {
                while ($line = mysql_fetch_object($table)) {
                        //Close - 2h
                        if (time() - $line->opened > 7200) {
                                //Reports pour cette game
                                $none = 0;
                                $se = 0;
                                $sc = 0;
                                $sreq = "SELECT * FROM lg_winnersreports WHERE game_id = '".$line->id."'";
                                $st = mysql_query($sreq);
                                while ($sl = mysql_fetch_object($st)) {
                                        switch ($sl->winner) {
                                                case 'none':
                                                        $none++;
                                                        break;
                                                case 'se':
                                                        $se++;
                                                        break;
                                                case 'sc':
                                                        $sc++;
                                                        break;
                                                default:
                                                        break;
                                        }
                                }
                                $total = $none + $se + $sc;
                                //echo $total;
                                if ($none == max($none, $se, $sc)) {
                                        GameReporter::report($line->id, GameReporter::NO_WINNER);
                                } elseif ($se == max($none, $se, $sc)) {
                                        GameReporter::report($line->id, GameReporter::SENTINEL);
                                } elseif ($sc = max($none, $se, $sc)) {
                                        GameReporter::report($line->id, GameReporter::SCOURGE);
                                }
                        }
                }
        }

?>
