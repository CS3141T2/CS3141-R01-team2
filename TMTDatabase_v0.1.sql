create table account(
	email Varchar(30) primary key,
    name varchar(50),
	code char(6) DEFAULT NULL,
	phone char(10),
    profile_description TEXT);
create table friend(
	friendID int auto_increment key,
    user1 varchar(30),
    user2 varchar(30),
    foreign key (user1) references account(email),
    foreign key (user2) references account(email));
create table community(
	name varchar(30) primary key,
	leader varchar(30),
    foreign key (leader) references account(email));
create table member(
	entry_no int auto_increment key,
	account_name Varchar(30),
	name varchar(30),
    foreign key (account_name) references account(email));
create table event(
	id int auto_increment key,
    owner_id varchar(30),
	date DATETIME,
	location varchar(50) NOT NULL,
	name varchar(50) NOT NULL,
	type ENUM('Movies', 'Sports'),
    description TEXT,
    foreign key (owner_id) references community(name));
create table communityAttend(
	entry_no int auto_increment key,
    id int,
    account_name varchar(30),
    foreign key (id) references event(id));
create table indiv_event(
	id int auto_increment key,
    owner_id varchar(30),
	date DATETIME,
	location varchar(50) NOT NULL,
	name varchar(50) NOT NULL,
	type ENUM('Movies', 'Sports'),
    description TEXT,
    foreign key (owner_id) references account(email));
create table indivAttend(
	entry_no int auto_increment key,
    id int,
	account_name varchar(30),
    foreign key (id) references indiv_event(id));