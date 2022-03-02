call addUser('colby@mtu.edu');
call addCommunity('The Gamers', 'colby@mtu.edu');
call addToCommunity('colby@mtu.edu', 'The Gamers');
call addCommunityEvent('The Gamers', 'Online', 'Minecraft Night', 'Sports', 'We play Minecraft');
call addUserEvent('colby@mtu.edu', 'McNair Dining Hall', 'Food and Movie Night!', 'Movies',
'I am eating dinner and am going to watch a movie. Please join!');
call addUser('andrew@mtu.edu');
call addUser('kevin@mtu.edu');
call makeFriends('colby@mtu.edu', 'andrew@mtu.edu');
call makeFriends('colby@mtu.edu', 'kevin@mtu.edu');
SELECT * FROM account;
SELECT * FROM friend;
SELECT * FROM community;
SELECT * FROM member;
SELECT * FROM event;
SELECT * FROM indiv_event;