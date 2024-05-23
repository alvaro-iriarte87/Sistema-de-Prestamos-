<template>
  <div class="container text-center">
    <br>
    <h2 class="mb-5">Detalles del Usuario  <i class="fas fa-chart-line"></i></h2>
    <div class="row">
      <div class="col-sm-12 col-md-6 col-lg-6 mb-4">
        <canvas ref="barChart" width="200" height="200"></canvas>
      </div>
      <div class="col-sm-12 col-md-6 col-lg-6 mb-4">
        <canvas ref="pieChart" width="200" height="200"></canvas>
      </div>
      <div class="col-sm-12 col-md-12 col-lg-12 mb-4">
        <canvas ref="scatterChart" width="200" height="200"></canvas>
      </div>
    </div>
    <div class="row mt-5-responsive">
      <div class="col">
        <h3>Cuotas Cobradas <i class="fas fa-coins"></i></h3>
        <div class="form-group row">
          <label for="searchClient" class="col-form-label col-sm-3">Buscar por cliente:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="searchClient" v-model="searchClient">
          </div>
          <label for="searchDate" class="col-form-label col-sm-3">Buscar por Fecha:</label>
          <div class="col-sm-3">
            <input type="date" class="form-control" id="searchDate" v-model="searchDate">
          </div>
        </div>
        <table class="table table-striped">
          <thead>
            <tr>
              <th></th> <!-- Checkbox para selección -->
              <th>Cliente</th>
              <th>Cuota N°</th>
              <th>Fecha</th>
              <th>Monto</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(cuota, index) in filteredCuotas" :key="index">
              <td>
                <input type="checkbox" v-model="cuota.selected">
              </td>
              <td>{{ cuota.cliente }}</td>
              <td>{{ cuota.numerosCuotas }}</td>
              <td>{{ cuota.fecha }}</td>
              <td>${{ Math.round(cuota.totalMonto) }}</td>
            </tr>
          </tbody>
        </table>
        <!-- Botón para sumar montos -->
        <div class="col-md-12 mt-3">
          <div class="d-flex justify-content-between">
            <button v-show="showSumarMontosButton" @click="mostrarConfirmacionSm" class="btn btn-success"><i class="far fa-file-alt"></i> Resumen </button>
            <button v-show="showSumarMontosButton" @click="mostrarConfirmacion" class="btn btn-secondary"><i class="fas fa-file-pdf"></i> Comprobante PDF</button>
          </div>
        </div>
        <div v-show="mostrarTotalSuma" class="form-group mt-3">
          <label for="totalSuma"><h4>Monto Total de las cuotas seleccionada</h4></label>
          <input id="totalSuma" v-model="formattedTotalSuma" type="text" class="form-control" readonly>
        </div>
        <div v-show="mostrarTotalSuma" class="form-group mt-3">
          <h4>Detalle de las cuotas seleccionadas:</h4>
          <ul class="list-group">
            <li class="list-group-item" v-for="cuota in cuotasSeleccionadas" :key="cuota.id">
              Cliente: {{ cuota.cliente }}, Cuota N°: {{ cuota.numerosCuotas }}, Fecha: {{ cuota.fecha }}, Monto: ${{ Math.round(cuota.totalMonto) }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto';
import axios from 'axios';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import Notiflix from 'notiflix';
import { Confirm } from 'notiflix/build/notiflix-confirm-aio';
import 'chartjs-adapter-date-fns';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

Chart.register(ChartDataLabels);

export default {
  data() {
    return {
      cuotasArray: [],
      searchDate: '',
      searchClient: '',
      selectedCuotas: [],
      showSumarMontosButton: false,
      totalSuma: 0,
      mostrarTotalSuma: false,
    };
  },
  computed: {
    filteredCuotas() {
      return this.cuotasArray.filter(cuota => {
        // Filtrar por fecha
        const isDateMatch = !this.searchDate || cuota.fecha === this.searchDate;
        // Filtrar por cliente
        const isClientMatch = !this.searchClient || cuota.cliente.toLowerCase().includes(this.searchClient.toLowerCase());
        return isDateMatch && isClientMatch;
      });
    },
    cuotasSeleccionadas() {
      return this.cuotasArray.filter(cuota => cuota.selected);
    },
     formattedTotalSuma() {
      return `$${this.totalSuma.toFixed(2)}`;
    }
  },
  watch: {
    cuotasSeleccionadas() {
      // Verificamos si hay al menos una cuota seleccionada
      this.showSumarMontosButton = this.cuotasSeleccionadas.length > 0;
    }
  },
  methods: {
    async fetchDataAndRenderCharts() {
      Notiflix.Loading.standard('Cargando gráficos...');

      const clientesData = await this.fetchClientesData();
      const prestamosData = await this.fetchPrestamosData();
      const cuotasData = await this.fetchCuotasData();
      const capitalData = await this.fetchCapitalData();
      const gananciaData = await this.fetchGananciaData();

      this.cuotasArray = cuotasData; // Asignar cuotasData a cuotasArray

      this.renderBarChart(clientesData, prestamosData);
      this.renderPieChart(capitalData, gananciaData);
      this.renderScatterChart(cuotasData);

      Notiflix.Loading.remove();
    },

    async fetchClientesData() {
      try {
        const response = await axios.get("/api/clientes");
        const cantidadClientes = response.data.length;
        return cantidadClientes;
      } catch (error) {
        console.error("Error al obtener la cantidad de clientes:", error);
        return 0;
      }
    },

    async fetchPrestamosData() {
      try {
        const userIdResponse = await axios.get('/api/user-id');
        const userId = userIdResponse.data;

        const response = await axios.get(`/api/prestamos/usuario/${userId}`);
        const prestamos = response.data;
        const cantidadPrestamos = prestamos.length;
        return cantidadPrestamos;
      } catch (error) {
        console.error("Error al obtener la cantidad de préstamos:", error);
        return 0;
      }
    },

    async fetchGananciaData() {
      try {
        const userIdResponse = await axios.get('/api/user-id');
        const userId = userIdResponse.data;

        const response = await axios.get(`/api/prestamos/ganancia-usuario/${userId}`);
        const gananciaTotal = response.data.ganancia_total;
        return gananciaTotal;
      } catch (error) {
        console.error("Error al obtener la ganancia total del usuario:", error);
        return 0;
      }
    },

    async fetchCuotasData() {
      try {
        const userIdResponse = await axios.get('/api/user-id');
        const userId = userIdResponse.data;

        const prestamosResponse = await axios.get(`/api/prestamos/usuario/${userId}`);
        const prestamos = prestamosResponse.data;

        let cuotas = [];

        for (let prestamo of prestamos) {
          const cuotasResponse = await axios.get(`/api/cuotas?prestamo_id=${prestamo.id}`);
          const cuotasDelPrestamo = cuotasResponse.data.filter(cuota => cuota.prestamo_id === prestamo.id);

          const totalPagar = prestamo.total_pagar;
          const numeroCuotas = prestamo.cuotas;
          const valorCuota = totalPagar / numeroCuotas;

          // Obtener el nombre del cliente asociado al préstamo
          const clienteId = prestamo.cliente_id; // Obtener el ID del cliente asociado al préstamo
          const clienteResponse = await axios.get(`/api/clientes/${clienteId}`);
          const clienteNombre = clienteResponse.data.nombre;

          // Enumerar las cuotas específicas para este préstamo, comenzando desde 1
          cuotasDelPrestamo.forEach((cuota, index) => {
            cuota.valor = valorCuota;
            cuota.numeroCuota = index + 1; 
            cuota.cliente = clienteNombre; 
            cuotas.push(cuota);
          });
        }

        const cuotasCobradasPorFecha = {};

        cuotas.forEach(cuota => {
          if (cuota.pagada === 1) {
            const fechaCuota = cuota.fechacuota;
            if (!cuotasCobradasPorFecha[fechaCuota]) {
              cuotasCobradasPorFecha[fechaCuota] = { cantidad: 0, totalMonto: 0, numerosCuotas: [] };
            }
            cuotasCobradasPorFecha[fechaCuota].cantidad += 1;
            cuotasCobradasPorFecha[fechaCuota].totalMonto += cuota.valor;
            cuotasCobradasPorFecha[fechaCuota].numerosCuotas.push(cuota.numeroCuota); // Guardar el número de cuota
          }
        });

        const cuotasCobradasArray = Object.entries(cuotasCobradasPorFecha).map(([fecha, data]) => ({
          fecha,
          cantidad: data.cantidad,
          totalMonto: data.totalMonto,
          numerosCuotas: data.numerosCuotas,
          cliente: cuotas.find(cuota => cuota.fechacuota === fecha).cliente // Tomar el nombre del cliente de cualquier cuota
        }));

        console.log("Datos de cuotas cobradas por fecha:", cuotasCobradasArray);

        return cuotasCobradasArray;
      } catch (error) {
        console.error("Error al obtener los datos de cuotas:", error);
        return [];
      }
    },

    async fetchCapitalData() {
      try {
        const response = await axios.get("/api/prestamos/monto-total");
        const montoTotal = response.data.monto_total;
        return montoTotal;
      } catch (error) {
        console.error("Error al obtener el monto total de préstamos:", error);
        return 0;
      }
    },

    renderBarChart(clientesData, prestamosData) {
      new Chart(this.$refs.barChart, {
        type: 'bar',
        data: {
          labels: ['Clientes', 'Préstamos'],
          datasets: [{
            label: 'Cantidad',
            data: [clientesData, prestamosData],
            backgroundColor: [
              'rgba(92, 184, 92, 0.5)', // Verde
              'rgba(54, 162, 235, 0.5)' // Azul
            ],
            borderColor: [
              'rgba(92, 184, 92, 1)',
              'rgba(54, 162, 235, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) { return value; }
              }
            }
          },
          plugins: {
            legend: {
              display: true,
              labels: {
                font: {
                  size: 14
                }
              }
            },
            datalabels: {
              formatter: (value, context) => {
                let label = context.chart.data.labels[context.dataIndex];
                return `${label}: ${value}`;
              }
            }
          }
        }
      });
    },

    renderPieChart(capitalData, gananciaData) {
      new Chart(this.$refs.pieChart, {
        type: 'pie',
        data: {
          labels: [`Capital Otorgado`, `Ganancia`],
          datasets: [{
            data: [capitalData, gananciaData],
            backgroundColor: [
              'rgba(255, 99, 132, 0.5)', // Rojo
              'rgba(153, 102, 255, 0.5)'  // Morado
            ],
            borderColor: [
              'rgba(255, 99, 132, 1)',
              'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              labels: {
                font: {
                  size: 14
                }
              }
            },
            datalabels: {
              formatter: (value) => `$${value}`,
              color: '#fff',
              font: {
                weight: 'bold',
                size: 16
              }
            }
          }
        }
      });
    },

    renderScatterChart(cuotasData) {
      const data = cuotasData.map(cuota => ({
        x: new Date(cuota.fecha),
        y: cuota.totalMonto,
        numeroCuota: cuota.numerosCuotas[0],
        cliente: cuota.cliente,
        fecha: cuota.fecha,
        montoCuota: cuota.totalMonto
      }));

      new Chart(this.$refs.scatterChart, {
        type: 'scatter',
        data: {
          datasets: [{
            label: 'Cuotas Cobradas: fecha,Monto',
            data: data,
            backgroundColor: 'rgba(255, 159, 64, 0.5)', // Color naranja
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 1,
            pointRadius: 5,
            pointHoverRadius: 8,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              type: 'time',
              time: {
                unit: 'day'
              },
              title: {
                display: true,
                text: 'Fecha'
              }
            },
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Total Monto'
              }
            }
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: function(context) {
                  const data = context.dataset.data[context.dataIndex];
                  return `Cuota: ${data.numeroCuota}\nCliente: ${data.cliente}\nFecha: ${data.fecha}\nMonto: ${data.montoCuota}`;
                }
              }
            },
            legend: {
              display: true,
              labels: {
                font: {
                  size: 14
                }
              }
            },
            datalabels: {
              display: false 
            }
          }
        }
      });
    },

     mostrarConfirmacionSm() {
    Confirm.show(
      'Notiflix Confirm',
      '¿ Estás seguro de que quieres Emitir Detalles y sumar los montos de estas cuotas seleccionadas ?',
      'Sí',
      'No',
      () => {
        this.sumarMontos();
      }
    );
  },
    // Función para sumar los montos de las cuotas seleccionadas
    sumarMontos() {
      this.totalSuma = this.cuotasSeleccionadas.reduce((sum, cuota) => sum + cuota.totalMonto, 0);
      this.mostrarTotalSuma = true;
    },

     mostrarConfirmacion() {
    Confirm.show(
      'Notiflix Confirm',
      '¿ Estás seguro de que quieres emitir en PDF estos detalles ?',
      'Sí',
      'No',
      () => {
        this.comprobantePdf();
      }
    );
  },
    comprobantePdf() {
      const doc = new jsPDF();
       doc.autoTable({ html: '#my-table' });

      // Título
      doc.setFontSize(18);
      doc.text('Sistema de Prestamos V 1.0', 14, 22);
      doc.text('Comprobante de Cuotas Seleccionadas', 14, 32);

      // Tabla con cuotas seleccionadas
      const tableColumn = ["Cliente", "Cuota N°", "Fecha", "Monto"];
      const tableRows = [];

      this.cuotasSeleccionadas.forEach(cuota => {
        const cuotaData = [
          cuota.cliente,
          cuota.numerosCuotas.join(', '), // Unir números de cuotas si hay más de uno
          cuota.fecha,
          `$${cuota.totalMonto.toFixed(2)}`
        ];
        tableRows.push(cuotaData);
      });

      doc.autoTable(tableColumn, tableRows, { startY: 42 });

      // Mostrar el total
      doc.setFontSize(14);
      doc.text(`Total de las Cuotas Seleccionadas: ${this.formattedTotalSuma}`, 14, doc.autoTable.previous.finalY + 10);

      doc.save('comprobante.pdf');
    },
  },

  mounted() {
    this.fetchDataAndRenderCharts();
  },
};
</script>
