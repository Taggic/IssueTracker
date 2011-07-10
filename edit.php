<?php


//if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
//require_once(DOKU_INC.'../inc/init.php');
//if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'plugins/');
require_once(realpath(dirname(__FILE__)).'/../../../inc/init.php');
//require_once(DOKU_INC.'../inc/utf8.php');
//require_once(DOKU_INC.'../inc/pageutils.php');

//require_once(DOKU_PLUGIN.'syntax.php');

// POST Sent by the edited array

//    * &row=n: The row index of the edited cell
//    * &cell=n: The cell index of the edited cell
//    * &id=id: The id attribute of the row, it may be useful to set this to the record ID you are editing
//    * &field=field: The id attribute of the header cell of the column of the edited cell, it may be useful to set this to the field name you are editing
//    * &value=xxxxxx: The rest of the POST body is the serialised form. The default name of the field is 'value'.

global $ID;


$exploded = explode(' ',htmlspecialchars(stripslashes($_POST['id'])));
$project = $exploded[0];
$id_bug = intval($exploded[1]);

// get bugs file contents
$pfile = metaFN($project, '.bugs');
if (@file_exists($pfile))
    {$bugs  = unserialize(@file_get_contents($pfile));}
else 
    {$bugs = array();}


$field = strtolower(htmlspecialchars(stripslashes($_POST['field'])));
$value = htmlspecialchars(stripslashes($_POST['value']));

//echo auth_isadmin();
//echo $_POST;
//print_r($_POST);

if (($field == 'status') || ($field == 'severity') || ($field == 'version') || ($field == 'description')|| ($field == 'resolution') && (auth_isadmin()==1))
    {
    $bugs[$id_bug][$field]=$value;
//    echo $id_bug;
//    echo $pfile;
    }

// Save bugs file contents
$fh = fopen($pfile, 'w');
fwrite($fh, serialize($bugs));
fclose($fh);
echo $_POST['value'];
?>
