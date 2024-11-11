import axios from 'axios';
import ws from './echo';
window.axios = axios;
window.Echo = ws;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
