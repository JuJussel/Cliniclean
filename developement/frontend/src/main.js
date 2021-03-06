import globals from './globals'
import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import '@fortawesome/fontawesome-free/css/all.css'
import SocketIO from 'socket.io-client'
import VueSocketIO from 'vue-socket.io'
import cardTab from './components/shared//CC_comp_card_tab'
import dateDisplay from './components/shared/CC_comp_date_display'
import selectInput from './components/shared/CC_comp_select_input'
import FullCalendar from '@fullcalendar/vue'
import Vuesax from 'vuesax-cc'
import 'vuesax-cc/dist/vuesax.css'
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import 'vue2-datepicker/locale/ja'
import http from './plugins/http'
import acl from './plugins/acl'
import LiquorTree from 'liquor-tree'

Vue.config.productionTip = false

Vue.use(new VueSocketIO({
  connection: SocketIO(globals.socketIOIP)
}))
Vue.use(Vuesax, {
  colors: {
    primary:'#00C7B2',
    success:'rgb(178,178,178)',
    danger:'rgb(242, 19, 93)',
    warning:'rgb(255, 130, 0)',
    dark:'#2c3e50'
  }
})

const moment = require('moment')
require('moment/locale/ja')
Vue.use(require('vue-moment'), {
  moment
})
Vue.use(LiquorTree)
Vue.use(http)
Vue.use(acl)

Vue.component('el-card-tab', cardTab)
Vue.component('dateDisplay', dateDisplay)
Vue.component('selectInput', selectInput)
Vue.component('datePicker', DatePicker)
Vue.component('FullCalendar', FullCalendar)

Vue.prototype.$eventHub = new Vue() // Global event bus
Vue.prototype.$globals = globals

new Vue({
  router,
  store,
  render: function (h) { return h(App) }
}).$mount('#app')
