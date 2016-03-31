<?php
/**
 * A hits counter that uses SQLite.
 *
 * @author Fabiano Monteiro
 * @version 1.1 2016-03-30
 * @license LGPL3
 * @copyright (C) 2010 Fabiano Monteiro
 */

/**
 * @file
 *
 * - Do not place the database file in visible directories on the web, that is,
 *   avoid exposing below the public_html folder. If you need to put in this structure,
 *   protect the directory with .htaccess;
 * - Use tables with small data. SQLite runs on the same server as your
 *   website (unlike MySQL, PostgreSQL running on separate servers)
 *   and can compromise performance if it is too large or have too many hits;
 * - Name the file with suffixes such as ".db", ".sqlite" to remind you that the file is a SQLite database.
 *
 */

class Counter
{
    protected $db_sqlite;
    protected $hits;
    public static $display_value;

    const dbpath = "dbpath/";

    /**
     *   First run:
     * - Check database in the specified directory, creating it if it does not exist.
     * - If the method returns FALSE, constructs the table and inserts the first value for hits.
     *
     * @see hits()
     *
     */
    public function  __construct(){
        try {
          $this->db_sqlite = new SQLite3(self::dbpath . 'counter.db');

          if(!self::hits()){
            try {
              $this->db_sqlite->exec('CREATE TABLE count (hits NUMERIC )');
              $this->db_sqlite->exec('INSERT INTO count (hits) VALUES (1)');
            } catch (Exception $e) {
              print $e->getMessage();
            }
          }
        } catch (Exception $e) {
          print $e->getMessage();
        }
    }

    /**
     * @return
     * querySingle returns a single result, the first column (array), otherwise it returns FALSE.
     */
    public function hits(){
        try {
          $this->hits = $this->db_sqlite->querySingle('SELECT hits FROM count');
        } catch (Exception $e) {
            print $e->getMessage();
        } finally {
          self::$display_value = $this->hits;
          return $this->hits;
        }
    }

    /**
     * Gets the value of querySingle, adds and updates the database.
     *
     * @see hits()
     *
     */
    public function updateCounter(){
        self::hits();
        $hits_updated = ++$this->hits;
        $this->db_sqlite->exec("UPDATE count SET hits = $hits_updated");
    }

    /**
     * Display
     */
    public static function show(){
      print self::$display_value;
    }

    public function  __destruct(){
        $this->db_sqlite->close();
    }
}

//Use within a conditional according to the context of each script code.
$obj = new Counter();
$obj->updateCounter();

//The Counter
'Hits ( '. Counter::show() .' )';
