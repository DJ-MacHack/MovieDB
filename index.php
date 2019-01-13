<head>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
th, td {
  padding: 15px;
}
th {
  text-align: left;
}
table {
  margin-left: 20px;
  border-spacing: 5px;
}
table tr:nth-child(even) {
  background-color: #CFCFCF;
}
table tr:nth-child(odd) {
  background-color: #fff;
}
table th {
  color: white;
  background-color: grey;
}
/* use class names to hide content for jQuery v3.0 - see https://github.com/jquery/jquery/issues/2308 */
.hidden { display: none; }

/* override child row hovering defined in the theme file */
#table2 tbody > tr.even.hover > td,
#table2 tbody > tr.even:hover > td,
#table2 tbody > tr.even:hover + tr.tablesorter-childRow > td,
#table2 tbody > tr.even:hover + tr.tablesorter-childRow + tr.tablesorter-childRow > td {
  background-color: #fff;
}
#table2 tbody > tr.odd.hover > td,
#table2 tbody > tr.odd:hover > td,
#table2 tbody > tr.odd:hover + tr.tablesorter-childRow > td,
#table2 tbody > tr.odd:hover + tr.tablesorter-childRow + tr.tablesorter-childRow > td {
  background-color: #ebf2fa;
}
#table2 tbody > tr.even:hover > td,
#table2 tbody > tr.odd:hover > td {
  background-color: #d9d9d9;
}
.column {
  float: left;
  width: 33.33%;
  padding: 5px;
}
.row::after {
  content: "";
  clear: both;
  display: table;
}
</style>
<link rel="stylesheet" href="tablesort/css/theme.default.css">
<!-- load jQuery and tablesorter scripts -->
<script type="text/javascript" src="tablesort/jquery.js"></script>
<script type="text/javascript" src="tablesort/js/jquery.tablesorter.js"></script>
<!-- tablesorter widgets (optional) -->
<script type="text/javascript" src="tablesort/js/jquery.tablesorter.widgets.js"></script>
</head>
<body>
<div id="infos" style="overflow: auto; height: 400px; margin-left: 15px">
<h1 id="demo"></h1>
<div class="row">
  <div class="column">
<p id="image"></p>
  </div>
  <div class="column">
<p id="image2"></p>
  </div>
</div>
<p id="link"></p>
</div>
<div id="back" style="margin: 10 10 10 10">
<button onClick="myFunction()">Infos</button>
<?php
$root = __DIR__;

function is_in_dir($file, $directory, $recursive = true, $limit = 1000) {
    $directory = realpath($directory);
    $parent = realpath($file);
    $i = 0;
    while ($parent) {
        if ($directory == $parent) return true;
        if ($parent == dirname($parent) || !$recursive) break;
        $parent = dirname($parent);
    }
    return false;
}

function getMimeType($filename)
{
    $mimetype = false;
    if(function_exists('finfo_open')) {
        // open with FileInfo
    } elseif(function_exists('getimagesize')) {
        // open with GD
    } elseif(function_exists('exif_imagetype')) {
       // open with EXIF
    } elseif(function_exists('mime_content_type')) {
       $mimetype = mime_content_type($filename);
    }
    return $mimetype;
}

function getDirectorySize($path){
    $bytestotal = 0;
    $path = realpath($path);
    if($path!==false && $path!='' && file_exists($path)){
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
            $bytestotal += $object->getSize();
        }
    }
    return $bytestotal;
}

$path = null;
if (isset($_GET['file'])) {
    $path = $_GET['file'];
    if (!is_in_dir($_GET['file'], $root)) {
        $path = null;
    } else {
        $path = '/'.$path;
    }
}

if (is_file($root.$path)) {
    readfile($root.$path);
    return;
}

if ($path) echo '<a href="?file='.urlencode(substr(dirname($root.$path), strlen($root) + 1)).' style="margin-left:20px">.. (back)</a><br /></div><div style="margin-top:10px; overflow: auto; height: 400px">';
else echo '<br /></div><div style="margin-top:10px; overflow: auto; height: 400px">';
echo '<table id="myTable" class="tablesorter"><thead><tr><th style="width: 400px" class="sorter-currency">File</th><th style="width: 300px" class="sorter-currency">Type</th><th style="width: 300px" class="sorter-currency">Size</th><th style="width: 100px">Infos</th></tr></thead><tbody>';
foreach (glob($root.$path.'/*') as $file) {
    $file = realpath($file);
    $link = substr($file, strlen($root) + 1);
	if ($file === "tablesort"){ continue;}
	echo '<tr>';
	if (is_file($file)){
		$size = filesize($file);
    	echo '<td><a href="?file='.urlencode($link).'">'.basename($file).'</a></td><td>';
		if (!getMimeType($file)){
			echo mime_content_type($file);
		} else {
			echo 'file';
		}
		echo '</td><td>'.$size/1000 .' KB</td><td></td>';
	} else {
		echo '<td><a href="?file='.urlencode($link).'">'.basename($file).'</a></td><td>'.mime_content_type($file).'</td><td>'.getDirectorySize($file)/1000 .' KB</td><td><button onclick="searchAPI(\''.basename($file).'\')">Get it</button><br />';
	}
	echo '</tr>';
}
echo '</table></div>';
?>
<script type="text/javascript">
$( function() {

  var $table1 = $( '#myTable' )
    .tablesorter({
      theme : 'blue',
      // this is the default setting
      cssChildRow : "tablesorter-childRow",
      // initialize zebra and filter widgets
      widgets : [ "zebra", "filter"],
      widgetOptions: {
        // include child row content while filtering, if true
        filter_childRows  : true,
        // class name applied to filter row and each input
        filter_cssFilter  : 'tablesorter-filter',
        // search from beginning
        filter_startsWith : false,
        // Set this option to false to make the searches case sensitive
        filter_ignoreCase : true
      }
    });
  $table1.find( '.tablesorter-childRow td' ).addClass( 'hidden' );
  $table1.delegate( '.toggle', 'click' ,function() {
    $( this )
      .closest( 'tr' )
      .nextUntil( 'tr.tablesorter-hasChildRow' )
      .find( 'td' )
      .toggleClass( 'hidden' );
    return false;
  });
  $( 'button.toggle-combined' ).click( function() {
    var wo = $table1[0].config.widgetOptions,
    o = !wo.filter_childRows;
    wo.filter_childRows = o;
    $( '.state1' ).html( o.toString() );
    $table1.trigger( 'search', false );
    return false;
  });

});
</script>
<script type="text/javascript">
function searchAPI(name){
var settings = {
  "async": true,
  "crossDomain": true,
  "url": "https://api.themoviedb.org/3/search/movie?include_adult=true&page=1&query="+name+"&language=en-US&api_key=a92c29da01d9421d10b1bdbb8b222f02",
  "method": "GET",
  "headers": {},
  "data": "{}"
}

$.ajax(settings).done(function (response) {
  console.log(response);
	document.getElementById("demo").innerHTML = response.results[0].original_title
	document.getElementById("image").innerHTML = "<img src=\"http://image.tmdb.org/t/p/w500/" + response.results[0].poster_path + "\" >"
	document.getElementById("image2").innerHTML = "<img src=\"http://image.tmdb.org/t/p/w500/" + response.results[0].backdrop_path + "\" >"
	document.getElementById("link").innerHTML = response.results[0].overview
	myFunction()
});}
</script>
<script type="text/javascript">
function myFunction() {
  var x = document.getElementById("infos");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script>
<script type="text/javascript">
$(document).ready(function() {
          $('.infos').hide();
});
</script>
</body>