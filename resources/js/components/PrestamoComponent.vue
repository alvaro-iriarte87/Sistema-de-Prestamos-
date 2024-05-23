<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <h2 class="mb-4">Simulador de Créditos <i class="fas fa-coins"></i></h2>
        <div class="form-group">
          <label for="cliente">Cliente:</label>
          <select class="form-control" v-model="clienteSeleccionado">
            <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">{{ cliente.nombre }} {{ cliente.apellido }}</option>
          </select>
        </div>
        <div class="form-group">
          <label for="monto">Monto a financiar:</label>
          <input type="number" class="form-control" id="monto" v-model="monto" required>
        </div>
        <div class="form-group">
          <label for="periodicidad">Periodicidad de pago:</label>
          <select class="form-control" id="periodicidad" v-model="periodicidad" required @change="calcularFechaPrimeraCuota">
            <option value="mensual">Mensual</option>
            <option value="quincenal">Quincenal</option>
            <option value="semanal">Semanal</option>
          </select>
        </div>
        <div class="form-group">
          <label for="interes">Interés (%):</label>
          <select class="form-control" id="interes" v-model="interes" required>
            <option v-for="tasa in tasasDeInteres" :key="tasa">{{ tasa }}%</option>
          </select>
        </div>
        <div class="form-group">
          <label for="cuotas">Cantidad de cuotas:</label>
          <input type="number" class="form-control" id="cuotas" v-model="cuotas" required>
        </div>
        <br>
        <button class="btn btn-primary btn-block" @click="calcularCredito">
         <i class="fas fa-calculator"></i> Calcular Crédito
        </button>
        <div v-if="resultado" class="mt-4">
          <h3>Peticion de Préstamo:</h3>
          <br> 
          <p><b>Nombre:</b> {{ clienteSeleccionado ? obtenerNombreCliente(clienteSeleccionado) : '' }}</p>
          <p><b>Apellido:</b> {{ clienteSeleccionado ? obtenerApellidoCliente(clienteSeleccionado) : '' }}</p>
          <p><b>DNI: </b>{{ clienteSeleccionado ? obtenerDNICliente(clienteSeleccionado) : '' }}</p>
          <p><b>Dirección:</b> {{ clienteSeleccionado ? obtenerDireccionCliente(clienteSeleccionado) : '' }}</p>
          <p><b>Celular:</b> {{ clienteSeleccionado ? obtenerCelularCliente(clienteSeleccionado) : '' }}</p>
          <h3> Detalle de Préstamo:</h3>
          <br>
          <p><b> Fecha Pedido: </b>{{ formatDate(fechaSolicitud) }}</p>
          <p><b>Registro N° de Préstamo: </b>{{ generarNumeroPrestamo() }}</p>
          <p><b>Monto Pedido: </b> ${{ monto }}</p>
           <p><b>Periodo:</b> {{ periodicidad }}</p>
          <p><b>Cantidad de cuotas:</b> {{ cuotas }}</p>
          <p><b>Monto de la cuota: $</b> {{ calcularMontoCuota() ? calcularMontoCuota().toFixed(2) : 'No calculado' }}</p>
          <p><b>Fecha de la primera cuota: </b>{{ fechaPrimeraCuota }}</p>
           <p><b>Interés total: </b> {{ interes }}</p>
          <p><b>Total a pagar: $</b> {{ calcularTotalAPagar() ? calcularTotalAPagar().toFixed(2) : 'No calculado' }}</p>
        </div>
      </div>
      <div v-if="resultado" class="col-md-12 mt-3">
        <div class="d-flex justify-content-between">
          <button class="btn btn-success" @click="prestar">
           <i class="fas fa-hand-holding-usd"></i> Prestar
          </button>
          <button class="btn btn-secondary" @click="comprobantePDF">
            <i class="fas fa-file-pdf"></i> Comprobante PDF
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import jsPDF from 'jspdf';
import { Notify } from 'notiflix/build/notiflix-notify-aio';
import { Confirm } from 'notiflix/build/notiflix-confirm-aio';

export default {
  data() {
    return {
      clienteSeleccionado: null,
      monto: null,
      periodicidad: 'mensual',
      interes: null,
      cuotas: null,
      resultado: false,
      tasasDeInteres: [5, 7, 10, 12, 15, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200],
      clientes: [],
      fechaSolicitud: null,
      fechaPrimeraCuota: null
    };
  },
  methods: {
    cargarClientes() {
      axios
        .get("/api/clientes")
        .then((response) => {
          this.clientes = response.data;
        })
        .catch((error) => {
          console.error("Error al cargar clientes", error);
        });
    },
    generarNumeroPrestamo() {
      const idCliente = this.clienteSeleccionado ? this.clienteSeleccionado : '0'; 
      const fechaHora = new Date().toISOString().replace(/[^\d]/g, ''); 
      return fechaHora + idCliente; 
    },
    calcularTotalAPagar() {
      try {
        const monto = parseFloat(this.monto);
        const interes = parseFloat(this.interes) / 100;
        const cuotas = parseFloat(this.cuotas);

        if (!isNaN(monto) && !isNaN(interes) && !isNaN(cuotas)) {
          const interesTotal = monto * interes;

          const totalAPagar = monto + interesTotal;

          return totalAPagar;
        } else {
          console.error("Los valores ingresados no son válidos para calcular el total a pagar.");
          return NaN;
        }
      } catch (error) {
        console.error("Error al calcular el total a pagar:", error);
        return NaN;
      }
    },
    calcularMontoCuota() {
      try {
        const totalAPagar = parseFloat(this.calcularTotalAPagar());
        const cuotas = parseFloat(this.cuotas);

        if (!isNaN(totalAPagar) && !isNaN(cuotas)) {
          return totalAPagar / cuotas;
        } else {
          console.error("Los valores ingresados no son válidos para calcular el monto de la cuota.");
          return NaN;
        }
      } catch (error) {
        console.error("Error al calcular el monto de la cuota:", error);
        return NaN;
      }
    },
    calcularCredito() {
      if (this.monto && this.interes && this.cuotas) {
        this.resultado = true;
      } else {
        console.error("Por favor, complete todos los campos antes de calcular el crédito.");
      }
    },
   comprobantePDF() {
  const doc = new jsPDF();

  doc.setFontSize(20);
  doc.text('Sistema de Prestamos V 1.0 ', 10, 20);

  const detalles = [
    `Peticion de Préstamo:`,
    ``,
    `Nombre: ${this.clienteSeleccionado ? this.obtenerNombreCliente(this.clienteSeleccionado) : ''}`,
    `Apellido: ${this.clienteSeleccionado ? this.obtenerApellidoCliente(this.clienteSeleccionado) : ''}`,
    `DNI: ${this.clienteSeleccionado ? this.obtenerDNICliente(this.clienteSeleccionado) : ''}`,
    `Dirección: ${this.clienteSeleccionado ? this.obtenerDireccionCliente(this.clienteSeleccionado) : ''}`,
    `Celular: ${this.clienteSeleccionado ? this.obtenerCelularCliente(this.clienteSeleccionado) : ''}`,
    ``,
    `Detalle de Préstamo:`,
    ``,
     `Fecha Pedido: ${this.formatDate(this.fechaSolicitud)}`,
    `Registro N° de Préstamo: ${this.generarNumeroPrestamo()}`,
    `Monto Pedido: $${this.monto}`,
    `Periodo: ${this.periodicidad}`,
    `Cantidad de cuotas: ${this.cuotas}`,
    `Monto de la cuota: $${this.calcularMontoCuota() ? this.calcularMontoCuota().toFixed(2) : 'No calculado'}`,
    `Fecha de la primera cuota: ${this.fechaPrimeraCuota}`,
    `Interés total: ${this.interes}`,
    `Total a pagar: $${this.calcularTotalAPagar() ? this.calcularTotalAPagar().toFixed(2) : 'No calculado'}`
  ];

  detalles.forEach((detalle, index) => {
    doc.text(detalle, 10, 30 + index * 10);
  });
   Notify.success('El detalle de tu Prestamo Ya se emitio en PDF para Compartirle a tu Cliente');
  doc.save('comprobante.pdf');
},
    obtenerNombreCliente(clienteId) {
      const cliente = this.clientes.find(cliente => cliente.id === clienteId);
      return cliente ? cliente.nombre : '';
    },
    obtenerApellidoCliente(clienteId) {
      const cliente = this.clientes.find(cliente => cliente.id === clienteId);
      return cliente ? cliente.apellido : '';
    },
    obtenerDNICliente(clienteId) {
      const cliente = this.clientes.find(cliente => cliente.id === clienteId);
      return cliente ? cliente.dni : '';
    },
    obtenerDireccionCliente(clienteId) {
      const cliente = this.clientes.find(cliente => cliente.id === clienteId);
      return cliente ? cliente.direccion : '';
    },
    obtenerCelularCliente(clienteId) {
      const cliente = this.clientes.find(cliente => cliente.id === clienteId);
      return cliente ? cliente.celular : '';
    },
    calcularFechaPrimeraCuota() {
      if (this.periodicidad && this.fechaSolicitud) {
        const fechaSolicitud = this.fechaSolicitud;
        let fechaPrimeraCuota = new Date(fechaSolicitud);

        switch (this.periodicidad) {
          case 'mensual':
            fechaPrimeraCuota.setMonth(fechaSolicitud.getMonth() + 1);
            if (fechaPrimeraCuota.getMonth() === fechaSolicitud.getMonth()) { 
              fechaPrimeraCuota.setFullYear(fechaPrimeraCuota.getFullYear() + 1); 
            }
            break;
          case 'quincenal':
            fechaPrimeraCuota.setDate(fechaSolicitud.getDate() + 15);
            break;
          case 'semanal':
            fechaPrimeraCuota.setDate(fechaSolicitud.getDate() + 7);
            break;
          default:
            console.error("Periodicidad no válida.");
        }

        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        this.fechaPrimeraCuota = fechaPrimeraCuota.toLocaleDateString('es-ES', options);
      }
    },
    formatDate(date) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(date).toLocaleDateString('es-ES', options);
    },
   prestar() {
  const interesDecimal = parseFloat(this.interes) / 100;

  const prestamo = {
    cliente_id: this.clienteSeleccionado,
    monto: this.monto,
    periodicidad: this.periodicidad,
    interes: interesDecimal,
    cuotas: this.cuotas,
    fecha_solicitud: this.fechaSolicitud,
    fecha_primera_cuota: this.fechaPrimeraCuota,
    total_pagar: this.calcularTotalAPagar()
  };

  Confirm.show(
    'Notiflix Confirm',
    '¿Estás seguro de que quieres emitir este préstamo?',
    'Sí',
    'No',
    () => {
      axios.post('/api/prestamos', prestamo)
        .then(response => {
          console.log('Préstamo guardado con éxito:', response.data);
          Notify.success('Tu préstamo se guardó correctamente, Continua en Seguimiento');
          // Resetear todos los campos y variables
          this.clienteSeleccionado = null;
          this.monto = null;
          this.periodicidad = 'mensual';
          this.interes = null;
          this.cuotas = null;
          this.resultado = false;
          this.fechaSolicitud = new Date();
          this.fechaPrimeraCuota = null;
        })
        .catch(error => {
          console.error('Error al guardar el préstamo:', error);
          Notify.failure('Ocurrió un error al intentar guardar el préstamo');
        });
    },
    () => {
      Notify.info('Operación cancelada');
    }
  );
}
  },
 
  mounted() {
    this.cargarClientes();
    this.fechaSolicitud = new Date();
    this.calcularFechaPrimeraCuota();
  }
};
</script>

