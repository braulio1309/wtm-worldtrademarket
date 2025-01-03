<template>
    <modal :modal-id="userAndRoles.users.inviteModalId"
                     :title="modalTitle"
                     :preloader="preloader"
                     :modal-scroll="false"
                     @submit="submit"
                     @close-modal="closeModal">
        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <form ref="form" :data-url='`create/transaction`'
                  :class="{'loading-opacity': preloader}">
                <div class="form-group row align-items-center" v-if="!hasData">
                    <div class="form-group row align-items-center">
                    <label for="inputs_name" class="col-sm-3 mb-0">
                        Ingrese el monto a depositar
                    </label>
                    <app-input id="inputs_name"
                               class="col-sm-9"
                               type="text"
                               v-model="user.amount"
                               :placeholder="'Monto a depositar'"
                               :required="true"/>

                </div>
                </div>
            </form>
        </template>
    </modal>
</template>

<script>
    import {FormMixin} from '../../../../../core/mixins/form/FormMixin';
    import {ModalMixin} from '../../../../Mixins/ModalMixin';
    import {UserAndRoleMixin} from '../Mixins/UserAndRoleMixin';
    import * as actions from '../../../../Config/ApiUrl';


    export default {
        name: "UserInvitationModal",
        mixins: [FormMixin, ModalMixin, UserAndRoleMixin],
        data() {
            return {
                user: {},
                roles:[],
                roleLists: [],
                hasData : false,
                modalTitle: 'Deposito',
            }
        },
        created(){
            if (!_.isEmpty(this.userAndRoles.rowData)) {
                this.hasData = true;
                this.modalTitle = this.$t('manage_role'),
                    this.user.email = this.userAndRoles.rowData.email;
                this.roles = this.userAndRoles.rowData.roles.map(function(roles) {
                    return roles.id;
                });
            }
            this.getRoles();
        },
        methods: {

            submit() {
                this.input.type = 'deposit'
                this.save(this.user);
            },

            afterSuccess(res) {
                this.$toastr.s(res.data.message);
                this.reLoadTable();
            },
            getRoles(){
                let url = actions.ROLES;

                this.preloader = this.hasData ? true : false;

                this.axiosGet(url).then(response => {

                    this.roleLists = response.data.data;

                }).catch(({response}) => {

                }).finally(() => {
                    this.preloader = false;
                });
            }
        }
    }
</script>