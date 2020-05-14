var helpers = {
    rules : {},
    form : null,
    err : {},

    init : function(config) {
        this.rules = config.rules;
        this.form = document.getElementById(config.formID);
    },

    updateInput : function(attr){
        var el = this.form.elements[this.form.id.replace("-","")+"-"+attr];
        var next = el.nextElementSibling;
        if(typeof this.err[attr] !== "undefined"){
            el.classList.add("has-error");
            next.innerHTML = this.err[attr];
        } else {
            el.classList.remove("has-error");
            next.innerHTML = "";
        }
    },

    validateAttribute : function(attr){
        var currentRule = this.rules[attr];
        var value = this.form.elements[this.form.id.replace("-","")+"-"+attr].value.trim();
        var e = null;
        for(var i=0;i<currentRule.length;i++){
            switch(currentRule[i].type){
                case "required":
                    e = this.validateRequired(value,currentRule[i]);
                    break;
                case "regExp":
                    e = this.validateRegExp(value,currentRule[i]);
                    break;
                case "length":
                    e = this.validate(value,currentRule[i]);
                    break;
                case "file":
                    e = this.validateFile(this.form.elements[this.form.id.replace("-","")+"-"+attr].files[0],currentRule[i]);
                    break;
            }
            if(e!=null){
                this.err[attr] = e;
                break;
            }
        }
        if(e==null){
            delete(this.err[attr]);
        }
        this.updateInput(attr);
    },

    validateForm : function() {
        for(attr in this.rules){
            this.validateAttribute(attr);
        }
        var hasError = false;
        for(err in this.err){
            hasError = true;
        }
        return !hasError;
    },

    validateRequired : function(str,config){
        var valid = true;
        if(str.length == 0) valid=false;
        return valid ? null : config['message']
    },

    validate : function(param,config){
        var valid = true;
        switch(config[0]){
            case 'min':
                if(param.length<config[1]) valid=false;
                break;
            case 'max':
                if(param.length>config[1]) valid=false;
                break;
        }
        return valid ? null : config['message']+config[1];
    },

    validateRegExp : function(str,config){
        var valid = true;
        var regExp = new RegExp(config['pattern'].slice(1,config['pattern'].length-1));
        if(!regExp.test(str))
            valid = false;
        return valid ? null : config['message'];
    },

    validateFile : function(fileData,config){
        var valid = true;
        if(typeof config.allowedType !== "undefined" && typeof fileData !== "undefined"){
            fileType = fileData.type.replace("image/","");
            find = false;
            for(ft in config.allowedType){
                if(config.allowedType[ft]==fileType) {find = true; break;}
            }
            valid = find;
        }
        return valid ? null : config['message'];
    }
};
