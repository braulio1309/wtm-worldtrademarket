<template>
    <div class="card card-with-shadow border-0 pb-primary">
        <div class="card-header d-flex align-items-center p-primary primary-card-color">
            <h5 class="card-title d-inline-block mb-0">Usuarios referidos</h5>
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
                    name: 'Referidos',
                    url: 'users/refered',
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
                        title: 'referred_first_name',
                        type: 'text',
                        key: 'referred_first_name',
                        isVisible: true,
                       
                    },
                    {
                        title: 'referred_last_name',
                        type: 'text',
                        key: 'referred_last_name',
                        isVisible: true,
                        modifier: (value, row) => {
                            return `${row.referred_last_name}`;
                        }
                    },
                    {
                        title: 'referred_email',
                        type: 'text',
                        key: 'referred_email',
                        isVisible: true,
                        modifier: (value, row) => {
                            return `${row.referred_email}`;
                        }
                    },
                    {
                        title: 'referred_balance',
                        type: 'text',
                        key: 'referred_balance',
                        isVisible: true,
                        modifier: (value, row) => {
                            return `${row.referred_balance}`;
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
                        {
                            title: this.$t('delete'),
                            type: 'none',
                        },
                    ],
                },
                userFilterOptions: [
                    {id: '', name: 'all_users', translated_name: 'All Users'},
                ],
            }
        },
        mounted(){
            this.getStatuses();
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
