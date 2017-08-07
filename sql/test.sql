INSERT INTO categories VALUES('AAA','aaa');
INSERT INTO categories VALUES('BBB','bbb');

INSERT INTO listings VALUES (1, 13, 'Graduate Project Management', 'Et aut blanditiis iusto.', 'AAA', '4374 Al Keys
South Pietro, UT 66059', '4094 Maverick Way Apt. 449
Hansenshire, AR 00575-3848', '2017-03-18', '2017-03-23', 4, 'open');

INSERT INTO images VALUES(1,1,'path1');
INSERT INTO images VALUES(1,2,'path2');
INSERT INTO images VALUES(1,3,'path3');
INSERT INTO images VALUES(1,4,'path4');
INSERT INTO images VALUES(1,5,'path5');
INSERT INTO images VALUES(1,6,'path6'); -- fail

INSERT INTO bids VALUES(1,1,3,'pending'); -- fail
INSERT INTO bids VALUES(1,1,4,'pending'); -- pass
INSERT INTO bids VALUES(2,1,5,'pending'); -- pass