// NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
// IT'S ALL JUST JUNK FOR OUR DOCS!
// ++++++++++++++++++++++++++++++++++++++++++

/*!
 * JavaScript for Bootstrap's docs (https://getbootstrap.com)
 * Copyright 2011-2017 The Bootstrap Authors
 * Copyright 2011-2017 Twitter, Inc.
 * Licensed under the Creative Commons Attribution 3.0 Unported License. For
 * details, see https://creativecommons.org/licenses/by/3.0/.
 */

/* global Clipboard, anchors */

// var Modal = function(modal) {
//     this.modal = modal;
//     this.contentCache = {};
//     this.content = {
//         signIn: function() {
//             return  '<div id="errorAlert" class="alert text-danger"></div>'+
//                     '<div class="col-lg-12">'+
//                     '<div class="form-group">'+
//                     '<label class="col-lg-4">Account</label>'+
//                     '<input type="text" class="form-control col-lg-8" id="account">'+
//                     '</div>'+
//                     '<div class="form-group">'+
//                     '<label class="col-lg-4">Password</label>'+
//                     '<input type="text" class="form-control col-lg-8" id="password">'+
//                     '</div>'+
//                     '</div>'+                           
//                     '</div>';
//         },
//         signOut: function() {
//             return '';
//         },
//         product: function() {
//             return  '<div class="container-fluid">'+
//                     '<div class="row">'+
//                     '<div class="col-sm-3 modalImage wrapper" id="modalImage"></div>'+
//                     '<div class="col-sm-9">'+
//                     '<h3 class="name"></h3>'+
//                     '<p class="amount"></p>'+
//                     '<p class="price"></p>'+
//                     '</div>'+
//                     '</div>'+
//                     '</div>';
                    
//         }
//     };
//     this.size = {
//         large: function() {
//             return 'modal-lg';
//         },
//         small: function() {
//             return 'modal-sm';
//         },
//     };

//     // this.modal.on('click', '#confirm', $.proxy(Modal.confirm, this.modal));
//     this.modal.on('hidden.bs.modal', $.proxy(this.hidden, this));
    
// };

// Modal.prototype = {
//     constructor: Modal,
//     create: function(contentType, size) {
//         if (contentType && this.content[contentType]) {
//             if (typeof size === 'string') {
//                 this.modal.find('.modal-dialog').addClass(this.size[size]());
//             }

//             if (contentType in this.contentCache) {
//                 this.contentCache[contentType] && this.modal.data('status', contentType);
//             }
//             this.contentCache[contentType] = this.modal.data('status', contentType)
//                 .find('#content').html(this.content[contentType].apply(this));
//         } else {
//             $.error('This method not in modal');
//         }

//         return this;
//     },
//     hidden: function() {
//         this.modal.removeData();
//     },
//     display: function() {
//         this.modal.modal('show');
//     }

// };

var Ajax = function(options) {
    var options = options || {};

    this.defaults = {
        url:  options.url || '/JiCoffee/app/postFactory.php',
        type: options.type || 'POST',
        dataType: options.dataType || 'json',
        cache: false
    };

};

Ajax.prototype = {
    constructor: Ajax,
    ajaxStore: {},
    request: function() {
        var process = function(condition) {    
            var self = this;
            var parameters = arguments;

            // if (condition.data.operation in this.ajaxStore) {
            //     return this.execute.apply(this, arguments);
            // }
            
            this.defaults = $.extend(this.defaults, condition || {});

            $.ajax(this.defaults).done(function(result) {
                self.ajaxStore[condition.data.operation] = result;
                return self.execute.apply(self, parameters);
            });

        };
        
        return process.apply(this, arguments);
        
    },
    execute: function(condition, callback, service=undefined) {
        return (service !== undefined) ? callback.trigger(service, this.ajaxStore[condition.data.operation])
         : callback(this.ajaxStore[condition.data.operation]);
    },
    when: function() {
        return $.when.apply(undefined, arguments);
    },
    validate: function() {
        $.each(arguments, function(i, result) {
            if (result[1] !== 'success') {
                $.error('Ajax result'+ i +' has error.');
                return false;
            }
        });

        return true;
    }
    
};

var Service = function() {
    this.clientList = {};
};

Service.prototype = {
    listen: function(key, process) {
        if (!this.clientList[key]) {
            this.clientList[key] = [];
        }
        this.clientList[key].push(process);
    },
    trigger: function() {
        var key = Array.prototype.shift.call(arguments);

        if (!this.clientList[key] || this.clientList[key].length === 0) {
            return false;
        }

        for (var i in this.clientList[key]) {
            this.clientList[key][i].apply(this, arguments);
        }
    },
    remove: function(key, process) {
        var thisCache = this.clientList[key];

        if (!process) {
            thisCache && (thisCache.length = 0);
        } else {
            $.each(thisCache, function(i, fn) {
                if (fn === process) {
                    thisCache.splice(fn, 1);
                }
            });
        }
    }
};

var Url = (function() {

    var getUrlParameter = function() {
        var resource = {};

        window.location.search.substr(1).split("&").forEach(function(item) {
            resource[item.split("=")[0]] = item.split("=")[1]
        });

        return resource;
    };

    return {
        getUrlParameter: getUrlParameter()
    }

}());

var Login = function($modal, ajax) {
    this.modal = $modal;
    this.ajax = ajax;
    this.target = 'login';
    this.defaultEvent();
};

Login.prototype = {

    defaultEvent: function() {
        this.modal
            .on('click.switchInterface', 'button.switch', $.proxy(this.setAnimate, this))
            .on('submit', 'form', $.proxy(this.submitProcess, this));
    },
    setAnimate: function(event) {
        event.preventDefault();
        
        this.target = $(event.target).data('action');
        this.switchAnimate(this.modal.find('form').filter(':visible'), this.modal.find('#' + this.target + '-form'));
    },
    switchAnimate: function($oldForm, $newForm) {
        var $frame = $oldForm.parent();
        var oldHeight = $oldForm.height();
        var newHeight = $newForm.height();
        
        $frame.animate({height: newHeight}, 200, function() {
            $oldForm.hide();
            $newForm.fadeToggle();    
        });
    },
    submitProcess: function(event) {
        var submitCategory = {
            login: function(ajax) {
                var authentication = {};
                
                var respond = function(result) {
                    var loginStatus = {
                        accountFail: function() {
                            this.find('#account').focus().next().show('fast').end().parent().addClass('has-error');
                        },
                        passwordFail: function() {
                            this.find('#password').focus().next().show('fast').end().parent().addClass('has-error');
                        },
                        detectDeviceFail:function() {
                            this.find('div.alert-warning').addClass('alert-danger').find('#text-lost-msg').text('無法辨別');
                        },
                        success: function() {
                            this.find('#text-lost-msg').text('登入成功');
                                                      
                            setTimeout(function() {
                                window.location.reload();
                            }, 700);
                        },
                    };

                    this.find('div.form-group').removeClass('has-error').find('span.validation').hide();
                    loginStatus[result].apply(this);
                };

                this.serializeArray().map(function(item) {
                    authentication[item.name] = item.value;
                });

                $.extend(authentication, {operation: 'login'});
                ajax.request({data: authentication}, $.proxy(respond, this));
                setTimeout(function() {
                    $loading.hide();
                }, 1000);
            },
            lost: function(ajax) {
            },
            register: function(ajax) {
                var registerParams = {};
                var $password = this.find('#register-password');
                var $passwordAgain = this.find('#register-password-again');
                var respond = function(result) {
                    if (result.error) {
                        return this.find('#text-register-msg').text('註冊發生錯誤');
                    } else if (result.warning) {
                        return this.find('#text-register-msg').text('此信箱已有人註冊');
                    }

                    swal({
                      title: '註冊成功',
                      timer: 1500
                    });

                    this.find('button[data="login"]').trigger('click.switchInterface');
                };

                this.find('span.validation').hide();
                
                if ($password.val().length < 6) {
                    return $password.next().show('fast');
                }

                if ($password.val() !== $passwordAgain.val()) {
                    return $passwordAgain.next().show('fast');
                }
                
                this.serializeArray().map(function(item) {
                    registerParams[item.name] = item.value;
                });

                // console.log(registerParams);
                $.extend(registerParams, {operation: 'register'});
                ajax.request({data: registerParams}, respond);
            }
        };

        var $loading = $(event.target).find('img');

        event.preventDefault();
        $loading.show();
        submitCategory[this.target].apply(this.modal.find('#'+this.target+'-form'), [this.ajax]);
        
    }

};


var App = function(ajax, service) {
    this.ajax = ajax;
    this.service = service;
};

App.prototype = {
    load: function() {
        this.service.trigger('app');
    },
    clearProduct: function() {
        sessionStorage.removeItem('product');
    },
    sessionProductLoad: function() {
        return this.products = JSON.parse(sessionStorage.getItem('product'));
        // return (sessionStorage.getItem('product') !== null) ?  : false;        
    },
    cartLoad: function() {
        $('#cart').trigger('cartLoad', [this.products]);
    },
    transactionLoad: function() {
        $('#productList').trigger('listProduct', [this.products]);
    },
    menuLoad: function() {
        var $menuInclude = $('#menu').find('div[data-include]');
        $menuInclude.load('/JiCoffee/view/component/' + $menuInclude.data('include') + '.html');
    },
    goErrorPage: function() {
        location.replace('/JiCoffee/view/404.html')
    },
    proxyInit: function(access) {
        var verify =  function(result) {
            if (result !== 'Member') {
                return this.goErrorPage();
            }
            
            this.init();
        };

        if (access !== undefined) {
            return this.ajax.request(access, $.proxy(verify, this));
        }
        
        this.ajax.request({data: {operation: 'getUser', authority: true}}, $.proxy(verify, this));
    },
    init: function() {
        this.menuLoad();
        this.ajax.request({data: {operation: 'initialize'}}, this.initialize);
        this.service !== undefined ? this.load() : false;
    },
    initialize: function(result) {
        var configuration = {
            Visitor: function() {
                $('#user').on('click.login', 'a', function(event) {
                    event.preventDefault();
                    $('#modal').trigger('loginLoad');
                });

            },
            Member: function(result, ajax) {
                // display user Status...
                var logout = function() {
                    $(this).trigger('logoutProcess', [new Ajax]);
                };

                // $('ul.navbar-nav', '#menu').first().append(result.orderMenu);
                $('#user').addClass('dropdown user user-menu').html(result.userMenu).find('#logout')
                    .on('click.logout', logout);
            }
        };

        configuration[result.user].apply(this, [result]);
    },
    
};


// (function ($) {
//   'use strict'

//     $(function () {

//         $('a[rel^="prettyPhoto"]').prettyPhoto({
//           social_tools: false
//         });

//         $('#modal').one('loginLoad', function() {
//             var loginProcess = function() {
//                 var $modal = this.find('#login-modal');
//                 var login = new Login($modal, new Ajax);
//                 $modal.modal('show');
//             };

//             $(this).load('/JiCoffee/view/component/' + $(this).data('include') + '.html');
                
//             setTimeout($.proxy(loginProcess, $(this)), 50);
//         }).on('shown.bs.modal', function() {
//             $(this).find('input').first().focus();
//         });

//     })

// }(jQuery));
