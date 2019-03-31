<head>
<title>MovieDB</title>
<link rel="shortcut icon" href="tablesort/fav.png" type="image/png" />
<link rel="icon" href="tablesort/fav.png" type="image/png" />
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="tablesort/css/theme.default.css">
<script type="text/javascript" src="tablesort/jquery.js"></script>
<script type="text/javascript" src="tablesort/js/jquery.tablesorter.js"></script>
<script type="text/javascript" src="tablesort/js/jquery.tablesorter.widgets.js"></script>
</head>
<body>
<h3 style="margin-left: 20px; margin-top: 10px; color:#00465B; font-size:24px">MovieDB &copy; by Hendrik Haas 2019</h3>
<!-- info container for jQuery changes -->
<div id="container" style="min-width: 400px;">
<div id="infos" style="overflow: auto; height: 400px; margin-left: 20px;">
<h1 id="demo"></h1>
<div class="row">
  <div class="column" style="width: 600px;">
<p id="image"></p>
  </div>
  <div class="column" style="width: 600px;">
<p id="image2"></p>
  </div>
</div>
<p id="link"></p>
<p id="genre"></p>
<p id="release"></p>
<p id="vote"></p>
</div>
</div>
<div id="back" style="margin: 20 20 20 20">
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
$ext = array(); #array for all movies in dir
if ($path) echo '<a href="?file='.urlencode(substr(dirname($root.$path), strlen($root) + 1)).' style="margin-left:20px">.. (back)</a><br /></div><div style="margin-top:10px; overflow: auto; height: 400px">';
else echo '<br /></div><div style="margin-top:10px; overflow: auto; height: 400px">';
echo '<table id="myTable" class="tablesorter"><thead><tr><th style="width: 400px" class="sorter-currency">File</th><th style="width: 300px" class="sorter-currency">Type</th><th style="width: 300px" class="sorter-currency">Size</th><th style="width: 100px">Infos</th></tr></thead><tbody>';
foreach (glob($root.$path.'/*') as $file) {
    $file = realpath($file);
    $link = substr($file, strlen($root) + 1);
	if(preg_match('/^.*\.(mp4|mov|avi|mkv|flv|vob|wmv|mpg|m4v|3gp)$/i', $file)){
    $ext[] = $link;
	}          
	if (basename($file) === "tablesort"){ continue;}
	if (basename($file) === "index.php"){ continue;}
	if (basename($file) === "style.css"){ continue;}
	if (basename($file) === "README.md"){ continue;}
	if (is_file($file)){
		$size = filesize($file);
    	echo '<tr><td><a href="?file='.urlencode($link).'">'.basename($file).'</a></td><td>';
		if (!getMimeType($file)){
			echo mime_content_type($file);
		} else {
			echo 'file';
		}
		echo '</td><td>'.round($size/1000000, 2) .' MB</td><td><button id="but_info" onclick="searchAPI(\''.basename(dirname($file)).'\')">Get it</button><br /></td>';
	} else {
		echo '<tr><td><a href="?file='.urlencode($link).'">'.basename($file).'</a></td><td>'.mime_content_type($file).'</td><td>'.round(getDirectorySize($file)/1000000, 2) .' MB</td><td><button onclick="searchAPI(\''.basename($file).'\')">Get it</button><br /></td>';}
	echo '</tr>';
	}
echo '</table></div><div style="margin-left:20px">';
if (count($ext) > 0) {
	foreach ($ext as $mfile){
	$file_video = pathinfo($mfile);
	if(basename($file_video['extension'] === "mp4")){
		echo "<h2>".basename($mfile)."</h2>";
		echo "<video style=\"margin-left: 20px; margin-top: 10px\" width='1024' controls><source src=\"" .$mfile."\" type=\"video/mp4\">Your browser does not support the video tag.</video>";
		echo "<p>Download: </p><a href=\"".$mfile."\" about=\"_blank\">".$mfile."</a><br /><br />"; 
		} else {
		if(basename($file_video['extension'] === "ogg")){
		echo "<h2>".basename($mfile)."</h2>";
		echo "<video style=\"margin-left: 20px; margin-top: 10px\" width='1024' controls><source src=\"" .$mfile."\" type=\"video/ogg\">Your browser does not support the video tag.</video>";
		echo "<p>Download: </p><a href=\"".$mfile."\" about=\"_blank\">".$mfile."</a><br /><br />";
		} else {	if(basename($file_video['extension'] === "webm")){
		echo "<h2>".basename($mfile)."</h2>";
		echo "<video style=\"margin-left: 20px; margin-top: 10px\" width='1024' controls><source src=\"" .$mfile."\" type=\"video/webm\">Your browser does not support the video tag.</video>";
		echo "<p>Download: </p><a href=\"".$mfile."\" about=\"_blank\">".$mfile."</a><br /><br />";
		}  else { if(basename($file_video['extension'] === "mkv")){
		echo "<h2>".basename($mfile)."</h2>";
		echo "<video style=\"margin-left: 20px; margin-top: 10px\" width='1024' controls><source src=\"" .$mfile."\" type=\"video/mp4\">Your browser does not support the video tag. Use Google Chrome instead!</video>";
		echo "<p>Download: </p><a href=\"".$mfile."\" about=\"_blank\">".$mfile."</a><br /><br />";
		} else  {
		echo "<h2>".basename($mfile)."</h2>";
		echo "<p>File type is not supported!</p>";
		echo "<p>Download: </p><a href=\"".$mfile."\" about=\"_blank\">".$mfile."</a><br /><br />";
		}}}}			
	}
}
echo "</div>";
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
  console.log(response); //for debugging -> comment if you want
	document.getElementById("demo").innerHTML = "<h1><a href=\"https://www.themoviedb.org/movie/" + response.results[0].id + "\" target=\"_blank\" >" + response.results[0].original_title + "</a></h1>"
	document.getElementById("image").innerHTML = "<img src=\"http://image.tmdb.org/t/p/w500/" + response.results[0].poster_path + "\" >"
	document.getElementById("image2").innerHTML = "<img src=\"http://image.tmdb.org/t/p/w500/" + response.results[0].backdrop_path + "\" >"
	document.getElementById("link").innerHTML = "<p style=\"width: 1000px\">" + response.results[0].overview + "</p>"
	var date=new Date(response.results[0].release_date);
    day=date.getDate();
    month=date.getMonth();
    month=month+1;
    if((String(day)).length==1)
    day='0'+day;
    if((String(month)).length==1)
    month='0'+month;

    dateT=day+ '.' + month + '.' + date.getFullYear();
	document.getElementById("release").innerHTML = "<p style=\"width: 400px\">Release date: " + dateT + "</p>"
	document.getElementById("vote").innerHTML = "<p style=\"width: 400px\">Viewer rating: " + response.results[0].vote_average + " / 10</p>"
	var le = response.results[0].genre_ids.length;
	var gen = "<p>Genre: ";
	var i;
	for (i = 0; i < le; i++) { 
		if(response.results[0].genre_ids[i] === 18) { gen = gen + "Drama, "; }
		if(response.results[0].genre_ids[i] === 28) { gen = gen + "Action, "; }
		if(response.results[0].genre_ids[i] === 12) { gen = gen + "Adventure, "; }
		if(response.results[0].genre_ids[i] === 878) { gen = gen + "Science-Ficition, "; }
		if(response.results[0].genre_ids[i] === 16) { gen = gen + "Animation, "; }
		if(response.results[0].genre_ids[i] === 35) { gen = gen + "Comedy, "; }
		if(response.results[0].genre_ids[i] === 80) { gen = gen + "Crime, "; }
		if(response.results[0].genre_ids[i] === 53) { gen = gen + "Thriller, "; }
		if(response.results[0].genre_ids[i] === 99) { gen = gen + "Documentary, "; }
		if(response.results[0].genre_ids[i] === 10751) { gen = gen + "Family, "; }
		if(response.results[0].genre_ids[i] === 14) { gen = gen + "Fantasy, "; }
		if(response.results[0].genre_ids[i] === 36) { gen = gen + "History, "; }
		if(response.results[0].genre_ids[i] === 10402) { gen = gen + "Music, "; }
		if(response.results[0].genre_ids[i] === 9648) { gen = gen + "Mystery, "; }
		if(response.results[0].genre_ids[i] === 10749) { gen = gen + "Romance, "; }
		if(response.results[0].genre_ids[i] === 10770) { gen = gen + "TV-Movie, "; }
		if(response.results[0].genre_ids[i] === 10752) { gen = gen + "War, "; }
		if(response.results[0].genre_ids[i] === 37) { gen = gen + "Western, "; }
		if(response.results[0].genre_ids[i] === 27) { gen = gen + "Horror, "; }
	}
	gen = gen.slice(0, -2);
	gen = gen + "</p>";
	document.getElementById("genre").innerHTML = gen;
});
var x = document.getElementById("infos");
  if (x.style.display === "none") {
	  x.style.display = "block";	//open info block if hidden
  }
}
</script>
<script type="text/javascript">
function myFunction() {	//closes or opens the info container
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
		  myFunction();	//hide info container on initial page load
});
</script>
</body>