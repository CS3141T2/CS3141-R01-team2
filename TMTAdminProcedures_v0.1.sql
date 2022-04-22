DELIMITER //

create procedure addUser (email varchar(30)) 
begin 
insert into account(username) values (username);
end //

create procedure addCommunity (name varchar(30), leader varchar(30))
begin
insert into community values (name, leader);
end //

create procedure addCommunityEvent (organization varchar(30), date datetime, location varchar(30), name varchar(50), type varchar(30), description TEXT)
begin
insert into event(owner_id, date, location, name, type, description) values (organization, date, location, name, type, description);
end //

create procedure addUserEvent (owner varchar(30), date datetime, location varchar(30), name varchar(50), type varchar(30), description TEXT, private boolean)
begin
insert into indiv_event(owner_id, date, location, name, type, description, private) values (owner, date, location, name, type, description, private);
end //

create procedure addToCommunity (user varchar(30), community varchar(30))
begin
insert ignore into member(account_name, name) values (user, community);
end //

create procedure makeFriends (sourceUser varchar(30), targetUser varchar(30))
begin
insert ignore into friend(user1, user2) values (sourceUser, targetUser);
end //