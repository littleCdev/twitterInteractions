const Twit = require('twit');
const Config = require('../config.json');
const MongoClient = require('mongodb').MongoClient;
const {exec} = require("child_process");
const Fs = require('fs');

let Collection = null;

let Twitter = null;

let working = false;

function execPromise(command) {
    return new Promise(function(resolve, reject) {
        exec(command, (error, stdout, stderr) => {
            if (error) {
                reject(error);
                return;
            }

            resolve(stdout.trim());
        });
    });
}

async function main() {
    let client = null;
    try{
        client = await MongoClient.connect(Config.MongoHost, {useNewUrlParser: true});
    }catch (e) {
        console.log(e);
        process.exit(1);
    }

    console.log("mongodb connected");
    let db = client.db(Config.MongoDb);
    Collection = db.collection("users");

    console.log("got collection");

    setInterval(getUserFromDb, 500);
}

async function setError(twitterId,message){
    await Collection.updateOne(
        {'twitterId':twitterId},
        {'$set':{
                'status':99,
                'message':message}}
    );
}

async function getUserFromDb() {
    if(working)
        return;
    working = true;
    let doc = null;

    try {
        doc = await Collection.findOne({status: 3});
    }catch (e) {
        console.log(e);
        process.exit(1);
    }

    if (doc === null){
        //console.log("nothing to do");
        working = false;
        return;
    }


    Twitter = new Twit({
        consumer_key: Config.ApiKey,
        consumer_secret: Config.ApiSecret,
        access_token: doc.oauth_token,
        access_token_secret: doc.oauth_token_secret,
        timeout_ms: 60 * 1000,  // optional HTTP request timeout to apply to all requests.
        strictSSL: true,     // optional - requires SSL certificates to be valid.
    });



    console.log("upgrading userstatus");
    try{
        await Collection.updateOne(
            {twitterId:doc.twitterId},
            {
                '$set':{
                    status: 4,
                    'download.total':0,
                    'download.current':0
                }}
        );
    }catch (e) {
        console.log(e);
        process.exit(1);
    }
    console.log("upgraded userstatus");



    console.log(doc.interactions);
    // sort array by interactions count
    let sortable = [];
    for (let interaction in doc.interactions) {
        sortable.push([interaction, doc.interactions[interaction]]);
    }
    sortable.sort(function(a, b) {
        return  b[1]-a[1];
    });


    // add the first 70 entries into an new array
    // 70 should be enough to fill "404" accounts
    let users = [];
    let userIds = ""; // to query twitter api
    for(let i=0;i<sortable.length;i++){
        users.push({
            userid:sortable[i][0],
            interactions:sortable[i][1],
            url: "",
            localpath:""
        });
        userIds = userIds+","+sortable[i][0];
        if(i===70)
            break;
    }
    // remove leading ,
    userIds = userIds.substring(1);

    // get info of all users from twitter api
    try{
        let userInfos = null;
        try{
            userInfos= await Twitter.post("users/lookup",{
                user_id:userIds,
                include_entities:false
            });
        }catch (e) {
            if(e.statusCode === 404){
                await setError(doc.twitterId,'sorry, it seems like you didn\'t interact much :(');
                working = false;
                return;
            }else {
                await setError(doc.twitterId,'something went wrong');
                working = false;
                return;
            }
        }

        // assign profile image url to every user
        for(let i=0;i<userInfos.data.length;i++){
            let currentUserId = userInfos.data[i].id;
            let currentUserProfilePicture = userInfos.data[i].profile_image_url_https.replace("_normal","");
            let localFileName = currentUserProfilePicture.substring(currentUserProfilePicture.lastIndexOf("/")+1);

            users.find(x=>x.userid == currentUserId).url = currentUserProfilePicture;
            users.find(x=>x.userid == currentUserId).localpath = localFileName;
        }
        console.log(users);

        // remove missing users from the array
        for(let i=users.length-1;i>=0;i--){
            if(users[i].url.length === 0){
                console.log(users[i].userid+" is an dead account");
                users.splice(i,1);
            }
        }

        let usersToDownload = 0;
        if(users.length > 50){
            usersToDownload = 50;
        }else if(users.length >= 19){
            usersToDownload = 19;
        }else if(users.length >= 11){
            usersToDownload = 11;
        }else {
            await setError(doc.twitterId,'sorry, it seems like you didn\'t interact much :(');
            working = false;
            return;
        }

        users = users.slice(0,usersToDownload);


        await Collection.updateOne(
            {twitterId:doc.twitterId},
            {'$set':{'download.total':usersToDownload}}
            );



        let userPath = Config.DownloadPath+doc.twitterId+"/";
        if (!Fs.existsSync(userPath)) {
            Fs.mkdirSync(userPath);
        }

        try {
            await execPromise(`rm -f ${userPath}*`);
        }catch (e) {
            console.log("err: "+e);
        }

        for(let i=0;i<usersToDownload;i++){
            let command = `wget -P ${userPath} ${users[i].url}`;

            await execPromise(command);

            try{
                await Collection.updateOne(
                    {twitterId:doc.twitterId},
                    {'$set':{'download.current':(i+1)}}
                );
            }catch (e) {
                // ignore errors here, just for progress, not needed
                console.log(e);
            }

            console.log(`downloaded ${users[i].url}`);
        }

    }catch (e) {
        console.log(e);
        working = false;
        return;
    }

    await Collection.updateOne(
        {twitterId:doc.twitterId},
        {'$set':{
            'status':5,
            'files':users
        }}
    );
    console.log("done with user "+doc.twitterId);
    working = false;
}

main();