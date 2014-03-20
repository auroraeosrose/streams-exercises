<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>PRETTY PICTURES FROM MICHAEL MACLEAN</title>

</head>

<body>
<ul>
    <?php
error_reporting(-1);
ini_set('display_errors', 1);

$mgdm_feed = 'http://api.flickr.com/services/feeds/photos_public.gne?id=51035813914@N01&lang=en-us&format=rss2';
$data = file_get_contents($mgdm_feed);

$posts = new SimpleXMLElement($data);

$count = 0;
foreach($posts->channel->item as $item ) {
    if($count == 6) {
        break;
    }

    echo '<li><a href="' . $item->link . '">' .
                '<img ' .
                'src="' .  $item->children('http://search.yahoo.com/mrss/')->thumbnail->attributes()->url . '" ' .
                'title="' . htmlentities( $item->title )  . '" ' .
                "/></a></li>\n";
    $count++;
}
?>
</ul>

</body>

</html>