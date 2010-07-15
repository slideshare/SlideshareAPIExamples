This is the SlideShare PHP API, and this contains the following files:

api_config.php - The API configuration file
example.php - The API usage example file
SSUtil.php - The API Class code which you have to include in your code
class.rss.php - This is a RSS generation helper file
rss.xml - Sample generated RSS feed file

To start using this you need to obtain an API key from SlideShare. That key has to be put in the api_config file.
i.e. 

$key='<Fill API key>';
$secret='<Fill Secret key>';

in api_config.php

In this following section we see all the function calls:

count_slideUser(Username) - This returns the total number of slideshows the user has uploaded.
count_slideTag(Tag) - This returns the total number of slidehshows with the given tag.
count_slideGroup(Group) - This returns the total number of slideshows found in the given group on slideshare
get_slideInfo(slideID) - This returns the information about the slide with the given slideID in an associative array. This will contain the title, description, thumbnail, and other meta information about the slideshow.
get_slideUser(Username,offset=0,limit=0) - This will return 'limit' number of the user's slideshows from a given 'offset' from the start. This system enables the developer to make an efficient pagination system.
get_slideTag(Tag,offset=0,limit=0) - This will return 'limit' number of a given tag's slideshows from a given 'offset' from the start. This system enables the developer to make an efficient pagination system.
get_slideGroup(Groupname,offset=0,limit=0) - This will return 'limit' number of the group's slideshows from a given 'offset' from the start. This system enables the developer to make an efficient pagination system.

get_RSS(url) - This allows you to extract any Slideshare RSS and convert that into an associative array easily.
make_RSS(title,description,date,slides,feed location,feed filename) - Want to generate your own Slideshare feed. This enables you to push in an array of slideshows (with the same associative format which get_slideInfo returns) and generate an RSS file.