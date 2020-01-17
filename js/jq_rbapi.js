function jq_rbapi(url,events=null)
{
    this.base_url = url;
    this.token = null;
    if(events==null) events={ };
    this.events = events;
}

jq_rbapi.prototype.detect_errors = function(_data)
{
    if(_data.hasOwnProperty("error"))
    {
        if(this.events.hasOwnProperty("onError"))
        {
            this.events.onError(_data.error.message);
        }
        return _data.error.mess;
    }
    else
    {
        if(Object.prototype.toString.call(_data) === "[object String]")
        {
            if(this.events.hasOwnProperty("onUnhandledError"))
            {
                this.events.onUnhandledError(_data);
            }
            return _data;
        }
        else
        {
            return false;
        }
    }
}

jq_rbapi.prototype.auth = function(_login_or_email,passw)
{
  //  var deferred = $.Deferred();
    return new Promise((resolve, reject) => {
        var a = this;
        $.ajax( this.base_url+"/?q=auth", {type : 'post',data: { login: _login_or_email, password: passw }, headers: {rbtoken: this.token}} ).done(function( data ) 
        {      
            var res = a.detect_errors(data); 
            if(res!==false)
            {
                reject(new Error(res));
            }
            else
            {
                a.token = data[0].response.SESS_ID;
                resolve(data);
            }   
        });
    });   
}

jq_rbapi.prototype.logout = function()
{
    return new Promise((resolve, reject) => {
        var a = this;
        $.ajax( this.base_url+"/?q=logout", {type : 'post', headers: {rbtoken: this.token}} ).done(function( data ) 
        {       
            var res = a.detect_errors(data); 
            if(res!==false)
            {
                reject(new Error(res));
            }
            else
            {
               // a.token = data[0].response.SESS_ID;
                resolve(res);
            }
        });
    });
}

jq_rbapi.prototype.get = function(query)
{
    var deffered = $.Deferred();
    var a = this;
    $.ajax( this.base_url+"/?q="+query,{type : 'get', headers: {rbtoken: this.token}}).done(function( data ) 
    {      
        var res = a.detect_errors(data); 
        if(res!==false)
        {
            deffered.reject(new Error(res));
        }
        else
        {
           // a.token = data[0].response.SESS_ID;
           deffered.resolve(data[0].response);
        }              
    });

    return deffered;//.promise();
}

jq_rbapi.prototype.send = function(query,formdata)
{
    var deffered = $.Deferred();
    var a = this;
    $.ajax( this.base_url+"/?q="+query,{type : 'post',data: formdata, headers: {rbtoken: this.token}}).done(function( data ) 
    {       

        var res = a.detect_errors(data); 
        if(res!==false)
        {
            deffered.reject(new Error(res));
        }
        else
        {
           // a.token = data[0].response.SESS_ID;
           deffered.resolve(data[0].response);
        }
      
    });

    return deffered;//.promise();
}