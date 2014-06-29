<?php
include_once 'SSUtil.php';

$apiobj=new SSUtil();

echo '#of slides with user variable_orr - '.$apiobj->count_slideUser('variable_orr')."<br/>";
echo '# of slides in the web 2.0 group - '.$apiobj->count_slideGroup('web-20')."<br/>";
echo '# of slides tagged as marketing - '.$apiobj->count_slideTag('marketing')."<br/>";

print_r($apiobj->get_slideUser('variable_orr',0,50));
print_r($apiobj->get_slideInfo(47236));

//RSS utility functions
$apiobj->get_RSS('http://www.slideshare.net/rss/latest');
echo $apiobj->make_RSS('Test feed','Test description','12',$apiobj->get_slideUser('variable_orr',0,50));

?>