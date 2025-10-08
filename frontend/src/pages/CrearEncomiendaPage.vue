<template>
  <div class="crear-encomienda">
    <div class="container">
      <h1>Crear Nueva Encomienda</h1>
      
      <form @submit.prevent="crearEncomienda" class="encomienda-form">
        <!-- Información básica -->
        <div class="form-section">
          <h2>Información Básica</h2>
          
          <div class="form-group">
            <label for="codigo">Código de Encomienda</label>
            <input 
              type="text" 
              id="codigo" 
              v-model="form.codigo" 
              readonly 
              class="form-control readonly"
              placeholder="Se generará automáticamente"
            >
          </div>
          
          <div class="form-group">
            <label for="descripcion">Descripción *</label>
            <textarea 
              id="descripcion" 
              v-model="form.descripcion" 
              class="form-control"
              rows="3"
              placeholder="Describe el contenido de la encomienda"
              required
            ></textarea>
          </div>
          
          <div class="form-group">
            <label for="costo">Costo (S/) *</label>
            <input 
              type="number" 
              id="costo" 
              v-model="form.costo" 
              class="form-control"
              step="0.01"
              min="0"
              placeholder="0.00"
              required
            >
          </div>
          
          <div class="form-group">
            <label for="observaciones">Observaciones</label>
            <textarea 
              id="observaciones" 
              v-model="form.observaciones" 
              class="form-control"
              rows="2"
              placeholder="Observaciones adicionales (opcional)"
            ></textarea>
          </div>
        </div>
        
        <!-- Clientes -->
        <div class="form-section">
          <h2>Cliente Remitente</h2>
          
          <div class="client-form">
            <div class="form-row">
              <div class="form-group">
                <label for="tipoClienteRemitente">Tipo de Cliente *</label>
                <select 
                  id="tipoClienteRemitente" 
                  v-model="form.clienteRemitente.tipoCliente" 
                  class="form-control"
                  @change="validarDNIRemitente"
                  required
                >
                  <option value="">Seleccionar tipo</option>
                  <option value="Persona Natural">Persona Natural</option>
                  <option value="Empresa">Empresa</option>
                  <option value="Extranjero">Extranjero</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="dniRemitente">Número de Documento *</label>
                <input 
                  type="text" 
                  id="dniRemitente" 
                  v-model="form.clienteRemitente.dni" 
                  class="form-control"
                  :maxlength="getMaxLength(form.clienteRemitente.tipoCliente)"
                  @input="validarDNIRemitente"
                  @blur="buscarClienteRemitente"
                  placeholder="Ingrese el número de documento"
                  required
                >
                <small class="help-text">{{ getHelpText(form.clienteRemitente.tipoCliente) }}</small>
              </div>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="nombreRemitente">Nombre/Razón Social *</label>
                <input 
                  type="text" 
                  id="nombreRemitente" 
                  v-model="form.clienteRemitente.nombre" 
                  class="form-control"
                  placeholder="Nombre completo o razón social"
                  required
                >
              </div>
              
              <div class="form-group">
                <label for="telefonoRemitente">Teléfono *</label>
                <input 
                  type="tel" 
                  id="telefonoRemitente" 
                  v-model="form.clienteRemitente.telefono" 
                  class="form-control"
                  placeholder="Número de teléfono"
                  required
                >
              </div>
            </div>
          </div>
        </div>
        
        <div class="form-section">
          <h2>Cliente Destinatario</h2>
          
          <div class="client-form">
            <div class="form-row">
              <div class="form-group">
                <label for="tipoClienteDestinatario">Tipo de Cliente *</label>
                <select 
                  id="tipoClienteDestinatario" 
                  v-model="form.clienteDestinatario.tipoCliente" 
                  class="form-control"
                  @change="validarDNIDestinatario"
                  required
                >
                  <option value="">Seleccionar tipo</option>
                  <option value="Persona Natural">Persona Natural</option>
                  <option value="Empresa">Empresa</option>
                  <option value="Extranjero">Extranjero</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="dniDestinatario">Número de Documento *</label>
                <input 
                  type="text" 
                  id="dniDestinatario" 
                  v-model="form.clienteDestinatario.dni" 
                  class="form-control"
                  :maxlength="getMaxLength(form.clienteDestinatario.tipoCliente)"
                  @input="validarDNIDestinatario"
                  @blur="buscarClienteDestinatario"
                  placeholder="Ingrese el número de documento"
                  required
                >
                <small class="help-text">{{ getHelpText(form.clienteDestinatario.tipoCliente) }}</small>
              </div>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="nombreDestinatario">Nombre/Razón Social *</label>
                <input 
                  type="text" 
                  id="nombreDestinatario" 
                  v-model="form.clienteDestinatario.nombre" 
                  class="form-control"
                  placeholder="Nombre completo o razón social"
                  required
                >
              </div>
              
              <div class="form-group">
                <label for="telefonoDestinatario">Teléfono *</label>
                <input 
                  type="tel" 
                  id="telefonoDestinatario" 
                  v-model="form.clienteDestinatario.telefono" 
                  class="form-control"
                  placeholder="Número de teléfono"
                  required
                >
              </div>
            </div>
          </div>
        </div>
        
        <!-- Sucursales -->
        <div class="form-section">
          <h2>Sucursales</h2>
          
          <div class="form-row">
            <div class="form-group">
              <label for="sucursalOrigen">Sucursal Origen *</label>
              <select 
                id="sucursalOrigen" 
                v-model="form.idSucursalOrigen" 
                class="form-control"
                required
              >
                <option value="">Seleccionar sucursal origen</option>
                <option 
                  v-for="sucursal in sucursales" 
                  :key="sucursal.idSucursal" 
                  :value="sucursal.idSucursal"
                >
                  {{ sucursal.nombreSucursal }} - {{ sucursal.direccion }}
                </option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="sucursalDestino">Sucursal Destino *</label>
              <select 
                id="sucursalDestino" 
                v-model="form.idSucursalDestino" 
                class="form-control"
                required
              >
                <option value="">Seleccionar sucursal destino</option>
                <option 
                  v-for="sucursal in sucursales" 
                  :key="sucursal.idSucursal" 
                  :value="sucursal.idSucursal"
                >
                  {{ sucursal.nombreSucursal }} - {{ sucursal.direccion }}
                </option>
              </select>
            </div>
          </div>
        </div>
        
        <!-- Botones -->
        <div class="form-actions">
          <button type="button" @click="limpiarFormulario" class="btn btn-secondary">
            Limpiar Formulario
          </button>
          <button type="submit" class="btn btn-primary" :disabled="loading">
            <span v-if="loading">Creando...</span>
            <span v-else>Crear Encomienda</span>
          </button>
        </div>
      </form>
      
      <!-- Mensaje de éxito/error -->
      <div v-if="mensaje" :class="['mensaje', mensaje.tipo]">
        {{ mensaje.texto }}
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'CrearEncomiendaPage',
  data() {
    return {
      loading: false,
      sucursales: [],
      form: {
        codigo: '',
        descripcion: '',
        costo: '',
        observaciones: '',
        clienteRemitente: {
          tipoCliente: '',
          dni: '',
          nombre: '',
          telefono: ''
        },
        clienteDestinatario: {
          tipoCliente: '',
          dni: '',
          nombre: '',
          telefono: ''
        },
        idSucursalOrigen: '',
        idSucursalDestino: ''
      },
      mensaje: null
    }
  },
  async mounted() {
    await this.cargarDatos();
    this.generarCodigo();
  },
  methods: {
    async cargarDatos() {
      try {
        // Solo cargar sucursales (los clientes se crean dinámicamente)
        const sucursalesRes = await axios.get('/sucursales');
        this.sucursales = sucursalesRes.data;
      } catch (error) {
        console.error('Error cargando datos:', error);
        this.mostrarMensaje('Error cargando sucursales', 'error');
      }
    },
    
    generarCodigo() {
      // Generar código único basado en fecha y hora
      const now = new Date();
      const timestamp = now.getTime().toString().slice(-6);
      const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
      this.form.codigo = `ENC-${timestamp}-${random}`;
    },
    
    getMaxLength(tipoCliente) {
      switch(tipoCliente) {
        case 'Persona Natural': return 8;
        case 'Empresa': return 11;
        case 'Extranjero': return 12;
        default: return 12;
      }
    },
    
    getHelpText(tipoCliente) {
      switch(tipoCliente) {
        case 'Persona Natural': return '8 dígitos';
        case 'Empresa': return '11 dígitos';
        case 'Extranjero': return '12 dígitos';
        default: return '';
      }
    },
    
    validarDNIRemitente() {
      const tipo = this.form.clienteRemitente.tipoCliente;
      const dni = this.form.clienteRemitente.dni;
      
      if (tipo && dni) {
        const maxLength = this.getMaxLength(tipo);
        if (dni.length !== maxLength) {
          this.mostrarMensaje(`El DNI debe tener ${maxLength} dígitos para ${tipo}`, 'error');
        }
      }
    },
    
    validarDNIDestinatario() {
      const tipo = this.form.clienteDestinatario.tipoCliente;
      const dni = this.form.clienteDestinatario.dni;
      
      if (tipo && dni) {
        const maxLength = this.getMaxLength(tipo);
        if (dni.length !== maxLength) {
          this.mostrarMensaje(`El DNI debe tener ${maxLength} dígitos para ${tipo}`, 'error');
        }
      }
    },
    
    async buscarClienteRemitente() {
      if (this.form.clienteRemitente.dni && this.form.clienteRemitente.dni.length >= 8) {
        try {
          const response = await axios.get(`/api/clientes/buscar/${this.form.clienteRemitente.dni}`);
          if (response.data) {
            this.form.clienteRemitente.nombre = response.data.nombre;
            this.form.clienteRemitente.telefono = response.data.telefono;
            this.mostrarMensaje('Cliente encontrado, datos cargados automáticamente', 'success');
          }
        } catch (error) {
          // Cliente no encontrado, continuar con formulario vacío
          console.log('Cliente no encontrado, se creará uno nuevo');
        }
      }
    },
    
    async buscarClienteDestinatario() {
      if (this.form.clienteDestinatario.dni && this.form.clienteDestinatario.dni.length >= 8) {
        try {
          const response = await axios.get(`/api/clientes/buscar/${this.form.clienteDestinatario.dni}`);
          if (response.data) {
            this.form.clienteDestinatario.nombre = response.data.nombre;
            this.form.clienteDestinatario.telefono = response.data.telefono;
            this.mostrarMensaje('Cliente encontrado, datos cargados automáticamente', 'success');
          }
        } catch (error) {
          // Cliente no encontrado, continuar con formulario vacío
          console.log('Cliente no encontrado, se creará uno nuevo');
        }
      }
    },
    
    async crearEncomienda() {
      this.loading = true;
      this.mensaje = null;
      
      try {
        const response = await axios.post('/api/encomiendas', this.form);
        
        this.mostrarMensaje('Encomienda creada exitosamente', 'success');
        this.limpiarFormulario();
        
        // Opcional: redirigir a la lista de encomiendas
        setTimeout(() => {
          this.$router.push('/encomiendas');
        }, 2000);
        
      } catch (error) {
        console.error('Error creando encomienda:', error);
        this.mostrarMensaje('Error al crear la encomienda', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    limpiarFormulario() {
      this.form = {
        codigo: '',
        descripcion: '',
        costo: '',
        observaciones: '',
        clienteRemitente: {
          tipoCliente: '',
          dni: '',
          nombre: '',
          telefono: ''
        },
        clienteDestinatario: {
          tipoCliente: '',
          dni: '',
          nombre: '',
          telefono: ''
        },
        idSucursalOrigen: '',
        idSucursalDestino: ''
      };
      this.generarCodigo();
      this.mensaje = null;
    },
    
    mostrarMensaje(texto, tipo) {
      this.mensaje = { texto, tipo };
      setTimeout(() => {
        this.mensaje = null;
      }, 5000);
    }
  }
}
</script>

<style scoped>
.crear-encomienda {
  padding: 2rem;
  background-color: #f8f9fa;
  min-height: 100vh;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  padding: 2rem;
}

h1 {
  color: #333;
  margin-bottom: 2rem;
  text-align: center;
  font-size: 2rem;
}

.form-section {
  margin-bottom: 2rem;
  padding: 1.5rem;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  background-color: #f8f9fa;
}

.form-section h2 {
  color: #495057;
  margin-bottom: 1rem;
  font-size: 1.2rem;
  border-bottom: 2px solid #007bff;
  padding-bottom: 0.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #495057;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ced4da;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.form-control:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.form-control.readonly {
  background-color: #e9ecef;
  color: #6c757d;
}

.help-text {
  display: block;
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #6c757d;
}

.client-form {
  background-color: white;
  padding: 1rem;
  border-radius: 6px;
  border: 1px solid #dee2e6;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 2rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: #0056b3;
  transform: translateY(-2px);
}

.btn-primary:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #545b62;
  transform: translateY(-2px);
}

.mensaje {
  margin-top: 1rem;
  padding: 1rem;
  border-radius: 6px;
  font-weight: 600;
}

.mensaje.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.mensaje.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .crear-encomienda {
    padding: 1rem;
  }
}
</style>
