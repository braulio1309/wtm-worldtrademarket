<template>
    <div v-if="componentLoader" class="position-relative h-100">
        <app-overlay-loader />
    </div>
    <div v-else>
        <!--<app-overlay-loader v-if="preloader"/>-->
        <form class="mb-0" ref="form" :data-url="'payment-methods'"
            :class="{ 'loading-opacity': preloader }">
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                        <label for="active_method" class="text-left d-block mb-lg-2 mb-xl-0">
                            Selecciona tu método de retiro
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <select id="active_method" v-model="paymentMethod.active_method" class="form-control">
                            <option value="usdt">Wallet - Only USDT TRC20</option>
                            <option value="bank_account">Cuenta bancaria</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group" v-if="paymentMethod.active_method == 'usdt'">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                        <label for="wallet" class="text-left d-block mb-lg-2 mb-xl-0">
                            Wallet
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input id="wallet" type="text" :placeholder="'Escribe tu wallet'"
                            v-model="paymentMethod.wallet" :error-message="$errorMessage(errors, 'wallet')"
                            :required="false" />
                    </div>
                </div>
            </div>
            <div class="form-group" v-if="paymentMethod.active_method == 'bank_account'">
                <div class="row">
                    <div class="col-lg-3 col-xl-3">
                        <label for="bank" class="text-left d-block mb-lg-2 mb-xl-0">
                            Nombre de banco
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input id="bank" type="text" :placeholder="'Escribe el nombre del banco'"
                            v-model="paymentMethod.bank" :error-message="$errorMessage(errors, 'bank')"
                            :required="false" />
                    </div>
                </div>
            </div>
            <div class="form-group" v-if="paymentMethod.active_method == 'bank_account'">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                        <label for="interbank_key" class="text-left d-block mb-lg-2 mb-xl-0">
                            Clave interbancaria
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input id="interbank_key" type="text" :placeholder="'Clave interbancaria'"
                            v-model="paymentMethod.interbank_key"
                            :error-message="$errorMessage(errors, 'interbank_key')" :required="false" />
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-0 mt-5">
                <div class="row">
                    <div class="col-12 action-buttons">
                        <button type="submit" class="btn btn-primary mr-3" @click.prevent="submit">
                            {{ $t('save') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</template>

<script>
import { FormMixin } from "../../../../core/mixins/form/FormMixin";
import GlobalHelperMixin from "../../../Mixins/global/GlobalHelperMixin";
import { UserMixin } from "./Mixins/UserMixin";
import AppLibrary from "../../../Helpers/AppLibrary";
import * as actions from "../../../Config/ApiUrl";
import { urlGenerator } from "../../../Helpers/AxiosHelper";

export default {
    name: "MetodoRetiro",
    extends: AppLibrary,
    mixins: [FormMixin, UserMixin, GlobalHelperMixin],

    data() {
        return {
            preloader: false,
            authUser: {},
            errors: {},
            paymentMethod: {
                active_method: '',
                wallet: '',
                bank: '',
                interbank_key: ''  
            }
        }
    },
    mounted(){
        let paymentMethodo = this.user.loggedInUser.payment_methods;
        if(paymentMethodo){
            if(paymentMethodo[paymentMethodo.length-1].type == 'usdt'){
                this.paymentMethod.active_method = paymentMethodo[paymentMethodo.length-1].type
                this.paymentMethod.wallet = paymentMethodo[paymentMethodo.length-1].wallet
            }else{
                let lastBankAccount = [...paymentMethodo].reverse().find(pm => pm.type === 'bank_account');
                if(lastBankAccount){
                    this.paymentMethod.active_method = lastBankAccount.type
                    this.paymentMethod.bank = lastBankAccount.bank
                    this.paymentMethod.interbank_key = lastBankAccount.interbank_key
                }    
            }
            
        }else {
            let lastBankAccount = [...paymentMethodo].reverse().find(pm => pm.type === 'bank_account');
            if (lastBankAccount) {
                this.paymentMethod.active_method = lastBankAccount.type;
                this.paymentMethod.bank = lastBankAccount.bank;
                this.paymentMethod.interbank_key = lastBankAccount.interbank_key;
            } else {
                this.paymentMethod.active_method = '';
                this.paymentMethod.bank = '';
                this.paymentMethod.interbank_key = '';
            }
        }
        console.log(this.user.loggedInUser)
    },
    methods: {
        beforeSubmit() {
            this.scrollToTop(true);
            this.preloader = true;
        },
        submit() {
            this.save(this.paymentMethod);
        },
        afterFinalResponse() {
            this.preloader = false;
        },
        afterSuccess(res) {
            this.$toastr.s(res.data.message);
            window.location = urlGenerator(actions.LOGOUT);
        },
        afterError(res) {
            this.errors = res.data.errors;
            //this.$toastr.e(res.data.message);
        }
    }
}
</script>
