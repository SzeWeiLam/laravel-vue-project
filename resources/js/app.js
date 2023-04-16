require('./bootstrap')

import { createApp } from 'vue'
import Datatable from './components/Datatable'

const app = createApp({})

app.component('datatable', Datatable)

app.mount('#app')