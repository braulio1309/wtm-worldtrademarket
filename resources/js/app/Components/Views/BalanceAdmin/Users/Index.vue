<template>
    <div class="card card-with-shadow border-0 pb-primary">
        <div class="card-header d-flex align-items-center p-primary primary-card-color">
            <h5 class="card-title d-inline-block mb-0">Depositos</h5>
            <app-search @input="getSearchValue" />
        </div>
        <div class="card-body px-primary">
            <app-table :id="data.tableId" class="remove-datatable-x-padding" :options="userTableOptions"
                :filtered-data="filteredData" :search="search" @action="action" />
        </div>
    </div>
</template>

<script>
import { TableMixin } from '../Mixins/TableMixin';
import * as actions from '../../../../Config/ApiUrl';

export default {
    name: "User",
    mixins: [TableMixin],
    data() {
        return {
            value: '',
            filteredData: {},
            userTableOptions: {
                name: 'Transactions',
                url: 'transaction/statu',
                tablePaddingClass: 'pt-0',
                datatableWrapper: false,
                showHeader: true,
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
                        modifier: (value, row) => {
                            return `$${row.amount}`;
                        }
                    },
                    {
                        title: 'status',
                        type: 'text',
                        key: 'status',
                        isVisible: true,
                        modifier: (value, row) => {
                            return `${row.status}`;
                        }
                    },
                    {
                        title: this.$t('action'),
                        type: 'action',
                        key: 'id',
                        isVisible: true
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
                            type: 'modal',
                            component: 'roles-add-edit-modal',
                            modalId: 'roles-add-edit-modal',
                            modifier: (row) => {
                                return row.is_default ? false : true;
                            },
                            uniqueKey: '',
                        },
                        {
                            title: this.$t('delete'),
                            type: 'modal',
                            component: 'manage-delete-Modal',
                            modalId: 'role-delete-Modal',
                            modifier: (row) => {
                                return row.is_default ? false : true;
                            },
                        },
                ],
            },
        }
    },
    mounted() {
        //this.getStatuses();
    },
    methods: {
        getFilterValue(item) {
            this.value = item;
            this.filteredData['status-id'] = item;
            this.$hub.$emit('reload-' + this.data.tableId);
        },
        getStatuses() {
            let url = `${actions.GET_STATUSES}?type=user`;

            this.axiosGet(url).then(response => {

                this.userFilterOptions = [...this.userFilterOptions, ...response.data];

            }).catch(({ response }) => {

            }).finally(() => {

            });
        },
        update(row, value) {
            let url = `transaction/edit`;
            let data = {
                accountId: row.accountId,
                status: value
            }
            this.axiosPost(url, data).then(response => {
                console.log(response.data)
            });
        },
    }
}

</script>
