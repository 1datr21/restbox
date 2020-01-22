function jq_rbapi(url,events=null,opts=null)
{
    this.base_url = url;
    this.token = null;
    this.opts = opts || { use_cookie : true };
    if(events==null) events={ };
    this.events = events;
    if(this.opts.use_cookie)
        this.load_sid();
}

jq_rbapi.prototype.load_sid = function() {
    this.token = this.get_sid();
    if(this.token!==undefined)
    {
        var a = this;
        this.userinfo().then(function(uinfo){
            if(uinfo)
            {
                a.events.onAuth();
            }
            else
            {
                a.events.onLogout();
            }
        });
        
    }
}

jq_rbapi.prototype.set_sid = function(_sid) {
    $.cookie('rbtoken', _sid, { path: '/' });
}

jq_rbapi.prototype.get_sid = function() {
    return $.cookie('rbtoken');
}

jq_rbapi.prototype.detect_errors = function(_data)
{
    if(_data.hasOwnProperty("SESS_ID"))
    {
        this.token = _data.SESS_ID;
        this.set_sid(this.token);
    }
    if(_data.hasOwnProperty("SessExpired"))
    {
        if(this.events.hasOwnProperty("onLostAuth"))
        {
            this.events.onLostAuth();
        }
    }
    if(_data.hasOwnProperty("error"))
    {
        if(this.events.hasOwnProperty("onError"))
        {
            this.events.onError(_data.error.message);
        }
        return _data.error.message;
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
                a.set_sid(a.token);
                if(a.events.hasOwnProperty("onAuth"))
                {
                    a.events.onAuth(res); // on auth
                }
                resolve(data);
            }   
        });
    });   
}

jq_rbapi.prototype.userinfo = function()
{
    return this.get('userinfo');
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
                if(a.events.hasOwnProperty("onLogout"))
                {
                    a.events.onLogout();
                }
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