let loadingHidden = false;
let interval = null;
$(document).ready(function(){

    interval = window.setInterval(getStatus,500);
});

function getImages() {
    var url= $("#domain").val()+"images/all";
    $.get(url)
        .done(function (data) {
            console.log("done",data);
            $("#images").html(data);
        })
        .fail(function (data) {
            console.log("fail",data);
             M.toast({html: "something went wrong"});
        })
}

function getStatus() {
    var url= $("#domain").val()+"status";
    $.get(url)
        .done(function (data) {
            console.log("done",data);
            $("#stageTweets").css("width",data.stageTweets+"%");
            $("#stageImages").css("width",data.stageImages+"%");
            $("#stageCollage").css("width",data.stageCollage+"%");

            $("#statusTextTweets").html(data.stageTweets+"%");
            $("#statusTextImages").html(data.stageImages+"%");
            $("#statusTextCollage").html(data.stageCollage+"%");

            if(data.status === 3 && !loadingHidden){
                loadingHidden=true;
                $("#loading").slideUp();
                clearInterval(interval);
                getImages();
            }

            if(data.status === 99){
                loadingHidden=true;
                $("#loading").slideUp();
                $("#errormessage").html(data.message);
                $("#message").removeClass("hide");
                clearInterval(interval);
            }

        })
        .fail(function (data) {

            console.log("fail",data);
//                M.toast({html: data.message});
        })
}