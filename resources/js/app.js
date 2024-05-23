import './bootstrap';
import { createApp } from 'vue';

import Notiflix from 'notiflix';
import VueDataTablesNet from 'vue-datatables-net'; // Importa VueDataTablesNet
import Chart from 'chart.js/auto';

import 'datatables.net';
import 'datatables.net-bs4/css/dataTables.bootstrap4.min.css'; // Importa el CSS de DataTables Bootstrap 4

Notiflix.Notify.init({
  useIcon: true,
  useFontAwesome: true, // Cambiar a true para usar FontAwesome
  fontAwesomeIconStyle: 'basic',
  fontAwesomeIconSize: '34px',
    width: 'auto', // Ancho automático para adaptarse al contenido
    position: 'center-top', // Posición en la parte superior central de la pantalla
    distance: '10px',
    opacity: 1,
    borderRadius: '5px',
    rtl: false,
    timeout: 3000,
    messageMaxLength: 110,
    backOverlay: false,
    backOverlayColor: 'rgba(0,0,0,0.5)',
    plainText: true,
    showOnlyTheLastOne: false,
    clickToClose: false,
    pauseOnHover: true,
    ID: 'NotiflixNotify',
    className: 'notiflix-notify',
    zindex: 4001,
    fontFamily: 'Quicksand',
    fontSize: '13px',
    cssAnimation: true,
    cssAnimationDuration: 400,
    cssAnimationStyle: 'fade',
    closeButton: false,
    useIcon: true,
    useFontAwesome: false,
    fontAwesomeIconStyle: 'basic',
    fontAwesomeIconSize: '34px',
    success: {
      background: '#32c682',
      textColor: '#fff',
      childClassName: 'notiflix-notify-success',
      notiflixIconColor: 'rgba(0,0,0,0.2)',
      fontAwesomeClassName: 'fas fa-check-circle',
      fontAwesomeIconColor: 'rgba(0,0,0,0.2)',
      backOverlayColor: 'rgba(50,198,130,0.2)',
    },
    failure: {
      background: '#ff5549',
      textColor: '#fff',
      childClassName: 'notiflix-notify-failure',
      notiflixIconColor: 'rgba(0,0,0,0.2)',
      fontAwesomeClassName: 'fas fa-times-circle',
      fontAwesomeIconColor: 'rgba(0,0,0,0.2)',
      backOverlayColor: 'rgba(255,85,73,0.2)',
    },
    warning: {
      background: '#eebf31',
      textColor: '#fff',
      childClassName: 'notiflix-notify-warning',
      notiflixIconColor: 'rgba(0,0,0,0.2)',
      fontAwesomeClassName: 'fas fa-exclamation-circle',
      fontAwesomeIconColor: 'rgba(0,0,0,0.2)',
      backOverlayColor: 'rgba(238,191,49,0.2)',
    },
    info: {
      background: '#26c0d3',
      textColor: '#fff',
      childClassName: 'notiflix-notify-info',
      notiflixIconColor: 'rgba(0,0,0,0.2)',
      fontAwesomeClassName: 'fas fa-info-circle',
      fontAwesomeIconColor: 'rgba(0,0,0,0.2)',
      backOverlayColor: 'rgba(38,192,211,0.2)',
    },
  });
  

import '@fortawesome/fontawesome-free/css/all.min.css';

// Crear una nueva instancia de la aplicación Vue
const app = createApp({});

// Importar el componente Home.vue
import Home from './components/Home.vue';
app.component('vue-datatables-net', VueDataTablesNet);

import 'bootstrap/dist/css/bootstrap.min.css';

// Registrar el componente Home con la instancia de la aplicación Vue
app.component('home', Home);

// Importar otros componentes si los tienes

// Montar la aplicación en el elemento con el ID "app"
app.mount('#app');
