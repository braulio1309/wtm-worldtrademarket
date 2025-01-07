<template>
    <div class="container mt-5">
      <div class="card">
        <div class="card-header text-center">
          <h4>Depósitos</h4>
        </div>
        <div class="card-body">
          <!-- Step Indicator -->
          <ul class="nav nav-pills nav-justified mb-4">
            <li class="nav-item">
              <button 
                class="nav-link"
                :class="{ active: step === 1, disabled: step !== 1 }"
              >
                Paso 1: Selecciona el método de depósito
              </button>
            </li>
            <li class="nav-item">
              <button 
                class="nav-link"
                :class="{ active: step === 2, disabled: step !== 2 }"
              >
                Paso 2: Detalles
              </button>
            </li>
            <li class="nav-item">
              <button 
                class="nav-link"
                :class="{ active: step === 3, disabled: step !== 3 }"
              >
                Paso 3: Confirmar
              </button>
            </li>
          </ul>
  
          <!-- Step 1 -->
          <div v-if="step === 1">
            <div class="form-group">
              <label for="deposit-method">Elige el método de depósito:</label>
              <select 
                id="deposit-method" 
                class="form-control" 
                v-model="form.method"
              >
                <option value="" disabled>Selecciona el método</option>
                <option value="SPEI">SPEI</option>
                <option value="Crypto">Crypto</option>
              </select>
            </div>
            <button class="btn btn-primary" @click="SiguienteStep" :disabled="!form.method">Siguiente</button>
          </div>
  
          <!-- Step 2 -->
          <div v-if="step === 2">
            <div class="form-group">
              <label for="deposit-amount">Ingrese el monto del depósito:</label>
              <input 
                type="number" 
                id="deposit-amount" 
                class="form-control" 
                v-model.number="form.amount" 
                placeholder="Enter amount"
              />
            </div>
  
            <div v-if="form.method === 'SPEI'">
              <h5>Cuenta de SPEI</h5>
              <div class="form-group">
                <label for="spei-account">Cuenta bancaria:</label>
                <input 
                  type="text" 
                  id="spei-account" 
                  class="form-control" 
                  value="Bank: ABC Bank, Account: 1234567890" 
                  readonly
                />
              </div>
            </div>
  
            <div v-if="form.method === 'Crypto'">
              <h5>Crypto Detalles</h5>
              <div class="form-group">
                <label for="crypto-wallet">Dirección de wallet:</label>
                <input 
                  type="text" 
                  id="crypto-wallet" 
                  class="form-control" 
                  value="0xABC123456DEF" 
                  readonly
                />
              </div>
            </div>
  
            <button class="btn btn-secondary mr-2" @click="prevStep">Atrás</button>
            <button class="btn btn-primary" @click="SiguienteStep" :disabled="!form.amount">Siguiente</button>
          </div>
  
          <!-- Step 3 -->
          <div v-if="step === 3">
            <h5>Confirma detalles de depósito</h5>
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th>Depósito dirigido a:</th>
                  <td>{{ form.method }}</td>
                </tr>
                <tr>
                  <th>Moneda </th>
                  <td>USD</td>
                </tr>
                <tr>
                  <th>Moneda de depósito</th>
                  <td>USD</td>
                </tr>
                <tr>
                  <th>Tasa de cambio</th>
                  <td>1 (USD/USD)</td>
                </tr>
                <tr>
                  <th>Monto del depósito</th>
                  <td>{{ form.amount }} USD</td>
                </tr>
                <tr>
                  <th>Total</th>
                  <td>{{ form.amount }} USD</td>
                </tr>
                <tr>
                  <th>Comisiones</th>
                  <td>0 USD</td>
                </tr>
                <tr>
                  <th>Total Depositado</th>
                  <td>{{ form.amount.toFixed(2) }} USD</td>
                </tr>
              </tbody>
            </table>
  
            <!-- Términos y condiciones -->
            <div class="form-check mb-3">
              <input 
                v-model="acceptedPolicies" 
                id="acceptPolicies" 
                type="checkbox" 
                class="form-check-input"
              />
              <label for="acceptPolicies" class="form-check-label">
                Acepto las políticas y condiciones
              </label>
            </div>
  
            <button class="btn btn-secondary mr-2" @click="prevStep">Atrás</button>
            <button class="btn btn-success" @click="submitForm" :disabled="!acceptedPolicies">Depositar</button>
  
            <!-- Mensaje de éxito -->
            <div v-if="isSuccessVisible" class="alert alert-success mt-3">
              ¡Depósito realizado con éxito!
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { axiosPost } from "../../../Helpers/AxiosHelper";
  
  export default {
    data() {
      return {
        step: 1,
        form: {
          method: "",
          amount: null,
        },
        acceptedPolicies: false,
        isSuccessVisible: false,
      };
    },
    methods: {
      SiguienteStep() {
        if (this.step < 3) {
          this.step++;
        }
      },
      prevStep() {
        if (this.step > 1) {
          this.step--;
        }
      },
      submitForm() {
        const url = "create/transaction";
        const data = {
          type: 'deposit',
          amount: this.form.amount,
        };
  
        axiosPost(url, data)
          .then((response) => {
            console.log("Depósito exitoso", response.data);
            this.isSuccessVisible = true;
          })
          .catch((error) => {
            console.error("Error en el depósito", error);
          });
      },
    },
  };
  </script>
  
  <style>
  .card {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  </style>
  