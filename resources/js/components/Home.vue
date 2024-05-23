<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="sidebar text-center mb-6 d-flex justify-content-center flex-wrap">
         <button class="btn btn-success custom-margin mb-3 mx-2 flex-sm-row" @click="mostrarClientesComponent">
          <i class="fas fa-users"></i>  Clientes
          </button>
          <button class="btn btn-primary mb-3 mx-2 custom-margin flex-sm-row" @click="mostrarPrestamosComponent">
             <i class="fas fa-money-check-alt"></i> Prestamos
            </button>
          <button class="btn btn-danger mb-3 mx-2 custom-margin flex-sm-row" @click="mostrarCobrosComponent">
           <i class="fas fa-info-circle"></i> Seguimiento
          </button>
          <button class="btn btn-secondary mb-3 mx-2 custom-margin flex-sm-row" @click="mostrarDetallesComponent">
           <i class="fas fa-chart-line"></i> Detalles
          </button>
        </div>
      </div>
      <div class="col-12">
        <div class="content" v-if="!mostrarClientes && !mostrarPrestamos && !mostrarCobros && !mostrarDetalle">
          <!-- Contenido de bienvenida -->
          <br>
          <h1 class="text-center">Bienvenido al Sistema de Préstamos V 1.0</h1>
          <br>
          <h4 class="text-center">
            Nuestro sistema ofrece una manera eficiente y segura de administrar
            tus préstamos.
          </h4>
          <br>
          <p class="text-center">
            Desarrollado por: <b>Alvaro B. Iriarte Maccio</b>
          </p>
          <br>
          <p class="text-center">Marca Registrada &copy; 2024</p>
          <div class="text-center">
            <svg enable-background="new 0 0 64 64" height="64px" id="Layer_1" version="1.1" viewBox="0 0 64 64" width="64px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="financial_report"><g id="training"><g><rect fill="#E0E0E0" height="37" width="54" x="5" y="12"/></g><g opacity="0.2"><rect fill="#37474F" height="2" width="54" x="5" y="12"/></g><g><g><path d="M61,11c0,1.1-0.9,2-2,2H5c-1.1,0-2-0.9-2-2l0,0c0-1.1,0.9-2,2-2h54C60.1,9,61,9.9,61,11L61,11z" fill="#90A4AE"/></g><g><path d="M3.279,10h57.441C60.372,9.406,59.734,9,59,9H5C4.266,9,3.627,9.406,3.279,10z" fill="#B0BEC5"/></g><g><path d="M3.279,12C3.627,12.594,4.266,13,5,13h54c0.734,0,1.372-0.406,1.721-1H3.279z" fill="#78909C"/></g></g><g><g><path d="M61,50c0,1.1-0.9,2-2,2H5c-1.1,0-2-0.9-2-2l0,0c0-1.1,0.9-2,2-2h54C60.1,48,61,48.9,61,50L61,50z" fill="#90A4AE"/></g><g><path d="M3.279,49h57.441c-0.349-0.594-0.986-1-1.721-1H5C4.266,48,3.627,48.406,3.279,49z" fill="#B0BEC5"/></g><g><path d="M3.279,51C3.627,51.594,4.266,52,5,52h54c0.734,0,1.372-0.406,1.721-1H3.279z" fill="#78909C"/></g></g><g><path d="M30,9V6c0-1.104,0.896-2,2-2l0,0c1.104,0,2,0.896,2,2v3H30z" fill="#607D8B"/></g><g><polygon fill="#607D8B" points="45,61 49,61 46,52 42,52    "/></g><g><polygon fill="#607D8B" points="19,61 15,61 18,52 22,52    "/></g><g opacity="0.2"><polygon fill="#37474F" points="21.333,54 22,52 18,52 17.333,54    "/></g><g opacity="0.2"><polygon fill="#37474F" points="46.667,54 46,52 42,52 42.667,54    "/></g><g><rect fill="#546E7A" height="1" width="8" x="10" y="20"/></g><g><rect fill="#546E7A" height="1" width="8" x="10" y="17"/></g><g><rect fill="#FF7043" height="8" width="4" x="11" y="34"/></g><g><rect fill="#FF7043" height="14" width="4" x="18" y="28"/></g><g><rect fill="#FF7043" height="20" width="4" x="25" y="22"/></g><g><rect fill="#546E7A" height="1" width="20" x="10" y="43"/></g><g><rect fill="#546E7A" height="1" width="8" x="35" y="42"/></g><g><rect fill="#546E7A" height="1" width="19" x="35" y="38"/></g><g><rect fill="#546E7A" height="1" width="19" x="35" y="34"/></g><g><rect fill="#546E7A" height="1" width="19" x="35" y="30"/></g></g><polygon fill="#FF7043" points="54.394,19.365 54,17 51.635,17.395 50.798,18.565 52.465,18.287 48.891,23.293 44.912,20.31    40.862,25.372 36.829,23.355 34.584,26.723 35.416,27.277 37.171,24.645 41.138,26.628 45.088,21.69 49.109,24.707 53.28,18.868    53.558,20.536  "/></g></svg>
          </div>
        </div>
        <div v-if="mostrarClientes">
          <!-- Componente de clientes -->
          <cliente-component />
        </div>
        <div v-if="mostrarPrestamos">
          <!-- Componente de préstamos -->
          <prestamo-component />
        </div>
        <div v-if="mostrarCobros">
          <!-- Componente de cobros -->
          <cobro-component />
        </div>
        <div v-if="mostrarDetalle">
          <!-- Componente de cobros -->
          <detalle-component />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import ClienteComponent from './ClientesComponent.vue';
import PrestamoComponent from './PrestamoComponent.vue';
import CobroComponent from './CobroComponent.vue';
import DetalleComponent from './DetalleComponent.vue';


const mostrarClientes = ref(false);
const mostrarPrestamos = ref(false);
const mostrarCobros = ref(false);
const mostrarDetalle = ref(false);

const mostrarClientesComponent = () => {
  console.log("Abriendo Clientes");
  mostrarClientes.value = true;
  mostrarPrestamos.value = false;
  mostrarCobros.value = false;
  mostrarDetalle.value = false;
};

const mostrarPrestamosComponent = () => {
  console.log("Abriendo Prestamos");
  mostrarClientes.value = false;
  mostrarPrestamos.value = true;
  mostrarCobros.value = false;
  mostrarDetalle.value = false;
};

const mostrarCobrosComponent = () => {
  console.log("Abriendo Detalles");
  mostrarClientes.value = false;
  mostrarPrestamos.value = false;
  mostrarCobros.value = true;
  mostrarDetalle.value = false;
};

const mostrarDetallesComponent = () => {
  console.log('abriendo detalles');
  mostrarDetalle.value = true;
  mostrarClientes.value = false;
  mostrarPrestamos.value = false;
  mostrarCobros.value = false;
}
</script>

<style scoped>
.sidebar {
  padding: 10px;
  background-color: #f0f0f0;
  border: 1px solid #5f5d5d;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.content {
  padding: 0px;
}

.custom-margin{
  margin-bottom: 10px;
}
</style>