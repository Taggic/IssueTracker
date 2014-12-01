<?php
/******************************************************************************/
/* 
 * Load all assignees from files into an array
 *  
 */
  function __get_assignees_from_files($fileextension) {
  	// loop through plugin conf path and load all '.mail.list.assignees'
    $BASE = DOKU_PLUGIN."issuetracker/conf/";
    $assignees_lists = __find_assignees($BASE, $fileextension);
  	$assignees_list_files = explode(',',$assignees_lists);

  	foreach ($assignees_list_files as $assignees_list)
    {   $assignees_list = trim($assignees_list);
        if(is_file($BASE.$assignees_list)) {
            $assigneesList_handle = fopen($BASE.$assignees_list, "rb");
            while (!feof($assigneesList_handle) ) {
        	     $line_of_text = fgets($assigneesList_handle);
               $parts = explode('=', $line_of_text);
               if(strlen($parts[0])==0) continue;
               elseif(strlen($parts[1])==0) {
                  $parts[1] = trim($parts[0]);
                  msg($parts[1],0);
                }
                 //      NAME      = E-MAIL ADDRESS  
                // delete doubles
                if(!stristr($x_umail_select,trim($parts[0]))) $x_umail_select = $x_umail_select . "['".trim($parts[0])."','".trim($parts[1])."'],";
        	  }
        	  fclose($assigneesList_handle);
  	    }
    }
    return $x_umail_select;        
  }
/*----------------------------------------------------------------------------*/
  function __find_assignees($path, $fileextension) { 
    if ($handle=opendir($path)) { 
      while (false!==($file=readdir($handle))) { 
        if ($file<>"." AND $file<>"..") { 
          if (is_file($path.'/'.$file)) { 
            $ext = explode('.',$file);
            $last = count($ext) - 1;
	          if (stristr($file, $fileextension)!==false) {
              $assignees_lists .= ','.$file;
            }
          } 
        } 
      } 
    }
    return $assignees_lists ; 
  }    
/******************************************************************************/