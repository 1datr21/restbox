function jq_rbapi(url,events=null,opts=null)
{
    this.base_url = url;
    this.token = null;
    this.opts = opts || { use_cookie : true };
    if(events==null) events={ };
    this.events = events;
    if(this.opts.use_cookie)    // need ready event
    {
        this.load_sid();
    }
    else
    {
        var a = this;
        setTimeout(function(){ a.events.ready(); },0);
    }
}

jq_rbapi.prototype.load_sid = function() {
    this.token = this.get_sid();
    var a = this;
    if(this.token)
    {
    //    console.log("Token = "+this.token);        
        this.userinfo().then(function(uinfo){
            if(uinfo)
            {
                a.events.onAuth();
            }
            else
            {
                a.events.onLogout();
            }
            a.load_rb_forms();
            a.events.ready();
        });
    }
    else
    {
        setTimeout(function()
        { 
            // if session loaded
            a.load_rb_forms();
            a.events.ready(); 
        },0);
    }
}



jq_rbapi.prototype.set_sid = function(_sid) {
    $.cookie('rbtoken', _sid, { path: '/' });
}

jq_rbapi.prototype.get_sid = function() {
    return $.cookie('rbtoken');
}

jq_rbapi.prototype.sendform = function(form_el) {
    var serialized_data = $(form_el).serialize();
    return this.send($(form_el).attr('action'),serialized_data);
}


jq_rbapi.prototype.init_form = function(form_el) // form info with csrf
{
    rb.get($('#form_url').val()).then(function(fdata)
            {
                console.log(fdata);
                var csrf_input = $('#form_task input[type=hidden][role=csrf]').one();
                if(csrf_input==null)
                {
                    $('#form_task').append($('<input />').attr('type','hidden').attr('role','csrf').attr('name',fdata.csrf.csrf_id).attr('value',fdata.csrf.csrf_val));
                }
                else
                {
                    csrf_input.attr('name',fdata.csrf.csrf_id).attr('value',fdata.csrf.csrf_val);
                }// add hidden to form of task adding
            });
}

jq_rbapi.prototype.form_url = function(form_el,action) {
    var theaction = $(form_el).attr('action');
    var pieces = theaction.split('/');
    pieces[2]='validate';
    theaction =pieces.join("/");
    return '/?q='+theaction;

jq_rbapi.prototype.load_rb_forms = function()
{
    var a = this;
    $('form:not([norb])').each(function(idx)
    {
        a.loadform(this);
    });    
}

jq_rbapi.prototype.loadform = function(form_el) // form info with csrf
{
    var get_form_action = $(form_el).attr('forminfo');
    var get_form_url = null;
    if(get_form_action!==undefined)
    {
        get_form_url = get_form_action;
    }
    else
    {
        var get_form_url = $(form_el).attr('action');
    }

    rb.get(get_form_url).then(function(fdata)
    {
        console.log(fdata);

        var csrf_input = $(form_el).find('input[type=hidden][role=csrf]').one();
        if(csrf_input.length==0)
        {
            $(form_el).append($('<input />').attr('type','hidden').attr('role','csrf').attr('name',fdata.csrf.csrf_id).attr('value',fdata.csrf.csrf_val));
        }
        else
        {
            csrf_input.attr('name',fdata.csrf.csrf_id).attr('value',fdata.csrf.csrf_val);
        }// add hidden to form of task adding
    });

}

jq_rbapi.prototype.validateform = function(form_el) {
    var serialized_data = $(form_el).serialize();
    var theaction = $(form_el).attr('action');
    var pieces = theaction.split('/');
    pieces[2]='validate';
    theaction =pieces.join("/");
    return this.send(theaction,serialized_data);
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
            $.removeCookie('rbtoken');
            this.token = undefined;
            console.log("Session is dead");
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

jq_rbapi.prototype.make_q_addr = function(query)
{
    if( /\?q\=/.test(query) )
        return query;
    else
        return this.base_url+"/?q="+query;
}

jq_rbapi.prototype.get = function(query)
{
    var deffered = $.Deferred();
    var a = this;
    var query_real = this.make_q_addr(query);
    $.ajax( this.make_q_addr(query),{type : 'get', headers: {rbtoken: this.token}}).done(function( data ) 
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
    var fullquery = query;
    if(!query.match('/?q=')) 
    {
        fullquery = this.base_url+"/?q="+query;
    }
    $.ajax( fullquery, {type : 'post',data: formdata, headers: {rbtoken: this.token}}).done(function( data ) 
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