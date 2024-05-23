<template>
  <div>
    <br />
    <h2 class="text-center">Clientes <i class="fas fa-user"></i></h2>
    <br />
    <button class="btn btn-success mb-2" @click="mostrarModalNuevoCliente">
      <i class="fas fa-plus"></i> Nuevo Cliente 
    </button>

    <br />
    <br />
    <div class="table-responsive">
      <table ref="clientTable" class="table table-striped">
        <thead>
          <tr>
            <th>Nombre:</th>
            <th>Apellido:</th>
            <th>Dni:</th>
            <th>Direccion:</th>
            <th>Telefono:</th>
            <th>Celular:</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cliente in clientes" :key="cliente.id">
            <td>{{ cliente.nombre }}</td>
            <td>{{ cliente.apellido }}</td>
            <td>{{ cliente.dni }}</td>
            <td>{{ cliente.direccion }}</td>
            <td>{{ cliente.telefono }}</td>
            <td>{{ cliente.celular }}</td>
            <td>
              <button class="btn btn-primary" @click="clientDetail(cliente)">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-danger" @click="eliminarCliente(cliente.id)">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal para agregar nuevo cliente -->
    <div class="modal fade" :class="{ 'show': mostrarModal }" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Nuevo Cliente</h5>
          </div>
          <form @submit.prevent="guardarNuevoCliente">
            <div class="modal-body">
              <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" v-model="nuevoCliente.nombre" placeholder="Nombre" required>
              </div>
              <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" class="form-control" id="apellido" v-model="nuevoCliente.apellido" placeholder="Apellido" required>
              </div>
              <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" class="form-control" id="dni" v-model="nuevoCliente.dni" placeholder="DNI" required>
              </div>
              <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" class="form-control" id="direccion" v-model="nuevoCliente.direccion" placeholder="Dirección" required>
              </div>
              <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" class="form-control" id="telefono" v-model="nuevoCliente.telefono" placeholder="Teléfono">
              </div>
              <div class="form-group">
                <label for="celular">Celular:</label>
                <input type="text" class="form-control" id="celular" v-model="nuevoCliente.celular" placeholder="Celular">
              </div>
              <div class="form-group">
                <label for="dniFrente">DNI Frente:</label>
                <input type="file" class="form-control" id="dniFrente" @change="procesarImagenFrente">
              </div>
              <div class="form-group">
                <label for="dniDorso">DNI Dorso:</label>
                <input type="file" class="form-control" id="dniDorso" @change="procesarImagenDorso">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="cerrarModal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal para detalles del cliente -->
    <div class="modal fade" :class="{ 'show': mostrarModalDetalles }" tabindex="-1" role="dialog" aria-labelledby="modalTitleDetails" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitleDetails">Detalles del Cliente</h5>
          </div>
          <form @submit.prevent="guardarClienteEditado">
            <div class="modal-body">
              <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" v-model="clienteSeleccionado.nombre" placeholder="Nombre" required>
              </div>
              <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" class="form-control" id="apellido" v-model="clienteSeleccionado.apellido" placeholder="Apellido" required>
              </div>
              <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" class="form-control" id="dni" v-model="clienteSeleccionado.dni" placeholder="DNI" required>
              </div>
              <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" class="form-control" id="direccion" v-model="clienteSeleccionado.direccion" placeholder="Dirección" required>
              </div>
              <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" class="form-control" id="telefono" v-model="clienteSeleccionado.telefono" placeholder="Teléfono">
              </div>
              <div class="form-group">
                <label for="celular">Celular:</label>
                <input type="text" class="form-control" id="celular" v-model="clienteSeleccionado.celular" placeholder="Celular">
              </div>
              <div class="form-group">
                <label for="dniFrente">DNI Frente:</label>
                <input type="file" class="form-control" id="dniFrente" @change="procesarImagenFrente">
              </div>
              <div class="form-group">
                <label for="dniDorso">DNI Dorso:</label>
                <input type="file" class="form-control" id="dniDorso" @change="procesarImagenDorso">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="cerrarModalDetalles">Cancelar</button>
              <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { Notify } from 'notiflix/build/notiflix-notify-aio';

const clientes = ref([]);
const mostrarModal = ref(false);
const mostrarModalDetalles = ref(false);
const nuevoCliente = ref({
  nombre: "",
  apellido: "",
  dni: "",
  direccion: "",
  telefono: "",
  celular: "",
  dniFrente: null,
  dniDorso: null,
});
const clienteSeleccionado = ref({
  nombre: "",
  apellido: "",
  dni: "",
  direccion: "",
  telefono: "",
  celular: "",
  dniFrente: null,
  dniDorso: null,
});

const cargarClientes = () => {
  axios
    .get("/api/clientes")
    .then((response) => {
      clientes.value = response.data;
    })
    .catch((error) => {
      console.error("Error al cargar clientes", error);
    });
};

const mostrarModalNuevoCliente = () => {
  mostrarModal.value = true;
};

const cerrarModal = () => {
  mostrarModal.value = false;
};

const procesarImagenFrente = (event) => {
  const file = event.target.files[0];
  nuevoCliente.value.dniFrente = file;
  clienteSeleccionado.value.dniFrente = file;
};

const procesarImagenDorso = (event) => {
  const file = event.target.files[0];
  nuevoCliente.value.dniDorso = file;
  clienteSeleccionado.value.dniDorso = file;
};

const guardarNuevoCliente = () => {
  const formData = new FormData();
  Object.keys(nuevoCliente.value).forEach(key => {
    formData.append(key, nuevoCliente.value[key]);
  });
  axios
    .post("/api/clientes", formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    .then((response) => {
      clientes.value.push(response.data);
      cerrarModal();
      Notify.success('Guardaste tu Cliente Correctamente');
    })
    .catch((error) => {
      console.error("Error al crear cliente", error);
      Notify.failure('Ocurrio error al guardar el Cliente asegurate de no Repetir algunos datos');
    });
};

const eliminarCliente = (clienteId) => {
  axios
    .delete(`/api/clientes/${clienteId}`)
    .then(() => {
      clientes.value = clientes.value.filter(
        (cliente) => cliente.id !== clienteId
      );
      Notify.failure('Eliminaste el Cliente De tu Lista');
    })
    .catch((error) => {
      console.error("Error al eliminar cliente", error);
    });
};

const clientDetail = (cliente) => {
  clienteSeleccionado.value = { ...cliente };
  mostrarModalDetalles.value = true;
};

const cerrarModalDetalles = () => {
  mostrarModalDetalles.value = false;
};

const guardarClienteEditado = () => {
  const clienteData = {
    nombre: clienteSeleccionado.value.nombre,
    apellido: clienteSeleccionado.value.apellido,
    dni: clienteSeleccionado.value.dni,
    direccion: clienteSeleccionado.value.direccion,
    telefono: clienteSeleccionado.value.telefono,
    celular: clienteSeleccionado.value.celular,
    id: clienteSeleccionado.value.id
  };

  if (clienteSeleccionado.value.dniFrente instanceof File) {
    clienteData.dniFrente = clienteSeleccionado.value.dniFrente;
  }

  if (clienteSeleccionado.value.dniDorso instanceof File) {
    clienteData.dniDorso = clienteSeleccionado.value.dniDorso;
  }

  axios
    .put(`/api/clientes/${clienteSeleccionado.value.id}`, clienteData)
    .then(() => {
      const index = clientes.value.findIndex(cliente => cliente.id === clienteSeleccionado.value.id);
      if (index !== -1) {
        console.log("Cliente antes de la actualización:", clientes.value[index])
        clientes.value[index] = { ...clienteSeleccionado.value };
      }
      cerrarModalDetalles();
      Notify.success('Cliente actualizado correctamente');
    })
    .catch((error) => {
      console.error("Error al actualizar cliente", error);
      Notify.failure('Ocurrió un error al actualizar el cliente');
    });
};

onMounted(cargarClientes);
</script>

<style scoped>
.modal {
  display: none;
}
.modal.show {
  display: block;
}
</style>
 