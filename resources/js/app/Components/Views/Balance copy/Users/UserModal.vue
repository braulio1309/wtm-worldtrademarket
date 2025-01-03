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
                  ref="form" :data-url='`/update-user-name/${inputs.id}`'>
                <div class="form-group row align-items-center">
                    <label for="inputs_name" class="col-sm-3 mb-0">
                        Ingrese el monto a depositar
                    </label>
                    <app-input id="inputs_name"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.amount"
                               :placeholder="'Monto a depositar'"
                               :required="true"/>

                </div>
                <div class="form-group row align-items-center">
                    <label for="inputs_email" class="col-sm-3 mb-0">
                        Concepto
                    </label>
                    <app-input id="inputs_email"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.description"
                               :placeholder="'Concepto'"
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