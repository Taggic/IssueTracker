
function formatDoc(sCmd, sValue) {
  document.execCommand(sCmd, false, sValue); 
}
function cleanUp(obj)
{
  hp = document.getElementById(obj);
  hp.innerHTML = hp.innerHTML.replace(/(<([^>]+)>)/ig,"");
}

function makeCite()
{
    var html = "";
    var sel, range;
    if (typeof window.getSelection != "undefined") {
        var sel = window.getSelection();
        if (sel.rangeCount) {
            var container = document.createElement("blockquote");
            for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                container.appendChild(sel.getRangeAt(i).cloneContents());
            }
            html = container.innerHTML;

        }
    } else if (typeof document.selection != "undefined") {
        if (document.selection.type == "Text") {
            html = document.selection.createRange().htmlText;
        }
    }
    
    range = sel.getRangeAt(0);
    range.deleteContents();
    var post_p = document.createElement("p")
    range.insertNode(post_p);
    var post_br = document.createElement("br")
    range.insertNode(post_br);
    range.insertNode(container);

}

function makeCode()
{
    var html = "";
    var sel, range;
    if (typeof window.getSelection != "undefined") {
        var sel = window.getSelection();
        if (sel.rangeCount) {
            var container = document.createElement("code");
            for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                container.appendChild(sel.getRangeAt(i).cloneContents());
            }
            html = "<p>"+container.innerHTML+"</p>";

        }
    } else if (typeof document.selection != "undefined") {
        if (document.selection.type == "Text") {
            html = document.selection.createRange().htmlText;
        }
    }
    
    range = sel.getRangeAt(0);
    range.deleteContents();
    var post_p = document.createElement("p")
    range.insertNode(post_p);
    var post_br = document.createElement("br")
    range.insertNode(post_br);
    var p_container = document.createElement("p");
    p_container.appendChild(container);
    range.insertNode(p_container);
}

function getfColor(block)
{ 
	hp = document.getElementById("hoverpopup1");
	hp.style.visibility = "Hidden";
  var s_url=block.href;
  var pColor=s_url.substr(s_url.indexOf("#"));
  document.execCommand('forecolor',         false, pColor);
}

function getbColor(block)
{ 
	hp = document.getElementById("hoverpopup2");
	hp.style.visibility = "Hidden";
  var s_url=block.href;
  var pColor=s_url.substr(s_url.indexOf("#"));
  document.execCommand('backcolor',         false, pColor);

}

function ShowPopup(hoveritem, hoverpopup)
{ var posArray = findPos(document.getElementById(hoveritem));
  HidePopup("hoverpopup1", "hoverpopup2");
  hp = document.getElementById(hoverpopup);
	// Set position of hover popup
	hp.style.top  = (posArray[1]-250) + 'px';
	hp.style.left = (posArray[0]-200 ) + 'px';    
	// Set popup to visible
	hp.style.visibility = "Visible";
}

function HidePopup(hoverpopup1, hoverpopup2)
{
  document.getElementById("hoverpopup1").style.visibility = "Hidden";
  document.getElementById("hoverpopup2").style.visibility = "Hidden";
}

function resizeBoxId(obj,size) {
    var arows = document.getElementById(obj).height;
    document.getElementById(obj).height = parseInt(arows)+size * 10;
}

function findPos(obj){
    var posX = obj.offsetLeft;var posY = obj.offsetTop;
    while(obj.offsetParent){
        if(obj==document.getElementsByTagName('body')[0]){break}
        else{
            posX=posX+obj.offsetParent.offsetLeft;
            posY=posY+obj.offsetParent.offsetTop;
            obj=obj.offsetParent;
        }
    }    
    var posArray=[posX,posY];
    return posArray;
}