CREATE TABLE IF NOT EXISTS loopstructure (
  Id int(10) unsigned NOT NULL AUTO_INCREMENT,
  IndexArticleId int(10) unsigned NOT NULL,
  TocLevel int(10) unsigned NOT NULL,
  TocNumber varchar(255) NOT NULL,
  TocText varchar(255) NOT NULL default '',
  Sequence int(10) unsigned NOT NULL,
  ArticleId int(10) unsigned NOT NULL,
  PreviousArticleId int(10) unsigned NOT NULL,
  NextArticleId int(10) unsigned NOT NULL,
  ParentArticleId int(10) unsigned NOT NULL,
  IndexOrder int(10) unsigned NOT NULL default 0,
  PRIMARY KEY (Id),
  KEY IndexArticle (IndexArticleId,Sequence) USING BTREE,
  KEY ArticleId (ArticleId)
) ENGINE=InnoDB   DEFAULT CHARSET=binary  ;
