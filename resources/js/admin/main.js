// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import './polyfill'
import '../bootstrap'
// import cssVars from 'css-vars-ponyfill'
import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
import App from './App'
import router from './router/index'

// todo
// cssVars()

Vue.use(BootstrapVue)

/* eslint-disable no-new */
new Vue({
  el: '#admin',
  router,
  template: '<App/>',
  components: {
    App
  }
})
