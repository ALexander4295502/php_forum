SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = '-06:00';
-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS categories (
  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  category varchar(20) NOT NULL,
  parent SMALLINT NOT NULL,
  PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT character SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 4;

--
-- Dumping data for table `categories`
--

INSERT INTO categories (id, category, parent) VALUES
(1, 'Sports', 0),
(2, 'Game', 0),
(3, 'Basketball', 1);

INSERT INTO categories (category, parent) VALUES ('Moba', 2);
INSERT INTO categories (category, parent) VALUES ('RPG', 2);
INSERT INTO categories (category, parent) VALUES ('Music', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cat_relate`
--

-- CREATE TABLE IF NOT EXISTS `cat_relate` (
--   `id` smallint(6) NOT NULL AUTO_INCREMENT,
--   `parent_id` smallint(6) NOT NULL,
--   `child_id` smallint(6) NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `cat_relate`
--

-- INSERT INTO `cat_relate` (`id`, `parent_id`, `child_id`) VALUES
-- (1, 1, 3);

-- INSERT INTO `cat_relate` ( `parent_id`, `child_id`) VALUES
-- ( 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--
-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS users (
  username varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  level TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (username)
) ENGINE = InnoDB DEFAULT character SET = utf8 COLLATE = utf8_general_ci;

--
-- Table structure for table `stories`
--

CREATE TABLE IF NOT EXISTS stories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cat_id SMALLINT UNSIGNED NOT NULL,
  dateposted datetime NOT NULL,
  username varchar(255) NOT NULL,
  subject varchar(50) NOT NULL,
  body text NOT NULL,
  link varchar(255),
  PRIMARY KEY (id),
  FOREIGN KEY (username) REFERENCES users (username),
  FOREIGN KEY (cat_id) REFERENCES categories (id) 
) ENGINE = InnoDB DEFAULT character SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 2;
  
--
-- Dumping data for table `stories`
--

INSERT INTO stories (id, cat_id, dateposted, username, subject, body, link) VALUES
(1, 3, '2017-02-09 23:27:00', 'ctong' ,'Basketball in Wikipedia', 
'Basketball is a sport that is played by two teams of five players on a rectangular court.\r\n 
The objective is to shoot a ball through a hoop 18 inches (46 cm) in diameter.', 'https://en.wikipedia.org/wiki/Basketball'
);

INSERT INTO stories (cat_id, dateposted, username, subject, body, link) VALUES
(2, '2017-02-11 08:53:00', 'ctong' ,'Dota2 in Wiki', 
'Dota 2 is a free-to-play multiplayer online battle arena (MOBA) video game developed and published by Valve Corporation. \r\n
The game is the stand-alone sequel to Defense of the Ancients (DotA), which was a community-created mod for Blizzard Entertainments\r\n
 Warcraft III: Reign of Chaos and its expansion pack, The Frozen Throne. Dota 2 is played in matches between two teams that consist of \r\n
 five players, with both teams occupying their own separate base on the map. Each of the ten players independently control a powerful character, \r\n
 known as a "hero", that each feature unique abilities and different styles of play. During a match, a player and their team collects experience\r\n
  points and items for their heroes in order to fight through the opposing teams defenses. A team wins by being the first to destroy a large\r\n 
  structure located in the opposing teams base, called the "Ancient".', 'https://en.wikipedia.org/wiki/Dota_2'
);


CREATE TABLE IF NOT EXISTS comments (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  story_id INT UNSIGNED NOT NULL,
  username varchar(255) NOT NULL,
  dateposted datetime NOT NULL,
  body text NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (username) REFERENCES users (username),  
  FOREIGN KEY (story_id) REFERENCES stories (id)
) ENGINE = InnoDB DEFAULT character SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT = 1;


-- CREATE TABLE IF NOT EXISTS `story_com_relate` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `story_id` int(11) NOT NULL,
--   `comment_id` int(11) NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- Sql For Test
DELETE stories, comments FROM stories JOIN comments ON (comments.story_id=stories.id) WHERE stories.cat_id = 7;


 