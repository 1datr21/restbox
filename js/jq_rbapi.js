function jq_rbapi(url)
{
    this.base_url = url;
    this.token = null;
}

jq_rbapi.prototype.auth = function(_login_or_email,passw)
{
  //  var deferred = $.Deferred();
    return new Promise((resolve, reject) => {
        var a = this;
        $.post( this.base_url+"/?q=auth", { login: _login_or_email, password: passw }).done(function( data ) 
        {       
            // то же что reject(new Error("o_O"))
            if(data.hasOwnProperty("error"))
                reject(new Error(data.error.mess));

            else
            {
                a.token = data[0].response.SESS_ID;
                resolve();
            }
        });
      //  console.log(data );
    });
   
}

jq_rbapi.prototype.get = function(query)
{
    var deffered = $.Deferred();
    var a = this;
    $.ajax( this.base_url+"/?q="+query,{type : 'get', headers: {rbtoken: this.token}}).done(function( data ) 
    {       
            // то же что reject(new Error("o_O"))
        if(data.hasOwnProperty("error"))
            deffered.reject(new Error(data.error.mess));

        else
        {
                //a.token = ;
            deffered.resolve(data[0].response);
        }
    });
      //  console.log(data );

    return deffered;//.promise();
}

jq_rbapi.prototype.send = function(query,data)
{

}