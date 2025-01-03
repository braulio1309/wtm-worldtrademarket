<template>
    <modal :modal-id="userAndRoles.users.userModalId" :title="modalTitle" :preloader="preloader" @submit="submit"
        @close-modal="closeModal">
        <template slot="body">
            <app-overlay-loader v-if="preloader" />
            <form class="mb-0" :class="{ 'loading-opacity': preloader }" ref="form"
                :data-url='`/update-user-name/${inputs.id}`'>
                <div class="form-group row align-items-center">
                    <label for="inputs_name" class="col-sm-3 mb-0">
                        {{ $t('first_name') }}
                    </label>
                    <app-input id="inputs_name" class="col-sm-9" type="text" v-model="inputs.first_name"
                        :placeholder="$t('enter_first_name')" :required="true" />

                </div>
                <div class="form-group row align-items-center">
                    <label for="inputs_email" class="col-sm-3 mb-0">
                        {{ $t('last_name') }}
                    </label>
                    <app-input id="inputs_email" class="col-sm-9" type="text" v-model="inputs.last_name"
                        :placeholder="$t('enter_last_name')" :required="true" />

                        <label for="inputs_email" class="col-sm-3 mb-0">
                        Saldo en cuenta
                    </label>
                    <app-input id="inputs_email" class="col-sm-9" type="number" v-if='inputs.account' v-model="inputs.account.balance"
                        :placeholder="'balance'" :required="true" /> 

                    <!-- Mostrar PDF si es un documento PDF -->
                    <template v-if="inputs.document">
                        <iframe :src="'https://darkorchid-okapi-696445.hostingersite.com/storage/app/public/' + inputs.document" width="100%"
                            height="400px"></iframe>
                    </template>
                </div>
            </form>
        </template>
    </modal>
</template>

<script>
import { FormMixin } from '../../../../../core/mixins/form/FormMixin.js';
import { ModalMixin } from "../../../../Mixins/ModalMixin";

import { UserAndRoleMixin } from '../Mixins/UserAndRoleMixin';

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