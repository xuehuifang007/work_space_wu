<?php
/* Debugging flags and functions available to sub packages...   */
$eol = "\n";
if (isset($_SERVER['REQUEST_URI'])) {
    $eol = '<br>';
}
function e1($s) {global $eol;echo '-->'.$s."$eol";}
function t1($s) {global $eol;echo '   '.$s."$eol";}
function x1($s) {global $eol;echo '<--'.$s."$eol";}

define('SAM_MQTT', 'mqtt');

/* ---------------------------------
    SAMConnection
   --------------------------------- */
class SAMConnection {
//  var $debug = true;
  var $debug = false;

  var $errno = 0;
  var $error = '';

  var $connection;

  /* ---------------------------------
      Create
     --------------------------------- */
  function Create($proto) {
      if ($this->debug) e1("SAMConnection.Create(proto=$proto)");
      $rc = false;
      /* search the PHP config for a factory to use...    */
      $x = get_cfg_var('sam.factory.'.$proto);
      if ($this->debug) t1('SAMConnection.Create() get_cfg_var() "'.$x.'"');

      /* If there is no configuration (php.ini) entry for this protocol, default it.  */
      if (strlen($x) == 0) {
          /* for every protocol other than MQTT assume we will use XMS    */
          if ($proto != 'mqtt') {
              $x = 'xms';
          } else {
              $x = 'mqtt';
          }
      }

      /* Invoke the chosen factory to create a real connection object...   */
      $x = 'sam_factory_'.$x.'.php';
      if ($this->debug) t1("SAMConnection.Create() calling factory - $x");
      $rc = include $x;

      if ($this->debug && $rc) t1('SAMConnection.Create() rc = '.get_class($rc));
      if ($this->debug) x1('SAMConnection.Create()');
      return $rc;
  }

  /* ---------------------------------
      Constructor
     --------------------------------- */
  function SAMConnection() {
    if ($this->debug) e1('SAMConnection()');

    if ($this->debug) x1('SAMConnection()');
  }

  /* ---------------------------------
      Commit
     --------------------------------- */
  function Commit() {
    if ($this->debug) e1('SAMConnection.Commit()');
    $rc = true;

    if (!$this->connection) {
        $errno = 106;
        $error = 'No active connection!';
        $rc = false;
    } else {
        /* Call the method on the underlying connection object...   */
        $rc = $this->connection->commit($target, $options);
        $this->errno = $this->connection->errno;
        $this->error = $this->connection->error;
        if (!$rc) {
            if ($this->debug) t1("SAMConnection.Commit() commit failed ($this->errno) $this->error");
            $rc = false;
        }
    }

    if ($this->debug) x1("SAMConnection.Commit() rc=$rc");
    return $rc;
  }

  /* ---------------------------------
      Connect
     --------------------------------- */
  function Connect($proto='', $options=array()) {
    if ($this->debug) e1('SAMConnection.Connect()');
    $rc = false;

    if ($proto == '') {
        $errno = 101;
        $error = 'Incorrect number of parameters on connect call!';
        $rc = false;
    } else {
        $this->connection = $this->create($proto);
        if (!$this->connection) {
            $errno = 102;
            $error = 'Unsupported protocol!';
            $rc = false;
        } else {
            if ($this->debug) t1("SAMConnection.Connect() connection created for protocol $proto");

            $this->connection->setdebug($this->debug);

            /* Call the connect method on the newly created connection object...   */
            $rc = $this->connection->connect($proto, $options);
            $this->errno = $this->connection->errno;
            $this->error = $this->connection->error;
            if (!$rc) {
               if ($this->debug) t1("SAMConnection.Connect() connect failed ($this->errno) $this->error");
            } else {
               $rc = true;
            }
        }
    }

    if ($this->debug) x1("SAMConnection.Connect() rc=$rc");
    return $rc;
  }

  /* ---------------------------------
      Disconnect
     --------------------------------- */
  function Disconnect() {
    if ($this->debug) e1('SAMConnection.Disconnect()');
    $rc = true;

    if (!$this->connection) {
        $errno = 106;
        $error = 'No active connection!';
        $rc = false;
    } else {
        /* Call the method on the underlying connection object...   */
        $rc = $this->connection->Disconnect();
        $this->errno = $this->connection->errno;
        $this->error = $this->connection->error;
        if (!$rc) {
            if ($this->debug) t1("SAMConnection.Disconnect() Disconnect failed ($this->errno) $this->error");
        } else {
            $rc = true;
            $this->connection = false;
        }
    }

    if ($this->debug) x1("SAMConnection.Disconnect() rc=$rc");
    return $rc;
  }

  /* ---------------------------------
      IsConnected
     --------------------------------- */
  function IsConnected() {
    if ($this->debug) e1('SAMConnection.IsConnected()');
    $rc = true;

    if (!$this->connection) {
        $errno = 106;
        $error = 'No active connection!';
        $rc = false;
    } else {
        /* Call the method on the underlying connection object...   */
        $rc = $this->connection->isconnected();
        $this->errno = $this->connection->errno;
        $this->error = $this->connection->error;
        if (!$rc) {
            if ($this->debug) t1("SAMConnection.IsConnected() isconnected failed ($this->errno) $this->error");
            $rc = false;
        }
    }

    if ($this->debug) x1("SAMConnection.IsConnected() rc=$rc");
    return $rc;
  }

  /* ---------------------------------
      Peek
     --------------------------------- */
  function Peek($target, $options=array()) {
    if ($this->debug) e1('SAMConnection.Peek()');
    $rc = true;

    if (!$this->connection) {
        $errno = 106;
        $error = 'No active connection!';
        $rc = false;
    } else {
        /* Call the method on the underlying connection object...   */
        $rc = $this->connection->peek($target, $options);
        $this->errno = $this->connection->errno;
        $this->error = $this->connection->error;
        if (!$rc) {
            if ($this->debug) t1("SAMConnection.Peek() peek failed ($this->errno) $this->error");
            $rc = false;
        }
    }

    if ($this->debug) x1("SAMConnection.Peek() rc=$rc");
    return $rc;
  }

  /* ---------------------------------
      PeekAll
     --------------------------------- */
  function PeekAll($target, $options=array()) {
    if ($this->debug) e1('SAMConnection.PeekAll()');
    $rc = true;

    if (!$this->connection) {
        $errno = 106;
        $error = 'No active connection!';
        $rc = false;
    } else {
        /* Call the method on the underlying connection object...   */
        $rc = $this->connection->peekall($target, $options);
        $this->errno = $this->connection->errno;
        $this->error = $this->connection->error;
        if (!$rc) {
            if ($this->debug) t1("SAMConnection.PeekAll() peekall failed ($this->errno) $this->error");
            $rc = false;
        }
    }

    if ($this->debug) x1("SAMConnection.PeekAll() rc=$rc");
    return $rc;
  }

  /* ---------------------------------
      Receive
     --------------------------------- */
  function Receive($target, $options=array()) {
    if ($this->debug) e1('SAMConnection.Receive()');
    $rc = true;

    if (!$this->connection) {
        $errno = 106;
        $error = 'No active connection!';
        $rc = false;
    } else {
        /* Call the receive method on the underlying connection object...   */
        $rc = $this->connection->receive($target, $options);
        $this->errno = $this->connection->errno;
        $this->error = $this->connection->error;
        if (!$rc) {
            if ($this->debug) t1("SAMConnection.Receive() receive failed ($this->errno) $this->error");
        }
    }

    if ($this->debug) x1("SAMConnection.Receive() rc=$rc");
    return $rc;
  }

  /* ---------------------------------
      Remove
     --------------------------------- */
  function Remove($target, $options=array()) {
    if ($this->debug) e1('SAMConnection.Remove()');
    $rc = true;

    if (!$this->connection) {
        $errno = 106;
        $error = 'No active connection!';
        $rc = false;
    } else {
        /* Call the method on the underlying connection object...   */
        $rc = $this->connection->remove($target, $options);
        $this->errno = $this->connection->errno;
        $this->error = $this->connection->error;
        if (!$rc) {
            if ($this->debug) t1("SAMConnection.Remove() remove failed ($this->errno) $this->error");
            $rc = false;
        }
    }

    if ($this->debug) x1("SAMConnection.Remove() rc=$rc");
    return $rc;
  }

  /* ---------------------------------
      Rollback
     --------------------------------- */
  function Rollback() {
    if ($this->debug) e1('SAMConnection.Rollback()');
    $rc = true;

    if (!$this->connection) {
        $errno = 106;
        $error = 'No active connection!';
        $rc = false;
    } else {
        /* Call the method on the underlying connection object...   */
        $rc = $this->connection->rollback($target, $options);
        $this->errno = $this->connection->errno;
        $this->error = $this->connection->error;
        if (!$rc) {
            if ($this->debug) t1("SAMConnection.Rollback() rollback failed ($this->errno) $this->error");
            $rc = false;
        }
    }

    if ($this->debug) x1("SAMConnection.Rollback() rc=$rc");
    return $rc;
  }

  /* ---------------------------------
      Send
     --------------------------------- */
  function Send($target, $msg, $options=array()) {
    if ($this->debug) e1('SAMConnection.Send()');
    $rc = true;

    if (!$this->connection) {
        $errno = 106;
        $error = 'No active connection!';
        $rc = false;
    } else {
        /* Call the send method on the underlying connection object...   */
        $rc = $this->connection->send($target, $msg, $options);
        $this->errno = $this->connection->errno;
        $this->error = $this->connection->error;
        if (!$rc) {
            if ($this->debug) t1("SAMConnection.Send() send failed ($this->errno) $this->error");
            $rc = false;
        }
    }

    if ($this->debug) x1("SAMConnection.Send() rc=$rc");
    return $rc;
  }

  /* ---------------------------------
      SetDebug
     --------------------------------- */
  function SetDebug($option=false) {
    if ($this->debug) e1("SAMConnection.SetDebug($option)");

    $this->debug = $option;

    if ($this->connection) {
        $this->connection->setdebug($option);
    }

    if ($this->debug) x1('SAMConnection.SetDebug()');
    return;
  }

  /* ---------------------------------
      Subscribe
     --------------------------------- */
  function Subscribe($topic, $options=array()) {
    if ($this->debug) e1("SAMConnection.Subscribe($topic)");
    $rc = true;

    if (!$this->connection) {
        $errno = 106;
        $error = 'No active connection!';
        $rc = false;
    } else {
        /* Call the subscribe method on the underlying connection object...   */
        $rc = $this->connection->subscribe($topic, $options);
        $this->errno = $this->connection->errno;
        $this->error = $this->connection->error;
        if (!$rc) {
            if ($this->debug) t1("SAMConnection.Subscribe() subscribe failed ($this->errno) $this->error");
            $rc = false;
        }
    }

    if ($this->debug) x1("SAMConnection.Subscribe() rc=$rc");
    return $rc;
  }

  /* ---------------------------------
      Unsubscribe
     --------------------------------- */
  function Unsubscribe($sub_id) {
    if ($this->debug) e1("SAMConnection.Unsubscribe($sub_id)");
    $rc = true;

    if (!$this->connection) {
        $errno = 106;
        $error = 'No active connection!';
        $rc = false;
    } else {
        /* Call the subscribe method on the underlying connection object...   */
        $rc = $this->connection->unsubscribe($sub_id);
        $this->errno = $this->connection->errno;
        $this->error = $this->connection->error;
        if (!$rc) {
            if ($this->debug) t1("SAMConnection.Unsubscribe() unsubscribe failed ($this->errno) $this->error");
            $rc = false;
        }
    }

    if ($this->debug) x1("SAMConnection.Unsubscribe() rc=$rc");
    return $rc;
  }
}

/* ---------------------------------
    SAMMessage
   --------------------------------- */
class SAMMessage {

  /* ---------------------------------
      Constructor
     --------------------------------- */
  function SAMMessage($body='') {

    if ($body != '') {
        $this->body = $body;
    }

  }
}

?>

