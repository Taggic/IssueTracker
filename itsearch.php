<?php
/*----------------------------------------------------------------------------*/
//  a simple search function for IssueTracker
/*----------------------------------------------------------------------------*/
//function it_simple_search ($project, &$renderer) {
    global $ID;
    global $conf;
    $Generated_Header  = '';
    $Generated_Scripts = '';
    $Generated_Report  = '';
    $Generated_Message = '';

    $ref_findings = array();
    $cnt_i_findings = -1;     // count issue findings
    $cnt_c_findings = -1;     // count comment findings

    $project = $this->project;
    $search_words = explode(' ', $this->parameter);
    
//---------------------------------------------------------------------------------------
//  0. handle multi_projects
    // detect the IssueTracker data store (path)
    if($this->getConf('it_data')==false) $it_datastore = DOKU_CONF."../data/meta/";
    else $it_datastore = DOKU_CONF."../". $this->getConf('it_data');
    
    // check if last sign is a slash
    $i = strrchr ($it_datastore, chr(47));     // chr(47) = "/"
    $j = strrchr ($it_datastore, chr(92));     // chr(92) = "\"
    if(($i !== strlen($it_datastore)) && ($i !== strlen($it_datastore))) { $it_datastore .= chr(47); }

    if($this->getConf('multi_projects')!==false) {
        // loop through it_datastore and list all .issues files
        $xprojects = $this->__find_projects($it_datastore);

        $x_projects = explode(',',$xprojects);
        $issues = array();
        $tmp    = array();
        
        foreach ($x_projects as $project)
        {   $project = trim($project);
            if(is_file($it_datastore.$project.'.issues') == true) {
                $tmp = unserialize(@file_get_contents($it_datastore.$project.'.issues'));
                
                //  1. get issue file content
                // loop through the field and add project to each row
                foreach($tmp as &$tmps)
                {   $tmps['project'] = $project; }
                
                $issues = array_merge($issues, $tmp);
                $tmp = array();
            }
        }    
    }
    else {
    //  1. get issue file content                                                 
        if($conf['plugin']['issuetracker']['it_data']==false) $pfile = DOKU_CONF."../data/meta/".$project.'.issues';
        else $pfile = DOKU_CONF."../". $conf['plugin']['issuetracker']['it_data'].$project.'.issues';   
        if (@file_exists($pfile))
            {  $issues  = unserialize(@file_get_contents($pfile));
               foreach($issues as &$issue) {
                  $issue['project'] = $project;
               }
            }
        elseif(strlen($project)>1)
        	  {// promt error message that project file does not exist
               msg(sprintf($this->getLang('msg_pfilemissing'), $pfile),-1); }
    }
    
//    echo var_dump($issues)."<br />";
    
//---------------------------------------------------------------------------------------        
//  2. loop through single issues                                             
    foreach($issues as &$issue) {
       $issue_string = implode($issue);

       if($conf['plugin']['issuetracker']['it_data']==false) $comments_file = DOKU_CONF."../data/meta/".$issue['project']."_".$issue['id']. '.cmnts';
       else $comments_file = DOKU_CONF."../". $conf['plugin']['issuetracker']['it_data'].$issue['project']."_".$issue['id']. '.cmnts';
       $comments ='';
       if (@file_exists($comments_file))  {  $comments  = @file_get_contents($comments_file);  }

//     2.0 loop through search words and search issue and comments per word
       foreach($search_words as $needle) {

//        2.0.1 store issue id to reference_Array
          if(stripos($issue_string,$needle)!==false) 
          {   $cnt_i_findings++;
/*              $issue[$issue['project']."_".$issue['id']] = $issue['project']."_".$issue['id'];
              $ref_findings['issues'][$cnt_i_findings]['project'] = $issue['project'];
              $ref_findings['issues'][$cnt_i_findings]['id'] = $issue['id'];
              $ref_findings['issues'][$cnt_i_findings]['match'] = $needle;    
*/
              $ref_findings['issues'][$cnt_i_findings] = $issue;
              $ref_findings['issues'][$cnt_i_findings]['match'] = $needle;
          }

//        2.0.2 remember comment reference_Array
          if(stripos($comments,$needle)!==false) 
          {   $cnt_c_findings++;
/*              $issue[$issue['project']."_".$issue['id']] = $issue['project']."_".$issue['id'];              
              $ref_findings['comment'][$cnt_c_findings]['project'] = $issue['project'];
              $ref_findings['comment'][$cnt_c_findings]['id'] = $issue['id'];
              $ref_findings['comment'][$cnt_c_findings]['match'] = $needle;   
*/
              $ref_findings['comment'][$cnt_c_findings] = $issue;
              $ref_findings['comment'][$cnt_c_findings]['match'] = $needle;
          }
       }
    }    
    
//---------------------------------------------------------------------------------------
//    3. Output
        $found_tbl = '
          <table class="inline it_search_result">
            <tr><th>'.$this->getLang('search_Type').'</th><th>'.$this->getLang('th_project').'</th><th>'.$this->getLang('search_ID').'</th><th>'.$this->getLang('search_Subject').'</th></tr>'.NL;
//     3.1 loop through reference_Array
        if($cnt_i_findings > -1) {
          foreach($ref_findings['issues'] as $item) {
              $link      = 'doku.php?id='.$ID.'&do=showcaselink&showid='.$item['id'].'&project='.$item['project'];
              $text_snip = $this->xs_format($issues[$item['project']."_".$item['id']]['description']);
              $h_txt     = '<span class="search_hit">'.$item['match'].'</span>';
              $text_snip = str_ireplace($item['match'], $h_txt, $text_snip);
              
              $found_issues .= '<tr><td>'.$this->getLang('search_Issue').'</td>
                                    <td>'.$item['project'].'</td>
                                    <td><a href="'.$link.'" title="'.$this->getLang('search_Issue')." ".$item['id'].'">'.$item['id'].'</a></td>
                                    <td><b><a href="'.$link.'" title="'.$this->getLang('search_Issue')." ".$item['id'].'">'.$item['title'].'</a></b><br />'.$text_snip.'</td>
                                </tr>'.NL;
          }
        }
        if($cnt_c_findings > -1) {
          foreach($ref_findings['comment'] as $item) {
              if($conf['plugin']['issuetracker']['it_data']==false) $comments_file = DOKU_CONF."../data/meta/".$item['project']."_".$item['id']. '.cmnts';
              else $comments_file = DOKU_CONF."../". $conf['plugin']['issuetracker']['it_data'].$item['project']."_".$item['id']. '.cmnts';
              $comments      = unserialize(@file_get_contents($comments_file));
              $link          = 'doku.php?id='.$ID.'&do=showcaselink&showid='.$item['id'].'&project='.$item['project'];
              $is_txt        = '<b>&raquo;</b> '.$this->getLang('search_Issue');
              $found_issues .= '<tr style="background: lightgrey;" ><td>'.$is_txt.'</td><td>'.$item['project'].'</td><td><a href="'.$link.'" title="'.$this->getLang('search_Issue')." ".$item['id'].'">'.$item['id'].'</a></td><td>'.$item['title'].'</td></tr>'.NL;
              foreach($comments as $comment) {
                  $text_snip = $this->xs_format($comment['comment']);
                  if(stripos($comment['comment'],$needle)!==false) {
                      $h_txt         = '<span class="search_hit">'.$item['match'].'</span>';
                      $text_snip     = str_ireplace($item['match'],$h_txt,$text_snip);                      

                      $found_issues .= '<tr><td>'.$this->getLang('search_Comment').'</td>
                                            <td>'.$item['project'].'</td>
                                            <td><a href="'.$link.'#a'.$comment['id'].'" title="'.$this->getLang('search_Comment')." ".$comment['id'].'">'.$comment['id'].'</a></td>
                                            <td><a href="'.$link.'#a'.$comment['id'].'" title="'.$this->getLang('search_Comment')." ".$comment['id'].'">'.$text_snip.'</a></td>
                                        </tr>'.NL;
                  }  
              }
          }                               
        }
        // Render
        if(strlen($found_issues) === 0) { 
            msg('found nothing',-1);
            html_show();
             }            
        else {
            $Generated_Table = $this->getLang('search_hl1').NL.
                               $this->getLang('search_txt1').NL.'<br /><br />'.NL.
                               '<table class="it_searchbox"><tr><td>'.NL.
                               '    <form  method="post" action="doku.php?id=' . $ID . '&do=it_search">'.NL.
                               '       <label class="it__searchlabel">'.$this->getLang('lbl_search').'</label>'.NL.
                               '       <input class="itl__sev_filter" name="it_str_search" id="it_str_search" type="text" value="'.$this->parameter.'"/>'.NL.
                               '       <input type="hidden" name="project" id="project" value="'.$item['project'].'"/>'.NL.
                               '       <input class="itl__search_button" id="searchcase" type="submit" name="searchcase" value="'.$this->getLang('btn_search').'" title="'.$this->getLang('btn_search_title').'"/>'.NL.
                               '    </form>'.NL.
                               '</td></tr></table>'.NL.
                               $this->getLang('search_hl2').NL.'<div class="level2">'.NL. 
                               $found_tbl . $found_issues . '</table></div>'.NL;
        }    
//}
//---------------------------------------------------------------------------------------
?>


