<template>
    <modal :modal-id="userAndRoles.users.userModalId" :title="modalTitle" :preloader="preloader" @submit="submit"
        @close-modal="closeModal">
        <template slot="body">
            <app-overlay-loader v-if="preloader" />
            <div class="card card-with-shadow border-0 pb-primary">
                <div class="card-header d-flex align-items-center p-primary primary-card-color">
                    <h5 class="card-title d-inline-block mb-0">Historial de comisiones </h5>
                </div>
                <div v-if='flag' class="card-body px-primary">
                    <app-table :id="'comisiones'"
                    :options="userTableOptions"
                            class="remove-datatable-x-padding"
                            />
                </div>
            </div>
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
                value: '',
                filteredData: {},
                userTableOptions: {},
                flag: false,
                userFilterOptions: [
                    {id: '', name: 'all_users', translated_name: 'All Users'},
                ],
            }
        },
    created() {
        this.inputs = this.userAndRoles.rowData;
        this.userTableOptions = {
                    name: 'Comisiones',
                    url: `users/comisiones/${this.inputs.id}`,
                    tablePaddingClass: 'pt-0',
                    datatableWrapper: false,
                    showHeader: false,
                    tableShadow: false,
                    columns: [
                    {
                        title: 'id',
                        type: 'text',
                        key: 'id',
                        isVisible: true,
                        modifier: (value, row) => {
                            return row.id;
                        }
                    },
                    {
                        title: 'amount',
                        type: 'text',
                        key: 'amount',
                        isVisible: true,
                       
                    },
                    {
                        title: 'created_at',
                        type: 'text',
                        key: 'created_at',
                        isVisible: true,
                    },
                   
                ],
                    showSearch: false,
                    showFilter: false,
                    paginationType: 'pagination',
                    responsive: true,
                    rowLimit: 10,
                    showAction: true,
                    orderBy: 'desc',
                    actionType: "dropdown",
                    actions: [
                        {
                            title: this.$t('edit'),
                            type: 'none',
                        },
                        {
                            title: this.$t('delete'),
                            type: 'none',
                        },
                    ],
                }
                this.flag = true
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