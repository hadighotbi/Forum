import Vue from "vue";

window._ = require('lodash');

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


let authorizations = require('./authorizations')

Vue.prototype.authorize = function (...params) {
    //Additional admin privileges here.
    if(! Window.App.signedIn) return false;

    if(typeof params[0] === 'string') {
        return authorizations[params[0]](params[1]);
    }
    return params[0](Window.App.user);
};

Vue.prototype.signedIn  = Window.App.signedIn;

window.events = new Vue();
window.flash = function (message, level = 'success') {
    window.events.$emit('flash', {message, level});
};
