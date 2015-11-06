<?php
    // starts login session for user
    // need a session start in every file
    session_start();
    // this cookie checks for if a user has two tabs open and logs out in one tab
    // refreshing the page on the non-logged out page will redirect to index.php
    // since the user logged out
    if (!isset($_COOKIE['username'])) {
        // redirects to index.php
        header('Location: index.php');
    }
    // sets the username cookie so that the user is logged in across all open tabs
    // 0 is the timeout time so that if the browser closes, the user is logged out
    setcookie('username', $_SESSION['username'], 0);
    // echo $_SESSION['username'];
?>

<html>
	<head>
		<meta charset="UTF-8">
		<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script src="javascript.js"></script>
		<title>Messages</title>
	</head>

	<body>
        <ul class="nav nav-pills">
            <li><a href="logout.php">Logout</a></li>
            <li><a href="contacts.php">Contacts</a></li>
            <li><a href="actualMessageList.php">Message List</a></li>
            <li><a href="userProfile.php">My Profile</a></li>
            <li><a id="userProf">Profile</a></li>
            <li class="active"><a href="">Messages</a></li>
		</ul>

        <div id="messageContainer">
        </div>

        <textarea id="newMessage" rows="5" cols="40"></textarea>
		<button id="send">Send</button>

		<script type="text/javascript">
            //used to get the GET variables
            //for more information see:
            //http://papermashup.com/read-url-get-variables-withjavascript/
            function getUrlVars() {
                var parts = window.location.href;
		        var vars = parts.substring(parts.indexOf("=")+1, parts.length);
                return vars;
            }

            // type = sent or received
            // message = content of the message
            // time = time of message delivery
			function Message(type, message, time){
				this.type = type;
				this.message = message;
				this.time = time;
			}
			function getNewMessages(t, recpUser){
				var username = parse();
				/*TODO*/
				//get the user the message is supposed to be sent to
				//from the selected conversation

                $.ajax({
					type: 'GET',
					url: './getMessages.php',
					data: {username:username, recpUser: recpUser, time:t},
					success: function(data){
						/*TODO*/
						//instead of outputting to console,
						//output to html elements in order they were sent
						//and user colors or identifiers to indicate who
						//sent and who received
						var obj = jQuery.parseJSON(data);
                        var messages = [];

                        if(t == null){
                            var messSent = obj.messageSent;
                            var messSentTime = obj.messageSentTime;
                            for(i = 0; i < messSent.length; i++) {
                                    messages.push(new Message('s', messSent[i], messSentTime[i]));
                            }
                            var messRec = obj.messageReceived;
                            var messRecTime = obj.messageReceivedTime;
                            for(i = 0; i < messRec.length; i++){
                                messages.push(new Message('r', messRec[i], messRecTime[i]));
                            }
							messages.sort(function(a, b){
								return a.time - b.time;
							});
							for(i = 0; i < messages.length; i++){
								if(messages[i].type == 'r'){
									$('#messageContainer').append(obj.receiver + ": " + messages[i].message + "<br>");
								}
								else{
									$('#messageContainer').append(obj.sender + ": " + messages[i].message + "<br>");

								}
							}
			}
			else{
							var messageSent = obj.messageSent;
							var messageReceived = obj.messageReceived;
							if(messageSent == "" && messageReceived != ""){
								$('#messageContainer').append(obj.receiver + ": " + messageReceived[0] + "<br>");
							}
							else if(messageReceived == "" && messageSent != ""){
								$('#messageContainer').append(obj.sender + ": " + messageSent[0] + "<br>");
							}
						}
						getNewMessages(obj.timestamp, recpUser);
					}
				});
			}

			$('#send').click(function()
			{
				var message = $('#newMessage').val();
				if(message == ""){
					return;
				}
				//clears the textarea after a message is sent
				document.getElementById("newMessage").value="";
				var username = parse();
                var recpUser = getUrlVars();
				var t = (new Date).getTime();

				$.ajax(
				{
					type: 'GET',
					url: './sendMessage.php',
					data: {username:username, recpUser:recpUser, time:t , message:message}
				});
			});

            $(function(){
	        document.getElementById("userProf").setAttribute("href", "profile.php?userVar="+parse());
				var time = null;
                var recpUser = getUrlVars();
				getNewMessages(time, recpUser);
            });
		</script>
	</body>
</html>






