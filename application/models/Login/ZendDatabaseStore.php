<?php

/**
 * @access private
 */
require_once 'Auth/OpenID/Interface.php';
require_once 'Auth/OpenID/Nonce.php';

/**
 * @access private
 */
require_once 'Auth/OpenID.php';

/**
 * @access private
 */
require_once 'Auth/OpenID/Nonce.php';


class Model_Login_ZendDatabaseStore extends Auth_OpenID_OpenIDStore
{
  function Model_Login_ZendDatabaseStore($connection,
                                  $associations_table = null,
                                  $nonces_table = null)
  {
    $this->associations_table_name = "oid_associations";
    $this->nonces_table_name = "oid_nonces";
    
    if (!is_object($connection))
    {
      trigger_error("Auth_OpenID_SQLStore expect    ed Zend DB connection " .
                    "object (got ".get_class($connection).")",
                    E_USER_ERROR);
      return;
    }
    
    $this->connection = $connection;
    
    // Be sure to run the database queries with auto-commit mode
    // turned OFF, because we want every function to run in a
    // transaction, implicitly.  As a rule, methods named with a
    // leading underscore will NOT control transaction behavior.
    // Callers of these methods will worry about transactions.
    $this->connection->beginTransaction();

    // Create an empty SQL strings array.
    $this->sql = array();

    // Call this method (which should be overridden by subclasses)
    // to populate the $this->sql array with SQL strings.
    $this->setSQL();

    // Verify that all required SQL statements have been set, and
    // raise an error if any expected SQL strings were either
    // absent or empty.
    list($missing, $empty) = $this->_verifySQL();

    if ($missing) {
        trigger_error("Expected keys in SQL query list: " .
                       implode(", ", $missing),
                       E_USER_ERROR);
        return;
    }

    if ($empty) {
        trigger_error("SQL list keys have no SQL strings: " .
                     implode(", ", $empty),
                          E_USER_ERROR);
        return;
    }

    // Add table names to queries.
    $this->_fixSQL();
  }
  
  function tableExists($table_name)
  {
  	$tables = $this->connection->listTables();
  	
  	foreach ($tables as $table)
  	{
  		if ($table == $table_name)
  		  return true;
  	}
  	
  	return false;
  }
  
  function setSQL()
  {
  	        $this->sql['nonce_table'] =
            "CREATE TABLE %s (\n".
            "  server_url VARCHAR(2047) NOT NULL,\n".
            "  timestamp INTEGER NOT NULL,\n".
            "  salt CHAR(40) NOT NULL,\n".
            "  UNIQUE (server_url(255), timestamp, salt)\n".
            ") ENGINE=InnoDB";

        $this->sql['assoc_table'] =
            "CREATE TABLE %s (\n".
            "  server_url BLOB NOT NULL,\n".
            "  handle VARCHAR(255) NOT NULL,\n".
            "  secret BLOB NOT NULL,\n".
            "  issued INTEGER NOT NULL,\n".
            "  lifetime INTEGER NOT NULL,\n".
            "  assoc_type VARCHAR(64) NOT NULL,\n".
            "  PRIMARY KEY (server_url(255), handle)\n".
            ") ENGINE=InnoDB";

        $this->sql['set_assoc'] =
            "REPLACE INTO %s (server_url, handle, secret, issued,\n".
            "  lifetime, assoc_type) VALUES (?, ?, ?, ?, ?, ?)";

        $this->sql['get_assocs'] =
            "SELECT handle, secret, issued, lifetime, assoc_type FROM %s ".
            "WHERE server_url = ?";

        $this->sql['get_assoc'] =
            "SELECT handle, secret, issued, lifetime, assoc_type FROM %s ".
            "WHERE server_url = ? AND handle = ?";

        $this->sql['remove_assoc'] =
            "DELETE FROM %s WHERE server_url = ? AND handle = ?";

        $this->sql['add_nonce'] =
            "INSERT INTO %s (server_url, timestamp, salt) VALUES (?, ?, ?)";

        $this->sql['clean_nonce'] =
            "DELETE FROM %s WHERE timestamp < ?";

        $this->sql['clean_assoc'] =
            "DELETE FROM %s WHERE issued + lifetime < ?";
  }
  
  /**
   * Resets the store by removing all records from the store's
   * tables.
   */
  function reset()
  {
      $this->connection->query(sprintf("DELETE FROM %s",
                                       $this->associations_table_name));

      $this->connection->query(sprintf("DELETE FROM %s",
                                       $this->nonces_table_name));
  }
  
    /**
     * @access private
     */
    function _verifySQL()
    {
        $missing = array();
        $empty = array();

        $required_sql_keys = array(
                                   'nonce_table',
                                   'assoc_table',
                                   'set_assoc',
                                   'get_assoc',
                                   'get_assocs',
                                   'remove_assoc'
                                   );

        foreach ($required_sql_keys as $key) {
            if (!array_key_exists($key, $this->sql)) {
                $missing[] = $key;
            } else if (!$this->sql[$key]) {
                $empty[] = $key;
            }
        }

        return array($missing, $empty);
    }

    /**
     * @access private
     */
    function _fixSQL()
    {
        $replacements = array(
                              array(
                                    'value' => $this->nonces_table_name,
                                    'keys' => array('nonce_table',
                                                    'add_nonce',
                                                    'clean_nonce')
                                    ),
                              array(
                                    'value' => $this->associations_table_name,
                                    'keys' => array('assoc_table',
                                                    'set_assoc',
                                                    'get_assoc',
                                                    'get_assocs',
                                                    'remove_assoc',
                                                    'clean_assoc')
                                    )
                              );

        foreach ($replacements as $item) {
            $value = $item['value'];
            $keys = $item['keys'];

            foreach ($keys as $k) {
                if (is_array($this->sql[$k])) {
                    foreach ($this->sql[$k] as $part_key => $part_value) {
                        $this->sql[$k][$part_key] = sprintf($part_value,
                                                            $value);
                    }
                } else {
                    $this->sql[$k] = sprintf($this->sql[$k], $value);
                }
            }
        }
    }
        
    function blobDecode($blob)
    {
        return $blob;
    }

    function blobEncode($blob)
    {
    	//Bug!! No need to bin hex blob
        //return "0x" . bin2hex($blob);
      return $blob;
    }

    function createTables()
    {
      $n = $this->create_nonce_table();
      $a = $this->create_assoc_table();
 
      if ($n && $a) 
      {
      	$this->connection->commit();
      	$this->connection->beginTransaction();
        return true;
      }     
      
      $this->connection->rollback();
      $this->connection->beginTransaction();
      return false;
    }

    function create_nonce_table()
    {
      if (!$this->tableExists($this->nonces_table_name)) 
      {
        if (!$this->connection->query($this->sql['nonce_table']))
          return false;
      }
      return true;
    }

    function create_assoc_table()
    {
        if (!$this->tableExists($this->associations_table_name)) 
        {
          if (!$this->connection->query($this->sql['assoc_table']))
            return false;
        }
        return true;
    }

    /**
     * @access private
     */
    function _set_assoc($server_url, $handle, $secret, $issued,
                        $lifetime, $assoc_type)
    {
        return $this->connection->query($this->sql['set_assoc'],
                                        array(
                                              $server_url,
                                              $handle,
                                              $secret,
                                              $issued,
                                              $lifetime,
                                              $assoc_type));
    }

    function storeAssociation($server_url, $association)
    {
      if ($this->_set_assoc($server_url,
                            $association->handle,
                            $this->blobEncode($association->secret),
                            $association->issued,
                            $association->lifetime,
                            $association->assoc_type)) 
      {
        $this->connection->commit();
        $this->connection->beginTransaction();
        return;
      } 

      $this->connection->rollback();
      $this->connection->beginTransaction();
    }

    /**
     * @access private
     */
    function _get_assoc($server_url, $handle)
    {
        $result = $this->connection->fetchRow($this->sql['get_assoc'],
                                            array($server_url, $handle));

        if ($result)
          return $result;
                                                  
        return null;
    }

    /**
     * @access private
     */
    function _get_assocs($server_url)
    {
        $result = $this->connection->fetchAll($this->sql['get_assocs'],
                                            array($server_url));
        if ($result)
          return $result;
                                                
        return array();
    }

    function removeAssociation($server_url, $handle)
    {
        if ($this->_get_assoc($server_url, $handle) == null) {
            return false;
        }

        if ($this->connection->query($this->sql['remove_assoc'],
                                     array($server_url, $handle))) 
        {
            $this->connection->commit();
            $this->connection->beginTransaction();
            
        } else {
            $this->connection->rollback();
            $this->connection->beginTransaction();
        }

        return true;
    }

    function getAssociation($server_url, $handle = null)
    {
//    	error_log("Getting association ".$server_url);
        if ($handle !== null) {
            $assoc = $this->_get_assoc($server_url, $handle);

            $assocs = array();
            if ($assoc) {
                $assocs[] = $assoc;
            }
            
  //          error_log("Return 1");
        } else {
            $assocs = $this->_get_assocs($server_url);
    //        error_log("Return assocs");
        }

        if (!$assocs || (count($assocs) == 0)) {
            return null;
        } else {
            $associations = array();

            foreach ($assocs as $assoc_row) {
                $assoc = new Auth_OpenID_Association($assoc_row['handle'],
                                                     $assoc_row['secret'],
                                                     $assoc_row['issued'],
                                                     $assoc_row['lifetime'],
                                                     $assoc_row['assoc_type']);

                $assoc->secret = $this->blobDecode($assoc->secret);

                if ($assoc->getExpiresIn() == 0) {
                    $this->removeAssociation($server_url, $assoc->handle);
                } else {
                    $associations[] = array($assoc->issued, $assoc);
                }
            }

            if ($associations) {
                $issued = array();
                $assocs = array();
                foreach ($associations as $key => $assoc) {
                    $issued[$key] = $assoc[0];
                    $assocs[$key] = $assoc[1];
                }

                array_multisort($issued, SORT_DESC, $assocs, SORT_DESC,
                                $associations);

                // return the most recently issued one.
                list($issued, $assoc) = $associations[0];
     //           error_log("Returning associations");
                return $assoc;
            } else {
                return null;
            }
        }
    }

    /**
     * @access private
     */
    function _add_nonce($server_url, $timestamp, $salt)
    {
        $sql = $this->sql['add_nonce'];
        $result = $this->connection->query($sql, array($server_url,
                                                       $timestamp,
                                                       $salt));
                                                       
        if ($result)
        {                                               
          $this->connection->commit();
          $this->connection->beginTransaction();
          return true;
        }  
        $this->connection->rollback();
        $this->connection->beginTransaction();

        return false;
    }

    function useNonce($server_url, $timestamp, $salt)
    {
        global $Auth_OpenID_SKEW;

        if ( abs($timestamp - time()) > $Auth_OpenID_SKEW ) {
            return False;
        }
        
        return $this->_add_nonce($server_url, $timestamp, $salt);
    }

    /**
     * "Octifies" a binary string by returning a string with escaped
     * octal bytes.  This is used for preparing binary data for
     * PostgreSQL BYTEA fields.
     *
     * @access private
     */
    function _octify($str)
    {
        $result = "";
        for ($i = 0; $i < Auth_OpenID::bytes($str); $i++) {
            $ch = substr($str, $i, 1);
            if ($ch == "\\") {
                $result .= "\\\\\\\\";
            } else if (ord($ch) == 0) {
                $result .= "\\\\000";
            } else {
                $result .= "\\" . strval(decoct(ord($ch)));
            }
        }
        return $result;
    }

    /**
     * "Unoctifies" octal-escaped data from PostgreSQL and returns the
     * resulting ASCII (possibly binary) string.
     *
     * @access private
     */
    function _unoctify($str)
    {
        $result = "";
        $i = 0;
        while ($i < strlen($str)) {
            $char = $str[$i];
            if ($char == "\\") {
                // Look to see if the next char is a backslash and
                // append it.
                if ($str[$i + 1] != "\\") {
                    $octal_digits = substr($str, $i + 1, 3);
                    $dec = octdec($octal_digits);
                    $char = chr($dec);
                    $i += 4;
                } else {
                    $char = "\\";
                    $i += 2;
                }
            } else {
                $i += 1;
            }

            $result .= $char;
        }

        return $result;
    }

    function cleanupNonces()
    {
      global $Auth_OpenID_SKEW;
      $v = time() - $Auth_OpenID_SKEW;

      $this->connection->query($this->sql['clean_nonce'], array($v));
      $num = $r->rowCount();
      $this->connection->commit();
      $this->connection->beginTransaction();
      return $num;
    }

    function cleanupAssociations()
    {
      $r = $this->connection->query($this->sql['clean_assoc'],
                                 array(time()));
      $num = $r->rowCount();
      $this->connection->commit();
      $this->connection->beginTransaction();
      return $num;
    }   
}
?>