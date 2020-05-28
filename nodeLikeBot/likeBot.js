const Twit = require('twit');
const Config = require('../likeBotConfig.json');

var T = new Twit({
    consumer_key:         Config.ApiKey,
    consumer_secret:      Config.ApiSecret,
    access_token:         Config.ConsumerKey,
    access_token_secret:  Config.ConsumerSecret,
    timeout_ms:           60*1000,  // optional HTTP request timeout to apply to all requests.
    strictSSL:            true,     // optional - requires SSL certificates to be valid.
});

let Stream = T.stream('statuses/filter',{track:'#interactionsMap'});


function faveTweet(tweetId){
    T.post('favorites/create',
        {
            id:tweetId
        },function (err, data, response) {
        if(err){
            console.log("failed to fave tweet: ");
            console.log(tweetId);
            console.log(err);
            return;
        }
        console.log("faved new tweet: "+tweetId);
    })
}

Stream.on('tweet',function (tweet) {
    console.log(`new tweet:  ${tweet.id_str}  from ${tweet.user.name}`);
    faveTweet(tweet.id_str);
});

Stream.on('error',function (err) {
    console.log(err);
    exit(1);
});
