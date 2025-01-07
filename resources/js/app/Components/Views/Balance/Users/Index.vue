<template>
    <div class="card card-with-shadow border-0 pb-primary">
        <div class="card-header d-flex align-items-center p-primary primary-card-color">
            <h5 class="card-title d-inline-block mb-0">Depositos</h5>
            <app-search @input="getSearchValue"/>
        </div>
        <div class="card-body px-primary">
            <app-table :id="data.tableId"
                       class="remove-datatable-x-padding"
                       :options="userTableOptions"
                       :filtered-data="filteredData"
                       :search="search"
                       @action="action"/>
        </div>
    </div>
</template>

<script>
    import {TableMixin} from '../Mixins/TableMixin';
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
                    url: 'transactions',
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
                            modifier:(value, row)=>{
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
                            title: 'type',
                            type: 'text',
                            key: 'type',
                            isVisible: true,
                            modifier: (value, row) => {
                                return `${row.type}`;
                            }
                        },
                        {
                            title: 'folio',
                            type: 'text',
                            key: 'folio',
                            isVisible: true,
                            modifier: (value, row) => {
                                return `${row.folio}`;
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
                            title: 'created_at',
                            type: 'text',
                            key: 'created_at',
                            isVisible: true,
                            modifier: (value, row) => {
                                return `${row.created_at}`;
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
                            type: 'none',
                        },
                        
                    ],
                },
            }
        },
        mounted(){
            //this.getStatuses();
        },
        methods: {
            getFilterValue(item) {
                this.value = item;
                this.filteredData['status-id'] = item;
                this.$hub.$emit('reload-' + this.data.tableId);
            },
            getStatuses(){
                let url = `${actions.GET_STATUSES}?type=user`;

                this.axiosGet(url).then(response => {

                    this.userFilterOptions = [...this.userFilterOptions, ...response.data];

                }).catch(({response}) => {

                }).finally(() => {

                });
            }
        }
    }
</script>
