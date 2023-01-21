<?php
abstract class Lang {
	
	const ARGH_DOTA_LEAGUE = 'Argh DotA League';
	const ARGH_TITLE = 'www.dota.fr / Argh DotA League - ';
	const ARGH_SHORT_DESCRIPTION = 'Ligue Française de DotA';
	const ARGH_DESCRIPTION = 'Ligue Française de DotA, Defense of the Ancients mod de Warcraft 3 Frozen Throne';
	const ARGH_KEYWORDS = 'dota, fr, league, ligues, leagues, ligue, france, française, defense, of, the, ancients, tournoi, competition, competitions, aAa, allstars, koth, eSport, guides, strategie, replays, replay, maps, 6.59d, 6.59, 6.60, changelog, arghdotaleague, argh, fra, mym, dl, dota-league, forum, news, infos, srs, aAa, eswc, ladder, dota-league';
	const ARGH_FOOTER = 'www.dota.fr / Argh DotA League - &copy;2006-2010 - <a href="mailto:arghcontact@gmail.com">Contact</a>';
	
	const REMEMBER_ME = 'rester connect&eacute;';
	const LOG_IN = 'Connexion';
	const LOG_OUT = 'D&eacute;connexion';
	const USER_FIELD = 'Login';
	const PASSWORD_FIELD = 'Passe';
	const MEMBER_SPACE = 'Espace membre';
	const NOTIFICATIONS = 'Notifications';
	const FORGOTTEN_PASSWORD = 'Mot de passe oubli&eacute;';
	
	const MENU = 'Menu';
	const MENU_HOME = 'Accueil';
	const MENU_NEWS_ARCHIVES = 'Archives de news';
	const MENU_REGISTRATION = 'Inscription';
	const MENU_STAFF = 'Staff';
	const MENU_FORUM = 'Forum';
	const MENU_SPONSORS = 'Sponsors';
	const MENU_PLAYERS = 'Joueurs';
	const MENU_TEAMS = 'Equipes';
	const MENU_LEAGUE_TIME = 'League Time';
	const REPLAY_CENTER = 'Replay Center';
	const REPLAY_UPLOAD = 'Upload de Replay';
	const ADMIN_LOG = 'Admin Log';
	const GUARDIAN_MULTIS = 'Guardian Multis';
	const SHOUTCAST = 'Shoutcast';
	const PLAYER_VOUCH = 'Vouch de joueur';
	const PLAYER_UNVOUCH = 'Unvouch de joueur';
	const VOUCH_MANAGEMENT = 'Gestion vouchs';
	const VOUCHERS_MANAGEMENT = 'Gestion staff voucher';
	const LADDER_VERSION = 'Version Ladder';
	const DIVISION_CACHE = 'Cache de division';
	const PICK_STATISTICS = 'Statistiques de pick';
	const BAN_STATISTICS = 'Statistiques de ban';
	const BAD_REPORTS = 'Mauvais reports';
	const LAST_REGISTERED = 'Derniers inscrits';
	const MULTIPLE_IP = 'IP Multiples';
	const BANLIST = 'BanList';
	const NEXT_MATCHES = 'Prochains matchs';
	const HERO_DATABASE = 'Base de h&eacute;ros';
	const HERO_ADDING = 'Ajouter un h&eacute;ros';
	const VOUCHS_ADMIN = 'Vouchs';
	const VOUCHERS_ADMIN = 'Vouchers';
	const NEWS = 'News';
	const TOP_LEAVERS = 'Top leavers';
	const REVERSE_RANKING = 'Classement inverse';
	const VOUCH_LIST = 'Liste vouchs';
	const WAITING_PLAYERS = 'Joueurs en attente';
	
	const STRENGTH = 'Force';
	const AGILITY = 'Agilit&eacute;';
	const INTELLIGENCE = 'Intelligence';
	
	const LADDER = 'Ladder';
	const LADDER_VIP = 'Ladder VIP';
	const LADDER_NORMAL = 'Ladder Normal';
	const LADDER_RULES = 'Règlement du ladder';
	const LADDERVIP_RULES = 'Règlement VIP';
	const LADDER_STATS = 'Statistiques ladder';
	const LADDER_STATUS = 'Statut Ladder';
	const LADDER_PLAYER_RANKING = 'Classement joueurs';
	const LADDER_TEAM_RANKING = 'Classement &eacute;quipes';
	const LADDER_JOIN = 'Rejoindre une partie';
	const LADDER_HQ = 'QG Ladder';
	const LADDER_RUNNING_GAMES = 'Parties en cours';
	const LADDER_ALLIES = 'Alli&eacute;s ladder';
	const LADDER_OPPONENTS = 'Ennemis ladder';
	const LADDER_ADMIN = 'Admin Ladder';
	const LADDER_XP = 'XP Ladder';
	const LADDER_XP_MEAN = 'XP moyenne';
	const LADDER_XP_MAX = 'XP max';
	//const LADDER_TEAM_RANKING = 'Classement Ladder des Clans';
	const LADDERVIP_STATS = 'Statistiques VIP';
	const LADDERVIP_RANK = 'Classement VIP';
	const LADDERVIP_JOIN = 'Rejoindre une VIP';
	const LADDERVIP_HQ = 'QG Ladder VIP';
	const LADDERVIP_RUNNING_GAMES = 'Parties VIP en cours';
	
	const LADDER_GAME = 'Partie Ladder';
	const LADDERVIP_GAME = 'Partie Ladder VIP';
	const LADDER_YOUR_GAME_HAS_STARTED = 'Votre partie ladder a commenc&eacute;.';
	const LADDER_MUST_FILL_GARENA_ACCOUNT = 'Vous devez remplir votre compte Garena pour participer au ladder';
	const LADDER_MINIMUM_LEVEL = 'Vous devez &#234;tre au moins niveau %d sur Garena pour participer au ladder';
	const LADDER_GAME_DURATION = 'Dur&eacute;e';
	const LADDER_GAME_STARTED_X_MINS_AGO = 'La partie a d&eacute;but&eacute; depuis %d minutes';
	const LADDER_ADMIN_OPENED_GAME = 'Partie en cours de gestion par administrateur';
	const LADDER_GAME_ADMINISTRATION = 'Administration';
	const LADDER_BANS_MANAGEMENT = 'Gestion des bans Ladder';
	const LADDER_BAN_DURATION = 'Dur&eacute;e';
	const LADDER_BANNED_ACCOUNTS = 'Comptes bannis du Ladder';
	const LADDER_BAN_REMAINING_LENGTH = 'Dur&eacute;e restante';
	const LADDER_CANT_ADMINISTRATE_GAME = 'Vous ne pouvez pas adminsitrer une partie ladder dans laquelle vous participez.';
	const LADDER_VOTES_INFORMATION = 'Information sur les votes';
	const LADDER_EMPTY_VOTES = 'Vider les votes';
	const LADDER_EMPTY_VOTES_EXPLANATION = 'Supprime tous les votes des joueurs sur cette partie.';
	const LADDER_FORCE_VOTES = 'Forcer le vote';
	const LADDER_FORCE_VOTES_EXPLANATION = 'Force 8 votes.';
	const LADDER_CANCEL_RESULT = 'Annuler le r&eacute;sultat';
	const LADDER_CANCEL_RESULT_EXPLANATION = 'Annule le r&eacute;sultat de la partie, pour permettre ensuite de d&eacute;finir un nouveau r&eacute;sultat.';
	const LADDER_FORCE_RESULT = 'Forcer le r&eacute;sultat';
	const LADDER_FORCE_RESULT_EXPLANATION = 'Donne un nouveau vainqueur pour la partie, en prenant en compte les votes des joueurs.';
	const LADDER_BANNED_ACCOUNT = 'Votre compte a &eacute;t&eacute; banni du ladder par %s.<br />Motif: %s.';
	const LADDER_DELAY_UNTIL_UNBAN = 'Votre compte sera de nouveau actif dans %s.';
	const LADDER_UNBAN_LESS_1_HOUR = 'Votre compte sera de nouveau actif dans moins d\'une heure.';
	const LADDER_GAME_OPENED_ON = 'Partie ouverte le %s';
	const LADDER_GAME_CLOSED_ON = 'Partie clôtur&eacute;e le %s';
	const LADDER_NO_GAME = 'Aucune partie recens&eacute;e';
	const LADDER_MUST_WAIT_BEFORE_CLOSING = 'Vous devez attendre au moins %d minutes avant de clore la partie.';
	const LADDER_CURRENT_GAME = 'Partie en cours - #%d';
	const LADDERVIP_CANT_PARTICIPATE = 'Vous ne pouvez pas participer au Ladder VIP.<br />Vous devez etre <a href="http://dota.fr/forum/viewforum.php?f=59">Vouched</a> pour participer au ladder.';
	const LADDERVIP_PICK_PHASE = 'Phase de picks';
	const LADDERVIP_BAN_PHASE = 'Phase de bans';
	const LADDERVIP_TO_PICK_1_PLAYER = 'A %s de <b>choisir</b> 1 joueur';
	const LADDERVIP_TO_PICK_2_PLAYERS = 'A %s de <b>choisir</b> 2 joueurs';
	const LADDERVIP_TO_PICK_1_HERO = 'A %s de <b>choisir</b> 1 h&eacute;ros';
	const LADDERVIP_TO_PICK_2_HEROES = 'A %s de <b>choisir</b> 2 h&eacute;ros';
	const LADDERVIP_TO_BAN_1_HERO = 'A %s\'s de <b>bannir</b> 1 h&eacute;ros';
	
	const LEAGUE_RULES = 'Règlement ligue';
	const LEAGUE_WARNINGS = 'Warnings';
	const LEAGUE_WARNING_MANAGEMENT = 'Gestion des warnings';
	const LEAGUE_WARN_ADDING = 'Ajout de warn';
	const LEAGUE_WARN_ADDED = 'La warning a &eacute;t&eacute; ajout&eacute;.';
	const LEAGUE_WARN_VALUE = 'Valeur du warning';
	const LEAGUE_STATISTICS = 'Statistiques';
	const LEAGUE_HALL_OF_FAME = 'Hall of Fame';
	const LEAGUE_FORECASTS = 'Pronostics';
	const LEAGUE_ADMIN = 'Admin Ligue';
	const LEAGUE_MATCH_REPORT = 'Rapport de match';
	const LEAGUE_MATCH = 'Match de ligue';
	
	const MAIN_NEWS = 'News';
	const MAIN_LATEST_MATCHES = 'Derniers matchs';
	const MAIN_NEXT_SHOUTCASTS = 'Prochains shoutcasts';
	
	const LOGGING_REQUIRED = 'Vous devez &#234;tre logg&eacute; pour effectuer cette action.';
	const AUTHORIZATION_REQUIRED = 'Vous devez &#234;tre autoris&eacute; pour effectuer cette action.';
	
	const ACCOUNT_ACTIVATION = 'Activation de compte';
	const ACCOUNT_ACTIVATED = '<b>%s</b>, votre compte a &eacute;t&eacute; <span class="win">activ&eacute;</span>.<br />Vous pouvez dès &agrave; pr&eacute;sent vous connecter.';
	const ACCOUNT_ACTIVATION_ERROR = 'Erreur dans les paramètres d\'activation.<br />V&eacute;rifiez le lien donn&eacute; dans l\'e-mail.';

	const DATE_PROPOSAL = 'Proposition de date';
	const DATE_PROPOSED = 'La date %s a bien &eacute;t&eacute; enregistr&eacute;e par %s';
	const DATE_CONFIRMED = 'La proposition a bien &eacute;t&eacute; accept&eacute;e.';
	const PROPOSE_DATE = 'Proposer une date';
	const PROPOSITION_ACCEPTED = 'Proposition accept&eacute;e le %s par <b>%s</b>';
	const ACCEPT_DATE_PROPOSAL = 'Accepter la date propos&eacute;e ?';
	
	const MESSAGE_ADDING = 'Ajouter un message';
	const MESSAGE_ADDED = 'Message ajout&eacute;';
	const MESSAGE_CANT_POST = 'Vous ne pouvez pas ajouter de message ici';
	const MESSAGE_MUST_BE_LOGGED = 'Vous devez &#234;tre connect&eacute; pour poster un message.';
	const MESSAGE_NUKE = 'Attention, ce message va &#234;tre supprim&eacute; d&eacute;finitivement. Continuer ?';
	const MESSAGE_MODERATED_BY = 'Message mod&eacute;r&eacute; par %s';
	const MESSAGE_MODERATED = 'Message mod&eacute;r&eacute; avec succès.';
	const MESSAGE_EDITION = 'Edition de message';
	const MESSAGE_SUCCESSFULLY_EDITED = 'Message &eacute;dit&eacute; avec succès';
	
	const NEWS_MODULE = 'Module de news';
	const NEWS_TITLE = 'Titre de la news';
	const NEWS_ARCHIVES = 'Archives';
	Const NEWS_CATEGORY = 'Cat&eacute;gorie';
	const NEWS_TOURN_PARTICIPATING_PLAYERS = 'Nombre de participants au tournoi ? (Puissance de 2)';
	const NEWS_TOURN_PARTICIPATING_ITEMS = 'Nombre d\'éléments ?';
	const NEWS_NUM_OBJET_TO_SORT = 'Nombre d\'objets &agrave; classer ?';
	const NEWS_CAT_1 = 'Coverage Argh League';
	const NEWS_CAT_2 = 'France';
	const NEWS_CAT_3 = 'Scène Internationale';
	const NEWS_CAT_4 = 'Downloads';
	const NEWS_CAT_5 = 'Last Standing Heroes';
	const NEWS_CAT_6 = 'ESWC';
	const NEWS_CAT_7 = 'King of the Argh';
	const NEWS_DISPLAY_NEWS = 'Afficher la news ?';
	const NEWS_BUMP = 'Bump la news ?';
	const NEWS_EDITOR_COMMANDS = 'Commandes &eacute;diteur';
	const NEWS_ADD_DIVISION_RECAP = 'Ajouter un r&eacute;capitulatif de division';
	const NEWS_ADD_TOURNAMENT_TREE = 'Ajouter un arbre de tournoi';
	const NEWS_ADD_GROUP = 'Ajouter une poule';
	const NEWS_ADD_CLANWAR = 'Ajouter un Clan War';
	const NEWS_ADD_RANKING = 'Ajouter un classement';
	const NEWS_SUBMIT = 'Soumettre la news';
	const NEWS_WISH_TO_DELETE = 'Voulez-vous supprimer cette news ?';
	const NEWS_CHOOSE_PERIOD = 'Choix de la p&eacute;riode';
	const NEWS_ADDING = 'Ajout de news';
	const NEWS_ADD_NEW_ONE = 'Ajouter une nouvelle news';
	const NEWS_SUCCESSFULLY_ADDED = 'La news a &eacute;t&eacute; cr&eacute;&eacute;e.';
	const NEWS_SUCCESSFULLY_UPDATED = 'La news a &eacute;t&eacute; mise &agrave; jour.';
	const NEWS_GO_TO = 'Voir la news';
	const NEWS_BACK_TO_MODULE = 'Retour au module de news';
	const NEWS_DOESNT_EXIST = 'La news demand&eacute; n\'&eacute;xiste pas. Elle a &eacute;t&eacute; soit suprim&eacute;e, soit le lien utilis&eacute; est mauvais.';
	const NEWS_MASKED = 'Cette news est masqu&eacute;e';
	const NEWS_MASKED_NEWSER_MESSAGE = 'Cette news est masqu&eacute;e, si vous pouvez la visualiser c\'est que vous disposez d\'un accès sp&eacute;cial. Elle n\'est pas accessible aux autres utilisateurs.';
	const NEWS_PERIOD = 'P&eacute;riode';
	const NEWSER = 'Newser';
	
	const ADMIN_TEAM = 'Admin Team';
	const ADMIN_DELETE_TEAM_LOGO = 'Supprimer le logo';
	const ADMIN_KICK_FROM_DIVISION = 'Kicker de la division';
	const ADMIN_UPDATE_INFORMATION = 'Mise &agrave; jour des informations';
	const ADMIN_LOGO_REMOVED = 'Le logo a bien &eacute;t&eacute; supprim&eacute;.';
	const ADMIN_INFORMATION_UPDATED = 'Informations mises &agrave; jour.';
	
	const ADMIN_WARNING_REMOVAL = 'Suppression de warnings';
	const ADMIN_WARNING_REMOVED = 'Warning supprimm&eacute; avec succès';
	
	const ADMIN_DIVISION_CACHE_MANAGEMENT = 'Gestion du cache de division';
	const ADMIN_CURRENT_CACHE = 'Cache actuel';
	const ADMIN_CURRENT_DIVISIONS = 'Divisions actuelles';
	const ADMIN_CACHE_UP_TO_DATE = 'Le cache est &agrave; jour';
	const ADMIN_CACHE_OUT_OF_DATE = 'Le cache est n\'est pas &agrave; jour';
	
	const ADMIN_DIVISIONS = 'Gestion des divisions';
	const ADMIN_DIVISION_NAMES_MUST_BE_UNIQUE = 'Les noms de divisions doivent &#234;tre uniques (pas de r&eacute;p&eacute;tition possible)';
	const ADMIN_CREATE_DIVISION = 'Cr&eacute;er une division';
	const ADMIN_DIVISION_NAME_TAKEN = 'Ce nom de division existe d&eacute;j&agrave;.';
	const ADMIN_DIVISION_START_DATE = 'Date de d&eacute;but';
	const ADMIN_DIVISION_PLAYDAY_DELAY = 'D&eacute;lai entre 2 journ&eacute;es';
	const ADMIN_DIVISION_DEFAULT_DATE = 'Heure de match par d&eacute;faut';
	
	const ADMIN_SAME_TEAMS = 'Erreur : les 2 &eacute;quipes sont identiques';
	const ADMIN_CHOOSE_MATCH_SCENARIO = 'Choisissez le cas de figure qui correspond au d&eacute;roulement de la rencontre.';
	const ADMIN_REGULAR_CASES = 'Cas r&eacute;guliers';
	const ADMIN_SPECIAL_CASES = 'Cas particuliers';
	const ADMIN_END_REPORT = 'Fin de rapport';
	const ADMIN_RESULT_SAVED = 'R&eacute;sultat enregistr&eacute;';
	
	const ADMIN_LOGGED_ACTIONS = 'Actions enregistr&eacute;es';
	const ADMIN_SHOUTCAST_MANAGEMENT = 'Gestion Shoutcasts';
	const ADMIN_SHOUTCAST_ADDING = 'Ajout de Shoutcast';
	const ADMIN_SHOUTCAST_DATE_FORMAT = 'JJ/MM hh:mm AAAA';
	
	const ADMIN_USER_MANAGEMENT = 'Gestion des utilisateurs';
	const ADMIN_USER_UPDATING_PROFILE = 'Modification du profil de %s';
	const ADMIN_USER_CANT_GIVE_ACCESS = 'Vous ne pouvez pas donner plus d\'accès que ce que vous n\'avez vous m&#234;me (max %d)';
	const ADMIN_USER_AREA = 'Cette section permet d\'&eacute;diter le profil d\'un joueur.';
	const ADMIN_USER_NEW_PASSWORD = 'Nouveau password';
	const ADMIN_USER_ACCOUNT_ACTIVATED_INTERR = 'Compte activ&eacute; ?';
	const ADMIN_USER_SITE_RANKS = 'Superadmin [100] - Admin toutes divisions [76] - Admin de division [75] - Arbitre [50] - Newser [25]';
	const ADMIN_MULTIPLE_IP = 'IP Multiples';
	const ADMIN_CHOOSE_SEARCH_CRITERIA = 'Choisissez un critère de recherche';
	
	const MATCH_OPENED = 'Match ouvert';
	const MATCH_NOT_PLAYED = 'Ce match n\'a pas encore &eacute;t&eacute; jou&eacute;';
	const MATCH_WON_BY_2_0 = '%s gagne par 2-0';
	const MATCH_VICTORY_SENTENCE = 'Victoire de la team %s. Les deux manches ont &eacute;t&eacute; jou&eacute;es.';
	const MATCH_DRAW_SENTINEL = 'Match nul. Les deux manches sont remport&eacute;es par Sentinel.';
	const MATCH_DRAW_SENTINEL_DETAILLED = 'Les deux manches ont &eacute;t&eacute; jou&eacute;es, la rencontre se solde par un match nul, voyant deux victoires de Sentinel.';
	const MATCH_DRAW_SCOURGE = 'Match nul. Les deux manches sont remport&eacute;es par Scourge.';
	const MATCH_DRAW_SCOURGE_DETAILLED = 'Les deux manches ont &eacute;t&eacute; jou&eacute;es, la rencontre se solde par un match nul, voyant deux victoires de Scourge.';
	const MATCH_DEFAULT_WIN = '[%s] gagne 2-0 par defwin.';
	const MATCH_DEFAULT_WIN_SENTENCE = 'Victoire par d&eacute;faut de la team %s suite &agrave; une d&eacute;cision d\'admin.';
	const MATCH_ADMIN_CLOSED = 'Match ferm&eacute; par admin. Aucun point pour les deux &eacute;quipes.';
	const MATCH_ADMIN_CLOSED_EXAMPLE = 'Exemple de cas: match non jou&eacute; dans les limites de temps.';
	const MATCH_WON_WITH_SCOURGE_DEFWIN = '%s gagne 2-0, en remportant la manche Scourge par defwin.';
	const MATCH_WON_WITH_SENTINEL_DEFWIN = '%s gagne 2-0, en remportant la manche Sentinel par defwin.';
	const MATCH_WON_WITH_SCOURGE_DEFWIN_DETAILLED = '%s gagne la manche Sentinel &agrave; la r&eacute;gulière, puis gagne la manche Scourge par defwin. %s gagne donc 2-0.';
	const MATCH_WON_WITH_SENTINEL_DEFWIN_DETAILLED = '%s gagne la manche Scourge &agrave; la r&eacute;gulière, puis gagne la manche Sentinel par defwin. %s gagne donc 2-0.';
	const MATCH_DATE_CONFIRMATION = 'Confirmation de date de match';
	const MATCH_DATE_REFUSAL = 'Refus de date de match';
	const MATCH_DATE_REFUSED = 'Proposition de date de match refus&eacute;e';
	const MATCH_ADMINISTRATION = 'Administration';
	const MATCH_EDIT_RESULT = 'Editer le r&eacute;sultat du Match';
	const MATCH_LAUNCH_PARSER = 'Lancer le Parser de Replays';
	const MATCH_EDIT_PICKS = 'Edition Picks / Joueurs';
	const MATCH_EDIT_BANS = 'Edition Bans';
	const MATCH_FILES = 'Fichiers';
	
	const DELETE_MESSAGE = 'Attention, ce message va &#234;tre supprim&eacute; d&eacute;finitivement. Continuer ?';
	
	const MEMBER_INVALID_TEAM_NAME = 'Nom de clan invalide.';
	const MEMBER_TAG_ALREADY_IN_USE = 'Tag d&eacute;j&agrave; utilis&eacute; par une autre &eacute;quipe';
	const MEMBER_INVALID_TAG = 'Tag invalide';
	const MEMBER_CHANGE_PASSWORD = 'Changer de mot de passe';
	const MEMBER_INVALID_CONFIRMATION_PASSWORD = 'Erreur dans la confirmation du mot de passe';
	const MEMBER_TOO_SHORT_PASSWORD = 'Mot de passe trop court';
	const MEMBER_PASSWORD_CHANGED = 'Mot de passe chang&eacute;';
	const MEMBER_TEAM_MANAGEMENT = 'Gestion de la team';
	
	const AVATAR_MANAGEMENT = 'Gestion de l\'avatar';
	const AVATAR_SUCCESSFULLY_UPLOADED = 'Image upload&eacute;e avec succès.';
	const AVATAR_UPLOAD_ERROR = 'Erreur lors de l\'upload';
	const AVATAR_DIMENSIONS_ERROR = 'Erreur dans les dimensions de l\'image (%d x %d max), %d ko max';
	const AVATAR_EXTENSION_ERROR = 'Mauvaise extension (doit &#234;tre au format jpg, gif ou png)';
	const AVATAR_REQUIREMENTS = 'format jpg, gif ou png,  taille %d x %d maximum';
	const AVATAR_DELETED = 'Avatar supprim&eacute;.';
	const LOGO_MANAGEMENT = 'Gestion du logo';
	const LOGO_DELETED = 'Logo supprim&eacute;';
	
	const FILE_UPLOAD = 'Upload de fichier';
	const FILE_SUCCESSFULLY_UPLOADED = 'Fichier upload&eacute;e avec succès.';
	const FILE_UPLOAD_ERROR = 'Erreur lors de l\'upload';
	const FILE_DIMENSIONS_ERROR = 'Erreur dans les dimensions de l\'image (%d x %d), %d ko max';
	const FILE_EXTENSION_ERROR = 'Mauvaise extension : doit &#234;tre au format w3g (Replay) ou jpg (Screenshot)';
	const FILE_EXTENSION_ERROR_REPLAY_ONLY = 'Mauvaise extension : doit &#234;tre au format w3g (Replay)';
	const FILE_EXTENSION_ERROR_JPEG_ONLY = 'Mauvaise extension : doit &#234;tre au format jpg';
	const FILE_EXTENSION_ERROR_GIF_ONLY = 'Mauvaise extension : doit &#234;tre au format gif';
	const FILE_MAX_WEIGHT_EXCEEDED = 'Poids maximum autoris&eacute; d&eacute;pass&eacute; : %d Mo maximum';
	const FILE_REQUIREMENTS = 'format w3g ou jpg, poids %s Mo maximum';
	//const FILE_DELETED = 'Fichier supprim&eacute;.';
	
	const TEAM_CREATED_ON = 'Clan cr&eacute;&eacute; le';
	const TEAM_JOINED_ON = 'A rejoint la team le';
	const TEAM_JOINED = 'F&eacute;licitations, vous avez rejoint la team %s';
	const TEAM_LEADER = 'Leader (Tauren)';
	const TEAM_LEAVE = 'Quitter votre team';
	const TEAM_CREATION = 'Cr&eacute;ation d\'une team';
	const TEAM_CANT_CREATE = 'Vous ne pouvez pas cr&eacute;er deteam';
	const TEAM_ENTER_VALID_NAME = 'Entrez un nom valide';
	const TEAM_NAME_ALREADY_IN_USE = 'Nom d&eacute;j&agrave; utilis&eacute;';
	const TEAM_ENTER_VALID_TAG = 'Entrez un tag valide';
	const TEAM_TAG_ALREADY_IN_USE = 'Tag d&eacute;j&agrave; utilis&eacute;';
	const TEAM_ENTER_VALID_PASSWORD = 'Entrez un mot de passe valide';
	const TEAM_SUCCESSFULLY_CREATED = 'Team cr&eacute;&eacute;e avec succès !';
	const TEAM_PASSWORD_EXPLANATION = 'utilis&eacute; pour rejoindre la team';
	const TEAM_CANT_DISBAND = 'Vous ne pouvez pas dissoudre votre team car elle est engag&eacute;e dans la division %d';
	const TEAM_DELETE = 'Dissoudre la team';
	const TEAM_DELETED = 'La team %s a &eacute;t&eacute; supprim&eacute; d&eacute;finitivement';
	const TEAM_ABOUT_TO_BE_DELETED = 'est sur le point d\' &#234;tre supprim&eacute;e.';
	const TEAM_JOINING = 'Rejoindre une team';
	const TEAM_ALREADY_MEMBER_OF = 'Vous &#234;tes d&eacute;j&agrave; membre de cette team !';
	const TEAM_ERROR_TAUREN_CANT_JOIN = 'Vous &#234;tes leader d\'une team, vous devez c&eacute;der votre lead avant de la quitter';
	const TEAM_CONFIRM_DELETE = 'Etes-vous sûr de vouloir supprimer votre team ?';
	const TEAM_PROBATIONARY_PERIOD = 'P&eacute;riode probatoire';
	const TEAM_PEON_CANT_PARTICIPATE = 'Vous ne pourrez participer &agrave; la league que lorsque vous ne serez plus p&eacute;on (membre de cette team depuis moins de 7 jours).';
	const TEAM_NAME = 'Nom de la team';
	const TEAM_ADD_OR_MODIFY_LOGO = 'Ajouter / Modifier le logo';
	const TEAM_MEMBER_MANAGEMENT = 'Gestion des membres';
	const TEAM_GIVE_LEAD = 'C&eacute;der le lead';
	const TEAM_OPPONENT = 'Team adverse';
	
	const TEAM_LEAGUE_PLANNER = 'Planificateur';
	const TEAM_HOME = 'Espace Team';
	const TEAM_MOTD = 'Message du Jour';
	const TEAM_ADD_DATE_PROPOSITION = 'Ajout d\'une proposition de date';
	const TEAM_DATE_PROPOSITION_ADDED = 'Une nouvelle proposition de date a été créée par %s. Veuillez donner votre disponibilité pour cet événement.';
	
	const RULES_ADMIN = 'Règlements';
	const RULES_MANAGEMENT = 'Gestion des règlements';
	const RULES_NEW = 'Nouveau règlement';
	const RULES_EDIT = 'Editer le règlement';
	const RULES_DELETE = 'Supprimer le règlement';
	const RULES_MODEL = 'Modèle';
	const RULES_DELETED = 'Le règlement a &eacute;t&eacute; supprim&eacute;';
	const RULES_ADDED = 'Le règlement a &eacute;t&eacute; ajout&eacute;e.';
	const RULES_UPDATED = 'Le Règlement a &eacute;t&eacute; &eacute;dit&eacute;';
	
	const GOLD_NO_MORE_CREDITS = 'Vous avez &eacute;puis&eacute; votre quota quotidien de parties. Souscrivez &agrave; un compte <a href="?f=buy_gold">Gold</a> pour b&eacute;n&eacute;ficier de nombreux avantages.';
	
	const SENTINEL = 'Sentinel';
	const SCOURGE = 'Scourge';
	const NEUTRAL = 'Neutre';
	const SCOURGE_HEROES = 'H&eacute;ros Scourge';
	const SENTINEL_HEROES = 'H&eacute;ros Sentinel';
	
	const SORT = 'Tri';
	const SORT_CHRONOLOGICAL = 'Chronologique';
	const SORT_USER = 'Par utilisateur';
	const SORT_ACTION = 'Par action';
	const SORT_BY = 'Trier par';
	
	const REPORT_OPEN = 'Ouvrir une r&eacute;clamation';
	const REPORT_OPENING_REASONS = 'Motif(s) d\'ourverture';
	const REPORT_INITIATOR = 'Initiateur';
	const REPORT_FLAMING = 'Flame, insultes';
	const REPORT_FLAMING_INFO = 'Insultes, injures, propos racistes ou p&eacute;joratifs.';
	const REPORT_GAME_RUINING = 'Anti-jeu';
	const REPORT_GAME_RUINING_INFO = 'Fait par un joueur de nuire volontairement au bon d&eacute;roulement de la partie. Exemple : destruction d\'items, feeding volontaire...';
	const REPORT_LEAVER_S_ = 'Leaver(s)';
	const REPORT_LEAVER_S_INFO = 'Un ou plusieurs joueurs ont quitt&eacute; la partie avant la fin, et n\'ont pas &eacute;t&eacute; report&eacute;s en tant que tel.';
	const REPORT_BAD_RESULT = 'Mauvais r&eacute;sultat';
	const REPORT_BAD_RESULT_INFO = 'Le r&eacute;sultat final du vainqueur (Sentinel, Scourge ou Aucun) ne correspond pas &agrave; l\'issue r&eacute;elle de la partie';
	const REPORT_OTHER = 'Autre motif';
	const REPORT_OTHER_INFO = 'Si le motif de la r&eacute;clamation n\'entre pas dans le cadre des autres proposition, choisissez celle-ci.';
	const REPORT_CONCERNED_PLAYERS = 'Joueurs concern&eacute;s';
	const REPORT_MANDATORY_REPLAY = 'Le replay &agrave; l\'appui est obligatoire.';
	const REPORT_IMPORTANT_RULES_TITLE = 'Règles importantes';
	const REPORT_IMPORTANT_RULES_1 = 'Le staff se r&eacute;serve le droit de ne pas traiter toute demande incomplète, mal formul&eacute;e ou mal orthographi&eacute;e. Soyez pr&eacute;cis, bref et exprimez vous dans un français intelligible.';
	const REPORT_IMPORTANT_RULES_2 = 'Soyez sûr que votre demande est fond&eacute;e et que vous disposez de tous les &eacute;l&eacute;ments (replay, screenshots) &agrave; disposition pour l\'appuyer.';
	const REPORT_IMPORTANT_RULES_3 = 'L\'auteur de toute demande inutile, infond&eacute;e, r&eacute;sultant en la perte de temps d\'un administrateur pourra &ecirc;tre sanctionn&eacute;. La cr&eacute;ation d\'une r&eacute;clamation n\'est pas anodine, r&eacute;fl&eacute;chissez-y donc &agrave; deux fois.';
	const REPORT_IMPORTANT_RULES_4 = 'Assurez-vous d\'avoir suffisamment consult&eacute; le règlement en vigueur. Toute m&eacute;connaissance flagrante du règlement pourra &ecirc;tre sanctionn&eacute;e.';
	const REPORT_IMPORTANT_RULES_5 = 'Si vous n\'avez pas entr&eacute; le bon r&eacute;sultat, ou oubli&eacute; un quelconque leaver, inutile d\'ouvrir une r&eacute;clamation : indiquez le simplement sur la feuille de match.';
	const REPORT_IMPORTANT_RULES_6 = 'Vous ne devez en aucun cas "demander" une sanction. Expliquez simplement les faits, et les administrateurs prendront les mesures n&eacute;cessaires';
	const REPORT_RULES_ACKNOWLEDGE = 'J\'affirme avoir pris connaissance du règlement et des règles r&eacute;gissant l\'ouverture de r&eacute;clamations.';
	const REPORT_ERROR_NOT_IN_GAME = 'Erreur : vous n\'avez pas particip&eacute; &agrave; cette partie.';
	const REPORT_GAME_DOESNT_EXIST = 'Erreur : la partie \'existe pas.';
	const REPORT_OPENED = 'R&eacute;clamation ouverte';
	const REPORT_CLOSED = 'R&eacute;clamation clôtur&eacute;e';
	const REPORT_CLOSED_ON = 'Date de clôture';
	const REPORT_VIEW_REPORT = 'Une r&eacute;clamation a &eacute;t&eacute; ouverte';
	const REPORT_REPORT_OPENED_BY = 'Une r&eacute;clamation a &eacute;t&eacute; ouverte par %s';
	const REPORT_REPLAY_REMOVED = 'Replay supprim&eacute;';
	const REPORT_OPENED_REPORTS = '%d r&eacute;clamation(s) ouverte(s)';
	const REPORT_LAST_REPORTS = 'Dernières r&eacute;clamations trait&eacute;es';
	const REPORT_NO_OPENED_REPORTS = 'Aucune r&eacute;clamation';
	const REPORT_HANDLE = 'Traiter';
	const REPORT_BEING_HANDLED_BY = 'R&eacute;clamation en cours de traitement par %s';
	const REPORT_STATUS_OPENED = 'A traiter';
	const REPORT_STATUS_BEING_HANDLED = 'En cours de traitement';
	const REPORT_STATUS_REPORT_CLOSED = 'Clôtur&eacute;e';
	const REPORT_WAITING_FOR_ADMIN = 'Aucun admin ne traite actuellement cette r&eacute;clamation.';
	const REPORT_NO_SANCTION = 'Aucune sanction';
	const REPORT_HOST_LEAVER = 'Host Leaver - 1j';
	const REPORT_FLAME_3_DAYS = 'Flame / Insultes - 3j';
	const REPORT_CAP_DISOBEY_3_DAYS = 'Non-respect consignes capitaine - 3j';
	const REPORT_FLAME_7_DAYS = 'Flame / Insultes lourdes - 7j';
	const REPORT_GAME_RUINING_3_DAYS = 'Anti-jeu - 3j';
	const REPORT_RULES_ABUSE_7_DAYS = 'Rules abuse - 7j';
	const REPORT_RAGE_LEAVE_3_DAYS = 'Rage leave - 3j';
	const REPORT_BAD_RESULT_3_DAYS = 'Mauvais r&eacute;sultat - 3j';
	const REPORT_BAD_RESULT_1_DAY = 'Mauvais r&eacute;sultat - 1j';
	const REPORT_GGC_ACCOUNT_1_DAY = 'Mauvais compte GGC - 1j';
	const REPORT_USELESS_REPORT_1_DAY = 'R&eacute;clam inutile - 1j';
	const REPORT_FF_BEFORE_10_MINS = 'FF avant 10 mins - 1j';
	const REPORT_BUG_ABUSE_20_DAYS = 'Bug abuse - 20j';
	const REPORT_CHEATING_120_DAYS = 'Triche / MH - 120j';
	//const REPORT_CUSTOM_BAN = '%dj';
	const REPORT_CLOSE = 'Clôturer la r&eacute;clamation';
	const REPORT_CLOSE_TIME = 'Heure de clôture';
	const REPORT_ADMIN_COMMENT = 'Commentaire de l\'admin';
	const REPORT_GAME_REPORT = 'R&eacute;clamations';
	const REPORT_REOPEN = 'R&eacute;ouvrir la r&eacute;clam';
	const REPORT_NOTIFICATION = 'Une r&eacute;clamation concernant une partie où vous avez jou&eacute; a &eacute;t&eacute; ouverte';
	const REPORT_NOTIFICATION_CLOSED = 'La r&eacute;clamation de la partie #%d a &eacute;t&eacute; clôtur&eacute;e';
	const REPORT_BAN = 'Vous avez &eacute;t&eacute; sanctionn&eacute; suite une d&eacute;cision d\'administrateur concernant la partie #%d';
	const REPORT_WARN = 'Vous avez reçu un warning suite une d&eacute;cision d\'administrateur concernant la partie #%d';
	const REPORT_GAME_RUINING_MINS = 'Minutes concernées';
	
	//const BANTYPE_OTHER = -1;
	//const BANTYPE_NO_STATEMENT = 0;
	const BANTYPE_FLAME = 'Flame';
	const BANTYPE_RUINING = 'Anti-jeu';
	const BANTYPE_RULES_ABUSE = 'Rules abuse';
	const BANTYPE_RAGE_LEAVE = 'Rage leave';
	const BANTYPE_BAD_RESULT = 'Mauvais r&eacute;sultat';
	const BANTYPE_GGC_ACCOUNT = 'Compte GGC';
	const BANTYPE_USELESS_REPORT = 'R&eacute;clamation inutile';
	const BANTYPE_BUG_ABUSE = 'Bug abuse';
	const BANTYPE_CHEATING = 'Tricherie';
	
	const ACCESS_NEWSER = 'newser';
	const ACCESS_REFEREE = 'referee';
	const ACCESS_ADMIN = 'admin';
	const ACCESS_WEBMASTER = 'webmaster';
	const ACCESS_LAN_ORGA = 'lan orga';
	const ACCESS_ADMIN_NEWS = 'admin news';
	
	const PEON = 'P&eacute;on';
	const GRUNT = 'Grunt';
	const SHAMAN = 'Shaman';
	const TAUREN = 'Tauren';
	
	const GAME_STARTED_AGO = 'Commenc&eacute;e depuis';
	const CAPTAINS = 'Capitaines';
	const CAPTAIN = 'Capitaine';
	const IP = 'IP';
	const IP_BEGINS = 'IP BEGINS';
	const IP_CONTAINS = 'IP_CONTAINS';
	const SEASON = 'Saison';
	const AUTHOR = 'Auteur';
	const PROFILE = 'Profil';
	const LOGIN = 'Login';
	const LOGIN_SUCCESS = 'Login effectu&eacute; !';
	const LOGIN_ERROR_WRONG = 'Erreur de login';
	const LOGIN_ERROR_INACTIVE = 'Compte inactif';
	const CONTINUE_WHERE_I_WERE = 'Continuer où j\'&eacute;tais';
	const DIVISION = 'Division';
	const DIVISION_CHOICE = 'Choix de la division';
	const DIVISIONS = 'Divisions';
	const WINNER = 'Vainqueur';
	const PICKS = 'Picks';
	const BAN = 'Ban';
	const BANS = 'Bans';
	const TOURNAMENT = 'tournoi';
	const TOURNAMENT_ROUND = 'Tour';
	const TEAM = 'Team';
	const TEAMS = 'Teams';
	const TOP = 'top';
	const MID = 'mid';
	const BOTTOM = 'bottom';
	const PLAYER = 'Joueur';
	const PLAYERS = 'Joueurs';
	const XP = 'XP';
	const RANKING = 'Classement';
	const RANK = 'Rang';
	const POINTS = 'points';
	const PTS = 'pts';
	const FLAGS = 'drapeaux';
	const PLAYDAY = 'Playday';
	const SCORE = 'Score';
	const REPORT = 'Rapport';
	const UNDEFINED = 'Ind&eacute;fini';
	const DATE = 'Date';
	const INFO = 'Info';
	const INFOS = 'Infos';
	const INFORMATION = 'Informations';
	const INFORMATION_SINGULAR = 'Information';
	const LEAGUE = 'Ligue';
	const USERS = 'Utilisateurs';
	const USER_OR_USERS = 'utilisateur(s)';
	const LOGO = 'Logo';
	const LOGOS = 'Logos';
	const STAFF_FUNCTION = 'Fonction';
	const NAME = 'Nom';
	const TAG = 'Tag';
	const PASSWORD = 'Password';
	const WEBSITE = 'Site internet';
	const ACTION = 'Action';
	const EDIT = 'Editer';
	const FIND = 'Rechercher';
	const GAMES = 'Parties jou&eacute;es';
	const CONTAINING = 'contenant';
	const SYNCHRONISE = 'Synchroniser';
	const ADMIN = 'Admin';
	const DELETE = 'Supprimer';
	const CREATE = 'Cr&eacute;er';
	const NO_TEAM = 'Aucune team.';
	const NO_MATCH = 'Aucun match.';
	const NO_GAME = 'Aucune partie.';
	const NO_RUNNING_GAME = 'Aucune partie en cours';
	const NO_USER = 'Aucun utilisateur';
	const NO_VOTE = 'Aucun vote';
	const NO_MESSAGE = 'Aucun message';
	const MESSAGE = 'Message';
	const NOT_VOTED = 'Non vot&eacute;';
	const VOTED = 'Vot&eacute;';
	const NO_DIVISION = 'Aucune';
	const DAY = 'jour';
	const DAYS = 'jours';
	const FILTER = 'Filtre';
	const FILTER_BY_DIVISION = 'Filtrer par division';
	const ALL_DIVISIONS = 'Toutes';
	const USERNAME = 'Username';
	const BNET_ACCOUNT = 'Compte Bnet';
	const GARENA_ACCOUNT = 'Compte Garena';
	const RGC_ACCOUNT = 'Compte RGC';
	const QAUTH = 'Compte Q (IRC)';
	const GARENA = 'Garena';
	const EMAIL = 'Email';
	const MODE = 'Mode';
	const ERROR_IN_INPUT_PARAMETERS = 'Erreur dans les paramètres';
	const MODIFICATIONS_SAVED = 'Modifications sauvegard&eacute;es';
	const SLOT = 'Slot';
	const LAST_24_HOURS = 'Dernières 24h';
	const LAST_WEEK = 'Dernière semaine';
	const LAST_MONTH = 'Dernier mois';
	const LAST_GAMES = 'Dernières parties';
	const ALL_LENGTHS = 'Tout';
	const ALL_CATEGORIES = 'Toutes';
	const ALL_NEWSERS = 'Tous';
	const CATEGORY = 'Cat&eacute;gorie';
	const VOTE = 'Vote';
	const VOTES = 'votes';
	const MY_VOTE = 'Mon vote';
	const VALIDATE = 'Valider';
	const POSTED_BY = 'Post&eacute; par';
	const VERSUS = 'vs';
	const LINEUPS = 'Lineups';
	const MATCH_SIDE = 'Manche';
	const COMMENT = 'Commentaire';
	const COMMENTS = 'Commentaires';
	const ACCESS = 'accès';
	const LOOK_FOR = 'Rechercher';
	const RESULT = 'R&eacute;sultat';
	const RESULTS = 'R&eacute;sultats';
	const AVATAR = 'Avatar';
	const CLAN_RANK = 'Grade';
	const VERSION = 'Version';
	//const VERSION_AND_MODE = 'Version & Mode';
	const REASON = 'Raison';
	const LENGTH = 'Dur&eacute;e';
	const FILE = 'Fichier';
	const SIZE = 'Taille';
	const KILO_BYTES = 'ko';
	const WIDTH = 'Largeur';
	const HEIGHT = 'Hauteur';
	const EMPTY_FORM = 'Le formulaire est vide';
	const UPLOAD = 'Uploader';
	const CONFIRM = 'Confirmer';
	const PASSWORD_MISMATCH = 'Les mots de passe ne correspondent pas';
	const CASE_IMPORTANCE = 'Attention &agrave; la casse (majuscules / miniscules)';
	const HERE = 'ici';
	const GAME_ID = 'Game ID';
	const GAME_SHARP = 'Game #';
	const GAME = 'Game';
	const PLATFORM = 'Plateforme';
	const TEAMSPEAK = 'TeamSpeak';
	const TEAMSPEAK_CHANNEL = 'Chan TS';
	const MINUTES = 'minutes';
	const MINUTES_SHORT = 'min';
	const TYPE = 'Type';
	const NB_GAMES = 'Nbr parties';
	const NB_PICKS = 'Nbr picks';
	const NB_BANS = 'Nbr bans';
	const WINS = 'Victoires';
	const LOSSES = 'D&eacute;faites';
	const LEFTS = 'parties quitt&eacute;es';
	const TIMES_NOT_SHOW_UP = 'fois non venu';
	const WIN = 'Victoire';
	const LOSS = 'D&eacute;faite';
	const DRAW = 'Nul';
	const DRAWS = 'Nuls';
	const LEFT = 'Quitt&eacute;e';
	const NOT_SHOW_UP = 'Non venu';
	const GAME_CLOSED = 'Ferm&eacute;e';
	const SHOW = 'Afficher';
	const NO_ENTRY = 'Aucune entr&eacute;e';
	const UNLIMITED = 'Illimit&eacute;';
	const LINK = 'Lien';
	const MESSAGES = 'Messages';
	const JOIN_TEAM = 'Rejoindre l\'&eacute;quipe';
	const WROTE = 'a &eacute;crit';
	const BY = 'par';
	const POSTED_ON = 'post&eacute; le';
	const VIEWS = 'vues';
	const PAGES = 'pages';
	const PAGE = 'page';
	const NUKE = 'nuke';
	const LAST_EDIT_ON = 'dernière &eacute;dition le';
	const QUOTE = 'citer';
	const LOADING = 'Chargement...';
	const VOTER = 'Votant';
	const TO_VOTE = 'Voter';
	const CONCERNED_PLAYER = 'Concern&eacute;';
	const WITH = 'Avec';
	const UNTIL = 'jusqu\'&agrave;';
	const SESSION_OVER = 'Votre session est termin&eacute;e';
	const PRONOSTICS = 'Pronostics';
	const DEFAULT_DATE = 'Date par d&eacute;faut';
	const DATE_IMPOSED_BY = 'impos&eacute;e par';
	const DATE_PROPOSED_BY = 'propos&eacute;e par';
	const CANCEL = 'Annuler';
	const STATUS = 'Statut';
	const STATE = 'Etat';
	const CLOSED = 'ferm&eacute;';
	const OPEN = 'ouvert';
	const ACCEPT = 'Accepter';
	const REFUSE = 'Refuser';
	const FILENAME = 'Nom fichier';
	const UPLOADED_BY = 'Upload&eacute; par';
	const UPLOAD_DATE = 'Date d\'upload';
	const REPLAY = 'Replay';
	const SCREENSHOT = 'Screenshot';
	const SCREENSHOTS = 'Screenshots';
	const SCREENSHOT_S_ = 'Screenshot(s)';
	const WEIGHT = 'Poids';
	const ADD = 'Ajouter';
	const CHANGE = 'Changer';
	const BIRTHDATE = 'Date de naissance';
	const COUNTRY = 'Pays';
	const CITY = 'Ville';
	const ID = 'Id';
	const ROLE = 'Rôle';
	const FRIENDLIST = 'Friendlist';
	const FRIENDLIST_FULL = 'La friendlist est pleine';
	const FRIENDLIST_INFO = '';
	const ADD_FRIEND = 'Ajouter un ami';
	const KICK = 'Kicker';
	const HALL_OF_FAME = 'Hall of Fame';
	const CREATION_DATE = 'Date de cr&eacute;ation';
	const LEADER = 'Leader';
	const PASSWORD_RECOVERY = 'R&eacute;cup&eacute;ration de mot de passe';
	const USER_PASSWORD_CHANGE = 'Changement du mot de passe de %s';
	const RESEARCH_CRITERIAS = 'Critères de recherche';
	const REGISTERATION_DATE = 'Inscrit le';
	const GOLD = 'Gold';
	const AGE = 'Age';
	const CONFIRM_VOUCH = 'Voulez-vous donner votre vouch &agrave; ce joueur ?';
	const VOUCH_VIP = 'Vouch VIP';
	const CAPLEVEL = 'CapLevel';
	const VOUCHER_VIP = 'Voucher VIP';
	const REMAINING = 'restant';
	const VOUCH = 'vouch';
	const UNVOUCH = 'unvouch';
	const IN_LADDERGAME = 'Dans la partie ladder';
	const UPDATE = 'Mettre &agrave; jour';
	const INVALID_USERNAME = 'Username invalide';
	const VALID_USERNAME = 'Username valide';
	const INVALID_PASSWORD = 'Password invalide';
	const INVALID_EMAIL = 'Email invalide';
	const REGISTERATION = 'Inscription';
	const REPEAT_PASSWORD = 'R&eacute;p&eacute;tez Password';
	const REPEAT_EMAIL = 'R&eacute;p&eacute;tez Email';
	const CORRECT = 'Corriger';
	const CALENDAR = 'Calendrier';
	const PLANIFIED = 'planifi&eacute;';
	const PROPOSED_DATE = 'date propos&eacute;e';
	const MATCH_SHEET = 'Feuille de match';
	const MATCHS = 'Matchs';
	const VALUE = 'Valeur';
	const WARNING = 'Warning';
	const LEAGUE_RECAP = 'R&eacute;sum&eacute; Ligue';
	const LADDER_RECAP = 'R&eacute;sum&eacute; Ladder';
	const RECAP = 'R&eacute;sum&eacute;';
	const GAME_LISTING = 'Listings';
	const LADDER_LISTING = 'Listing Ladder';
	const LADDER_VIP_LISTING = 'Listing Ladder VIP';
	const LADDER_VIP_LISTING_PICKS = 'Listing Picks Ladder VIP';
	const SANCTION = 'Sanction';
	const SANCTIONS = 'Sanctions';
	const LADDER_VIP_STATS = 'Statistiques VIP';
	const VIP = 'VIP';
	const COMPETITION = 'Comp&eacute;tition';
	const PICTURE = 'Image';
	const HERO = 'H&eacute;ros';
	const HEROES = 'H&eacute;ros';
	const NEWSERS = 'Newsers';
	const ERROR = 'Erreur';
	const CHATLOG = 'Chatlog';
	const VOUCHES = 'Vouchs';
	const ADD_VOUCHER = 'Ajouter un voucher';
	const SELECTION = 'S&eacute;lection';
	const CREDITS = 'Cr&eacute;dits';
	const LANGUAGE = 'Langue';
	const CURRENT_USERNAME = 'Username actuel';
	const REQUESTED_USERNAME = 'Username demand&eacute;';
	const USERNAME_CHANGE_REQUESTS = 'Demandes de changement de username en cours';
	const RATING = 'Note';
	const TOTAL = 'Total';
	const TRANSACTIONS = 'Transactions';
	const PRODUCT = 'Produit';
	const ACCOUNT = 'Compte';
	const INFORMATION_SENT = 'Informations d&eacute;j&agrave; envoy&eacute;es';
	const CUSTOM = 'Custom';
	const LEGEND = 'Légende';
	const AVAILABLE = 'Disponible';
	const IN_A_GAME = 'In-game';
	const IN_A_VIP_GAME = 'En game VIP';
	const IN_A_LADDER_GAME = 'En game Ladder';
	const W3_VERSION = 'Version Warcraft III';
	const POOL = 'Pool';
	const MUTUAL = 'Mutuel';
	const LOCK_COMMENTS = 'Verouiller les commentaires ?';
	const UNSURE = 'Incertain';
	const UNAVAILABLE = 'Indisponible';
	const AUTHOR_LOCK = 'Verrou auteur';
	const MISSING_NAME = 'Nom manquant';
	const AFFILIATION = 'Affiliation';
	const UNBLOCK = 'd&eacutebloquer';
	const BLOCK = 'bloquer';
	const SUSPENDED_PLAYERS = 'Joueurs suspendus';
	const PRECISE_SEARCH_CRITERIA = 'Précisez un critère de recherche';
	const NO_PLAYER_MATCH_CRITERIA = 'Aucun joueur ne correspond à la recherche';

	const LADDER_GUARDIAN_ADMIN = 'LadderGuardian';
	const LADDER_GUARDIAN_LAST_BANS = 'Derniers bans';
	const LADDER_GUARDIAN_PLAYERS = 'Joueurs';
	const LADDER_GUARDIAN_UIDS = 'UIDs';
	const LADDER_GUARDIAN_IPS = 'IPs';
	const LADDER_GUARDIAN_PROXYS = 'Proxys';
	const LADDER_GUARDIAN_CONNECTS = '24h Connects';
	
	const REG_USERNAME_DESCR = 'Il s\'agit de votre login (sensible &agrave; la casse)';
	const REG_PASSWORD_DESCR = 'Il s\'agit de votre mot de passe';
	const REG_REPEAT_PASSWORD_DESCR = 'Saisissez &agrave; nouveau votre mot de passe pour v&eacute;rification';
	const REG_BNET_DESCR = 'Indiquez votre compte Battle.Net (optionnel)';
	const REG_GARENA_DESCR = 'Indiquez votre compte <a href="http://www.garena.com/">Garena</a>';
	const REG_EMAIL_DESCR = 'Entrez une adresse e-mail valide, vous recevrez un e-mail de confirmation permettant d\'activer votre compte. Merci.';
	const REG_REPEAT_EMAIL_DESCR = 'Saisissez &agrave; nouveau votre adresse e-mail pour v&eacute;rification';
	const REG_MANDATORY_FIELD = ' = champ obligatoire.';
	const REG_RULES_READ = 'J\'ai lu et j\'accèpte les règlements :';
	const REG_ENTER_USERNAME = 'Entrez un nom d\'utilisateur';
	const REG_UNAUTHORIZED_USERNAME = 'Nom d\'utilisateur non autoris&eacute;';
	const REG_TOO_LONG_USERNAME = 'Nom d\'utilisateur trop long (25 caractères max)';
	const REG_NO_SPECIAL_CHARACTERS = 'N\'utilisez pas de caractères sp&eacute;ciaux';
	const REG_INVALID_USERNAME = 'Nom d\'utilisateur invalide. Veuillez entrer une chaîne de caractères';
	const REG_USERNAME_ALREADY_IN_USE = 'Ce nom d\'utilisateur est d&eacute;j&agrave; utilis&eacute;';
	const REG_PASSWORD_MISMATCH = 'Les mots de passe ne correspondent pas';
	const REG_MAIL_MISMATCH = 'Les adresses e-mail ne correspondent pas';
	const REG_ENTER_GARENA_ACCOUNT = 'Veuillez entrer un compte Garena';
	const REG_GARENA_ACCOUNT_ALREADY_IN_USE = 'Ce compte Garena est d&eacute;j&agrave; utilis&eacute;';
	const REG_ENTER_PASSWORD = 'Entrez un mot de passe';
	const REG_ENTER_VALID_EMAIL = 'Entrez une adresse e-mail valide';
	const REG_CANT_USE_THIS_MAIL = 'Vous ne pouvez pas utiliser une adresse e-mail de ce type. Veuillez utiliser une autre boite mail, merci.';
	const REG_MAIL_ALREADY_IN_USE = 'Cette adresse e-mail est d&eacute;j&agrave; utilis&eacute;e';
	const REG_MULTI_IP_REGISTERING_INFO = 'Un ou plusieurs utilisateurs bannis sont d&eacute;j&agrave; inscris avec la m&ecirc;me adresse IP.<br />Vous ne pourrez pas participer au ladder tant que vous n\'aurez pas contact&eacute; un administrateur via le forum.';
	const REG_MAIL_BODY = "Vous venez de vous inscrire sur Argh DotA League. Voici vos informations &agrave; conserver pr&eacute;cieusement:\n
		Votre login: %s\n
		Votre mot de passe: %s\n
		\n
		Pour activer votre compte, rendez-vous &agrave; l'addresse suivante: http://www.dota.fr/ligue/?f=activate&user=%s&key=%s\n\n
		Merci pour votre inscription et &agrave; très bientôt.\n\tLe staff.";
	const REG_MAIL_TITLE = 'Inscription sur Argh DotA League';
	const REG_SUCCESS = 'L\' inscritpion a &eacute;t&eacute; r&eacute;alis&eacute;e avec succès.<br />Un e-mail a &eacute;t&eacute;; envoy&eacute; &agrave; l\'adresse que vous avez indiqu&eacute; pour activer votre compte.<br /><br />Il se peut que le mail arrive dans le dossier "spam" de votre boite mail.';
	//const LEAGUE_RULES = 'Règlement ligue';
	//const LADDER_RULES = 'Règlement ladder';
	
	const ONLINE = 'Online';
	const ONLINE_VIP_PLAYERS = 'Joueurs VIP en ligne';
	const OFFLINE = 'Offline';
	
	const ALLOWED_EXTENSIONS = 'Extensions autoris&eacute;es';
	const MAXIMUM_WEIGHT = 'Poids maximum';
	
	const THEME = 'Thème';
	const THEME_CLASSIC = 'Classic (bleu)';
	const THEME_BLACK = 'Black';
	const THEME_RED = 'Red';
	const THEME_PURPLE = 'Purple';
	const THEME_GREEN = 'Green';
	
	const PASSWORD_RECOVERY_MAIL_TITLE = 'R&eacute;cup&eacute;ration de mot de passe sur Argh DotA League';
	const PASSWORD_RECOVERY_MAIL_BODY = 'Vous (ou une personne mal intentionn&eacute;e) avez fait une demande de r&eacute;cup&eacute;ration de mot de passe sur www.dota.fr .\n\nPour le moment rien n\'a &eacute;t&eacute; chang&eacute;, rendez vous sur la page http://www.dota.fr/ligue/?f=pass_recovery&mode=newpass&keycode=%s pour choisir un nouveau mot de passe.\n\nMerci.';
	const PASSWORD_RECOVERY_MAIL_SENT = 'Un e-mail a &eacute;t&eacute; envoy&eacute; contenant vos informations de connexion';
	const PASSWORD_RECOVERY_USERNAME_INFO = 'Indiquez votre nom d\'utilisateur';
	
	const PIE_WINS = 'Victoires';
	const PIE_LOSSES = 'Defaites';
	const PIE_LEFTS = 'Quittees';
	const PIE_AWAYS = 'Non venus';
	const PIE_WIN = 'Victoire';
	const PIE_LOSS = 'D&eacute;faite';
	const PIE_LEFT = 'Quitt&eacute;e';
	const PIE_AWAY = 'Non venu';
	const PIE_CLOSED = 'Ferm&eacute;e';
	
	const BANNER_MANAGEMENT = 'Gestion des Bannières';
	const BANNER_ADDING = 'Ajouter une bannière';
	const BANNER = 'Bannière';
	const BANNERS = 'Bannières';
	const BANNER_DEFAULT = 'D&eacute;faut';
	const WARNING_ADDED_TO = 'Warn ajout&eacute; pour %s';
	const BAN_ADDED_TO = 'Ban ajout&eacute; pour %s';
	
	const LEAVER = 'Leaver';
	const AWAY = 'Pas venu';
	const BEHAVIOR = 'Mauvais comportement';
	
	const GO_ON = 'Continuer';
	const GO_BACK = 'Retour';
	const BACK_TO_HOME = 'Retour &agrave; l\'acceuil';
	
	const ASCENDING = 'Ascendant';
	const DESCENDING = 'Descendant';
	
	const ERROR_OCCURED = 'Une erreur est survenue';
	const ERROR_EMPTY_MESSAGE = 'Erreur : message vide';
	
	const CHANGENICK_7DAYS = 'Username d&eacute;j&agrave; modifi&eacute; il y a moins de 7 jours.';
	const CHANGENICK_NEXT_OPPORTUNITY = 'Prochaine modification disponible le %s.';
	const CHANGENICK_PENDING_REQUEST = 'Une demande est d&eacute;j&agrave; en cours.';
	const CHANGENICK_REQUEST_ACCEPTED = 'Demande de changement accept&eacute;e';
	const CHANGENICK_UNAVAILABLE = 'Username non disponible';
	//const CHANGENICK_NO_LADDER = 'Vous ne devez pas &ecirc;tre dans une partie ladder';
	
	const LADDER_STATUS_READY = 'Ready';
	const LADDER_STATUS_IN_NORMAL = 'Normal';
	const LADDER_STATUS_IN_VIP = 'VIP';
	
	const MONDAY = 'Lundi';
	const TUESDAY = 'Mardi';
	const WEDNESDAY = 'Mercredi';
	const THURSDAY = 'Jeudi';
	const FRIDAY = 'Vendredi';
	const SATURDAY = 'Samedi';
	const SUNDAY = 'Dimanche';
	
	static $DAYS_ARRAY = array(
		Lang::SUNDAY,
		Lang::MONDAY,
		Lang::TUESDAY,
		Lang::WEDNESDAY,
		Lang::THURSDAY,
		Lang::FRIDAY,
		Lang::SATURDAY
	);

	const JANUARY = 'Janvier';
	const FEBRUARY = 'F&eacute;vrier';
	const MARCH = 'Mars';
	const APRIL = 'Avril';
	const MAY = 'Mai';
	const JUNE = 'Juin';
	const JULY = 'Juillet';
	const AUGUST = 'Ao&ucirc;t';
	const SEPTEMBER = 'Septembre';
	const OCTOBER = 'Octobre';
	const NOVEMBER = 'Novembre';
	const DECEMBER = 'D&eacute;cembre';
	
	static $MONTHS_ARRAY = array(
		Lang::JANUARY,
		Lang::FEBRUARY,
		Lang::MARCH,
		Lang::APRIL,
		Lang::MAY,
		Lang::JUNE,
		Lang::JULY,
		Lang::AUGUST,
		Lang::SEPTEMBER,
		Lang::OCTOBER,
		Lang::NOVEMBER,
		Lang::DECEMBER
	);
		
	const STREAK = 'Streak';
	const STREAK_3 = 'Winning Spree';
	const STREAK_4 = 'Dominating';
	const STREAK_5 = 'Mega-Win';
	const STREAK_6 = 'Unstoppable';
	const STREAK_7 = 'Wicked Sick';
	const STREAK_8 = 'M-m-m-monster Win';
	const STREAK_9 = 'Godlike';
	const STREAK_10 = 'Holy Shit';
	const STREAK_20 = 'Jesus';
	
	const YES = 'oui';
	const NO = 'non';
	const OK = 'OK';
	const WHO = 'Qui';
	const WHEN = 'Quand';
	const WHAT = 'Quoi';
	const NONE = 'Aucun';
	
	const DATE_FORMAT_HOUR = "d/m/Y-G:i";
	const DATE_FORMAT_DAY = "d/m/Y";
	const DATE_FORMAT_DAY_MONTH_ONLY = "d/m";
	
	const DAY_LETTER = 'j';
	const HOUR_LETTER = 'h';
	const MINUTE_LETTER = 'min';
	const SECOND_LETTER  = 's';
	
	const MONTH_LABEL = 'Mois';
	
	const ADMIN_LOG_DIVISION_KICKED = 'Kick of team <a href="?f=clanprofile&id=%d">%d</a> from it\'s division';
	const ADMIN_LOG_LOGO_REMOVED = 'Deletion of team <a href="?f=clanprofile&id=%d">%d</a>\'s logo';
	const ADMIN_LOG_TEAM_EDITED = 'Team <a href="?f=clanprofile&id=%d">%d</a>\'s profile edited';
	const ADMIN_LOG_WARNING_REMOVED = 'Warn %d deleted';
	const ADMIN_LOG_DIVISION_CACHE_UPDATED = 'Division\'s cache updated';
	const ADMIN_LOG_NEWS_REMOVED = 'News %d deleted';
	const ADMIN_LOG_MATCH_REPORT = 'Match #%d\'s report filled';
	const ADMIN_LOG_NEWS_ADDED = 'News %d created';
	const ADMIN_LOG_NEWS_UPDATED = 'News %d edited';
	const ADMIN_LOG_TEAM_WARNED = 'Team %d received a %d value warn';
	const ADMIN_LOG_USER_PROFILE_EDITED = '%s\'s profile updated';
	const ADMIN_LOG_LADDER_VERSION_MODIFIED = 'Ladder version modificated : %s with mode %s. W3 version : %s';
	const ADMIN_LOG_EMPTY_VOTES = 'Votes of game <a href="?f=ladder_game&id=%d">#%d</a> reset';
	const ADMIN_LOG_CANCEL_GAME = 'Result of game <a href="?f=ladder_game&id=%d">#%d</a>\'s cancelled';
	const ADMIN_LOG_FORCE_RESULT = 'Force result of game <a href="?f=ladder_game&id=%d">#%d</a>';
	const ADMIN_LOG_FORCE_VOTES = 'Force votes of game <a href="?f=ladder_game&id=%d">#%d</a> : %s %s';
	const ADMIN_LOG_EMPTY_VOTES_VIP = 'Votes of VIP game <a href="?f=laddervip_game&id=%d">#%d</a> reset';
	const ADMIN_LOG_CANCEL_GAME_VIP = 'Result of VIP game <a href="?f=laddervip_game&id=%d">#%d</a>\'s cancelled';
	const ADMIN_LOG_FORCE_RESULT_VIP = 'Force result of VIP game <a href="?f=laddervip_game&id=%d">#%d</a>';
	const ADMIN_LOG_FORCE_VOTES_VIP = 'Force votes of VIP game <a href="?f=laddervip_game&id=%d">#%d</a> : %s %s';
	const ADMIN_LOG_DELETE_MATCH_FILE = 'File deleted (match_id: %d)';
	const ADMIN_LOG_PLAYER_VOUCHED = 'Vouch of %s - CapLevel %d';
	const ADMIN_LOG_PLAYER_UNVOUCHED = '%s\'s unvouch';
	const ADMIN_LOG_RIGHTS_MODIFIED = 'Rights of <a href="?f=admin_rights&player=%s">%s</a> - %s';
	const ADMIN_LOG_GOLD_CREATION = 'Subscription to a gold account : <a href="?f=player_profile&player=%s">%s</a> (%s) - Code: %s';
	const ADMIN_LOG_UNBAN_USER = 'Unban of %s';
	const ADMIN_LOG_DIVISION_DELETED = 'Division %s deleted (id: %d)';
	const ADMIN_LOG_DIVISION_EDITED = 'Division edited';
	const ADMIN_LOG_FILLING_BANS = 'Filling bans of match %d';
	const ADMIN_LOG_FILLING_RESULT = 'Filling result of match %d';
	const ADMIN_LOG_FILLING_PICKS = 'Filling picks of match %d';
	const ADMIN_LOG_PARSING_PICKS = 'Parsing picks of match %d, tier %d';
	
	/*
	const ADMIN_LOG_DIVISION_KICKED = 'Kick de la team <a href="?f=team_profile&id=%d">%d</a> de sa division';
	const ADMIN_LOG_LOGO_REMOVED = 'Suppression du logo de la team <a href="?f=team_profile&id=%d">%d</a>';
	const ADMIN_LOG_TEAM_EDITED = 'Edition du profil de la team <a href="?f=team_profile&id=%d">%d</a>';
	const ADMIN_LOG_WARNING_REMOVED = 'Suppression du warn %d';
	const ADMIN_LOG_DIVISION_CACHE_UPDATED = 'Mise &agrave; jour du cache de divisions';
	const ADMIN_LOG_NEWS_REMOVED = 'Suppression news %d';
	const ADMIN_LOG_MATCH_REPORT = 'Remplissage du rapport de match #%d';
	const ADMIN_LOG_NEWS_ADDED = 'Cr&eacute;ation de la news %d';
	const ADMIN_LOG_NEWS_UPDATED = 'Edition de la news %d';
	const ADMIN_LOG_TEAM_WARNED = 'La team %d reçoit un warn de valeur %d';
	const ADMIN_LOG_USER_PROFILE_EDITED = 'Edition du profil de %s';
	const ADMIN_LOG_LADDER_VERSION_MODIFIED = 'Modification de la version ladder (%s), et du mode (%s)';
	const ADMIN_LOG_EMPTY_VOTES = 'Reset des votes de la game <a href="?f=ladder_game&id=%d">#%d</a>';
	const ADMIN_LOG_CANCEL_GAME = 'Annulation du r&eacute;sultat de la game <a href="?f=ladder_game&id=%d">#%d</a>';
	const ADMIN_LOG_FORCE_RESULT = 'Force r&eacute;sultat game <a href="?f=ladder_game&id=%d">#%d</a>';
	const ADMIN_LOG_FORCE_VOTES = 'Force votes game <a href="?f=ladder_game&id=%d">#%d</a> : %s %s';
	const ADMIN_LOG_DELETE_MATCH_FILE = 'Suppresion d\'un fichier (match id: %d)';
	const ADMIN_LOG_PLAYER_VOUCHED = 'Vouch de %s - CapLevel %d';
	const ADMIN_LOG_PLAYER_UNVOUCHED = 'Unvouch de %s';
	const ADMIN_LOG_RIGHTS_MODIFIED = 'Droits de <a href="?f=admin_rights&player=%s">%s</a> - %s';
	*/
	
	const NOTIFICATION_NICK_ACCEPTED = 'Votre nouveau username "%s" a &eacute;t&eacute; accept&eacute; et sera chang&eacute; d\'ici peu';
	const NOTIFICATION_NICK_REFUSED = 'Votre demande de username "%s" a &eacute;t&eacute; refus&eacute;e';

	const RIGHTS = 'Droits';
	const RIGHTS_WEBMASTER = 'Webmaster';
	const RIGHTS_LEAGUE_HEADADMIN = 'Head Admin League';
	const RIGHTS_LEAGUE_ADMIN = 'Admin League';
	const RIGHTS_LADDER_HEADADMIN = 'Head Admin Ladder';
	const RIGHTS_LADDER_ADMIN = 'Admin Ladder';
	const RIGHTS_VIP_HEADADMIN = 'Head Admin VIP';
	const RIGHTS_VIP_ADMIN = 'Admin VIP';
	const RIGHTS_VIP_VOUCHER = 'Voucher VIP';
	const RIGHTS_NEWS_HEADADMIN = 'Head Admin News';
	const RIGHTS_NEWS_NEWSER = 'Newser';
	const RIGHTS_SHOUTCAST_HEADADMIN = 'Head Admin Shoutcast';
	const RIGHTS_SHOUTCAST_SHOUTCASTER = 'Shoutcaster';
	const RIGHTS_LADDERGUARDIAN_ADMIN = 'Ladder Guardian Admin';
	const RIGHTS_SCREENSHOTS_ADMIN = 'Admin Screenshots';
	const RIGHTS_NONE = 'Aucun';

	const ADMIN_RIGHTS_TITLE = 'Gestion des droits';
	const ADMIN_USERNAME_CHANGES = 'Nick Requests';
	const ADMIN_GOLD_ACCOUNTS = 'Comptes gold';
	const USERNAME_CHANGE = 'Changement Username';
	const KEYWORDS = 'Mots cl&eacute;s';
	const HEROES_INVOLVED = 'H&eacute;ros concern&eacute;s';
	const ALREADY_VOTED = 'D&eacute;j&agrave; vot&eacute;';
	const MODERATE = 'Mod&eacute;rer';

	const SEARCH_NO_CRITERIA = 'Pr&eacute;cisez un critère de recherche';
	const SEARCH_NO_RESULT = 'Aucun r&eacute;sultat ne correspond &agrave; la recherche';
	
	const SCREENSHOTS_LAST_ONES = 'Derniers screenshots';
	const SCREENSHOTS_RANDOM = 'Screenshots al&eacute;atoires';
	const SCREENSHOTS_UPLOAD = 'Upload de screenshot';
	const SCREENSHOTS_PENDING = 'Screenshots en attente';
	const SCREENSHOTS_WAITING_FOR_VALIDATION = 'Screenshot upload&eacute;, en attente de validation admin.';
	const SCREENSHOTS_BESTS = 'Meilleurs screenshots';
	const NO_PENDING_SCREENSHOTS = 'Aucun screenshot en attente.';
	
	const GOLD_ACCOUNT = 'Compte Gold';
	const BASIC_ACCOUNT = 'Compte basique';
	const GOLD_ALREADY_MEMBER = 'Vous disposez d&eacute;j&agrave; d\'un compte <span class="vip">gold</span>. Expiration le <i>%s</i>';
	const GOLD_SUBSCRIBED = 'F&eacute;licitations <b>%s</b>, compte <span class="vip">gold</span> activ&eacute; !';

	const LADDER_STATS_ALLIES_BEST_TITLE = 'Les joueurs avec qui %s gagne le plus d\'xp';
	const LADDER_STATS_ALLIES_WORST_TITLE = 'Les joueurs avec qui %s perd le plus d\'xp';
	const LADDER_STATS_AGAINST_BEST_TITLE = 'Les joueurs contre qui %s gagne le plus d\'xp';
	const LADDER_STATS_AGAINST_WORST_TITLE = 'Les joueurs contre qui %s perd le plus d\'xp';

	const LADDERVIP_STATS_ALLIES_BEST_TITLE = 'Les joueurs avec qui %s gagne le plus d\'xp';
	const LADDERVIP_STATS_ALLIES_WORST_TITLE = 'Les joueurs avec qui %s perd le plus d\'xp';
	const LADDERVIP_STATS_AGAINST_BEST_TITLE = 'Les joueurs contre qui %s gagne le plus d\'xp';
	const LADDERVIP_STATS_AGAINST_WORST_TITLE = 'Les joueurs contre qui %s perd le plus d\'xp';
	
	const LADDER_STATS_PLAYED_LETTER = 'J';
	const LADDER_STATS_PLAYED_TITLE = 'Parties jou&eacute;es';
	const LADDER_STATS_CLOSED_LETTER = 'F';
	const LADDER_STATS_CLOSED_TITLE = 'Parties Ferm&eacute;es';
	const LADDER_STATS_WIN_LETTER = 'V';
	const LADDER_STATS_WIN_TITLE = 'Victoires';
	const LADDER_STATS_LOSE_LETTER = 'D';
	const LADDER_STATS_LOSE_TITLE = 'D&eacute;faites';
	const LADDER_STATS_AWAY_LETTER = 'N';
	const LADDER_STATS_AWAY_TITLE = 'Non venus';
	const LADDER_STATS_LEFT_LETTER = 'Q';
	const LADDER_STATS_LEFT_TITLE = 'Quitt&eacute;es';
	const LADDER_STATS_XP_LETTER = 'XP';
	const LADDER_STATS_XP_TITLE = 'Exp&eacute;rience';
	
	const LADDER_TOP_PLAYERS = 'Top joueurs Ladder';
	const LADDERVIP_TOP_PLAYERS = 'Top joueurs Ladder VIP';

	const LADDER_STATS_GRAPH_XP_EVOLUTION_TITLE = 'Evolution XP';
	const LADDER_PICKS_PIE_TITLE = 'Statistiques de pick';

	const LADDER_VIP_PICK_CAPTAIN = 'Capitaine';
	const LADDER_VIP_PICK_FIRST = '1<sup>er</sup> choix';
	const LADDER_VIP_PICK_SECOND = '2<sup>&egrave;me</sup> choix';
	const LADDER_VIP_PICK_THIRD = '3<sup>&egrave;me</sup> choix';
	const LADDER_VIP_PICK_FOURTH = '4<sup>&egrave;me</sup> choix';
	const LADDER_VIP_PICK_LAST = 'Dernier choix';

	static $LADDER_VIP_PICKS_ARRAY = array(
		Lang::LADDER_VIP_PICK_CAPTAIN,
		Lang::LADDER_VIP_PICK_FIRST,
		Lang::LADDER_VIP_PICK_SECOND,
		Lang::LADDER_VIP_PICK_THIRD,
		Lang::LADDER_VIP_PICK_FOURTH,
		Lang::LADDER_VIP_PICK_LAST
	);

	const PARSER = 'Replay Parser';
	const PARSER_DEFINITIONS = 'Fichiers';

	const ADMIN_VIP_ACCESS = 'Acces VIP';
	const ADMIN_VIP_NOTIFICATION_BLOCK = 'Votre accès VIP a été bloqué. Veuillez contacter un admin VIP sur le forum.';
	const ADMIN_VIP_NOTIFICATION_UNBLOCK = 'Votre accès VIP a été débloqué.';

	const REPLAY_CENTER_DOWNLOAD = 'T&eacute;l&eacute;charger';
}
?>
