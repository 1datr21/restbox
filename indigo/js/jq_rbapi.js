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

jq_rbapi.prototype.set_form_errors = function(err_data,form_el) {
    for(key in err_data)
    {
        $(form_el).find('[field='+key+'][role=error]').text(err_data[key]);
    }
}

jq_rbapi.prototype.clear_form_errors = function(form_el) {
    
    $(form_el).find('[role=error]').text('');
    
}

jq_rbapi.prototype.sendform = function(form_el) {
    var serialized_data = $(form_el).serialize();
    var form_action = $(form_el).attr('action');
    var _action = this.get_q_seg(form_action);
    var q_validate = this.make_q_addr(this.action_seg_change(_action,'validate'));
    var q_submit = this.make_q_addr(this.action_seg_change(_action,'submit'));
    var a =  this;
  
    this.clear_form_errors(form_el);
    this.send(q_validate,serialized_data).then(
        function(qres)
        {
            
            if(qres==null)
            {
                a.send(q_submit,serialized_data).then(
                    function(qres)  // successfull send form
                    {
                        if(getattr(form_el,'autoclear',true))
                        {
                            a.autoclear_form(form_el);
                        }
                        //if( $(form_el).hasAttr('rblogin') && getattr(form_el,'rblogin',false))
                        if( ($(form_el).hasAttr('rblogin')) && (getattr(form_el,'rblogin',false)!==false))
                        {
                            a.exe_login(qres);
                        }
                        else if( ($(form_el).hasAttr('rblogout')) && (getattr(form_el,'rblogout',false)!==false))
                        {    
                            a.exe_logout(qres);
                        }
                        exe_event(form_el,'aftersubmit',qres)
                    });    
            }
            else
            {
                a.set_form_errors(qres,form_el);
            }
        }
    );
}

jq_rbapi.prototype.autoclear_form = function(form_el) { 
    var elements = $(form_el).find('input[type=text],textarea');
    for(idx=0;idx<elements.length;idx++)
    {
        var emptyval = getattr(elements[idx], 'defval','');
        $(elements[idx]).val(emptyval);
    }
}

jq_rbapi.prototype.init_form = function(form_el) // form info with csrf
{
    rb.get($('#form_url').val()).then(function(fdata)
    {    
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
}

jq_rbapi.prototype.load_rb_forms = function(parent_el=null)
{
    if(parent_el==null)
        parent_el = document;
    var a = this;
    var forms_to_load = Array.from($(parent_el).find('form:not([norb])'));

    $(parent_el).on('submit','form:not([norb])',function(e) {
        e.preventDefault();
        a.sendform($(e.target));
    });

    var chunked = forms_to_load.chunk(20);
    
    this.load_chunks(chunked);  
  
}

jq_rbapi.prototype.load_chunks = function(chunk_list,idx=0)
{
    if(idx<chunk_list.length)
    {
        var a = this;
        this.loadchunk(chunk_list[idx], function()
        {
            a.load_chunks(chunk_list,idx+1);
        });
    }
}

jq_rbapi.prototype.loadchunk = function(forms_chunk,_ready) // load one form chunk
{
    
    var a = this;
    var urls_str = this.make_q_addr( [].map.call(forms_chunk, function(el) {
        return a.get_q_seg(a.form_info_url(el));
      }).reverse().join(';') );
    __ready=_ready;
    this.get(urls_str,'array').then(
        function(fdata)
        {
            console.log(fdata);
            for(idx=0;idx<fdata.length;idx++)
            {
                a.loadform(forms_chunk[idx],fdata[idx]);
            }
            __ready();
        }
    );  
}

jq_rbapi.prototype.form_info_url = function(form_el) // form info with csrf
{
    var get_form_action = $(form_el).attr('forminfo');
    var get_form_url = null;
    if(get_form_action!==undefined)
    {
        return get_form_action;
    }
    else
    {
        return $(form_el).attr('action');
    }
    return null;
}


jq_rbapi.prototype.loadform = function(form_el, fdata) // form info with csrf
{ 
    var csrf_input = $(form_el).find('input[type=hidden][role=csrf]').one();
    if(csrf_input.length==0)
    {
        $(form_el).append($('<input />').attr('type','hidden').attr('role','csrf').attr('name',fdata.csrf.csrf_id).attr('value',fdata.csrf.csrf_val));
    }
    else
    {
        csrf_input.attr('name',fdata.csrf.csrf_id).attr('value',fdata.csrf.csrf_val);
    }// add hidden to form of task adding

    if(fdata.hasOwnProperty('addinfo'))
    {
        for(fld in fdata.addinfo)
        {
            var def = fdata.addinfo[fld].defdata;

            this.set_def_val(form_el,fld,def);
        
            if(fdata.addinfo[fld].hasOwnProperty('valuelist'))
            {
                

            }
        }
    }
}

jq_rbapi.prototype.set_def_val(form_el,fld,fld_val) // set value of element of the form marked fld 
{
  //  var the_element = $(form_el).find('[field='+fld+']');
  /*  if(the_element!=null) // 
    {

    }
    */
    //.val()
}

jq_rbapi.prototype.action_seg_change = function(theaction,newseg,segno=2) {
 
    var pieces = theaction.split('/');
    pieces[segno]=newseg;
    return pieces.join("/");
    
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
        delete _data.SESS_ID;
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
        delete _data.SessExpired;
    }
    if(_data.hasOwnProperty("csrf_changed"))
    {
        for(idx=0;idx<_data.csrf_changed.length;idx++)
        {
            var token_fld = $('input[type=hidden][role=csrf][name='+_data.csrf_changed[idx].token_old+']');
            $(token_fld).attr('name',_data.csrf_changed[idx].token_new).attr('value',_data.csrf_changed[idx].token_new_val); 
        }
        delete _data.csrf_changed; 
    }
    if(_data.hasOwnProperty("error"))
    {
        if(this.events.hasOwnProperty("onError"))
        {
            this.events.onError(_data.error.message);
        }

        var themes = _data.error.message;
        delete _data.error;
        
        return themes;
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
                a.exe_login(res);
            }   
        });
    });   
}

jq_rbapi.prototype.exe_login = function(res)
{
    if(this.events.hasOwnProperty("onAuth"))
    {
        this.events.onAuth(res); // on auth
    }
    resolve(data);
}

jq_rbapi.prototype.exe_logout = function(res)
{
    if(this.events.hasOwnProperty("onLogout"))
    {
        this.events.onLogout();
    }
    resolve(res);
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
                a.exe_logout(res);            
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


jq_rbapi.prototype.get_q_seg = function(query)
{
    var re = /\?q\=(.*)/g;
    var matches = re.exec(query);//.matchAll(/\?q\=(.*)/g);
    if(matches!=null)
    {
        return matches[1];
    }

    return query;    
}

jq_rbapi.prototype.format_json = function(json_data,_format='object')
{
    switch(_format)
    {
        case 'object':
            {
                var dkeys = Object.keys(json_data);
                if(dkeys.length===1)
                {
                    return json_data[0].response;
                }
            }
            return json_data;
        case 'array':
            res = new Array();
            for(key in json_data)
            {
                res.push(json_data[key].response);
            }
            return res;// json_data;
    }
}

jq_rbapi.prototype.get = function(query,_format='object')
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
           deffered.resolve( a.format_json(data,_format));
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
           deffered.resolve(a.format_json(data));
        }
      
    });

    return deffered;//.promise();
}

Object.defineProperty(Array.prototype, 'chunk', {
    value: function(chunkSize) {
      var R = [];
      for (var i = 0; i < this.length; i += chunkSize)
        R.push(this.slice(i, i + chunkSize));
      return R;
    }
  });

  $.fn.hasAttr = function(name) {  
    return this.attr(name) !== undefined;
 };

function exe_event(element,event,params)
{
    var attr_ev = $(element).attr(event);
    if(attr_ev!=null)
    {
        attr_ev(params);
    }
    
    $(element).trigger(event, params);
}

function getattr(instance,attrname,defval=null)
{
    var avalue = $(instance).attr(attrname);
    if(avalue==null)
        return defval;
    return avalue;
}