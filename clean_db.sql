DELETE FROM tm_card;
DELETE FROM tm_injury;
DELETE FROM tm_play_time;
DELETE FROM tm_goal;
DELETE FROM tm_event;
DELETE FROM tm_team;
DELETE FROM tm_player WHERE tm_player.id <> 11;