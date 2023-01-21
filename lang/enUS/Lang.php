<?php
abstract class Lang {
	
	const ARGH_DOTA_LEAGUE = 'Argh DotA League';
	const ARGH_TITLE = 'www.dota.fr / Argh DotA League - ';
	const ARGH_SHORT_DESCRIPTION = 'French DotA League';
	const ARGH_DESCRIPTION = 'French DotA League, Defense of the Ancients mod of Warcraft 3 : the Frozen Throne';
	const ARGH_KEYWORDS = 'dota, fr, league, ligues, leagues, ligue, france, french, defense, of, the, ancients, tournoi, competition, competitions, aAa, allstars, koth, eSport, guides, strategie, replays, replay, maps, changelog, arghdotaleague, argh, fra, mym, dl, dota-league, forum, news, infos, srs, eswc, ladder, dota-league';
	const ARGH_FOOTER = 'www.dota.fr / Argh DotA League - &copy;2006-2010 - <a href="mailto:arghcontact@gmail.com">Contact</a>';
	
	const REMEMBER_ME = 'remember me';
	const LOG_IN = 'Username';
	const LOG_OUT = 'Logout';
	const USER_FIELD = 'Login';
	const PASSWORD_FIELD = 'Pass';
	const MEMBER_SPACE = 'Profile';
	const NOTIFICATIONS = 'Notifications';
	const FORGOTTEN_PASSWORD = 'Lost your password';
	
	const MENU = 'Menu';
	const MENU_HOME = 'Home';
	const MENU_NEWS_ARCHIVES = 'News archive';
	const MENU_REGISTRATION = 'Register';
	const MENU_STAFF = 'Staff';
	const MENU_FORUM = 'Forum';
	const MENU_SPONSORS = 'Sponsors';
	const MENU_PLAYERS = 'Members';
	const MENU_TEAMS = 'Teams';
	const MENU_LEAGUE_TIME = 'League Time';
	const REPLAY_CENTER = 'Replay Center';
	const REPLAY_UPLOAD = 'Replay upload';
	const ADMIN_LOG = 'Admin Log';
	const GUARDIAN_MULTIS = 'Guardian Multis';
	const SHOUTCAST = 'Shoutcast';
	const PLAYER_VOUCH = 'Player vouch';
	const PLAYER_UNVOUCH = 'Player unvouch';
	const VOUCH_MANAGEMENT = 'Vouch management';
	const VOUCHERS_MANAGEMENT = 'VIP staff management';
	const LADDER_VERSION = 'Ladder version';
	const DIVISION_CACHE = 'Division cache';
	const PICK_STATISTICS = 'Pick statistics';
	const BAN_STATISTICS = 'Ban statistics';
	const BAD_REPORTS = 'False result';
	const LAST_REGISTERED = 'Last registered';
	const MULTIPLE_IP = 'Multiple IP';
	const BANLIST = 'BanList';
	const NEXT_MATCHES = 'Next matches';
	const HERO_DATABASE = 'Heroes database';
	const HERO_ADDING = 'Add a hero';
	const VOUCHS_ADMIN = 'Vouchs';
	const VOUCHERS_ADMIN = 'Vouchers';
	const NEWS = 'News';
	const TOP_LEAVERS = 'Top leavers';
	const REVERSE_RANKING = 'Reverse Ranking';
	const VOUCH_LIST = 'Vouchlist';
	const WAITING_PLAYERS = 'Waiting players';
	
	const STRENGTH = 'Strength';
	const AGILITY = 'Agility';
	const INTELLIGENCE = 'Intelligence';
	
	const LADDER = 'Ladder';
	const LADDER_VIP = 'Ladder VIP';
	const LADDER_NORMAL = 'Normal ladder';
	const LADDER_RULES = 'Ladder rules';
	const LADDERVIP_RULES = 'VIP Ladder rules';
	const LADDER_STATS = 'Ladder statistics';
	const LADDER_STATUS = 'Ladder status';
	const LADDER_PLAYER_RANKING = 'Player ranking';
	const LADDER_TEAM_RANKING = 'Team ranking';
	const LADDER_JOIN = 'Join a game';
	const LADDER_HQ = 'Ladder HQ';
	const LADDER_RUNNING_GAMES = 'Current games';
	const LADDER_ALLIES = 'Ladder allies';
	const LADDER_OPPONENTS = 'Ladder foes';
	const LADDER_ADMIN = 'Ladder admin';
	const LADDER_XP = 'Ladder XP';
	const LADDER_XP_MEAN = 'Average XP';
	const LADDER_XP_MAX = 'Max XP';
	//const LADDER_TEAM_RANKING = 'Team ladder ranking';
	const LADDERVIP_STATS = 'VIP statistics';
	const LADDERVIP_RANK = 'VIP ranking';
	const LADDERVIP_JOIN = 'Join a VIP';
	const LADDERVIP_HQ = 'VIP Ladder HQ';
	const LADDERVIP_RUNNING_GAMES = 'Current VIP games';
	
	const LADDER_GAME = 'Ladder game';
	const LADDERVIP_GAME = 'VIP Ladder game';
	const LADDER_YOUR_GAME_HAS_STARTED = 'Your ladder game has started.';
	const LADDER_MUST_FILL_GARENA_ACCOUNT = 'Garena account required to join a ladder game';
	const LADDER_MINIMUM_LEVEL = 'You have to be at least level %d on Garena to play';
	const LADDER_GAME_DURATION = 'Length';
	const LADDER_GAME_STARTED_X_MINS_AGO = 'The game started %d minutes ago';
	const LADDER_ADMIN_OPENED_GAME = 'Game managed by an admin';
	const LADDER_GAME_ADMINISTRATION = 'Administration';
	const LADDER_BANS_MANAGEMENT = 'Ladder bans management';
	const LADDER_BAN_DURATION = 'Length';
	const LADDER_BANNED_ACCOUNTS = 'Banned accounts';
	const LADDER_BAN_REMAINING_LENGTH = 'Length remaining';
	const LADDER_CANT_ADMINISTRATE_GAME = 'You can\'t manage a game in which you played.';
	const LADDER_VOTES_INFORMATION = 'Results informations';
	const LADDER_EMPTY_VOTES = 'Empty the results';
	const LADDER_EMPTY_VOTES_EXPLANATION = 'Empty all the votes';
	const LADDER_FORCE_VOTES = 'Force the vote';
	const LADDER_FORCE_VOTES_EXPLANATION = 'Force 8 votes';
	const LADDER_CANCEL_RESULT = 'Cancel the result';
	const LADDER_CANCEL_RESULT_EXPLANATION = 'Cancel the game\'s result in order to enter a new one';
	const LADDER_FORCE_RESULT = 'Force the result';
	const LADDER_FORCE_RESULT_EXPLANATION = 'Set a new winner according to the player\'s votes.';
	const LADDER_BANNED_ACCOUNT = 'Your account has been banned by %s.<br />Reason: %s.';
	const LADDER_DELAY_UNTIL_UNBAN = 'Your account will be unbanned in %s.';
	const LADDER_UNBAN_LESS_1_HOUR = 'Your account will be unbanned in less than one hour.';
	const LADDER_GAME_OPENED_ON = 'Game opened the %s';
	const LADDER_GAME_CLOSED_ON = 'Game closed the %s';
	const LADDER_NO_GAME = 'No game running';
	const LADDER_MUST_WAIT_BEFORE_CLOSING = 'You must wait at least %d minutes before closing the game.';
	const LADDER_CURRENT_GAME = 'Current game - #%d';
	const LADDERVIP_CANT_PARTICIPATE = 'You can\'t participate in the VIP Ladder.<br />First, you have to be <a href="http://dota.fr/forum/viewforum.php?f=59">Vouched</a>.';
	const LADDERVIP_PICK_PHASE = 'Pick phase';
	const LADDERVIP_BAN_PHASE = 'Ban phase';
	const LADDERVIP_TO_PICK_1_PLAYER = '%s\'s turn to <b>pick</b> 1 player';
	const LADDERVIP_TO_PICK_2_PLAYERS = '%s\'s turn to <b>pick</b> 2 players';
	const LADDERVIP_TO_PICK_1_HERO = '%s\'s turn to <b>pick</b> 1 hero';
	const LADDERVIP_TO_PICK_2_HEROES = '%s\'s turn to <b>pick</b> 2 heroes';
	const LADDERVIP_TO_BAN_1_HERO = '%s\'s turn to <b>ban</b> 1 hero';
	
	const LEAGUE_RULES = 'League rules';
	const LEAGUE_WARNINGS = 'Warnings';
	const LEAGUE_WARNING_MANAGEMENT = 'Warning management';
	const LEAGUE_WARN_ADDING = 'Warn add';
	const LEAGUE_WARN_ADDED = 'The warning has been added.';
	const LEAGUE_WARN_VALUE = 'Warning value';
	const LEAGUE_STATISTICS = 'Statistics';
	const LEAGUE_HALL_OF_FAME = 'Hall of Fame';
	const LEAGUE_FORECASTS = 'Prognostics';
	const LEAGUE_ADMIN = 'League admin';
	const LEAGUE_MATCH_REPORT = 'Match report';
	const LEAGUE_MATCH = 'League match';
	
	const MAIN_NEWS = 'News';
	const MAIN_LATEST_MATCHES = 'Last matches';
	const MAIN_NEXT_SHOUTCASTS = 'Oncoming shoutcasts';
	
	const LOGGING_REQUIRED = 'You have to be logged to do this.';
	const AUTHORIZATION_REQUIRED = 'You have to be authorized to do this.';
	
	const ACCOUNT_ACTIVATION = 'Account activation';
	const ACCOUNT_ACTIVATED = '<b>%s</b>, your account has been <span class="win">activated</span>.<br />You can now log in.';
	const ACCOUNT_ACTIVATION_ERROR = 'Activation failed.<br />Please check the link written in the email.';

	const DATE_PROPOSAL = 'Schedule suggestion';
	const DATE_PROPOSED = 'The schedule %s has been suggested by %s';
	const DATE_CONFIRMED = 'Suggestion has been accepted.';
	const PROPOSE_DATE = 'Suggest a schedule';
	const PROPOSITION_ACCEPTED = 'Suggestion has been accepted the %s by <b>%s</b>';
	const ACCEPT_DATE_PROPOSAL = 'Accept the suggested schedule ?';
	
	const MESSAGE_ADDING = 'Post a message';
	const MESSAGE_ADDED = 'Message posted';
	const MESSAGE_CANT_POST = 'You can\'t post a message here';
	const MESSAGE_MUST_BE_LOGGED = 'You have to be logged to post a message.';
	const MESSAGE_NUKE = 'Warning ! This message will be definitely erased. Proceed ?';
	const MESSAGE_MODERATED_BY = 'Message moderated by %s';
	const MESSAGE_MODERATED = 'The message has been successfully moderated';
	const MESSAGE_EDITION = 'Edit message';
	const MESSAGE_SUCCESSFULLY_EDITED = 'Message successfully edited';
	
	const NEWS_MODULE = 'News plug-in';
	const NEWS_TITLE = 'News title';
	const NEWS_ARCHIVES = 'Archive';
	Const NEWS_CATEGORY = 'News category';
	const NEWS_TOURN_PARTICIPATING_PLAYERS = 'Tournament : number of participants ? (Power of 2)';
	const NEWS_TOURN_PARTICIPATING_ITEMS = 'Number of items ?';
	const NEWS_NUM_OBJET_TO_SORT = 'Number of items to sort ?';
	const NEWS_CAT_1 = 'Argh League coverage';
	const NEWS_CAT_2 = 'France';
	const NEWS_CAT_3 = 'International stage';
	const NEWS_CAT_4 = 'Downloads';
	const NEWS_CAT_5 = 'Last Standing Heroes';
	const NEWS_CAT_6 = 'ESWC';
	const NEWS_CAT_7 = 'King of the Argh';
	const NEWS_DISPLAY_NEWS = 'Display the news ?';
	const NEWS_BUMP = 'Bump the news ?';
	const NEWS_EDITOR_COMMANDS = 'Editor commands';
	const NEWS_ADD_DIVISION_RECAP = 'Add a division summary';
	const NEWS_ADD_TOURNAMENT_TREE = 'Add a tournament tree';
	const NEWS_ADD_GROUP = 'Add a tournament group';
	const NEWS_ADD_CLANWAR = 'Add a clan war';
	const NEWS_ADD_RANKING = 'Add a ranking';
	const NEWS_SUBMIT = 'Submit the news';
	const NEWS_WISH_TO_DELETE = 'Erase this news ?';
	const NEWS_CHOOSE_PERIOD = 'Choose a period';
	const NEWS_ADDING = 'Add a news';
	const NEWS_ADD_NEW_ONE = 'Add a new news';
	const NEWS_SUCCESSFULLY_ADDED = 'News successfully created.';
	const NEWS_SUCCESSFULLY_UPDATED = 'News successfully updated.';
	const NEWS_GO_TO = 'News preview';
	const NEWS_BACK_TO_MODULE = 'Back to the news plug-in';
	const NEWS_DOESNT_EXIST = 'The requested news does not exist. It has either been removed or the link you used is wrong';
	const NEWS_MASKED = 'This news is hidden';
	const NEWS_MASKED_NEWSER_MESSAGE = 'This news is hidden. If you can see it, it means you have a special access to it. Other users can\'t see it.';
	const NEWS_PERIOD = 'Period';
	const NEWSER = 'Newser';
	
	const ADMIN_TEAM = 'Team admin';
	const ADMIN_DELETE_TEAM_LOGO = 'Erase the logo';
	const ADMIN_KICK_FROM_DIVISION = 'Kick from division';
	const ADMIN_UPDATE_INFORMATION = 'Update informations';
	const ADMIN_LOGO_REMOVED = 'The logo has been successfully erased';
	const ADMIN_INFORMATION_UPDATED = 'Informations updated';
	
	const ADMIN_WARNING_REMOVAL = 'Remove warnings';
	const ADMIN_WARNING_REMOVED = 'Warning successfully removed';
	
	const ADMIN_DIVISION_CACHE_MANAGEMENT = 'Division cache management';
	const ADMIN_CURRENT_CACHE = 'Current cache';
	const ADMIN_CURRENT_DIVISIONS = 'Current divisions';
	const ADMIN_CACHE_UP_TO_DATE = 'The cache is up to date';
	const ADMIN_CACHE_OUT_OF_DATE = 'The cache isn\'t up to date';
	
	const ADMIN_DIVISIONS = 'Divisions management';
	const ADMIN_DIVISION_NAMES_MUST_BE_UNIQUE = 'Divisions names have to be unique (no repetition allowed)';
	const ADMIN_CREATE_DIVISION = 'Create a division';
	const ADMIN_DIVISION_NAME_TAKEN = 'This division already exists.';
	const ADMIN_DIVISION_START_DATE = 'Start date';
	const ADMIN_DIVISION_PLAYDAY_DELAY = 'Time period between 2 playdays';
	const ADMIN_DIVISION_DEFAULT_DATE = 'Default schedule';
	
	const ADMIN_SAME_TEAMS = 'Error : both teams are identical';
	const ADMIN_CHOOSE_MATCH_SCENARIO = 'Choose the match scenario.';
	const ADMIN_REGULAR_CASES = 'Regular cases';
	const ADMIN_SPECIAL_CASES = 'Special cases';
	const ADMIN_END_REPORT = 'End of report';
	const ADMIN_RESULT_SAVED = 'Result saved';
	
	const ADMIN_LOGGED_ACTIONS = 'Logged actions';
	const ADMIN_SHOUTCAST_MANAGEMENT = 'Shoutcast management';
	const ADMIN_SHOUTCAST_ADDING = 'Add a shoutcast';
	const ADMIN_SHOUTCAST_DATE_FORMAT = 'DD/MM hh:mm YYYY';
	
	const ADMIN_USER_MANAGEMENT = 'User management';
	const ADMIN_USER_UPDATING_PROFILE = 'Edit %s\'s profile';
	const ADMIN_USER_CANT_GIVE_ACCESS = 'You can\'t give more access than what you have (max %d)';
	const ADMIN_USER_AREA = 'This section allows you to edit a user\'s profile.';
	const ADMIN_USER_NEW_PASSWORD = 'New password';
	const ADMIN_USER_ACCOUNT_ACTIVATED_INTERR = 'Account activated ?';
	const ADMIN_USER_SITE_RANKS = 'Superadmin [100] - League admin [76] - Division admin [75] - Referee [50] - Newser [25]';
	const ADMIN_MULTIPLE_IP = 'Multiple IP';
	const ADMIN_CHOOSE_SEARCH_CRITERIA = 'Choose a search criteria';
	
	const MATCH_OPENED = 'Match opened';
	const MATCH_NOT_PLAYED = 'This match hasn\'t been played yet';
	const MATCH_WON_BY_2_0 = '%s wins 2-0';
	const MATCH_VICTORY_SENTENCE = 'Team %s wins. Both games have been played.';
	const MATCH_DRAW_SENTINEL = 'Draw. Sentinel wins both matches.';
	const MATCH_DRAW_SENTINEL_DETAILLED = 'Both games have been played, the teams tied, sentinel won twice.';
	const MATCH_DRAW_SCOURGE = 'Draw. Scourge won both games.';
	const MATCH_DRAW_SCOURGE_DETAILLED = 'Both games have been played, the teams tied, scourge won twice.';
	const MATCH_DEFAULT_WIN = '[%s] wins 2-0 by defwin.';
	const MATCH_DEFAULT_WIN_SENTENCE = 'Default win for team %s on the admin\'s decision.';
	const MATCH_ADMIN_CLOSED = 'Match closed by the admin. No points for both teams.';
	const MATCH_ADMIN_CLOSED_EXAMPLE = 'Example : teams didn\'t play the match in time.';
	const MATCH_WON_WITH_SCOURGE_DEFWIN = '%s wins 2-0, with a defwin on the Scourge side.';
	const MATCH_WON_WITH_SENTINEL_DEFWIN = '%s wins 2-0, with a defwin on the Sentinel side.';
	const MATCH_WON_WITH_SCOURGE_DEFWIN_DETAILLED = '%s wins the Sentinel side, then gets a defwin on the Scourge side. %s wins 2-0.';
	const MATCH_WON_WITH_SENTINEL_DEFWIN_DETAILLED = '%s wins the Scourge side, then gets a defwin on the Sentinel side. %s wins 2-0.';
	const MATCH_DATE_CONFIRMATION = 'Match date confirmation';
	const MATCH_DATE_REFUSAL = 'Match date refusal';
	const MATCH_DATE_REFUSED = 'Match date refused';
	const MATCH_ADMINISTRATION = 'Administration';
	const MATCH_EDIT_RESULT = 'Edit match result';
	const MATCH_LAUNCH_PARSER = 'Launch replay parser';
	const MATCH_EDIT_PICKS = 'Edit Picks / Players';
	const MATCH_EDIT_BANS = 'Edit Bans';
	const MATCH_FILES = 'Files';
	
	const DELETE_MESSAGE = 'Warning, this message is about to be permanently deleted. Proceed ?';
	
	const MEMBER_INVALID_TEAM_NAME = 'Invalid team name';
	const MEMBER_TAG_ALREADY_IN_USE = 'Tag already in use';
	const MEMBER_INVALID_TAG = 'Invalid Tag';
	const MEMBER_CHANGE_PASSWORD = 'Change password';
	const MEMBER_INVALID_CONFIRMATION_PASSWORD = 'Invalid password confirmation';
	const MEMBER_TOO_SHORT_PASSWORD = 'Password too short';
	const MEMBER_PASSWORD_CHANGED = 'Password updated';
	const MEMBER_TEAM_MANAGEMENT = 'Team management';
	
	const AVATAR_MANAGEMENT = 'Profile picture management';
	const AVATAR_SUCCESSFULLY_UPLOADED = 'Profile picture successfully uploaded';
	const AVATAR_UPLOAD_ERROR = 'Error while uploading the profile picture';
	const AVATAR_DIMENSIONS_ERROR = 'Error : incorrect profile picture size (%d x %d max), %d Kb max';
	const AVATAR_EXTENSION_ERROR = 'Error : wrong file extension (jpg, gif or png accepted)';
	const AVATAR_REQUIREMENTS = 'jpg, gif or png format, %d x %d maximum pixel size';
	const AVATAR_DELETED = 'Profile picture successfully deleted';
	const LOGO_MANAGEMENT = 'Logo management';
	const LOGO_DELETED = 'Logo successfully deleted';
	
	const FILE_UPLOAD = 'File upload';
	const FILE_SUCCESSFULLY_UPLOADED = 'File successfully uploaded';
	const FILE_UPLOAD_ERROR = 'Error while uploading file';
	const FILE_DIMENSIONS_ERROR = 'Error : incorrect picture size (%d x %d max), %d Kb max';
	const FILE_EXTENSION_ERROR = 'Error : file extension must be w3g (Replay) or jpg (Screenshot)';
	const FILE_EXTENSION_ERROR_REPLAY_ONLY = 'Error : file extension must be w3g (Replay)';
	const FILE_EXTENSION_ERROR_JPEG_ONLY = 'Error : file extension must be jpg';
	const FILE_EXTENSION_ERROR_GIF_ONLY = 'Error : file extension must be gif';
	const FILE_MAX_WEIGHT_EXCEEDED = 'Maximum size exceeded (%d Mb)';
	const FILE_REQUIREMENTS = 'file extension must be w3g or jpg, maximum size is %s Mb';
	//const FILE_DELETED = 'File successfully deleted';
	
	const TEAM_CREATED_ON = 'Team created on';
	const TEAM_JOINED_ON = 'Joined team on';
	const TEAM_JOINED = 'Congratulations, you have join the %s team';
	const TEAM_LEADER = 'Leader (Tauren)';
	const TEAM_LEAVE = 'Team leave';
	const TEAM_CREATION = 'Team creation';
	const TEAM_CANT_CREATE = 'You can\'t create a team';
	const TEAM_ENTER_VALID_NAME = 'Please enter a valid name';
	const TEAM_NAME_ALREADY_IN_USE = 'Name already in use';
	const TEAM_ENTER_VALID_TAG = 'Please enter a valide tag';
	const TEAM_TAG_ALREADY_IN_USE = 'Tag already in use';
	const TEAM_ENTER_VALID_PASSWORD = 'Please enter a valid password';
	const TEAM_SUCCESSFULLY_CREATED = 'Team successfully created !';
	const TEAM_PASSWORD_EXPLANATION = 'used to join the team';
	const TEAM_CANT_DISBAND = 'You can\'t disband your team since it is competing in division %d';
	const TEAM_DELETE = 'Team disband';
	const TEAM_DELETED = 'Team was permanently deleted';
	const TEAM_ABOUT_TO_BE_DELETED = 'is about to be permanently deleted.';
	const TEAM_JOINING = 'Join a team';
	const TEAM_ALREADY_MEMBER_OF = 'You\'re already a member of this team !';
	const TEAM_ERROR_TAUREN_CANT_JOIN = 'You\'re a team leader, please leave your position before joining a new team';
	const TEAM_CONFIRM_DELETE = 'Team is about to be permanently deleted. Proceed ?';
	const TEAM_PROBATIONARY_PERIOD = 'Probationary period';
	const TEAM_PEON_CANT_PARTICIPATE = 'You can\'t take part in the League if your status is Peon. You must have been in the team for at least 7 days.';
	const TEAM_NAME = 'Team name';
	const TEAM_ADD_OR_MODIFY_LOGO = 'Add / Modify logo';
	const TEAM_MEMBER_MANAGEMENT = 'Team member management';
	const TEAM_GIVE_LEAD = 'Give away the lead';
	const TEAM_OPPONENT = 'Opponent team';
	
	const TEAM_LEAGUE_PLANNER = 'League Planner';
	const TEAM_HOME = 'Team Home';
	const TEAM_MOTD = 'Message of the Day';
	const TEAM_ADD_DATE_PROPOSITION = 'Date proposal adding';
	const TEAM_DATE_PROPOSITION_ADDED = 'Une nouvelle proposition de date a été créée par %s. Veuillez donner votre disponibilité pour cet événement.';//Normal
	
	const RULES_ADMIN = 'Rules';
	const RULES_MANAGEMENT = 'Rules management';
	const RULES_NEW = 'New rules';
	const RULES_EDIT = 'Edit rules';
	const RULES_DELETE = 'Delete rules';
	const RULES_MODEL = 'Rules model';
	const RULES_DELETED = 'Rules successfully deleted';
	const RULES_ADDED = 'Rules successfully added.';
	const RULES_UPDATED = 'Rules successfully updated';
	
	const GOLD_NO_MORE_CREDITS = 'You have reached the games daily limit. You can subscribe to a <a href="?f=buy_gold">Gold</a> account to access to many advantages.';
	
	const SENTINEL = 'Sentinel';
	const SCOURGE = 'Scourge';
	const NEUTRAL = 'Neutral';
	const SCOURGE_HEROES = 'Scourge heroes';
	const SENTINEL_HEROES = 'Sentinel heroes';
	
	const SORT = 'Sort';
	const SORT_CHRONOLOGICAL = 'Chronological';
	const SORT_USER = 'By user';
	const SORT_ACTION = 'By action';
	const SORT_BY = 'Sort by';
	
	const REPORT_OPEN = 'Open a report';
	const REPORT_OPENING_REASONS = 'Opening reasons';
	const REPORT_INITIATOR = 'Initiator';
	const REPORT_FLAMING = 'Flame, insults';
	const REPORT_FLAMING_INFO = 'Insults, racism.';
	const REPORT_GAME_RUINING = 'Game ruining';
	const REPORT_GAME_RUINING_INFO = 'Action by a player to intentionnaly ruin the game. For instance : items destruction, on purpose feeding...';
	const REPORT_LEAVER_S_ = 'Leaver(s)';
	const REPORT_LEAVER_S_INFO = 'One or more players have quit the game before the end and have not been reported so.';
	const REPORT_BAD_RESULT = 'Bad result';
	const REPORT_BAD_RESULT_INFO = 'The final result (Sentinel, Scourge ou None) doesn\'t correspond to the real game result';
	const REPORT_OTHER = 'Other reason';
	const REPORT_OTHER_INFO = 'If your report opening reason doesn\'t fit in any other category, choose this one.';
	const REPORT_CONCERNED_PLAYERS = 'Concerned players';
	const REPORT_MANDATORY_REPLAY = 'The replay is mandatory';
	const REPORT_IMPORTANT_RULES_TITLE = 'Important rules';
	const REPORT_IMPORTANT_RULES_1 = 'The staff can decide not to handle a request if it\'s incomplete, badly explained or badly spelled. Be short, precise and check your spelling twice.';
	const REPORT_IMPORTANT_RULES_2 = 'Be sure your request is solid and that you have all the elements (replays, screens, witnesses...) for it.';
	const REPORT_IMPORTANT_RULES_3 = 'The author of any useless, unfounded request, resulting in a loss of time for an admin can be warned or banned. Opening a request is a high responsability, be sure of what you are doing.';
	const REPORT_IMPORTANT_RULES_4 = 'Be sure you have read the current rules. The lack of knowledge of it can be sanctionned.';
	const REPORT_IMPORTANT_RULES_5 = 'If you did not entered the good result, or forgot a leaver, don\'t open a report for this. Just indicate it on the ladder game chat.';
	const REPORT_IMPORTANT_RULES_6 = 'You must not ask for a punishment. Just explain the facts, and the admin will do their job.';
	const REPORT_RULES_ACKNOWLEDGE = 'I \'ve read and agreed to this rules, concerning the opening of ladder games reports.';
	const REPORT_ERROR_NOT_IN_GAME = 'Error : you did not played this game';
	const REPORT_GAME_DOESNT_EXIST = 'Error : this game does not exist';
	const REPORT_OPENED = 'Report opened';
	const REPORT_CLOSED = 'Report closed';
	const REPORT_CLOSED_ON = 'Close date';
	const REPORT_VIEW_REPORT = 'A report has been opened';
	const REPORT_REPORT_OPENED_BY = 'A report has been opened by %s';
	const REPORT_REPLAY_REMOVED = 'Replay deleted';
	const REPORT_OPENED_REPORTS = '%d opened report(s)';
	const REPORT_LAST_REPORTS = 'Last handled reports';
	const REPORT_NO_OPENED_REPORTS = 'No report';
	const REPORT_HANDLE = 'Handle';
	const REPORT_BEING_HANDLED_BY = 'Report being handled by %s';
	const REPORT_STATUS_OPENED = 'To handle';
	const REPORT_STATUS_BEING_HANDLED = 'Being handled';
	const REPORT_STATUS_REPORT_CLOSED = 'Closed';
	const REPORT_WAITING_FOR_ADMIN = 'No admin is currently handling this report.';
	const REPORT_NO_SANCTION = 'No sanction';
	const REPORT_HOST_LEAVER = 'Host Leaver - 1d';
	const REPORT_FLAME_3_DAYS = 'Flame / Insults - 3d';
	const REPORT_FLAME_7_DAYS = 'Flame / Heavy Insults - 7d';
	const REPORT_GAME_RUINING_3_DAYS = 'Game ruining - 3d';
	const REPORT_CAP_DISOBEY_3_DAYS = 'Captain disobedience - 3d';
	const REPORT_RULES_ABUSE_7_DAYS = 'Rules abuse - 7d';
	const REPORT_RAGE_LEAVE_3_DAYS = 'Rage leave - 3d';
	const REPORT_BAD_RESULT_3_DAYS = 'Bad result - 3d';
	const REPORT_BAD_RESULT_1_DAY = 'Bad result - 1d';
	const REPORT_GGC_ACCOUNT_1_DAY = 'Bad GGC account - 1d';
	const REPORT_USELESS_REPORT_1_DAY = 'Useless report - 1d';
	const REPORT_FF_BEFORE_10_MINS = 'FF before 10 mins - 1d';
	const REPORT_BUG_ABUSE_20_DAYS = 'Bug abuse - 20d';
	const REPORT_CHEATING_120_DAYS = 'Cheating / MH - 120d';
	//const REPORT_CUSTOM_BAN = '%dj';
	const REPORT_CLOSE = 'Close the report';
	const REPORT_CLOSE_TIME = 'Close date';
	const REPORT_ADMIN_COMMENT = 'Admin comment';
	const REPORT_GAME_REPORT = 'Reports';
	const REPORT_REOPEN = 'Re-open the report';
	const REPORT_NOTIFICATION = 'A ladder game report on a game where you played has been opened.';
	const REPORT_NOTIFICATION_CLOSED = 'The ladder game #%d \'s report has been closed.';
	const REPORT_BAN = 'You\'ve been sanctionned by and administrator, concerning ladder game #%d';
	const REPORT_WARN = 'You received a warning by an administrator, concerning ladder game #%d';
	const REPORT_GAME_RUINING_MINS = 'Minutes concerned';
	
	//const BANTYPE_OTHER = -1;
	//const BANTYPE_NO_STATEMENT = 0;
	const BANTYPE_FLAME = 'Flame';
	const BANTYPE_RUINING = 'Game ruining';
	const BANTYPE_RULES_ABUSE = 'Rules abuse';
	const BANTYPE_RAGE_LEAVE = 'Rage leave';
	const BANTYPE_BAD_RESULT = 'Wrong result';
	const BANTYPE_GGC_ACCOUNT = 'GGC account';
	const BANTYPE_USELESS_REPORT = 'Useless report';
	const BANTYPE_BUG_ABUSE = 'Bug abuse';
	const BANTYPE_CHEATING = 'Cheating';
	
	//@deprecated
	const ACCESS_NEWSER = 'newser';
	const ACCESS_REFEREE = 'referee';
	const ACCESS_ADMIN = 'admin';
	const ACCESS_WEBMASTER = 'webmaster';
	const ACCESS_LAN_ORGA = 'lan orga';
	const ACCESS_ADMIN_NEWS = 'news admin';
	
	const PEON = 'Peon';
	const GRUNT = 'Grunt';
	const SHAMAN = 'Shaman';
	const TAUREN = 'Tauren';
	
	const GAME_STARTED_AGO = 'Has been running for';
	const CAPTAINS = 'Captains';
	const CAPTAIN = 'Captain';
	const IP = 'IP';
	const IP_BEGINS = 'IP BEGINS';
	const IP_CONTAINS = 'IP_CONTAINS';
	const SEASON = 'Season';
	const AUTHOR = 'Author';
	const PROFILE = 'Profile';
	const LOGIN = 'Login';
	const LOGIN_SUCCESS = 'You are now logged.';
	const LOGIN_ERROR_WRONG = 'Login error';
	const LOGIN_ERROR_INACTIVE = 'Inactive account';
	const CONTINUE_WHERE_I_WERE = 'Continue where I was';
	const DIVISION = 'Division';
	const DIVISION_CHOICE = 'Division choice';
	const DIVISIONS = 'Divisions';
	const WINNER = 'Winner';
	const PICKS = 'Picks';
	const BAN = 'Ban';
	const BANS = 'Bans';
	const TOURNAMENT = 'tournament';
	const TOURNAMENT_ROUND = 'Tour';
	const TEAM = 'Team';
	const TEAMS = 'Teams';
	const TOP = 'top';
	const MID = 'mid';
	const BOTTOM = 'bottom';
	const PLAYER = 'Player';
	const PLAYERS = 'Players';
	const XP = 'XP';
	const RANKING = 'Ranking';
	const RANK = 'Rank';
	const POINTS = 'points';
	const PTS = 'pts';
	const FLAGS = 'flags';
	const PLAYDAY = 'Playday';
	const SCORE = 'Score';
	const REPORT = 'Report';
	const UNDEFINED = 'Undefined';
	const DATE = 'Date';
	const INFO = 'Info';
	const INFOS = 'Infos';
	const INFORMATION = 'Information';
	const INFORMATION_SINGULAR = 'Information';
	const LEAGUE = 'League';
	const USERS = 'User';
	const USER_OR_USERS = 'user(s)';
	const LOGO = 'Logo';
	const LOGOS = 'Logos';
	const STAFF_FUNCTION = 'Function';
	const NAME = 'Name';
	const TAG = 'Tag';
	const PASSWORD = 'Password';
	const WEBSITE = 'Website';
	const ACTION = 'Action';
	const EDIT = 'Edit';
	const FIND = 'Find';
	const GAMES = 'Played games';
	const CONTAINING = 'containing';
	const SYNCHRONISE = 'Synchronise';
	const ADMIN = 'Admin';
	const DELETE = 'Delete';
	const CREATE = 'Create';
	const NO_TEAM = 'No team.';
	const NO_MATCH = 'No match.';
	const NO_GAME = 'No game.';
	const NO_RUNNING_GAME = 'No running game';
	const NO_USER = 'No user';
	const NO_VOTE = 'No vote';
	const NO_MESSAGE = 'No message';
	const MESSAGE = 'Message';
	const NOT_VOTED = 'Not voted';
	const VOTED = 'Voted';
	const NO_DIVISION = 'None';
	const DAY = 'day';
	const DAYS = 'days';
	const FILTER = 'Filter';
	const FILTER_BY_DIVISION = 'Filter by division';
	const ALL_DIVISIONS = 'All';
	const USERNAME = 'Username';
	const BNET_ACCOUNT = 'Bnet account';
	const GARENA_ACCOUNT = 'Garena Account';
	const RGC_ACCOUNT = 'RGC Account';
	const QAUTH = 'Q account (IRC)';
	const GARENA = 'Garena';
	const EMAIL = 'Email';
	const MODE = 'Mode';
	const ERROR_IN_INPUT_PARAMETERS = 'Error in parameters';
	const MODIFICATIONS_SAVED = 'Modifications saved';
	const SLOT = 'Slot';
	const LAST_24_HOURS = 'Last 24h';
	const LAST_WEEK = 'Last week';
	const LAST_MONTH = 'Last month';
	const LAST_GAMES = 'Last games';
	const ALL_LENGTHS = 'All';
	const ALL_CATEGORIES = 'All';
	const ALL_NEWSERS = 'All';
	const CATEGORY = 'Category';
	const VOTE = 'Vote';
	const VOTES = 'votes';
	const MY_VOTE = 'My vote';
	const VALIDATE = 'Validate';
	const POSTED_BY = 'Posted by';
	const VERSUS = 'vs';
	const LINEUPS = 'Lineups';
	const MATCH_SIDE = 'Side';
	const COMMENT = 'Comment';
	const COMMENTS = 'Comments';
	const ACCESS = 'access';
	const LOOK_FOR = 'Look for';
	const RESULT = 'Result';
	const RESULTS = 'Résults';
	const AVATAR = 'Avatar';
	const CLAN_RANK = 'Rank';
	const VERSION = 'Version';
	//const VERSION_AND_MODE = 'Version & Mode';
	const REASON = 'Reason';
	const LENGTH = 'Length';
	const FILE = 'File';
	const SIZE = 'Size';
	const KILO_BYTES = 'kb';
	const WIDTH = 'Width';
	const HEIGHT = 'Height';
	const EMPTY_FORM = 'The form is empty';
	const UPLOAD = 'Upload';
	const CONFIRM = 'Confirm';
	const PASSWORD_MISMATCH = 'Passwords mismatch';
	const CASE_IMPORTANCE = 'Careful with case (HeLLo is not Hello)';
	const HERE = 'here';
	const GAME_ID = 'Game ID';
	const GAME_SHARP = 'Game #';
	const GAME = 'Game';
	const PLATFORM = 'Plateform';
	const TEAMSPEAK = 'TeamSpeak';
	const TEAMSPEAK_CHANNEL = 'TS Channel';
	const MINUTES = 'minutes';
	const MINUTES_SHORT = 'min';
	const TYPE = 'Type';
	const NB_GAMES = 'Nbr of games';
	const NB_PICKS = 'Nbr of picks';
	const NB_BANS = 'Nbr of bans';
	const WINS = 'Wins';
	const LOSSES = 'Losses';
	const LEFTS = 'Games left';
	const TIMES_NOT_SHOW_UP = 'Times did not come';
	const WIN = 'Win';
	const LOSS = 'Loss';
	const DRAW = 'Draw';
	const DRAWS = 'Draws';
	const LEFT = 'Lefts';
	const NOT_SHOW_UP = 'Did not come';
	const GAME_CLOSED = 'Closed';
	const SHOW = 'Show';
	const NO_ENTRY = 'No entry';
	const UNLIMITED = 'Unlimited';
	const LINK = 'Link';
	const MESSAGES = 'Messages';
	const JOIN_TEAM = 'Join team';
	const WROTE = 'wrote';
	const BY = 'by';
	const POSTED_ON = 'posted on';
	const VIEWS = 'views';
	const PAGES = 'pages';
	const PAGE = 'page';
	const NUKE = 'nuke';
	const LAST_EDIT_ON = 'last edition on';
	const QUOTE = 'quote';
	const LOADING = 'Loading...';
	const VOTER = 'Voter';
	const TO_VOTE = 'Vote';
	const CONCERNED_PLAYER = 'Concerned';
	const WITH = 'With';
	const UNTIL = 'until';
	const SESSION_OVER = 'Your session is over';
	const PRONOSTICS = 'Pronostics';
	const DEFAULT_DATE = 'Default date';
	const DATE_IMPOSED_BY = 'imposed by';
	const DATE_PROPOSED_BY = 'proposed by';
	const CANCEL = 'Cancel';
	const STATUS = 'Status';
	const STATE = 'State';
	const CLOSED = 'closed';
	const OPEN = 'open';
	const ACCEPT = 'Accept';
	const REFUSE = 'Refuse';
	const FILENAME = 'Filename';
	const UPLOADED_BY = 'Uploaded by';
	const UPLOAD_DATE = 'Upload date';
	const REPLAY = 'Replay';
	const SCREENSHOT = 'Screenshot';
	const SCREENSHOTS = 'Screenshots';
	const SCREENSHOT_S_ = 'Screenshot(s)';
	const WEIGHT = 'Weight';
	const ADD = 'Add';
	const CHANGE = 'Change';
	const BIRTHDATE = 'Birthdate';
	const COUNTRY = 'Country';
	const CITY = 'City';
	const ID = 'Id';
	const ROLE = 'Role';
	const FRIENDLIST = 'Friendlist';
	const FRIENDLIST_FULL = 'Friendlist is full';
	const FRIENDLIST_INFO = '';
	const ADD_FRIEND = 'Add a friend';
	const KICK = 'Kick';
	const HALL_OF_FAME = 'Hall of Fame';
	const CREATION_DATE = 'Creation date';
	const LEADER = 'Leader';
	const PASSWORD_RECOVERY = 'Password recovery';
	const USER_PASSWORD_CHANGE = 'Password change for %s';
	const RESEARCH_CRITERIAS = 'Research criterias';
	const REGISTERATION_DATE = 'Registered on';
	const GOLD = 'Gold';
	const AGE = 'Age';
	const CONFIRM_VOUCH = 'Give a vouch to this player';
	const VOUCH_VIP = 'VIP Vouched';
	const CAPLEVEL = 'CapLevel';
	const VOUCHER_VIP = 'VIP Voucher';
	const REMAINING = 'remaining';
	const VOUCH = 'vouch';
	const UNVOUCH = 'unvouch';
	const IN_LADDERGAME = 'In laddergame';
	const UPDATE = 'Update';
	const INVALID_USERNAME = 'Invalid username';
	const VALID_USERNAME = 'Valid username';
	const INVALID_PASSWORD = 'Invalid password';
	const INVALID_EMAIL = 'Invalid e-mail';
	const REGISTERATION = 'Registration';
	const REPEAT_PASSWORD = 'Repeat Password';
	const REPEAT_EMAIL = 'Repeat E-mail';
	const CORRECT = 'Correct';
	const CALENDAR = 'Calendar';
	const PLANIFIED = 'scheduled';
	const PROPOSED_DATE = 'proposed date';
	const MATCH_SHEET = 'Match overview';
	const MATCHS = 'Matchs';
	const VALUE = 'Value';
	const WARNING = 'Warning';
	const LEAGUE_RECAP = 'League recap';
	const LADDER_RECAP = 'Ladder recap';
	const RECAP = 'Recap';
	const GAME_LISTING = 'Listings';
	const LADDER_LISTING = 'Ladder Listing';
	const LADDER_VIP_LISTING = 'Ladder VIP Listing';
	const LADDER_VIP_LISTING_PICKS = 'Ladder VIP Picks Listing';
	const SANCTION = 'Penalty';
	const SANCTIONS = 'Penalties';
	const LADDER_VIP_STATS = 'VIP Statistics';
	const VIP = 'VIP';
	const COMPETITION = 'Competition';
	const PICTURE = 'Image';
	const HERO = 'Hero';
	const HEROES = 'Heroes';
	const NEWSERS = 'Newsers';
	const ERROR = 'Error';
	const CHATLOG = 'Chatlog';
	const VOUCHES = 'Vouchs';
	const ADD_VOUCHER = 'Add a voucher';
	const SELECTION = 'Selection';
	const CREDITS = 'Credits';
	const LANGUAGE = 'Language';
	const CURRENT_USERNAME = 'Current username';
	const REQUESTED_USERNAME = 'Requested username';
	const USERNAME_CHANGE_REQUESTS = 'Usernames\' change requests';
	const RATING = 'Rating';
	const TOTAL = 'Total';
	const TRANSACTIONS = 'Transactions';
	const PRODUCT = 'Product';
	const ACCOUNT = 'Account';
	const INFORMATION_SENT = 'Information already sent';
	const CUSTOM = 'Custom';
	const LEGEND = 'Legend';
	const AVAILABLE = 'Available';
	const IN_A_GAME = 'In-game';
	const IN_A_VIP_GAME = 'In VIP game';
	const IN_A_LADDER_GAME = 'In Ladder game';
	const W3_VERSION = 'Warcraft III version';
	const POOL = 'Pool';
	const MUTUAL = 'Mutual';
	const LOCK_COMMENTS = 'Lock comments ?';
	const UNSURE = 'Unsure';
	const UNAVAILABLE = 'Unavailable';
	const AUTHOR_LOCK = 'Author lock';
	const MISSING_NAME = 'Name is missing';
	const AFFILIATION = 'Affiliation';
	const UNBLOCK = 'unblock';
	const BLOCK = 'block';
	const SUSPENDED_PLAYERS = 'Suspended players';
	const PRECISE_SEARCH_CRITERIA = 'Precise a search criteria';
	const NO_PLAYER_MATCH_CRITERIA = 'No player match the search criteria';

	const LADDER_GUARDIAN_ADMIN = 'LadderGuardian';
	const LADDER_GUARDIAN_LAST_BANS = 'Last bans';
	const LADDER_GUARDIAN_PLAYERS = 'Players';
	const LADDER_GUARDIAN_UIDS = 'UIDs';
	const LADDER_GUARDIAN_IPS = 'IPs';
	const LADDER_GUARDIAN_PROXYS = 'Proxys';
	const LADDER_GUARDIAN_CONNECTS = '24h Connects';
	
	const REG_USERNAME_DESCR = 'This is your login (case sensitive)';
	const REG_PASSWORD_DESCR = 'This is your password';
	const REG_REPEAT_PASSWORD_DESCR = 'Provide your password again, for verification';
	const REG_BNET_DESCR = 'Provide your Battle.Net account (optionnal)';
	const REG_GARENA_DESCR = 'Provide your <a href="http://www.garena.com/">Garena</a> account';
	const REG_EMAIL_DESCR = 'Provide a valid e-mail address, as you\'ll need it later for activating your account. Thanks.';
	const REG_REPEAT_EMAIL_DESCR = 'Provide your e-mail address again, for verification';
	const REG_MANDATORY_FIELD = ' = mandatory field';
	const REG_RULES_READ = 'I\'ve read and agrees to the site rules:';
	const REG_ENTER_USERNAME = 'Please provide a username';
	const REG_UNAUTHORIZED_USERNAME = 'Forbidden username';
	const REG_TOO_LONG_USERNAME = 'The username is too long (25 characters max)';
	const REG_NO_SPECIAL_CHARACTERS = 'Don\'t use special characters';
	const REG_INVALID_USERNAME = 'Invalid username. (Must be alphanumerical)';
	const REG_USERNAME_ALREADY_IN_USE = 'This username is already in use';
	const REG_PASSWORD_MISMATCH = 'Passwords don\'t match';
	const REG_MAIL_MISMATCH = 'E-mail addresses don\'t match';
	const REG_ENTER_GARENA_ACCOUNT = 'Please provide a Garena account';
	const REG_GARENA_ACCOUNT_ALREADY_IN_USE = 'This Garena account is already in use';
	const REG_ENTER_PASSWORD = 'Please provide a password';
	const REG_ENTER_VALID_EMAIL = 'Please enter a valid e-mail address';
	const REG_CANT_USE_THIS_MAIL = 'This kind of e-mail address is forbidden. Please choose another one.';
	const REG_MAIL_ALREADY_IN_USE = 'This e-mail address is already in use.';
	const REG_MULTI_IP_REGISTERING_INFO = 'One or more banned users have already registered with the same IP address. Please contact and administrator to solve this issue.';
	const REG_MAIL_BODY = "You\'ve just registered on Argh DotA League. Please keep these important information:\n
		Your login: %s\n
		Your password: %s\n
		\n
		In order to activate your account, please visit: http://www.dota.fr/ligue/?f=activate&user=%s&key=%s\n\n
		Thanks for registering !\n\tThe staff.";
	const REG_MAIL_TITLE = 'Registration on Argh DotA League';
	const REG_SUCCESS = 'Registration success ! <br />An e-mail has been sent to your e-mail box, check it to activate your account.<br /><br />Also check your "spam" folder if you can\'t find the mail, it might be in it.';
	//const LEAGUE_RULES = 'Règlement ligue';
	//const LADDER_RULES = 'Règlement ladder';
	
	const ONLINE = 'Online';
	const ONLINE_VIP_PLAYERS = 'Online VIP players';
	const OFFLINE = 'Offline';
	
	const ALLOWED_EXTENSIONS = 'Allowed extensions';
	const MAXIMUM_WEIGHT = 'Max weight';
	
	const THEME = 'Theme';
	const THEME_CLASSIC = 'Classic (blue)';
	const THEME_BLACK = 'Black';
	const THEME_RED = 'Red';
	const THEME_PURPLE = 'Purple';
	const THEME_GREEN = 'Green';
	
	const PASSWORD_RECOVERY_MAIL_TITLE = 'Password recovery on Argh DotA League';
	const PASSWORD_RECOVERY_MAIL_BODY = "You (or someone else) have made a password recovery request on www.dota.fr.\n\nNothing has changed yet, go on http://dota.fr/ligue/?f=pass_recovery&mode=newpass&keycode=%s to choose a new password.\n\nThanks.";
	const PASSWORD_RECOVERY_MAIL_SENT = 'An e-mail containing information has been sent to you.';
	const PASSWORD_RECOVERY_USERNAME_INFO = 'Indicate you username';
	
	const PIE_WINS = 'Wins';
	const PIE_LOSSES = 'Losses';
	const PIE_LEFTS = 'Leaves';
	const PIE_AWAYS = 'Aways';
	const PIE_WIN = 'Win';
	const PIE_LOSS = 'Loss';
	const PIE_LEFT = 'Left';
	const PIE_AWAY = 'Away';
	const PIE_CLOSED = 'Closed';
	
	const BANNER_MANAGEMENT = 'Banners management';
	const BANNER_ADDING = 'Add a banner';
	const BANNER = 'Banner';
	const BANNERS = 'Banners';
	const BANNER_DEFAULT = 'Default';
	const WARNING_ADDED_TO = 'Warn added for %s';
	const BAN_ADDED_TO = 'Ban added for %s';
	
	const LEAVER = 'Leaver';
	const AWAY = 'Did not come';
	const BEHAVIOR = 'Bad behaviour';
	
	const GO_ON = 'Continue';
	const GO_BACK = 'Back';
	const BACK_TO_HOME = 'Back to home';
	
	const ASCENDING = 'Ascending';
	const DESCENDING = 'Descending';
	
	const ERROR_OCCURED = 'Une erreur est survenue';
	const ERROR_EMPTY_MESSAGE = 'Erreur : message vide';
	
	const CHANGENICK_7DAYS = 'Username already changed in less than a 7 days time.';
	const CHANGENICK_NEXT_OPPORTUNITY = 'Next modification available on %s';
	const CHANGENICK_PENDING_REQUEST = 'A request is already pending.';
	const CHANGENICK_REQUEST_ACCEPTED = 'Request accepted';
	const CHANGENICK_UNAVAILABLE = 'Username unavailable';
	//const CHANGENICK_NO_LADDER = 'You must not be in a ladder game in order to change you';
	
	const LADDER_STATUS_READY = 'Ready';
	const LADDER_STATUS_IN_NORMAL = 'Normal';
	const LADDER_STATUS_IN_VIP = 'VIP';
	
	const MONDAY = 'Monday';
	const TUESDAY = 'Tuesday';
	const WEDNESDAY = 'Wednesday';
	const THURSDAY = 'Thursday';
	const FRIDAY = 'Friday';
	const SATURDAY = 'Saturday';
	const SUNDAY = 'Sunday';
	
	static $DAYS_ARRAY = array(
		Lang::SUNDAY,
		Lang::MONDAY,
		Lang::TUESDAY,
		Lang::WEDNESDAY,
		Lang::THURSDAY,
		Lang::FRIDAY,
		Lang::SATURDAY
	);
	
	const JANUARY = 'January';
	const FEBRUARY = 'February';
	const MARCH = 'March';
	const APRIL = 'April';
	const MAY = 'May';
	const JUNE = 'June';
	const JULY = 'July';
	const AUGUST = 'August';
	const SEPTEMBER = 'September';
	const OCTOBER = 'October';
	const NOVEMBER = 'November';
	const DECEMBER = 'December';
	
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
	
	const YES = 'yes';
	const NO = 'no';
	const OK = 'OK';
	const WHO = 'Who';
	const WHEN = 'When';
	const WHAT = 'What';
	const NONE = 'None';
	
	const DATE_FORMAT_HOUR = "m/d/Y-G:i";
	const DATE_FORMAT_DAY = "m/d/Y";
	const DATE_FORMAT_DAY_MONTH_ONLY = "m/d";
	
	const DAY_LETTER = 'd';
	const HOUR_LETTER = 'h';
	const MINUTE_LETTER = 'min';
	const SECOND_LETTER  = 's';
	
	const MONTH_LABEL = 'Month';

	//Mis en base
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
	const ADMIN_LOG_GOLD_CREATION = 'Subscription to a gold account : <a href="?f=player_profile&player=%s">%s</a> (%s) - Code %s';
	const ADMIN_LOG_UNBAN_USER = 'Unban of %s';
	const ADMIN_LOG_DIVISION_DELETED = 'Division %s deleted (id: %d)';
	const ADMIN_LOG_DIVISION_EDITED = 'Division edited';
	const ADMIN_LOG_FILLING_BANS = 'Filling bans of match %d, tier %d';
	const ADMIN_LOG_FILLING_PICKS = 'Filling picks of match %d, tier %d';
	const ADMIN_LOG_FILLING_RESULT = 'Filling result of match %d';
	const ADMIN_LOG_PARSING_PICKS = 'Parsing picks of match %d, tier %d';
	
	const NOTIFICATION_NICK_ACCEPTED = 'Your new username "%s" has been accepted and will be changed soon';
	const NOTIFICATION_NICK_REFUSED = 'Your username request "%s" has been refused';
	
	const RIGHTS = 'Rights';
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
	const RIGHTS_SCREENSHOTS_ADMIN = 'Screenshots Admin';
	const RIGHTS_NONE = 'None';

	const ADMIN_RIGHTS_TITLE = 'Rights Management';
	const ADMIN_USERNAME_CHANGES = 'Nick Requests';
	const ADMIN_GOLD_ACCOUNTS = 'Gold accounts';
	const USERNAME_CHANGE = 'Change username';
	const KEYWORDS = 'Keywords';
	const HEROES_INVOLVED = 'Heroes involved';
	const ALREADY_VOTED = 'Already voted';
	const MODERATE = 'Moderate';

	const SEARCH_NO_CRITERIA = 'Give a search criteria';
	const SEARCH_NO_RESULT = 'No result found';
	
	const SCREENSHOTS_LAST_ONES = 'Last screenshots';
	const SCREENSHOTS_RANDOM = 'Random screenshots';
	const SCREENSHOTS_UPLOAD = 'Screenshot upload';
	const SCREENSHOTS_PENDING = 'Pending screenshots';
	const SCREENSHOTS_WAITING_FOR_VALIDATION = 'Screenshot uploaded, waiting for admin validation.';
	const SCREENSHOTS_BESTS = 'Best screenshots';
	const NO_PENDING_SCREENSHOTS = 'No pending screenshots.';
	
	const GOLD_ACCOUNT = 'Gold Account';
	const BASIC_ACCOUNT = 'Basic account';
	const GOLD_ALREADY_MEMBER = 'You already have a <span class="vip">gold</span> account. Expiration on <i>%s</i>';
	const GOLD_SUBSCRIBED = 'Congratulations <b>%s</b>, <span class="vip">gold</span> account activated !';
	
	const LADDER_STATS_ALLIES_BEST_TITLE = 'Players with whom %s gains the most xp';
	const LADDER_STATS_ALLIES_WORST_TITLE = 'Players with whom %s loses the most xp';
	const LADDER_STATS_AGAINST_BEST_TITLE = 'Players cons that %s gains the most xp';
	const LADDER_STATS_AGAINST_WORST_TITLE = 'Players cons that %s loses the most xp';
	
	const LADDERVIP_STATS_ALLIES_BEST_TITLE = 'Players with whom %s gains the most xp';
	const LADDERVIP_STATS_ALLIES_WORST_TITLE = 'Players with whom %s loses the most xp';
	const LADDERVIP_STATS_AGAINST_BEST_TITLE = 'Players cons that %s gains the most xp';
	const LADDERVIP_STATS_AGAINST_WORST_TITLE = 'Players cons that %s loses the most xp';

	const LADDER_STATS_PLAYED_LETTER = 'P';
	const LADDER_STATS_PLAYED_TITLE = 'Played games';
	const LADDER_STATS_CLOSED_LETTER = 'C';
	const LADDER_STATS_CLOSED_TITLE = 'Closed games';
	const LADDER_STATS_WIN_LETTER = 'W';
	const LADDER_STATS_WIN_TITLE = 'Wins';
	const LADDER_STATS_LOSE_LETTER = 'L';
	const LADDER_STATS_LOSE_TITLE = 'Losses';
	const LADDER_STATS_AWAY_LETTER = 'A';
	const LADDER_STATS_AWAY_TITLE = 'Aways';
	const LADDER_STATS_LEFT_LETTER = 'L';
	const LADDER_STATS_LEFT_TITLE = 'Leaves';
	const LADDER_STATS_XP_LETTER = 'XP';
	const LADDER_STATS_XP_TITLE = 'Experience';	

	const LADDER_TOP_PLAYERS = 'Ladder top players';
	const LADDERVIP_TOP_PLAYERS = 'VIP Ladder top players';
	
	const LADDER_STATS_GRAPH_XP_EVOLUTION_TITLE = 'XP Evolution';
	const LADDER_PICKS_PIE_TITLE = 'Pick statistics';

	const LADDER_VIP_PICK_CAPTAIN = 'Captain';
	const LADDER_VIP_PICK_FIRST = '1<sup>st</sup> pick';
	const LADDER_VIP_PICK_SECOND = '2<sup>nd</sup> pick';
	const LADDER_VIP_PICK_THIRD = '3<sup>rd</sup> pick';
	const LADDER_VIP_PICK_FOURTH = '4<sup>th</sup> pick';
	const LADDER_VIP_PICK_LAST = 'Last pick';

	static $LADDER_VIP_PICKS_ARRAY = array(
		Lang::LADDER_VIP_PICK_CAPTAIN,
		Lang::LADDER_VIP_PICK_FIRST,
		Lang::LADDER_VIP_PICK_SECOND,
		Lang::LADDER_VIP_PICK_THIRD,
		Lang::LADDER_VIP_PICK_FOURTH,
		Lang::LADDER_VIP_PICK_LAST
	);
	
	const PARSER = 'Replay Parser';
	const PARSER_DEFINITIONS = 'Files';

	const ADMIN_VIP_ACCESS = 'VIP Access';
	const ADMIN_VIP_NOTIFICATION_BLOCK = 'Your VIP access has been blocked. Please contant a VIP Admin through the forum.';
	const ADMIN_VIP_NOTIFICATION_UNBLOCK = 'Your VIP access has been unbloked.';

	const REPLAY_CENTER_DOWNLOAD = 'Download';
}
?>
