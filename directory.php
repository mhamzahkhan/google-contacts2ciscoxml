<?php
  require_once "header.php";
  require_once "config.php";
  require_once 'Zend/Loader.php';

  Zend_Loader::loadClass('Zend_Gdata');
  Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
  Zend_Loader::loadClass('Zend_Http_Client');
  Zend_Loader::loadClass('Zend_Gdata_Query');
  Zend_Loader::loadClass('Zend_Gdata_Feed');
    
  try {
    $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, 'cp');
    $gdata = new Zend_Gdata($client);
    $gdata->setMajorProtocolVersion(3);
    
    $query = new Zend_Gdata_Query('http://www.google.com/m8/feeds/contacts/default/full');
    $query->setMaxResults("2147483647");
    $query->setParam('orderby', 'lastmodified');
    $query->setParam('sortorder', 'descending');
    $feed = $gdata->getFeed($query);

?>

<CiscoIPPhoneDirectory>
  <Title><?php echo $feed->title; ?></Title>
  <Prompt>Options:</Prompt>

<?php
    $results = array();

    foreach($feed as $entry){
      $obj = new stdClass;
      $xml = simplexml_load_string($entry->getXML());
      $obj->name = (string) $entry->title;
     
      foreach ($xml->phoneNumber as $p) {
        $obj->phoneNumber[] = (string) $p;
      }

      $results[] = $obj;
    }
  } catch (Exception $e) {
    die('ERROR:' . $e->getMessage());  
  }

  sort($results);
  foreach ($results as $r) {
    if($r->phoneNumber != null) {
      foreach($r->phoneNumber as $phoneNumber) {
?>
  <DirectoryEntry>
    <Name><?php echo (!empty($r->name)) ? $r->name : 'Name not available'; ?></Name>
    <Telephone><?php echo "$phoneNumber"; ?></Telephone>
  </DirectoryEntry>

<?php
      }
    }
  }
?>
  <SoftKeyItem>
    <Name>Dial</Name>
    <URL>SoftKey:Dial</URL>
    <Position>1</Position>
  </SoftKeyItem>

  <SoftKeyItem>
    <Name>Exit</Name>
    <URL>SoftKey:Exit</URL>
    <Position>3</Position>
  </SoftKeyItem>
</CiscoIPPhoneDirectory>
