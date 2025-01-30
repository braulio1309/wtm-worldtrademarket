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
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>CLABE:</th>
                                    <td> {{ depositData.clabe }}</td>
                                </tr>
                                <tr>
                                    <th>Beneficiario</th>
                                    <td>{{ depositData.beneficiary }}</td>
                                </tr>
                                <tr>
                                    <th>Tasa de conversión</th>
                                    <td>20.58</td>
                                </tr>
                                <tr>
                                    <th>Importe:</th>
                                    <td>{{ depositData.amount }} MXN</td>
                                </tr>
                            </tbody>
                        </table>
                        <button @click="copyToClipboard(depositData.clabe)" class="btn btn-primary">Copiar
                            CLABE</button>
                    </div>

                    <h5 v-if="depositData.method === 'Crypto'">Dirección de Depósito</h5>
                    <div v-if="depositData.method === 'Crypto'">
                        <img :src="depositData.qrImage" alt="QR Code" class="img-fluid" />
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Red:</th>
                                    <td>{{ depositData.network }}</td>
                                </tr>
                                <tr>
                                    <th>Dirección</th>
                                    <td>{{ depositData.wallet }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <button @click="copyToClipboard(depositData.wallet)" class="btn btn-primary">Copiar
                            Dirección</button>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-success" @click="goHome">Volver al dashboard</button>
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
                method: null,
                amount: null,
                clabe: "646682177602616125",
                bank: "STP",
                beneficiary: "Unlimit",
                qrImage: "https://darkorchid-okapi-696445.hostingersite.com/storage/app/uploads/qr.jpg", // Cambiar por la imagen real
                network: "Tron (TRC20)",
                wallet: "TLXLzGVK2X2oFYQBSxPcqAxiJakmB6TCve"
            }
        };
    },
    mounted() {

        const params = new URLSearchParams(window.location.search);
        this.depositData.method = params.get('method');
        this.depositData.amount = params.get('amount')

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