<template>
    <div class="deposit-confirmation">
      <div class="container">
        <div class="card">
          <div class="card-header text-center">
            <h3>Ficha de Depósito</h3>
          </div>
          <div class="card-body text-center">
            <h5 v-if="depositData.method === 'SPEI'">Información Bancaria</h5>
            <div v-if="depositData.method === 'SPEI'">
              <p><strong>CLABE:</strong> {{ depositData.clabe }}</p>
              <button @click="copyToClipboard(depositData.clabe)" class="btn btn-primary">Copiar CLABE</button>
              <p><strong>Banco:</strong> {{ depositData.bank }}</p>
              <p><strong>Beneficiario:</strong> {{ depositData.beneficiary }}</p>
              <p><strong>Importe:</strong> {{ depositData.amount }} MXN</p>
            </div>
  
            <h5 v-if="depositData.method === 'Crypto'">Dirección de Depósito</h5>
            <div v-if="depositData.method === 'Crypto'">
              <img :src="depositData.qrImage" alt="QR Code" class="img-fluid" />
              <p><strong>Red:</strong> {{ depositData.network }}</p>
              <p><strong>Dirección:</strong> {{ depositData.wallet }}</p>
              <button @click="copyToClipboard(depositData.wallet)" class="btn btn-primary">Copiar Dirección</button>
            </div>
          </div>
          <div class="card-footer text-center">
            <button class="btn btn-success" @click="goHome">Aceptar</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        depositData: {
          method: this.$route.query.method || "SPEI",
          amount: this.$route.query.amount || "0.00",
          clabe: "646682177602616125",
          bank: "STP",
          beneficiary: "Unlimit",
          qrImage: "/path/to/qrcode.png", // Cambiar por la imagen real
          network: "Tron (TRC20)",
          wallet: "TLXLzGVK2X2oFYQBSxPcqAxiJakmB6TCve"
        }
      };
    },
    created(){
        console.log('aaa')
    },
    methods: {
      copyToClipboard(value) {
        navigator.clipboard.writeText(value)
          .then(() => alert("Copiado al portapapeles"))
          .catch(err => console.error("Error al copiar:", err));
      },
      goHome() {
        this.$router.push("/"); // Redirige a la página principal
      }
    }
  };
  </script>
  
  <style scoped>
  .deposit-confirmation {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: rgba(0, 0, 0, 0.7);
  }
  
  .card {
    width: 50%;
    padding: 20px;
  }
  
  .btn {
    margin-top: 10px;
  }
  </style>

  
  