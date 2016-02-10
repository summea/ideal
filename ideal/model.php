<?php
/**
 * Model class
 * @package ideal
 */
class Model extends Ideal
{
  /**
   * Constructor
   */
  public function __construct()
  {
    if (file_exists('config/conf.php')) {
      require('config/conf.php');
      $this->dbDriver = $DBDRIVER;
      $this->dbHost = $HOST;
      $this->dbDatabase = $DATABASE;
      $this->dbUser = $USER;
      $this->dbPassword = $PASSWORD;
    } else {
      die('Please make sure to edit the database config file!');
    }
  }

  /**
   * Take out non-word characters from string.
   * @param string $data
   * @return string
   */
  public function cleanQuery($data)
  {
    return preg_replace("/\W+/", "", $data);
  }

  /**
   * Database connection.
   * @return object
   */
  public function dbConnect()
  {
    try {
      if (preg_match("/sqlite/i", $this->dbDriver)) {
        $dbh = new PDO(
          $this->dbDriver . ':' . $this->dbDatabase
        );
      } else {
        $dbh = new PDO(
          $this->dbDriver . ':host='.
          $this->dbHost . ';dbname=' .
          $this->dbDatabase,
          $this->dbUser,
          $this->dbPassword
        );
      }
    # display PDO errors
    # $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "Database connection error: " . $e->getMessage() . "<br />";
    }
    return $dbh;
  }

  /**
   * Delete (DELETE) from database table.
   * @param array $data
   * @return boolean
   */
  function delete($data=array())
  {
    if ($data) {
      $table = (isset($data['table'])) ? $data['table'] : $this->table;
      $query = 'DELETE FROM `' . $table . '`';
      $conditions_a = array();
      $values_a = array();
      if (isset($data['conditions'])) {
        foreach ($data['conditions'] as $k => $v) {
          array_push($conditions_a, " $k = :$k");
          $values_a = $values_a + array(":$k" => $v);
        }
        $query .= ' WHERE' . implode(", ", $conditions_a);

        $dbh = $this->dbConnect();
        $q = $dbh->prepare($query);
        return $q->execute($values_a);
      }
    }
  }

  /**
   * General SQL query execute.
   * @param string $query
   * @param array $values
   * @return boolean
   */
  public function execute($query, $values)
  {
    $dbh = $this->dbConnect();
    $q = $dbh->prepare($query);
    return $q->execute($values);
  }

  /**
   * Find (SELECT) from database table.
   * @param string $range
   * @param array $options
   * Currently supported $options keys:
   * - table => array
   * - fields => string
   * - conditions => array
   * - order => string
   * - desc => string (a-z or z-a else a-z)
   * - limit => integer
   * - keep => array
   *
   * @return array
   */
  public function find($range='all', $options=array('fields' => '*'))
  {
    $tables = (isset($options['table'])) ? $options['table'] : $this->table;
    $fields = (isset($options['fields'])) ? $options['fields'] : $range;

    switch ($fields) {
      case "all":
        $fields = '*';
        break;
    }

    if (is_array($tables))
      $tables = implode(", ", $tables);

    $query = 'SELECT ' . $fields .
      ' FROM ' . $tables;

    $conditions_a = array();
    $values_a = array();
    if (isset($options['conditions'])) {
      $conditions = $options['conditions'];
      foreach ($conditions as $k => $v) {
        if (is_array($v)) {
          foreach ($v as $inner_k => $inner_v) {
            $clean_key = str_replace(".", "____", $inner_k);
            array_push($conditions_a, " $inner_k = :$clean_key");
            $values_a = $values_a + array(":$clean_key" => $inner_v);
          }
        } else {
          # is this a weird non-key-value array item?
          if (is_int($k)) {
            array_push($conditions_a, "$v");
          } else {
            $clean_key = str_replace(".", "____", $k);
            array_push($conditions_a, " $k = :$clean_key");
            $values_a = $values_a + array(":$clean_key" => $v);
          }
        }
      }
      $query .= ' WHERE ' . implode(" ", $conditions_a);
    }

    if (isset($options['order'])) {
      $query .= ' ORDER BY `' . $this->cleanQuery($options['order']) . '`';
    }
    
    if (isset($options['desc'])) {
      if (preg_match("/z-a/i", $options['desc']))
        $query .= ' DESC';
      else
        $query .= ' ASC';
    }

    if (isset($options['limit'])) {
      $limit = $this->cleanQuery($options['limit']);
      $query .= ' LIMIT ' . $limit;
    }

    if (isset($options['keep'])) {
      $keep_results_a = $options['keep'];
    }

    $dbh = $this->dbConnect();
    $q = $dbh->prepare($query);
    $q->execute($values_a);

    if (preg_match("/sqlite/i", $this->dbDriver)) {
      # have to manually check returned rows for sqlite
      $rows = $q->fetchAll();
      if ($range == 'all')
        $return = $rows;
      else
        $return = $rows[0];
    } else {
      if ($range == 'all')
        $return = $q->fetchAll();
      else
        $return = $q->fetch();
    }

    # look for related tables
    $empty_results = array(); # for tables that won't be included
    if (isset($keep_results_a)){
      $check_related_a = array();
      foreach ($this->related['has_many'] as $k => $v) {
        if (in_array($k, $keep_results_a)) {
          $check_related_a['has_many'][$k] = $this->related['has_many'][$k];
        } else {
          $empty_results[$k] = array();
        }
      }
    } else {
      foreach ($this->related as $k => $v) {
        foreach ($v as $table => $original_key) {
          $empty_results[$table] = array();
        }
      }
      $check_related_a = $this->related;
    }

    $found = array();
    if (isset($check_related_a) && $check_related_a) {
      $found = $this->findRelated($check_related_a, $return['id']);
    }
    # create empty array keys to prevent warnings
    if (!$found && $check_related_a) {
      $relation_key = array_keys($check_related_a);
      $relation_key = $relation_key[0];
      foreach ($check_related_a[$relation_key] as $k => $v) {
        $found = array($k => array());
      }
    }
    $found += $empty_results;
    $return += $found;
    return $return;
  } 

  /**
   * Find related tables (set in extending model.)
   * @param array $related
   * @param string $original_key -- for the foreign key to match
   * @return array
   */
  public function findRelated($related=array(), $original_key='')
  {
    $this->related = array(); # prevent infinite loop
    $found = array();
    foreach ($related['has_many'] as $k => $v) {
      $results = $this->find('all', array(
        'table' => $k,
        'conditions' => array($v => $original_key)
      ));
      if ($results)
        $found[$k] = $results;
    }
    return $found;
  }

  /**
   * General SQL query.
   * - Note: $multi_level_array_results returns all result rows no matter what.
   * @param string $query
   * @param array $values
   * @param boolean $multi_level_array_results
   * @return array
   */
  public function query($query, $values, $multi_level_array_results=true)
  {
    $dbh = $this->dbConnect();
    $q = $dbh->prepare($query);
    $q->execute($values);

    if (preg_match("/sqlite/i", $this->dbDriver)) {
      # have to manually check returned rows for sqlite
      $rows = $q->fetchAll();
      if (count($rows) > 1 || $multi_level_array_results)
        $return = $rows;
      else
        $return = $rows[0];
    } else {
      if ($q->rowCount() > 1 || $multi_level_array_results)
        $return = $q->fetchAll();
      else
        $return = $q->fetch();
    }

    return $return;
  }

  /**
   * Save (INSERT, UPDATE) into database table.
   * @param array $data
   * @param array $options
   * @return boolean
   */
  public function save($data, $options=array())
  {
    $table = (isset($options['table'])) ? $options['table'] : $this->table;
    $related = array();

    $query = '';
    if (isset($data['id']) && $data['id']) {
      $set_a = array();
      $values_a = array();
      foreach ($data as $k => $v) {
        if ($k != 'id' && !preg_match("/\//", $k) && !preg_match("/__/", $k)) {
          array_push($set_a, " $k = :$k");
          $values_a = $values_a + array(":$k" => $v);
        } else {
          if ($k == 'id')
            $values_a = $values_a + array(":$k" => $v);
          if (preg_match("/\//", $k))
            $related += array($k => $v);
          if (preg_match("/__/", $k))
            $table = $v;
        }
      }

      $query .= 'UPDATE `' . $table . '`' .
        ' SET ' . implode(",", $set_a) .
        ' WHERE id = :id';
    } else {
      $keys_a = array();
      $key_symbols_a = array();
      $values_a = array();
      foreach ($data as $k => $v) {
        if ($k != 'id' && !preg_match("/\//", $k) && !preg_match("/__/", $k)) {
          array_push($keys_a, $k);
          array_push($key_symbols_a, ":$k");
          $values_a = $values_a + array(":$k" => $v);
        } else {
          if (preg_match("/\//", $k))
            $related += array($k => $v);
          if (preg_match("/__/", $k)) {
            $table = $v;
          }
        }
      }
      $query .= 'INSERT INTO `' . $table . '`' .
        ' (' . implode(",", $keys_a) . ') ' .
        ' VALUES (' . implode(",", $key_symbols_a) . ') ';
    }

    $dbh = $this->dbConnect();
    $q = $dbh->prepare($query);
    $return = $q->execute($values_a);
    if (count($related) > 0)
      $this->saveRelated($related);
    return $return;
  }

  /**
   * Save related table data.
   *
   * Overview:
   * - Get array of related from $data (from save() call.)
   * - Find first batch.
   * - Separate first batch into [subject], [body], etc. top-level fields.
   * - Send back to save.
   * - Repeat until there are no more top-level fields.
   *
   * - $data_a[0] = table
   * - $data_a[1] = column name
   * - $data_a[2] = column row key
   *
   * @param array $related
   * @param string $original_key -- for the foreign key to match
   * @return array
   */
  public function saveRelated($related=array(), $original_key='')
  {
    if (count($related > 0)) {

      $localized_related_a = array();

      foreach ($related as $k => $v) {
        $data_a = explode("/", $k);
        if (!isset($last_table))
          $last_table = $data_a[0];
        if (!isset($last_id))
          $last_id = $data_a[2];

        if (($data_a[2] != $last_id) || ($data_a[0] != $last_table)) {
          $localized_related_a['__table'] = $last_table;
          if (preg_match("/^[0-9]+$/", $last_id))
            $localized_related_a['id'] = $last_id;
          break;
        }

        $localized_related_a[$data_a[1]] = $v;
        unset($related[$data_a[0] . '/' . $data_a[1] . '/' . $data_a[2]]);

        $last_table = $data_a[0];
        $last_id = $data_a[2];
      }

      $localized_related_a['__table'] = $last_table;
      $related = $localized_related_a + $related;
      $this->save($related);
    }
  }
}
?>
