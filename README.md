# Yahoo Fantasy Sports tracker

This is a small web app created to track one or more Yahoo fantasy sports leagues. Each install can only track one year of one sport. 

## Configuration

All the config is done in stuff/creds.json, there is a creds-sample.json to get you started, rename this file or the app won't work.

1. siteTitle - Used in the title bar and the h1 at the top of the page.
2. consumer_key and consumer_secret - Yahoo Developer Network credentials for the app. [Create a key](https://developer.yahoo.com/apps/create/)
3. game_key - this is the key that identifies the sport and the year.
4. leagues - this is an array of the ids for the leagues you want to track.
5. colours - this is an array of hex colours (could be CSS colour names too) of the background colours for the different leagues. They're assigned in order and you should have at least as many different colours as you have leagues.

There's really no error checking done and every once in a while it will time out (more so with more leagues) and return no data.

