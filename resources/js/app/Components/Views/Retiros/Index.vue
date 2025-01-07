<template>
  <div class="container mt-5">
    <div class="card shadow p-4">
      <h3 class="text-center mb-4">Formulario de Retiros</h3>
      <form @submit.prevent="showConfirmationModal">
        <!-- Método de Retiro -->
        <div class="mb-3">
          <label for="method" class="form-label">Método de Retiro</label>
          <select 
            v-model="formData.method" 
            id="method" 
            class="form-select" 
            @change="handleMethodChange"
          >
            <option value="" disabled>Seleccione un método</option>
            <option value="wallet">Wallet</option>
            <option value="cuenta_bancaria">Cuenta Bancaria</option>
          </select>
        </div>

        <!-- Canal (para Wallet) -->
        <div v-if="formData.method === 'wallet'" class="mb-3">
          <label for="channel" class="form-label">Canal</label>
          <select 
            v-model="formData.channel" 
            id="channel" 
            class="form-select"
          >
            <option value="" disabled>Seleccione un canal</option>
            <option value="USDT-ERC20">USDT-ERC20</option>
            <option value="USDT-TRC20">USDT-TRC20</option>
          </select>
        </div>

        <!-- Monto -->
        <div class="mb-3">
          <label for="amount" class="form-label">Monto</label>
          <input 
            v-model="formData.amount" 
            id="amount" 
            type="number" 
            class="form-control" 
            placeholder="Ingrese el monto"
          />
        </div>

        <!-- Aceptar Políticas -->
        <div class="form-check mb-3">
          <input 
            v-model="formData.acceptedPolicies" 
            id="acceptPolicies" 
            type="checkbox" 
            class="form-check-input"
          />
          <label for="acceptPolicies" class="form-check-label">
            Acepto las políticas y condiciones
          </label>
        </div>

        <!-- Botón de Retirar -->
        <button 
          type="submit" 
          class="btn btn-primary w-100" 
          :disabled="!formData.acceptedPolicies"
        >
          Retirar
        </button>
      </form>
    </div>

    <!-- Modal de Confirmación -->
    <div 
      v-if="isConfirmationModalVisible"
      class="modal fade show d-block"
      tabindex="-1"
      aria-labelledby="confirmationModalLabel"
      aria-hidden="true"
      style="background: rgba(0, 0, 0, 0.5)"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmationModalLabel">Confirmación</h5>
            <button type="button" class="btn-close" @click="closeConfirmationModal"></button>
          </div>
          <div class="modal-body">
            ¿Estás seguro de que deseas realizar este retiro?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeConfirmationModal">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="confirmSubmit">Confirmar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mensaje de Éxito -->
    <div v-if="isSuccessVisible" class="alert alert-success mt-3">
      ¡Retiro realizado con éxito, se le notificará la aprobación del retiro!
    </div>
  </div>
</template>

<script>
import { axiosPost } from "../../../Helpers/AxiosHelper";

export default {
  data() {
    return {
      formData: {
        method: "",
        channel: "",
        amount: null,
        acceptedPolicies: false,
      },
      isConfirmationModalVisible: false,
      isSuccessVisible: false,
    };
  },
  methods: {
    handleMethodChange() {
      if (this.formData.method !== "wallet") {
        this.formData.channel = ""; // Resetear el canal si no es Wallet
      }
    },
    showConfirmationModal() {
      if (!this.formData.method || !this.formData.amount) {
        alert("Por favor, complete todos los campos requeridos.");
        return;
      }
      this.isConfirmationModalVisible = true;
    },
    closeConfirmationModal() {
      this.isConfirmationModalVisible = false;
    },
    confirmSubmit() {
      this.closeConfirmationModal();
      let url = `create/transaction`;
      let data = {
        type: 'withdrawal',
        amount: this.formData.amount,
      };
      axiosPost(url, data).then(response => {
        this.isSuccessVisible = true;
        this.formData = {
        method: "",
        channel: "",
        amount: null,
        acceptedPolicies: false,
      },
        console.log(response.data);
      });
    },
  },
};
</script>
