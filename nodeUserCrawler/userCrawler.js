const Twit = require('twit');
const Config = require('../config.json');
const MongoClient = require('mongodb').MongoClient;

let Collection = null;

let Twitter = null;
let Working = false;
let Stop = false;

process.on('SIGINT', function() {
    console.log("Caught interrupt signal");
    console.log("Stopping...");

    if(Stop){
        console.log("fast exit");
        process.exit(1);
    }
    Stop = true;

    if(!Working){
        process.exit(0);
    }

});


async function main() {
    try{
        const client = await MongoClient.connect(Config.MongoHost, {
            useUnifiedTopology: true
        });

        console.log("mongodb connected");
        let db = client.db(Config.MongoDb);

        Collection = db.collection("users");

        console.log("got collection");

    }catch (e) {
        console.log(e);
        process.exit(1);
    }

    setInterval(getUserFromDb, 500);
}

async function getUserFromDb() {
    if(Stop){
        console.log("exit 0");
        process.exit(0);
    }
    if(Working){
        return;
    }

    Working = true;

    let doc = null;
    try{
        doc = await Collection.findOne({status: 0});
    }catch (e) {
        console.log(e);
        process.exit(1);
    }

    if (doc === null){
 //       console.log("nothing to do");
        Working = false;
        return;
    }

    console.log(doc);
    console.log("upgrading userstatus");

    try{
        await Collection.updateOne(
            {twitterId:doc.twitterId},
            {'$set':{status: 2}}
        );
    }catch(e){
        console.log(e);
        process.exit(1);
    }

    console.log("upgraded userstatus");


    Twitter = new Twit({
        consumer_key: Config.ApiKey,
        consumer_secret: Config.ApiSecret,
        access_token: doc.oauth_token,
        access_token_secret: doc.oauth_token_secret,
        timeout_ms: 60 * 1000,  // optional HTTP request timeout to apply to all requests.
        strictSSL: true,     // optional - requires SSL certificates to be valid.
    });


    let lastTweet = 0;
    let tweetCount = 0;
    let currentTweetCount = 0;
    let singleTweetResponse = 0;
    try {
        do {
            let opts = {
                user_id: doc.twitterId,
                count: Config.TweetChunk,
                include_rts:false
            };
            if (lastTweet > 0)
                opts["max_id"] = lastTweet;

            let res = await Twitter.get("statuses/user_timeline", opts);
            currentTweetCount = res.data.length;

            console.log("tweets: " + tweetCount+ " + "+currentTweetCount);

            if(currentTweetCount === 1)
                singleTweetResponse++;
            else
                singleTweetResponse = 0;

            if(currentTweetCount > 0){
                lastTweet = res.data[currentTweetCount-1].id_str;

                await addUsersToDb(doc.twitterId, res.data);
                tweetCount += currentTweetCount;
            }

            await Collection.updateOne(
                {twitterId:doc.twitterId},
                {'$set':{'tweets.current': tweetCount}}
            );

            if(singleTweetResponse >= 50)
                break;

        } while (tweetCount < Config.TweetsLimit && currentTweetCount > 0);

        console.log("done adding: "+tweetCount+" tweets");
        console.log("upgrading userstatus to done");
        try{
            await Collection.updateOne(
                {twitterId:doc.twitterId},
                {'$set':{status: 3}}
            );
        }catch (e) {
            console.log(e);
            process.exit(1);
        }

        console.log("upgraded userstatus");
        Working = false;

    } catch (e) {
        console.log(e);
        process.exit(1);
    }
}


async function addUsersToDb(currentUser, tweets) {
    for (let i = 0; i < tweets.length; i++) {
        let tweet = tweets[i];

        // if no reply ignore
        if (tweet.in_reply_to_user_id_str === null)
            continue;

        // ignore answers to own
        if(tweet.in_reply_to_user_id_str === currentUser+"")
            continue;

        let propertyName = {};
        propertyName['interactions.' + tweet.in_reply_to_user_id_str] = 1;

        await Collection.update(
            {twitterId: currentUser},
            {
                '$inc': propertyName
            }
        );

        console.log("addded tweet: " + tweet.id);
    }

}

main();

