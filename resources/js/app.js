
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import Vuex from 'vuex';
import Notifications from 'vue-notification';

Vue.use(Vuex);
Vue.use(Notifications);

const store = new Vuex.Store({
    state: {
      modal: {
          active: false,
          taskId: null
      }
    },
    mutations: {
      setModal (state, {active, taskId}) {
        state.modal.active = active;
        state.modal.taskId = taskId;
      }
    }
  })
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('Modal', require('./components/Modal.vue'));
Vue.component('Score', require('./components/Score.vue'));
Vue.component('Task', require('./components/Task.vue'));
Vue.component('Rules', require('./components/Rules.vue'));
Vue.component('main-chart', require('./components/MainChart.vue'));

const app = new Vue({
    el: '#app',
    store
});