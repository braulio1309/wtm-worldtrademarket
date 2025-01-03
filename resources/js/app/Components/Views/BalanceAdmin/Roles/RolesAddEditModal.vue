<template>
    <modal :modal-id="userAndRoles.users.userModalId"
                     :title="'Depositar'"
                     :preloader="preloader"
                     @submit="submit"
                     @close-modal="closeModal">
        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <form class="mb-0"
                  :class="{'loading-opacity': preloader}"
                  ref="form" :data-url='`/admin/edit/transaction2/${inputs.id}`'>
                <div class="form-group row align-items-center">
                    <label for="inputs_name" class="col-sm-3 mb-0">
                        ID usuario
                    </label>
                    <app-input id="inputs_name"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.account.user.id"
                               :placeholder="'Monto a depositar'"
                               :read-only="true"
                               :required="true"/>
                    <label for="inputs_name" class="col-sm-3 mb-0">
                        Nombre
                    </label>
                    <app-input id="inputs_name"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.account.user.first_name"
                               :placeholder="'Monto a depositar'"
                               :read-only="true"
                               :required="true"/>

                    <label for="inputs_name" class="col-sm-3 mb-0">
                        Email
                    </label>
                    <app-input id="inputs_name"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.account.user.email"
                               :placeholder="'Monto a depositar'"
                               :read-only="true"
                               :required="true"/>
                    <label for="inputs_name" class="col-sm-3 mb-0">
                        Folio
                    </label>
                    <app-input id="inputs_name"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.folio"
                               :placeholder="'Monto a depositar'"
                               :read-only="true"
                               :required="true"/>
                    <label for="inputs_name" class="col-sm-3 mb-0">
                        Método de retiro
                    </label>
                    <app-input id="inputs_name"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.account.user.payment_methods[0].type"
                               :placeholder="'Monto a depositar'"
                               :read-only="true"
                               :required="true"/>
                    <label for="inputs_name" class="col-sm-3 mb-0" v-if="inputs.account.user.payment_methods[0].type == 'usdt'">
                        Wallet
                    </label>
                    <app-input id="inputs_name"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.account.user.payment_methods[0].wallet"
                               :placeholder="'Monto a depositar'"
                               :read-only="true"
                               :required="true"
                               v-if="inputs.account.user.payment_methods[0].type == 'usdt'"
                               />
                    <label for="inputs_name" class="col-sm-3 mb-0" v-if="inputs.account.user.payment_methods[0].type == 'bank_account'">
                        Clave interbancaria
                    </label>
                    <app-input id="inputs_name"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.account.user.payment_methods[0].interbank_key"
                               :placeholder="'Monto a depositar'"
                               :read-only="true"
                               :required="true"
                               v-if="inputs.account.user.payment_methods[0].type == 'bank_account'"
                               />
                               <label for="inputs_name" class="col-sm-3 mb-0" v-if="inputs.account.user.payment_methods[0].type == 'bank_account'">
                        Banco
                    </label>
                    <app-input id="inputs_name"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.account.user.payment_methods[0].bank"
                               :placeholder="'Monto a depositar'"
                               :read-only="true"
                               :required="true"
                               v-if="inputs.account.user.payment_methods[0].type == 'bank_account'"
                               />
                    <label for="inputs_name" class="col-sm-3 mb-0">
                        Ingrese el monto a retirar
                    </label>
                    <app-input id="inputs_name"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.amount"
                               :placeholder="'Monto a retirar'"
                               :read-only="true"
                               :required="true"/>

                </div>
                
                <div class="form-group row align-items-center">
                    <label for="inputs_email" class="col-sm-3 mb-0">
                        Estatus
                    </label>
                    <app-input id="inputs_email"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.status"
                               :placeholder="'approved'"
                               :required="true"/>

                </div>
            </form>
        </template>
    </modal>
</template>

<script>
    import {FormMixin} from '../../../../../core/mixins/form/FormMixin.js';
    import {ModalMixin} from "../../../../Mixins/ModalMixin.js";

    import {UserAndRoleMixin} from '../Mixins/UserAndRoleMixin.js';

    export default {
        name: "UserModal",
        mixins: [FormMixin, ModalMixin, UserAndRoleMixin],
        data() {
            return {
                preloader: false,
                inputs: {},
                modalTitle: this.$t('edit_user'),
            }
        },
        created() {
            this.inputs = this.userAndRoles.rowData;
        },
        methods: {
            submit() {
                this.save(this.inputs);
            },
            afterSuccess(response) {
                this.$toastr.s(response.data.message);
                this.reLoadTable();
            },
        },
    }
</script>