import {registerVueControllerComponents} from '@symfony/ux-vue';
import vuetify from "./vue/plugins/vuetify";
import Vue3Toastify, {toast} from "vue3-toastify";

import './bootstrap.js';

import 'bootstrap'
import 'vue3-toastify/dist/index.css';
import 'vuetify/styles'
import './scripts/setup-bootstrap';

import './styles/app.scss';

registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));
document.addEventListener('vue:before-mount', (event) => {
    const {app} = event.detail;
    app.use(vuetify)
    app.use(Vue3Toastify, {
        position: toast.POSITION.TOP_LEFT,
        theme: toast.THEME.DARK,
    })
});
