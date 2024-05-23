<template>
  <div class="table-responsive text-center">
    <br>
    <h2 class="mb-5">Prestamos Vigentes <i class="fas fa-briefcase"></i></h2>
    <br>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Cliente</th>
          <th>Monto</th>
          <th>Periodicidad</th>
          <th>Interés</th>
          <th>Cuotas</th>
          <th>Fecha Solicitud</th>
          <th>Fecha Primera Cuota</th>
          <th>Total a Pagar</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="prestamo in prestamos" :key="prestamo.id">
          <td>{{ prestamo.cliente.nombre }}</td>
          <td>{{ prestamo.monto }}</td>
          <td>{{ prestamo.periodicidad }}</td>
          <td>{{ prestamo.interes * 100 }}%</td>
          <td>{{ prestamo.cuotas }}</td>
          <td>{{ prestamo.fecha_solicitud }}</td>
          <td>{{ prestamo.fecha_primera_cuota }}</td>
          <td>{{ prestamo.total_pagar }}</td>
          <td>
            <button class="btn btn-primary" @click="detallePrestamo(prestamo)">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-danger" @click="eliminarPrestamo(prestamo.id)">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <!-- Modal para mostrar detalles del préstamo -->
    <div class="modal fade" :class="{ 'show': mostrarModalDetalle }">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detalle del Préstamo</h5>
          </div>
          <div class="modal-body">
            <p><strong>Estado:</strong> {{ estadoPrestamo }}</p>
            <p><strong>Fecha Solicitud:</strong> {{ prestamoSeleccionado ? prestamoSeleccionado.fecha_solicitud : '' }}</p>
            <p><strong>Periodicidad:</strong> {{ prestamoSeleccionado ? prestamoSeleccionado.periodicidad : '' }}</p>
            <p><strong>Cantidad Cuotas:</strong> {{ prestamoSeleccionado ? prestamoSeleccionado.cuotas : '' }}</p>
            <p><strong>Monto de Cuota:</strong> $ {{ calcularMontoCuota() }}</p>
            <p v-if="fechasCuotas && fechasCuotas.length > 0">
              <strong>Fechas de Cuotas:</strong>
              <ul>
                <li v-for="(cuota, index) in fechasCuotas" :key="cuota.id">
                    Cuota {{ index + 1 }} - {{ cuota }}
                   <span @click="toggleCuotaEstado(index)">
                     <input type="checkbox" v-model="cuotasPagadas[index]">
                     <i :class="cuotasPagadas[index] ? 'far fa-check-circle icon-green' : 'far fa-times-circle icon-red'"></i>
                     {{ cuotasPagadas[index] ? 'Pagada' : 'No pagada' }}
                  </span>
                </li>
              </ul>
            </p>
            <p><strong>Saldo a pagar:</strong> $ {{ saldoAPagar }}</p>
            <p><strong>Total a pagar:</strong> $ {{ prestamoSeleccionado ? prestamoSeleccionado.total_pagar : '' }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="EmitPdf">
              Comprobante <i class="fas fa-file-pdf"></i>
            </button>
            <button type="button" class="btn btn-danger" @click="cerrarModalDetalle">
              Salir
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import { Notify } from "notiflix/build/notiflix-notify-aio";
import { Loading } from 'notiflix/build/notiflix-loading-aio';
import { Confirm } from 'notiflix/build/notiflix-confirm-aio';
import { jsPDF } from 'jspdf';
import { addDays, addWeeks, addMonths, format } from 'date-fns';

export default {
  data() {
    return {
      prestamos: [],
      mostrarModalDetalle: false,
      estadoPrestamo: '',
      prestamoSeleccionado: null,
      saldoAPagar: 0,
      cuotasPagadas: [],
      fechasCuotas: [],
      cuotasCreadas: [],
      cuotasCreadasMap: {},
    };
  },
  mounted() {
    this.obtenerPrestamosVigentes();
  },
  methods: {
    obtenerPrestamosVigentes() {
      axios
        .get("/api/prestamos/vigentes")
        .then((response) => {
          this.prestamos = response.data;
        })
        .catch((error) => {
          console.error("Error al obtener los préstamos vigentes:", error);
        });
    },
    detallePrestamo(prestamo) {
      this.mostrarModalDetalle = true;
      this.prestamoSeleccionado = prestamo;
      this.estadoPrestamo = '';
      this.fechasCuotas = []; 
      this.cuotasPagadas = []; 

      Loading.dots('Cargando datos...'); 

      this.generarFechasCuotas(prestamo);
      this.obtenerEstadoCuotas(prestamo.id);
    },
    obtenerEstadoCuotas(idPrestamo) {
      axios.get(`/api/prestamos/${idPrestamo}`)
        .then((response) => {
          this.estadoPrestamo = response.data.estado;
        })
        .catch((error) => {
          console.error("Error al obtener el estado de las cuotas:", error);
          Notify.failure("Error al obtener el estado de las cuotas");
        });
    },
    async generarFechasCuotas(prestamo) {
      const { id, fecha_solicitud, cuotas } = prestamo;
      const cantidad = prestamo.periodicidad === 'mensual' ? 1 :
                        prestamo.periodicidad === 'quincenal' ? 15 : 
                        7; 

      let fechaActual = new Date(fecha_solicitud); 
      let fechasCuotas = [];
      let cuotasPromises = [];
      let cuotasCreadas = [];

      if (prestamo.periodicidad === 'mensual') {
        fechaActual.setMonth(fechaActual.getMonth() + 1);
      } else {
        fechaActual.setDate(fechaActual.getDate() + cantidad);
      }

      for (let i = 0; i < cuotas; i++) {
        const cuotaId = `${id}_${i + 1}`.substring(0, 14);
        const fechaCuota = new Date(fechaActual);
        fechasCuotas.push(format(fechaCuota, 'dd/MM/yyyy'));

        try {
          const response = await axios.get(`/api/cuotas/${cuotaId}`);
          const cuota = response.data;
          cuotasCreadas.push(cuota);
          this.cuotasPagadas.push(cuota.pagada === 1);
        } catch (error) {
          cuotasPromises.push(
            new Promise((resolve, reject) => {
              axios.post('/api/cuotas', {
                  id_cuota: cuotaId, 
                  fechacuota: fechaCuota.toISOString().split('T')[0],
                  prestamo_id: id,
                  pagada: false
                })
                .then((response) => {
                  cuotasCreadas.push(response.data); 
                  resolve(response.data);
                })
                .catch((error) => {
                  reject(error);
                });
            })
          );
        }

        if (prestamo.periodicidad === 'mensual') {
          fechaActual.setMonth(fechaActual.getMonth() + 1);
        } else {
          fechaActual.setDate(fechaActual.getDate() + cantidad);
        }
      }

      try {
        await Promise.all(cuotasPromises);

        this.cuotasCreadasMap = {};
        cuotasCreadas.forEach((cuota, index) => {
          this.cuotasCreadasMap[cuota.id] = index;
        });

        await this.obtenerEstadoCuotas(id);

        Loading.remove();
      } catch (error) {
        Notify.failure("Error al generar fechas de cuotas");
        Loading.remove();
      }

      this.fechasCuotas = fechasCuotas;
      this.cuotasCreadas = cuotasCreadas;
    },
   async toggleCuotaEstado(index) {
    try {
        const cuotaId = Object.keys(this.cuotasCreadasMap).find(key => this.cuotasCreadasMap[key] === index);
        console.log("ID de la cuota:", cuotaId);

        if (!cuotaId) {
            console.error("La cuota no existe con el índice especificado:", index);
            Notify.failure("Error al procesar la cuota: cuota no encontrada");
            return;
        }

        // Calcular el nuevo estado de la cuota
        const nuevoEstado = this.cuotasPagadas[index] ? 0 : 1;

        // Hacer una solicitud al backend para actualizar el estado de la cuota
        const response = await axios.put(`/api/cuotas/${cuotaId}`, { pagada: nuevoEstado });

        console.log("Respuesta del servidor:", response);

        // Verificar el estado de la respuesta del servidor
        if (response.status === 200) {
            // Verificar el contenido de la respuesta
            const responseData = response.data;
            console.log("Datos de respuesta:", responseData);

            if (responseData.message && responseData.message === "Estado de la cuota actualizado correctamente") {
                // Actualizar el estado localmente
                this.cuotasPagadas.splice(index, 1, nuevoEstado === 1);
                Notify.success(`Estado de la cuota ${cuotaId} actualizado correctamente`);

                // Calcular el nuevo saldo a pagar
                const saldoRestante = this.prestamoSeleccionado.total_pagar - 
                    this.prestamoSeleccionado.total_pagar / this.prestamoSeleccionado.cuotas * this.cuotasPagadas.filter(p => p).length;

                // Verificar si todas las cuotas están pagadas
                if (this.cuotasPagadas.every(p => p)) {
                    Notify.success("El préstamo se ha terminado de pagar correctamente");
                }

                // Actualizar el saldo restante mostrado
                this.saldoAPagar = saldoRestante.toFixed(2);
            } else {
                console.error("Respuesta inesperada del servidor:", responseData);
                Notify.failure("Error al procesar la cuota: respuesta inesperada del servidor");
            }
        } else {
            console.error("Error en la respuesta del servidor al actualizar la cuota");
            Notify.failure("Error al procesar la cuota");
        }
    } catch (error) {
        console.error("Error al procesar la cuota:", error);
        Notify.failure("Error al procesar la cuota");
    }
},

EmitPdf() {
    Confirm.show(
        '¿Estás seguro de que quieres emitir el PDF con los detalles del préstamo?',
        'Si confirmas, se generará un PDF con los detalles actuales del préstamo y las cuotas.',
        'Sí',
        'No',
        () => {
            // Crear un nuevo documento PDF
            const doc = new jsPDF();

            const fechaActual = new Date().toLocaleDateString('es-ES');

            // Agregar el encabezado al PDF
            let y = 20; // Posición vertical inicial
              doc.text(`Sistema de Prestamos V 1.0`, 10, 10);
             y += 10;
            doc.text(`Detalles del Préstamo - ${fechaActual}`, 10, y);
            y += 20;
            // Agregar los detalles del préstamo
            doc.text(`Cliente: ${this.prestamoSeleccionado.cliente.nombre}`, 10, y);
            y += 10; // Incrementar la posición vertical para la próxima línea
            doc.text(`Monto Otorgado: $${this.prestamoSeleccionado.monto}`, 10, y);
            y += 10;
            doc.text(`Periodicidad: ${this.prestamoSeleccionado.periodicidad}`, 10, y);
            y += 10;
            doc.text(`Fecha de Solicitud: ${this.prestamoSeleccionado.fecha_solicitud}`, 10, y);
            y += 10;
            doc.text(`Total a Pagar: $${this.prestamoSeleccionado.total_pagar}`, 10, y);
            y += 10;
            doc.text(`Saldo a Pagar: $${this.saldoAPagar}`, 10, y);
            y += 10;

            // Agregar las cuotas al PDF
            y += 10; // Espacio entre los detalles del préstamo y las cuotas
            doc.text('Cuotas:', 10, y);
            y += 10;
            this.fechasCuotas.forEach((cuota, index) => {
                const estado = this.cuotasPagadas[index] ? 'Pagada' : 'No pagada';
                doc.text(`Cuota ${index + 1}: ${cuota} - ${estado}`, 10, y);
                y += 10; // Incrementar la posición vertical para la próxima línea
            });

            // Verificar si el saldo a pagar es cero
            if (this.saldoAPagar === 0) {
                // Si el saldo es cero, agregar un mensaje especial en lugar del saldo
                y += 10; // Espacio entre las cuotas y el mensaje especial
                doc.text("¡El préstamo ha sido saldado por completo!", 10, y);
            }

            // Guardar y descargar el PDF
            doc.save('detalles_prestamo.pdf');
        },
        () => {
            Notify.info("Se ha cancelado la generación del PDF");
        }
    );
},

    cerrarModalDetalle() {
      this.mostrarModalDetalle = false;
      this.estadoPrestamo = '';
      this.prestamoSeleccionado = null;
    },
    eliminarPrestamo(id) {
      axios
        .delete(`/api/prestamos/${id}`)
        .then(() => {
          this.obtenerPrestamosVigentes();
          Notify.success("Prestamo eliminado correctamente");
        })
        .catch((error) => {
          Notify.failure("Error al eliminar el préstamo");
        });
    },
    calcularMontoCuota() {
      if (this.prestamoSeleccionado) {
        return (this.prestamoSeleccionado.total_pagar / this.prestamoSeleccionado.cuotas).toFixed(2);
      }
      return 0;
    }
  }
};
</script>
<style scoped>
.modal {
  display: none;
}
.modal.show {
  display: block;
}
.icon-green {
  color: green;
}
.icon-red {
  color: red;
}
</style>
