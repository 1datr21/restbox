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