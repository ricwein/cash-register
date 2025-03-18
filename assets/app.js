import {registerVueControllerComponents} from '@symfony/ux-vue';
import vuetify from "./vue/plugins/vuetify";

import './bootstrap.js';

import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css';
import 'vue3-toastify/dist/index.css';
import '@fortawesome/fontawesome-free/css/all.css';
import './scripts/setup-bootstrap';

import './styles/app.scss';


registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));
document.addEventListener('vue:before-mount', (event) => {
    const {app} = event.detail;
    app.use(vuetify)
});
