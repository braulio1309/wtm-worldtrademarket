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
            <button class="nav-link" :class="{ active: step === 1, disabled: step !== 1 }">
              Paso 1: Selecciona el método de depósito
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" :class="{ active: step === 2, disabled: step !== 2 }">
              Paso 2: Detalles
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" :class="{ active: step === 3, disabled: step !== 3 }">
              Paso 3: Confirmar
            </button>
          </li>
        </ul>

        <!-- Step 1 -->
        <div v-if="step === 1">
          <div class="form-group">
            <label for="deposit-method">Elige el método de depósito:</label>
            <select id="deposit-method" class="form-control" v-model="form.method">
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
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">USD</span>
              </div>
              <input type="number" id="deposit-amount" class="form-control" v-model.number="form.amount"
                placeholder="Ingrese el monto" min="100"/>
            </div>
            <div v-if="form.amount < 100" class="alert alert-warning" role="alert">
              El monto minimo es de 100 USD
            </div>
          </div>

          <button class="btn btn-secondary mr-2" @click="prevStep">Atrás</button>
          <button class="btn btn-primary" @click="SiguienteStep" :disabled="form.amount < 100">Siguiente</button>
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
                <th>Moneda</th>
                <td>USD</td>
              </tr>
              <tr v-if="form.method == 'SPEI'">
                <th>Tasa de cambio</th>
                <td> 20.58 MXM</td>
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
            <input v-model="acceptedPolicies" id="acceptPolicies" type="checkbox" class="form-check-input" />
            <label for="acceptPolicies" class="form-check-label">
              Acepto las políticas y condiciones
            </label>
          </div>

          <button class="btn btn-secondary mr-2" @click="prevStep">Atrás</button>
          <button class="btn btn-success" @click="submitForm" :disabled="!acceptedPolicies">Depositar</button>

          <!-- Mensaje de éxito -->
          <div v-if="isSuccessVisible" class="alert alert-success mt-3">
            ¡Orden generada con éxito!
          </div>

          <!-- Modal de Depósito con los detalles -->
          <div v-if="isModalVisible" class="modal fade show d-block deposit-confirmation" tabindex="-1"
            style="background: rgba(0, 0, 0, 0.7)">
            <div class="modal-dialog modal-lg card">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Ficha de Depósito</h5>
                  <button type="button" class="btn-close" @click="closeDepositModal"></button>
                </div>
                <div class="modal-body text-center">
                  <!-- Información SPEI -->
                  <h5 v-if="form.method === 'SPEI'">Información Bancaria</h5>
                  <div v-if="form.method === 'SPEI'">
                    <table class="table table-bordered">
                      <tbody>
                        <tr>
                          <th>CLABE:</th>
                          <td>{{ clabe }}</td>
                        </tr>
                        <tr>
                          <th>Banco</th>
                          <td>STP</td>
                        </tr>
                        <tr>
                          <th>Beneficiario</th>
                          <td>Unlimit</td>
                        </tr>
                        <tr>
                          <th>Importe:</th>
                          <td>{{ calculateConversion(form.amount) }} MXN</td>
                        </tr>
                      </tbody>
                    </table>

                    <button class="btn btn-sm btn-outline-primary" @click="copyToClipboard(clabe)">
                      Copiar clabe
                    </button>


                  </div>

                  <!-- Información Crypto -->
                  <h5 v-if="form.method === 'Crypto'">Dirección de Depósito</h5>
                  <div v-if="form.method === 'Crypto'">
                    <img :src="qrImage" alt="QR Code" class="img-fluid" width="25%" height="25%" />
                    <table class="table table-bordered" style="margin-top: 20px;">
                      <tbody>
                        <tr>
                          <th>Red:</th>
                          <td>Tron (TRC20)</td>
                        </tr>
                        <tr>
                          <th>Dirección</th>
                          <td>{{ wallet }}</td>
                        </tr>
                      </tbody>
                    </table>
                    <button class="btn btn-sm btn-outline-primary" @click="copyToClipboard(wallet)">
                      Copiar Wallet
                    </button>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" @click="closeDepositModal">Cerrar</button>
                  <button type="button" class="btn btn-success" @click="goToDepositView">Abrir en otra ventana</button>
                </div>
              </div>
            </div>
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
        amount: 0,
      },
      clabe: "646682177602616125",
      wallet: "TLXLzGVK2X2oFYQBSxPcqAxiJakmB6TCve",
      acceptedPolicies: false,
      isSuccessVisible: false,
      convertedAmount: 0,
      qrImage: "https://darkorchid-okapi-696445.hostingersite.com/storage/app/uploads/qr.jpg",
      isModalVisible: false,
    };
  },
  methods: {
    calculateConversion() {
      const conversionRate = 20.58; // Tasa de conversión de ejemplo (1 USD = 20 MXN)
      if (this.form.amount) {
        return this.form.amount * conversionRate;
      } else {
        return 0;
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
          this.openDepositModal(); // Abre el modal con los detalles después del depósito
        })
        .catch((error) => {
          console.error("Error en el depósito", error);
        });
    },
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
    openDepositModal() {
      this.isModalVisible = true;
    },
    closeDepositModal() {
      this.isModalVisible = false;
    },
    reloadPage() {
      window.location.reload();
    },
    copyToClipboard(value) {
      const textarea = document.createElement("textarea");
      textarea.value = value;
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand("copy"); // Método compatible con más navegadores
      document.body.removeChild(textarea);
      alert("Copiado al portapapeles");
    },
    goToDepositView() {
      const depositData = {
        method: this.form.method,
        amount: this.form.amount,
        clabe: this.clabe,
        wallet: this.wallet
      };

      // Redirigir a la vista en Laravel con los datos
      window.open(`deposit/confirmation?method=${this.form.method}&amount=${this.form.amount}`, '_blank');


    },
    closeDepositModal() {
      this.isModalVisible = false;
    }
  },
};
</script>

<style>
.card {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.modal-content {
  background-color: #f8f9fa;
}

.modal-dialog.modal-lg {
  max-width: 90%;
  /* Hace que el modal sea más grande */
}

.modal-header {
  background-color: #28a745;
  color: white;
}

.modal-footer {
  text-align: center;
}

.alert-success {
  text-align: center;
}

.deposit-confirmation {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: rgba(0, 0, 0, 0.7);
}


.btn {
    margin-top: 10px;
}
</style>


