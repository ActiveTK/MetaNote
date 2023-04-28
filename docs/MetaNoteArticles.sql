CREATE TABLE `MetaNoteArticles` (
 `ArticleID` varchar(11) DEFAULT '',
 `ArticleTitle` varchar(500) DEFAULT '',
 `ArticleSubtitle` varchar(2000) DEFAULT '',
 `InPublic` varchar(5) DEFAULT 'false',
 `Writers` varchar(500) DEFAULT '',
 `LikedCount` varchar(11) DEFAULT '0',
 `DonateWayOrBTC` varchar(120) DEFAULT '',
 `CreateIPAddress` varchar(120) DEFAULT '',
 `CreateTime` varchar(16) DEFAULT '',
 `LastUpdateTime` varchar(16) DEFAULT '',
 `DateType` varchar(20) DEFAULT '',
 `DataSrc` varchar(200) DEFAULT '',
 `PVCount` varchar(20) DEFAULT '0',
 `CommentsJsonfp` varchar(200) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8