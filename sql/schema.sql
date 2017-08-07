CREATE TABLE users (
userId SERIAL PRIMARY KEY,
username TEXT UNIQUE NOT NULL,
password TEXT NOT NULL,
userType VARCHAR(6) NOT NULL CHECK (userType = 'admin' OR userType = 'normal'),
bidPts INT NOT NULL CHECK (bidPts >= 0)
);

CREATE TABLE categories (
name TEXT PRIMARY KEY,
description TEXT
);

CREATE TABLE listings (
itemId SERIAL PRIMARY KEY,
ownerId INT NOT NULL REFERENCES users(userId) ON UPDATE CASCADE ON DELETE CASCADE,
name VARCHAR(128) NOT NULL,
description VARCHAR(512),
category VARCHAR(64) NOT NULL REFERENCES categories(name) ON UPDATE CASCADE ON DELETE CASCADE,
pickupLocation VARCHAR(128) NOT NULL,
returnLocation VARCHAR(128) NOT NULL,
pickupDate DATE NOT NULL ,
returnDate DATE NOT NULL CHECK (returnDate >= pickupDate),
minPrice INT NOT NULL CHECK (minPrice >= 0),
status VARCHAR(6) NOT NULL CHECK (status = 'open' OR status = 'closed')
);

CREATE TABLE bids (
bidderId INT REFERENCES users(userId) ON UPDATE CASCADE ON DELETE CASCADE,
listingId INT REFERENCES listings(itemId) ON UPDATE CASCADE ON DELETE CASCADE,
bidAmt INT NOT NULL CHECK (bidAmt >= 0),
status VARCHAR(7) NOT NULL CHECK (status = 'pending' OR status = 'success' OR status = 'fail'),
PRIMARY KEY (bidderId, listingId)
);

CREATE TABLE images (
listingId INT REFERENCES listings(itemId) ON UPDATE CASCADE ON DELETE CASCADE,
position INT,
imgPath TEXT UNIQUE,
PRIMARY KEY (listingId, position)
);

CREATE OR REPLACE FUNCTION enforce_image_count()
RETURNS TRIGGER AS
$$
DECLARE
	num_images INTEGER := 0;
BEGIN
	LOCK TABLE images IN EXCLUSIVE MODE;
	
	SELECT INTO num_images COUNT(*) FROM images WHERE listingId = NEW.listingId;
	
	IF (num_images) >= 5
	THEN
		RAISE EXCEPTION 'Cannot insert more than 5 photos';
	ELSE
		RETURN NEW;
	END IF;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER enforce_image_count
	BEFORE INSERT OR UPDATE ON images
	FOR EACH ROW EXECUTE PROCEDURE enforce_image_count();

CREATE OR REPLACE FUNCTION enforce_minimum_bid()
RETURNS TRIGGER AS
$$
DECLARE
	minBidAmt INTEGER := 0;
BEGIN
	LOCK TABLE listings IN EXCLUSIVE MODE;
	
	SELECT INTO minBidAmt minPrice FROM listings WHERE itemId = NEW.listingId;
	
	IF (NEW.bidAmt < minBidAmt)
	THEN
		RAISE EXCEPTION 'Bid must be greater than the minimum bid amount';
	ELSE
		RETURN NEW;
	END IF;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER enforce_minimum_bid
	BEFORE INSERT OR UPDATE ON bids
	FOR EACH ROW EXECUTE PROCEDURE enforce_minimum_bid();

CREATE OR REPLACE FUNCTION enforce_date_check()
RETURNS TRIGGER AS
$$
DECLARE
BEGIN
	LOCK TABLE listings IN EXCLUSIVE MODE;
	
	IF (NEW.status like 'closed')
	THEN
		RETURN NEW;	
	ELSEIF (NEW.pickupDate < current_date)
	THEN
		RAISE EXCEPTION 'Pickupdate must be greater than current date';
	ELSE
		RETURN NEW;
	END IF;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER enforce_date_check
	BEFORE INSERT OR UPDATE ON listings
	FOR EACH ROW EXECUTE PROCEDURE enforce_date_check();

CREATE OR REPLACE FUNCTION close_bid()
RETURNS TRIGGER AS
$$
DECLARE
BEGIN
    LOCK TABLE bids IN EXCLUSIVE MODE;
    LOCK TABLE users IN EXCLUSIVE MODE;
    
    IF (OLD.status LIKE 'pending' AND NEW.status LIKE 'fail')
    THEN
        -- reimburse the bid points to the user
        UPDATE users SET bidPts = bidPts + OLD.bidAmt
        WHERE userId = OLD.bidderId;
        RETURN NEW;
	ELSEIF (OLD.status LIKE 'success' AND NEW.status LIKE 'fail')
    THEN
        -- reimburse the bid points to the user
        UPDATE users SET bidPts = bidPts + OLD.bidAmt
        WHERE userId = OLD.bidderId;
		-- deduct bid points from owner
		UPDATE users SET bidPts = bidPts - OLD.bidAmt
		WHERE userId = (SELECT l.ownerId FROM listings l WHERE l.itemId = OLD.listingId);
        RETURN NEW;
    ELSEIF (OLD.status LIKE 'fail' AND NEW.status LIKE 'success')
    THEN
        -- deduct the bid points from the user again
        UPDATE users SET bidPts = bidPts - OLD.bidAmt
        WHERE userId = OLD.bidderId;
        RETURN NEW;
	ELSEIF (OLD.status LIKE 'pending' AND NEW.status LIKE 'success')
	THEN
		-- credit bid points to owner
		UPDATE users SET bidPts = bidPts + OLD.bidAmt
		WHERE userId = (SELECT l.ownerId FROM listings l WHERE l.itemId = OLD.listingId);
		RETURN NEW;
    ELSE
        RETURN NEW;
    END IF;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER close_bid
	BEFORE UPDATE ON bids
	FOR EACH ROW EXECUTE PROCEDURE close_bid();
