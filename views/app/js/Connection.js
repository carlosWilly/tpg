 var conn = new WebSocket("ws://localhost:2000?mode=099");
(function(){
     conn.onopen=function (e)   { 
      console.log("Connection established!"); 
      conn.send(JSON.stringify({me:toke_main}));
     }; 
     conn.onmessage = function (e)   { 
         var  data=$.parseJSON(e.data); 
         if (data.action==0) addMessage (data); 
         if (data.action==1) addNotification(data);
     };  
 })();









