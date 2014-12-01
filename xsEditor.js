<script type="text/javascript">
  function resizeBoxId(obj,size) {
      var arows = document.getElementById(obj).rows;
      document.getElementById(obj).rows = arows + size;
  }
  function doHLine(tag1,obj)
  { textarea = document.getElementById(obj);
  	if (document.selection) 
  	{     // Code for IE
  				textarea.focus();
  				var sel = document.selection.createRange();
  				sel.text = tag1 + sel.text;
  	}
    else 
    {   // Code for Mozilla Firefox
     		var len = textarea.value.length;
     	  var start = textarea.selectionStart;
     		var end = textarea.selectionEnd;
      		
     		var scrollTop = textarea.scrollTop;
     		var scrollLeft = textarea.scrollLeft;
      		
        var sel = textarea.value.substring(start, end);
 		    var rep = tag1 + sel;
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
      		
     		textarea.scrollTop = scrollTop;
     		textarea.scrollLeft = scrollLeft;
  	}
  }
  function doLink(tag1,tag2,obj)
  {   var sel;
      textarea = document.getElementById(obj);
      var url = prompt('Enter the URL:','http://');
      var scrollTop = textarea.scrollTop;
      var scrollLeft = textarea.scrollLeft;
      
      if (url != '' && url != null) 
      {   if (document.selection) 
          {   textarea.focus();
              var sel = document.selection.createRange();
              
              if(sel.text=='') { sel.text = '<a href=\"' + url + '\">' + url + '</a>'; }
              else { sel.text = '<a href=\"' + url + '\">' + sel.text + '</a>'; }				
          }
          else 
          {   var len = textarea.value.length;
              var start = textarea.selectionStart;
              var end = textarea.selectionEnd;
              var sel = textarea.value.substring(start, end);
              
              if(sel==''){ sel=url; } 
              else { var sel = textarea.value.substring(start, end); }
              
              var rep = '<a href=\"' + url + '\">' + sel + '</a>';
              textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
              textarea.scrollTop = scrollTop;
              textarea.scrollLeft = scrollLeft;
        	}
      }
  }
  function doAddTags(tag1,tag2,obj)
  { textarea = document.getElementById(obj);
  	// Code for IE
  	if (document.selection) 
  			{ textarea.focus();
  				var sel = document.selection.createRange();
  				sel.text = tag1 + sel.text + tag2;
  			}
     else 
      {  // Code for Mozilla Firefox
  		  var len = textarea.value.length;
  	    var start = textarea.selectionStart;
  		  var end = textarea.selectionEnd;
  		
  		  var scrollTop = textarea.scrollTop;
  		  var scrollLeft = textarea.scrollLeft;
  		
        var sel = textarea.value.substring(start, end);
  		  var rep = tag1 + sel + tag2;
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
  		
  		  textarea.scrollTop = scrollTop;
  		  textarea.scrollLeft = scrollLeft;
  	}
  }
  
  function doList(tag1,tag2,obj)
  {
      textarea = document.getElementById(obj);

  		if (document.selection) 
  			{ // Code for IE
  				textarea.focus();
  				var sel = document.selection.createRange();
  				var list = sel.text.split("\n");
  		
  				for(i=0;i<list.length;i++) 
  				{
  				list[i] = "[li]" + list[i] + "[/li]";
  				}
  				sel.text = tag1 + "\n" + list.join("\n") + "\n" + tag2;
  			} 
      else
  			{ // Code for Firefox
  		    var len = textarea.value.length;
  	      var start = textarea.selectionStart;
  		    var end = textarea.selectionEnd;
  		    var i;

  		    var scrollTop = textarea.scrollTop;
  		    var scrollLeft = textarea.scrollLeft;

          var sel = textarea.value.substring(start, end);
  		    var list = sel.split("\n");
  		
      		for(i=0;i<list.length;i++) 
      		{ list[i] = "[li]" + list[i] + "[/li]"; }

      		var rep = tag1 + "\n" + list.join("\n") + "\n" +tag2;
      		textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);

      		textarea.scrollTop = scrollTop;
      		textarea.scrollLeft = scrollLeft;
      }
  }
 </script>